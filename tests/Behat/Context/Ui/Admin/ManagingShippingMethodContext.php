<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingShippingMethodContext implements Context
{
	public function __construct(private readonly UpdatePageInterface $updatePage)
 {
 }

	/**
	 * @Then it should be shipped to PPL parcelshop
	 */
	public function ttShouldBeShippedToPplParcelshop()
	{
		Assert::true($this->updatePage->iSeePplParcelshopInsteadOfShippingAddress());
	}

	/**
	 * @When I enable PPL parcelshops
	 */
	public function iEnablePplParcelshops()
	{
		$this->updatePage->enablePplParcelshops();
	}

	/**
	 * @Then the PPL parcelshops should be enabled
	 */
	public function thePplParcelshopsShouldBeEnabled()
	{
		Assert::true((bool) $this->updatePage->isSingleResourceOnPage('pplCheckbox'));
	}

	/**
	 * @When I disable PPL parcelshops
	 */
	public function iDisablePplParcelshops()
	{
		$this->updatePage->disablePplParcelshops();
	}

	/**
	 * @Then the PPL parcelshops should be disabled
	 */
	public function thePplParcelshopsShouldBeDisabled()
	{
		Assert::false((bool) $this->updatePage->isSingleResourceOnPage('pplCheckbox'));
	}

	/**
	 * @When I select :countryCodes as allowed PPL countries
	 */
	public function iSelectAllowedPplCountries(string $countryCodes)
	{
		$countryList = array_map('trim', explode(',', $countryCodes));
		$this->updatePage->selectPplAllowedCountries($countryList);
	}

	/**
	 * @When I select :countryCode as default PPL country
	 */
	public function iSelectDefaultPplCountry(string $countryCode)
	{
		$this->updatePage->selectPplDefaultCountry($countryCode);
	}

	/**
	 * @Then the allowed PPL countries should be :countryCodes
	 */
	public function theAllowedPplCountriesShouldBe(string $countryCodes)
	{
		$expectedCountries = array_map('trim', explode(',', $countryCodes));
		$actualCountries = $this->updatePage->getSelectedPplAllowedCountries();

		sort($expectedCountries);
		sort($actualCountries);

		Assert::eq($actualCountries, $expectedCountries);
	}

	/**
	 * @Then the default PPL country should be :countryCode
	 */
	public function theDefaultPplCountryShouldBe(string $countryCode)
	{
		Assert::eq($this->updatePage->getSelectedPplDefaultCountry(), $countryCode);
	}

	/**
	 * @Then I should see a validation error for the default country field
	 */
	public function iShouldSeeValidationErrorForDefaultCountry()
	{
		Assert::true($this->updatePage->hasValidationErrorFor('pplDefaultCountry'));
	}
}
