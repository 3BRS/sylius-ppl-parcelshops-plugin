<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl\PplPagesInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use Webmozart\Assert\Assert;

final class ManagingPplContext implements Context
{
	public function __construct(
		private readonly PplPagesInterface $pplPages,
		private readonly CheckoutShippingContext $checkoutShippingContext,
		private readonly ShipmentRepositoryInterface $shipmentRepository,
		private readonly EntityManagerInterface $entityManager,
	) {
	}

	#[Then('I should not be able to go to the payment step again')]
	public function iShouldNotBeAbleToGoToThePaymentStepAgain()
	{
		Assert::throws(function () {
			$this->checkoutShippingContext->iShouldBeAbleToGoToThePaymentStepAgain();
		}, UnexpectedPageException::class);
	}

	#[When('I choose PPL parcelshop with ID :id, name :name and address :address')]
	public function iSelectPplBranch(string $id, string $name, string $address)
	{
		$this->pplPages->selectPplBranch($id, $name, $address);
	}

	#[Given('I have chosen PPL parcelshop with code :code and name :name')]
	#[When('I choose PPL parcelshop with code :code and name :name')]
	public function iChoosePplParcelshop(string $code, string $name): void
	{
		$this->pplPages->choosePplParcelshop($code, $name);
	}

	#[Given("/^the PPL parcelshop field contains '([^']*)'$/")]
	public function thePplParcelshopFieldContains(string $value): void
	{
		$this->pplPages->fillPplDataField($value);
	}

	#[Then('I should be notified that I have to choose a PPL parcelshop')]
	public function iShouldBeNotifiedThatIHaveToChooseAPplParcelshop(): void
	{
		Assert::contains($this->pplPages->getValidationMessage(), 'Choose PPL ParcelShop');
	}

	#[Then('I should see PPL parcelshop :name as the shipping address')]
	public function iShouldSeePplParcelshopAsTheShippingAddress(string $name): void
	{
		$shippingAddress = $this->pplPages->getShippingAddress();

		Assert::contains($shippingAddress, 'PPL ParcelShop');
		Assert::contains($shippingAddress, $name);
	}

	#[Then('my shipment should not have a PPL parcelshop')]
	public function myShipmentShouldNotHaveAPplParcelshop(): void
	{
		$shipments = $this->shipmentRepository->findAll();
		Assert::notEmpty($shipments);

		foreach ($shipments as $shipment) {
			$this->entityManager->refresh($shipment);

			Assert::isInstanceOf($shipment, PplShipmentInterface::class);
			Assert::null($shipment->getPplData());
			Assert::null($shipment->getPplKTMID());
		}
	}

	#[Given('I see PPL parcelshop instead of shipping address')]
	public function iSeePplBranchInsteadOfShippingAddress()
	{
		Assert::true($this->pplPages->iSeePplBranchInsteadOfShippingAddress());
	}
}
