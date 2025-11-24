<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
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
            ->add('pplKTMname', HiddenType::class)
            ->add('pplKTMaddress', HiddenType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (
                FormEvent $event,
            ): void {
                $orderData = $event->getData();

                assert(array_key_exists('pplKTMID', $orderData));
                assert(array_key_exists('method', $orderData));

                $orderData['pplKTMID'] = null;
                $orderData['pplKTMname'] = null;
                $orderData['pplKTMaddress'] = null;
                if (
                    array_key_exists('pplKTMID_' . $orderData['method'], $orderData) &&
                    in_array($orderData['method'], $this->pplMethodsCodes, true) &&
                    $orderData['pplKTMID_' . $orderData['method']] !== ''
                ) {
                    $orderData['pplKTMID'] = $orderData['pplKTMID_' . $orderData['method']];
                    $orderData['pplKTMname'] = $orderData['pplKTMname_' . $orderData['method']];
                    $orderData['pplKTMaddress'] = $orderData['pplKTMaddress_' . $orderData['method']];
                }

                $event->setData($orderData);

                // validation
                $data = $event->getData();
                if (array_key_exists('pplKTMID_' . $data['method'], $data) && !((bool) $orderData['pplKTMID_' . $orderData['method']])) {
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

                foreach ($shippingMethods as $method) {
                    assert($method instanceof ShippingMethodInterface);
                    assert($method instanceof PplShippingMethodInterface);

                    if ($method->getPplParcelshopsShippingMethod()) {
                        assert($method->getCode() !== null);
                        $zone = $method->getZone();
                        assert($zone instanceof \Sylius\Component\Addressing\Model\ZoneInterface);

                        $dataLabel = null;
                        if ($selectedMethodCode !== null && $selectedMethodCode === $method->getCode() && $shipment->getPplKTMID() !== null) {
                            $dataLabel = $shipment->getPplKTMname() . ', ' . $shipment->getPplKTMaddress();
                        }

                        $this->pplMethodsCodes[] = $method->getCode();
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
