<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class PplPages extends BaseUpdatePage implements PplPagesInterface
{
	public function selectPplBranch(int $id, string $name, string $address): void
	{
		$this->getElement('ppl_hidden_input_id')->setValue($id);
		$this->getElement('ppl_hidden_input_name')->setValue($name);
		$this->getElement('ppl_hidden_input_address')->setValue($address);
	}

	public function iSeePplBranchInsteadOfShippingAddress(): bool
	{
		$shippingAddress = $this->getElement('shippingAddress')->getText();

		return str_contains($shippingAddress, 'PPL ParcelShop');
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'ppl_hidden_input_id' => 'input[type="hidden"][name^="sylius_checkout_select_shipping[shipments][0][pplKTMID_"]',
			'ppl_hidden_input_name' => 'input[type="hidden"][name^="sylius_checkout_select_shipping[shipments][0][pplKTMname_"]',
			'ppl_hidden_input_address' => 'input[type="hidden"][name^="sylius_checkout_select_shipping[shipments][0][pplKTMaddress_"]',
			'shippingAddress' => '#sylius-shipping-address',
		]);
	}
}
