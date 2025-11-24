<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
	public function enablePplParcelshops(): void
	{
		$this->getElement('pplCheckbox')->setValue(true);
	}

	public function disablePplParcelshops(): void
	{
		$this->getElement('pplCheckbox')->setValue(false);
	}

	public function isSingleResourceOnPage(string $elementName)
	{
		return $this->getElement($elementName)->getValue();
	}

	public function iSeePplParcelshopInsteadOfShippingAddress(): bool
	{
		$shippingAddress = $this->getElement('shippingAddress')->getText();

		return str_contains($shippingAddress, 'PPL ParcelShop');
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'pplCheckbox' => '#sylius_shipping_method_pplParcelshopsShippingMethod',
			'shippingAddress' => '#shipping-address',
		]);
	}
}
