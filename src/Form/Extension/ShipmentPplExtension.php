<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Form\Extension;

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

class ShipmentPplExtension extends AbstractTypeExtension
{
    /** @var string[]; */
    private array $pplMethodsCodes = [];

    public function __construct(
        private readonly ShippingMethodsResolverInterface $shippingMethodsResolver,
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /** @param array<mixed> $options */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder
            ->add('pplKTMID', HiddenType::class)
            // Deprecated fields - kept for backward compatibility
            ->add('pplKTMname', HiddenType::class)
            ->add('pplKTMaddress', HiddenType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (
                FormEvent $event,
            ): void {
                $orderData = $event->getData();

                assert(\array_key_exists('pplKTMID', $orderData));
                assert(\array_key_exists('method', $orderData));

                $orderData['pplKTMID'] = null;
                $orderData['pplKTMname'] = null;
                $orderData['pplKTMaddress'] = null;

                // Handle new JSON-based data
                if (
                    \array_key_exists('ppl_data_' . $orderData['method'], $orderData) &&
                    \in_array($orderData['method'], $this->pplMethodsCodes, true) &&
                    $orderData['ppl_data_' . $orderData['method']] !== ''
                ) {
                    $pplData = \json_decode($orderData['ppl_data_' . $orderData['method']], true);

                    if ($pplData && \is_array($pplData)) {
                        // Extract data for deprecated fields (backward compatibility)
                        $orderData['pplKTMID'] = $pplData['code'] ?? null;
                        $orderData['pplKTMname'] = $pplData['name'] ?? null;

                        // Build address string from address object
                        $addressParts = [];
                        if (isset($pplData['address'])) {
                            if (isset($pplData['address']['street'])) {
                                $addressParts[] = $pplData['address']['street'];
                            }
                            if (isset($pplData['address']['city'])) {
                                $addressParts[] = $pplData['address']['city'];
                            }
                            if (isset($pplData['address']['zipCode'])) {
                                $addressParts[] = $pplData['address']['zipCode'];
                            }
                        }
                        $orderData['pplKTMaddress'] = \implode(', ', $addressParts);

                        // Also populate deprecated per-method fields if they exist
                        if (\array_key_exists('pplKTMID_' . $orderData['method'], $orderData)) {
                            $orderData['pplKTMID_' . $orderData['method']] = $orderData['pplKTMID'];
                            $orderData['pplKTMname_' . $orderData['method']] = $orderData['pplKTMname'];
                            $orderData['pplKTMaddress_' . $orderData['method']] = $orderData['pplKTMaddress'];
                        }
                    }
                }
                // Fallback: handle old redirect-based data (backward compatibility)
                elseif (
                    \array_key_exists('pplKTMID_' . $orderData['method'], $orderData) &&
                    \in_array($orderData['method'], $this->pplMethodsCodes, true) &&
                    $orderData['pplKTMID_' . $orderData['method']] !== ''
                ) {
                    $orderData['pplKTMID'] = $orderData['pplKTMID_' . $orderData['method']];
                    $orderData['pplKTMname'] = $orderData['pplKTMname_' . $orderData['method']];
                    $orderData['pplKTMaddress'] = $orderData['pplKTMaddress_' . $orderData['method']];
                }

                $event->setData($orderData);

                // Validation - check if parcelshop was selected for PPL methods
                $data = $event->getData();
                if (
                    \in_array($data['method'], $this->pplMethodsCodes, true) &&
                    (
                        (\array_key_exists('ppl_data_' . $data['method'], $data) && empty($data['ppl_data_' . $data['method']])) ||
                        (\array_key_exists('pplKTMID_' . $data['method'], $data) && empty($data['pplKTMID_' . $data['method']]))
                    ) &&
                    empty($orderData['pplKTMID'])
                ) {
                    $event->getForm()->addError(new FormError($this->translator->trans('threebrs.shop.checkout.pplBranch', [], 'validators')));
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (
                FormEvent $event,
            ) {
                $form = $event->getForm();
                $shipment = $event->getData();
                if ($shipment && $this->shippingMethodsResolver->supports($shipment)) {
                    $shippingMethods = $this->shippingMethodsResolver->getSupportedMethods($shipment);
                } else {
                    $shippingMethods = $this->shippingMethodRepository->findAll();
                }

                assert($shipment instanceof ShipmentInterface);
                assert($shipment instanceof PplShipmentInterface);

                $selectedMethodCode = $shipment !== null && $shipment->getMethod() instanceof \Sylius\Component\Shipping\Model\ShippingMethodInterface
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
                    // For Czech Republic, use approximate coordinates based on postal code prefix
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

                        if ($selectedMethodCode !== null && $selectedMethodCode === $method->getCode()) {
                            // Try to get stored JSON data first
                            if (\method_exists($shipment, 'getPplData') && $shipment->getPplData() !== null) {
                                $pplJsonData = $shipment->getPplData();
                                $pplArray = \json_decode($pplJsonData, true);
                                if ($pplArray && isset($pplArray['name'])) {
                                    $labelParts = [$pplArray['name']];
                                    if (isset($pplArray['address']['street'])) {
                                        $labelParts[] = $pplArray['address']['street'];
                                    }
                                    if (isset($pplArray['address']['city'])) {
                                        $labelParts[] = $pplArray['address']['city'];
                                    }
                                    $dataLabel = \implode(', ', $labelParts);
                                }
                            }
                            // Fallback to deprecated fields
                            elseif ($shipment->getPplKTMID() !== null) {
                                $dataLabel = $shipment->getPplKTMname() . ', ' . $shipment->getPplKTMaddress();
                            }
                        }

                        $this->pplMethodsCodes[] = $method->getCode();

                        // Add new JSON data field
                        $form->add('ppl_data_' . $method->getCode(), HiddenType::class, [
                            'attr' => [
                                'data-country' => $method->getPplOptionCountry(),
                                'data-lat' => $latitude,
                                'data-lng' => $longitude,
                                'data-label' => $dataLabel,
                            ],
                            'data' => $pplJsonData,
                            'required' => false,
                            'mapped' => false,
                        ]);

                        // Add deprecated fields for backward compatibility
                        $form
                            ->add('pplKTMID_' . $method->getCode(), HiddenType::class, [
                                'attr' => [
                                    'data-country' => $method->getPplOptionCountry(),
                                    'data-label' => $dataLabel,
                                ],
                                'data' => $shipment->getPplKTMID(),
                                'required' => false,
                                'mapped' => false,
                            ])
                            ->add('pplKTMname_' . $method->getCode(), HiddenType::class, [
                                'required' => false,
                                'mapped' => false,
                                'data' => $shipment->getPplKTMname(),
                            ])
                            ->add('pplKTMaddress_' . $method->getCode(), HiddenType::class, [
                                'required' => false,
                                'mapped' => false,
                                'data' => $shipment->getPplKTMaddress(),
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
                            $shipment->setPplData($pplJsonData);
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
}
