<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
	public function enablePplParcelshops(): void;

	public function disablePplParcelshops(): void;

	public function isSingleResourceOnPage(string $elementName);

	public function iSeePplParcelshopInsteadOfShippingAddress(): bool;

	/**
	 * @param array<string> $countries
	 */
	public function selectPplAllowedCountries(array $countries): void;

	public function selectPplDefaultCountry(string $country): void;

	/**
	 * @return array<string>
	 */
	public function getSelectedPplAllowedCountries(): array;

	public function getSelectedPplDefaultCountry(): ?string;

	public function hasValidationErrorFor(string $field): bool;
}
