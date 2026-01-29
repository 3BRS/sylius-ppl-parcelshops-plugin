<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Symfony\Component\Workflow\WorkflowInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;

final class ShippingMethodContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly WorkflowInterface $syliusOrderCheckoutWorkflow,
    ) {
    }

    /**
     * @Given /^(this shipping method) is enabled PPL parcelshops$/
     */
    public function thisPaymentMethodHasZone(ShippingMethodInterface $shippingMethod): void
    {
        assert($shippingMethod instanceof PplShippingMethodInterface);

        $shippingMethod->setPplParcelshopsShippingMethod(true);

        $this->entityManager->persist($shippingMethod);
        $this->entityManager->flush();
    }

    /**
     * @Given choose PPL parcelshop ID ":id", name ":name" and address ":address"
     */
    public function choosePplBranch(string $id, string $name, string $address): void
    {
        $order = $this->sharedStorage->get('order');
        assert($order instanceof OrderInterface);

        $shipment = $order->getShipments()->last();
        assert($shipment instanceof PplShipmentInterface);

        // Create minimal PPL data structure for testing
        $shipment->setPplData([
            'id' => (int) $id,
            'accessPointType' => 'PARCEL_SHOP',
            'code' => $id,
            'dhlPsId' => '',
            'depot' => '',
            'depotName' => '',
            'name' => $name,
            'street' => $address,
            'city' => '',
            'zipCode' => '',
            'country' => 'CZ',
            'parcelshopName' => $name,
            'gps' => ['latitude' => 0.0, 'longitude' => 0.0],
            'www' => '',
            'ktmNote' => '',
            'openHours' => [],
            'capacityStatus' => 'OK',
            'externalNumbers' => [],
            'capacitySettings' => [],
            'visiblePs' => true,
            'activeCardPayment' => false,
            'tribalServicePoint' => false,
            'dimensionForced' => false,
            'pickupEnabled' => true,
            'activeCashPayment' => false,
            'newPs' => false,
            'accessPointTypeInternal' => ['id' => 1, 'name' => 'ParcelShop'],
            'distance' => 0.0,
            'isCapacityAvailable' => true,
            'availableCmCodes' => [],
        ]);

        // Complete the order so shipment transitions to ready state
        if ($this->syliusOrderCheckoutWorkflow->can($order, OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            $this->syliusOrderCheckoutWorkflow->apply($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
