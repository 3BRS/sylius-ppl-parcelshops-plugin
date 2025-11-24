<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ShippingMethodContext implements Context
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var SharedStorageInterface */
	private $sharedStorage;

	public function __construct(
		EntityManagerInterface $entityManager,
		SharedStorageInterface $sharedStorage
	) {
		$this->entityManager = $entityManager;
		$this->sharedStorage = $sharedStorage;
	}

	/**
	 * @Given /^(this shipping method) is enabled PPL parcelshops$/
	 */
	public function thisPaymentMethodHasZone(ShippingMethodInterface $shippingMethod)
	{
		assert($shippingMethod instanceof PplShippingMethodInterface);

		$shippingMethod->setPplParcelshopsShippingMethod(true);

		$this->entityManager->persist($shippingMethod);
		$this->entityManager->flush();
	}

	/**
	 * @Given choose PPL parcelshop ID ":id", name ":name" and address ":address"
	 */
	public function choosePplBranch(int $id, string $name, string $address)
	{
		$order = $this->sharedStorage->get('order');
		assert($order instanceof OrderInterface);

		$shipment = $order->getShipments()->last();
		assert($shipment instanceof PplShipmentInterface);

		$shipment->setPplKTMname($name);
		$shipment->setPplKTMaddress($address);
		$shipment->setPplKTMID($id);

		$this->entityManager->persist($order);
		$this->entityManager->flush();
	}
}
