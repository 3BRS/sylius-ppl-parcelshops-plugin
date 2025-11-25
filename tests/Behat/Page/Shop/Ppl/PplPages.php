<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

final class PplPages extends BaseUpdatePage implements PplPagesInterface
{
	public function selectPplBranch(string $id, string $name, string $address): void
	{
		// Click the "Choose PPL ParcelShop" button to open modal
		$button = $this->getElement('ppl_select_button');
		$button->click();

		// Wait for modal to appear
		$this->getDocument()->waitFor(10, function () {
			return $this->hasElement('ppl_modal') && $this->getElement('ppl_modal')->isVisible();
		});

		// Wait for PPL widget to load inside modal
		$this->getDocument()->waitFor(10, function () {
			return $this->hasElement('ppl_widget_container');
		});

		// Mock/simulate parcelshop selection by directly populating the hidden JSON field
		// In a real scenario, this would interact with the PPL widget
		// For testing purposes, we create a mock JSON response matching PPL's format
		$mockPplData = [
			'code' => $id, // PPL parcelshop code (e.g., "KM10833808" or just "1" for testing)
			'name' => $name,
			'address' => [
				'street' => explode(',', $address)[0] ?? $address,
				'city' => 'Prague',
				'zipCode' => '12345',
				'country' => 'CZ',
			],
			'accessPointType' => 'ParcelShop',
			'gps' => [
				'latitude' => 50.0755,
				'longitude' => 14.4378,
			],
		];

		// Set the JSON data in the hidden field
		$this->getSession()->evaluateScript(sprintf(
			"document.querySelector('input[name*=\"ppl_data_\"]').value = %s;",
			json_encode(json_encode($mockPplData))
		));

		// Trigger the custom event to simulate PPL widget selection
		$this->getSession()->evaluateScript(sprintf(
			"document.dispatchEvent(new CustomEvent('ppl-parcelshop-map', { detail: %s }));",
			json_encode($mockPplData)
		));

		// Wait a moment for the JavaScript to process the event
		$this->getSession()->wait(500);

		// Close the modal if it's still open
		if ($this->hasElement('ppl_modal_close') && $this->getElement('ppl_modal')->isVisible()) {
			$this->getElement('ppl_modal_close')->click();

			// Wait for modal to close
			$this->getDocument()->waitFor(5, function () {
				return !$this->getElement('ppl_modal')->isVisible();
			});
		}
	}

	public function iSeePplBranchInsteadOfShippingAddress(): bool
	{
		$shippingAddress = $this->getElement('shippingAddress')->getText();

		return str_contains($shippingAddress, 'PPL ParcelShop');
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			// New modal elements
			'ppl_select_button' => 'button[id*="_button"][onclick*="openPplModal"]',
			'ppl_modal' => 'div[id*="_modal"].ui.modal',
			'ppl_modal_close' => 'div[id*="_modal"] i.close.icon',
			'ppl_widget_container' => '#ppl-parcelshop-map',
			// Legacy hidden inputs (still used for backward compatibility)
			'ppl_hidden_input_id' => 'input[type="hidden"][name*="pplKTMID_"]',
			'ppl_hidden_input_name' => 'input[type="hidden"][name*="pplKTMname_"]',
			'ppl_hidden_input_address' => 'input[type="hidden"][name*="pplKTMaddress_"]',
			'ppl_hidden_json_data' => 'input[type="hidden"][name*="ppl_data_"]',
			'shippingAddress' => '#sylius-shipping-address',
		]);
	}
}
