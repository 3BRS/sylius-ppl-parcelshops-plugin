<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class PplPages extends SymfonyPage implements PplPagesInterface
{
    public function getRouteName(): string
    {
        // This page object works on multiple routes (shipping selection and complete)
        // Return empty string as we don't navigate to this page directly
        return '';
    }

    public function selectPplBranch(
        string $id,
        string $name,
        string $address,
    ): void {
        // Wait for PPL selector container to be visible
        $this->getDocument()->waitFor(20, function () {
            if (!$this->hasElement('ppl_selector_container')) {
                return false;
            }
            $container = $this->getElement('ppl_selector_container');

            return $container->isVisible();
        });

        // Wait for button to be enabled and clickable
        $this->getDocument()->waitFor(20, function () {
            if (!$this->hasElement('ppl_select_button')) {
                return false;
            }
            $button = $this->getElement('ppl_select_button');

            return $button->isVisible() && !$button->hasAttribute('disabled');
        });

        // Click the "Choose PPL ParcelShop" button to open modal
        $button = $this->getElement('ppl_select_button');
        $button->click();

        // Wait for Bootstrap 5 modal to appear
        $this->getDocument()->waitFor(10, function () {
            return $this->hasElement('ppl_modal') && $this->getElement('ppl_modal')->hasClass('show');
        });

        // Wait for PPL widget container inside modal
        $this->getDocument()->waitFor(10, function () {
            return $this->hasElement('ppl_widget_container');
        });

        // Mock/simulate parcelshop selection by directly populating the hidden JSON field
        // In a real scenario, this would interact with the PPL widget
        // For testing purposes, we create a mock JSON response matching PPL's format
        $mockPplData = [
            'code' => $id, // PPL parcelshop code (e.g., "KM10833808" or just "1" for testing)
            'name' => $name,
            'street' => explode(',', $address)[0] ?? $address,
            'city' => 'Prague',
            'zipCode' => '12345',
            'country' => 'CZ',
            'accessPointType' => 'ParcelShop',
            'gps' => [
                'latitude' => 50.0755,
                'longitude' => 14.4378,
            ],
        ];

        // Set the JSON data in the hidden field
        $this->getSession()->evaluateScript(sprintf(
            "document.querySelector('input[name*=\"ppl_data_\"]').value = %s;",
            json_encode(json_encode($mockPplData)),
        ));

        // Trigger the custom event to simulate PPL widget selection
        $this->getSession()->evaluateScript(sprintf(
            "document.dispatchEvent(new CustomEvent('ppl-parcelshop-map', { detail: %s }));",
            json_encode($mockPplData),
        ));

        // Wait a moment for the JavaScript to process the event
        $this->getSession()->wait(500);

        // Close the Bootstrap 5 modal if it's still open
        if ($this->hasElement('ppl_modal') && $this->getElement('ppl_modal')->hasClass('show')) {
            // Use Bootstrap close button
            if ($this->hasElement('ppl_modal_close')) {
                $this->getElement('ppl_modal_close')->click();
            } else {
                // Fallback: close via JavaScript
                $this->getSession()->evaluateScript(
                    "document.querySelector('.modal.show .btn-close')?.click();",
                );
            }

            // Wait for modal to close
            $this->getDocument()->waitFor(5, function () {
                $modal = $this->getElement('ppl_modal');

                return !$modal->hasClass('show');
            });
        }
    }

    public function iSeePplBranchInsteadOfShippingAddress(): bool
    {
        // Wait for the page to load
        $this->getSession()->wait(2000);

        // Try multiple selectors for shipping address in Sylius 2.0
        $selectors = [
            '[data-test-shipping-address]',
            '#sylius-shipping-address',
            '.shipping-address',
            '[data-test-order-shipping-address]',
        ];

        foreach ($selectors as $selector) {
            $element = $this->getDocument()->find('css', $selector);
            if ($element !== null) {
                $text = $element->getText();

                return str_contains($text, 'PPL') || str_contains($text, 'ParcelShop');
            }
        }

        // Fallback: search anywhere on the page for PPL parcelshop indication
        $pageText = $this->getDocument()->getText();

        return str_contains($pageText, 'PPL ParcelShop') || str_contains($pageText, 'PPL 1');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            // Bootstrap 5 / Sylius 2.0 selectors
            'ppl_selector_container' => '.ppl-selector-container',
            'ppl_select_button' => '.ppl-selector-container button[data-ppl-button]',
            'ppl_modal' => '.modal[data-ppl-modal-id]',
            'ppl_modal_close' => '.modal[data-ppl-modal-id] .btn-close',
            'ppl_widget_container' => '.ppl-map-container',
            'shippingAddress' => '[data-test-shipping-address], #sylius-shipping-address, .shipping-address',
        ]);
    }
}
