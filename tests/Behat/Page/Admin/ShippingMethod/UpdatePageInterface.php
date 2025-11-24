<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
	public function enablePplParcelshops(): void;

	public function disablePplParcelshops(): void;

	public function isSingleResourceOnPage(string $elementName);

	public function iSeePplParcelshopInsteadOfShippingAddress(): bool;
}
