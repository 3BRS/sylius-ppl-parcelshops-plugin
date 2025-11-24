<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl\PplPagesInterface;
use Webmozart\Assert\Assert;

final class ManagingPplContext implements Context
{
	/** @var CheckoutShippingContext */
	private $checkoutShippingContext;

	/** @var PplPagesInterface */
	private $pplPages;

	public function __construct(
		PplPagesInterface $pplPages,
		CheckoutShippingContext $checkoutShippingContext
	) {
		$this->checkoutShippingContext = $checkoutShippingContext;
		$this->pplPages = $pplPages;
	}

	/**
	 * @Then I should not be able to go to the payment step again
	 */
	public function iShouldNotBeAbleToGoToThePaymentStepAgain()
	{
		Assert::throws(function () {
			$this->checkoutShippingContext->iShouldBeAbleToGoToThePaymentStepAgain();
		}, UnexpectedPageException::class);
	}

	/**
	 * @Then I choose PPL parcelshop with ID ":id", name ":name" and address ":address"
	 */
	public function iSelectPplBranch(int $id, string $name, string $address)
	{
		$this->pplPages->selectPplBranch($id, $name, $address);
	}

	/**
	 * @Given I see PPL parcelshop instead of shipping address
	 */
	public function iSeePplBranchInsteadOfShippingAddress()
	{
		Assert::true($this->pplPages->iSeePplBranchInsteadOfShippingAddress());
	}
}
