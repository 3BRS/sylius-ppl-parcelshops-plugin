<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePage as BaseUpdatePage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials\WaitForElementTrait;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use WaitForElementTrait;

    public function saveChanges(): void
    {
        parent::saveChanges();

        self::waitForPageToLoad($this->getSession());
    }

    public function enablePplParcelshops(): void
    {
        // Use JavaScript to check the checkbox (it's hidden by Semantic UI)
        $this->getSession()->executeScript("document.getElementById('sylius_shipping_method_pplParcelshopsShippingMethod').checked = true;");
    }

    public function disablePplParcelshops(): void
    {
        // Use JavaScript to uncheck the checkbox (it's hidden by Semantic UI)
        $this->getSession()->executeScript("document.getElementById('sylius_shipping_method_pplParcelshopsShippingMethod').checked = false;");
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

    /**
     * @param array<string> $countries
     */
    public function selectPplAllowedCountries(array $countries): void
    {
        // Set the values directly on the select element
        $countriesJson = json_encode($countries);
        $script        = sprintf(
            "var select = document.getElementById('sylius_shipping_method_pplOptionCountries'); " .
            "var values = %s; " .
            "Array.from(select.options).forEach(function(option) { " .
            "  option.selected = values.includes(option.value); " .
            "}); " .
            "$(select).trigger('change');",
            $countriesJson,
        );
        $this->getSession()->executeScript($script);
    }

    public function selectPplDefaultCountry(string $country): void
    {
        // Set the value directly on the select element
        $script = sprintf(
            "var select = document.getElementById('sylius_shipping_method_pplDefaultCountry'); " .
            "select.value = '%s'; " .
            "$(select).trigger('change');",
            $country,
        );
        $this->getSession()->executeScript($script);
    }

    /**
     * @return array<string>
     */
    public function getSelectedPplAllowedCountries(): array
    {
        // Use JavaScript to get selected values to avoid Mink validation
        $script = "return $('#sylius_shipping_method_pplOptionCountries').val() || [];";
        $result = $this->getSession()->evaluateScript($script);

        return is_array($result)
            ? $result
            : [];
    }

    public function getSelectedPplDefaultCountry(): ?string
    {
        // Use JavaScript to get selected value to avoid Mink validation
        $script = "return document.getElementById('sylius_shipping_method_pplDefaultCountry').value || null;";
        $result = $this->getSession()->evaluateScript($script);

        return $result
            ?: null;
    }

    public function hasValidationErrorFor(string $field): bool
    {
        $fieldElement = $this->getElement($field . 'Select');
        $formGroup    = $fieldElement->getParent();

        return $formGroup->hasClass('error') || $formGroup->find('css', '.sylius-validation-error') !== null;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pplCheckbox'              => '#sylius_shipping_method_pplParcelshopsShippingMethod',
            'shippingAddress'          => '#shipping-address',
            'pplDefaultCountrySelect'  => 'select#sylius_shipping_method_pplDefaultCountry',
            'pplOptionCountriesSelect' => 'select#sylius_shipping_method_pplOptionCountries',
        ]);
    }
}
