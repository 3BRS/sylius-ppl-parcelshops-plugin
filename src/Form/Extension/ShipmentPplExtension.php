<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Form\Extension;

use JsonException;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;
use Traversable;

class ShipmentPplExtension extends AbstractTypeExtension
{
    /** @var string[] */
    private array $pplMethodsCodes = [];

    public function __construct(
        private readonly ShippingMethodsResolverInterface $shippingMethodsResolver,
        /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface> */
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
        private readonly TranslatorInterface $translator,
        /** @var array<string>|null */
        private readonly ?array $allowedCountries,
    ) {
    }

    /** @param array<mixed> $options */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (
                FormEvent $event,
            ): void {
                $orderData = $event->getData();

                if (empty($orderData)) {
                    return;
                }

                if ($orderData instanceof Traversable) {
                    $orderData = iterator_to_array($orderData);
                }

                if (!\is_array($orderData)) {
                    return;
                }

                assert(\array_key_exists('method', $orderData));
                $method = $orderData['method'];

                // Validation - check if parcelshop was selected for PPL methods
                if (
                    \in_array($method, $this->pplMethodsCodes, true) &&
                    \array_key_exists('ppl_data_' . $method, $orderData) &&
                    empty($orderData['ppl_data_' . $method])
                ) {
                    $event->getForm()->addError(new FormError($this->translator->trans('threebrs.shop.checkout.pplBranch', [], 'validators')));
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (
                FormEvent $event,
            ) {
                $form = $event->getForm();
                $shipment = $event->getData();

                if ($shipment instanceof ShipmentInterface && $this->shippingMethodsResolver->supports($shipment)) {
                    $shippingMethods = $this->shippingMethodsResolver->getSupportedMethods($shipment);
                } else {
                    $shippingMethods = $this->shippingMethodRepository->findAll();
                }

                if (!$shipment instanceof PplShipmentInterface) {
                    return;
                }

                $selectedMethodCode = $shipment->getMethod() instanceof ShippingMethodInterface
                    ? $shipment->getMethod()->getCode()
                    : null;

                // Get shipping address for lat/lng
                $order = $shipment->getOrder();
                $shippingAddress = $order?->getShippingAddress();
                $latitude = '50.0755'; // Default: Prague
                $longitude = '14.4378';

                // Try to get more accurate coordinates from address
                // In production, you might want to geocode the address
                if ($shippingAddress) {
                    // For the Czech Republic, use approximate coordinates based on postal code prefix
                    // This is a simple approximation - for better accuracy, use a geocoding service
                    $postcode = $shippingAddress->getPostcode();
                    if ($postcode && \preg_match('/^(\d{3})/', $postcode, $matches)) {
                        // Simple approximation - you could improve this with a lookup table
                        $prefix = (int) $matches[1];
                        // Keep Prague coordinates as default
                    }
                }

                foreach ($shippingMethods as $method) {
                    assert($method instanceof ShippingMethodInterface);
                    assert($method instanceof PplShippingMethodInterface);

                    if ($method->getPplParcelshopsShippingMethod()) {
                        assert($method->getCode() !== null);
                        $zone = $method->getZone();
                        assert($zone instanceof ZoneInterface);

                        $dataLabel = null;
                        $pplJsonData = null;
                        $selectedPplCode = null;

                        if ($selectedMethodCode !== null && $selectedMethodCode === $method->getCode()) {
                            // Try to get stored JSON data first
                            $pplData = $shipment->getPplData();
                            if ($pplData !== null) {
                                // Store the JSON data for the hidden field
                                $pplJsonData = \json_encode($pplData);

                                if (isset($pplData['name'])) {
                                    $dataLabel = $pplData['name'];
                                }

                                // Store code for pre-selection in widget
                                $selectedPplCode = $pplData['code'];
                            } // Fallback to deprecated fields
                            elseif ($shipment->getPplKTMID() !== null) {
                                $dataLabel = $shipment->getPplKTMname() . ', ' . $shipment->getPplKTMaddress();
                            }
                        }

                        $this->pplMethodsCodes[] = $method->getCode();

                        // Add JSON data field
                        $form->add('ppl_data_' . $method->getCode(), HiddenType::class, [
                            'attr' => [
                                'data-allowed-countries' => $this->getAllowedCountriesForMethod($method),
                                'data-default-country' => $method->getPplDefaultCountry(),
                                'data-lat' => $latitude,
                                'data-lng' => $longitude,
                                'data-label' => $dataLabel,
                                'data-selected-code' => $selectedPplCode,
                            ],
                            'data' => $pplJsonData,
                            'required' => false,
                            'mapped' => false,
                        ]);
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (
                FormEvent $event,
            ): void {
                $shipment = $event->getData();
                $form = $event->getForm();

                if (!$shipment instanceof PplShipmentInterface) {
                    return;
                }

                // Find which PPL method was selected and save its JSON data
                $methodCode = $shipment->getMethod()?->getCode();
                if ($methodCode && \in_array($methodCode, $this->pplMethodsCodes, true)) {
                    $pplDataFieldName = 'ppl_data_' . $methodCode;

                    // Check if the form has this field
                    if ($form->has($pplDataFieldName)) {
                        $pplDataField = $form->get($pplDataFieldName);
                        $pplJsonData = $pplDataField->getData();

                        if ($pplJsonData && \is_string($pplJsonData)) {
                            try {
                                $pplData = \json_decode($pplJsonData, true, 512, \JSON_THROW_ON_ERROR);
                            } catch (JsonException $jsonException) {
                                trigger_error($jsonException->getMessage(), \E_USER_WARNING);

                                return;
                            }
                            // @phpstan-ignore argument.type
                            $shipment->setPplData($pplData);
                        }
                    }
                }
            });
    }

    /** @return array<int, string> */
    public static function getExtendedTypes(): array
    {
        return [
            ShipmentType::class,
        ];
    }

    private function getAllowedCountriesForMethod(PplShippingMethodInterface $pplShippingMethod): ?string
    {
        $allowedCountriesForMethod = $pplShippingMethod->getPplOptionCountries();
        if (!$allowedCountriesForMethod) {
            return null;
        }
        $allowedCountries = $this->allowedCountries;
        if ($allowedCountries === null) {
            return null;
        }
        $loweredAllowedCountriesForMethod = array_map('strtolower', $allowedCountriesForMethod);
        $loweredAllowedCountries = array_map('strtolower', $allowedCountries);

        return implode(', ', array_intersect($loweredAllowedCountries, $loweredAllowedCountriesForMethod));
    }
}
