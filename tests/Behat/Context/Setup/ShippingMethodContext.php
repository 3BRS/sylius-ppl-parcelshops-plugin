<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;

final class ShippingMethodContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly StateMachineInterface $stateMachine,
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

        $shipment->setPplKTMname($name);
        $shipment->setPplKTMaddress($address);
        $shipment->setPplKTMID($id);

        // Complete the order so shipment transitions to ready state
        if ($this->stateMachine->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            $this->stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
