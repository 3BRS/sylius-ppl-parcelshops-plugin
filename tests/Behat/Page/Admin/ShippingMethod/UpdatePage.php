<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function enablePplParcelshops(): void
    {
        $checkbox = $this->getElement('pplCheckbox');
        if (!$checkbox->isChecked()) {
            $checkbox->check();
        }
    }

    public function disablePplParcelshops(): void
    {
        $checkbox = $this->getElement('pplCheckbox');
        if ($checkbox->isChecked()) {
            $checkbox->uncheck();
        }
    }

    public function isSingleResourceOnPage(string $elementName): bool|string|null
    {
        $element = $this->getElement($elementName);

        if ($element->getAttribute('type') === 'checkbox') {
            return $element->isChecked();
        }

        return $element->getValue();
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
        $select = $this->getElement('pplOptionCountriesSelect');
        foreach ($countries as $country) {
            $select->selectOption($country, true);
        }
    }

    public function selectPplDefaultCountry(string $country): void
    {
        $this->getElement('pplDefaultCountrySelect')->selectOption($country);
    }

    /**
     * @return array<string>
     */
    public function getSelectedPplAllowedCountries(): array
    {
        $value = $this->getElement('pplOptionCountriesSelect')->getValue();

        return is_array($value) ? $value : [];
    }

    public function getSelectedPplDefaultCountry(): ?string
    {
        $value = $this->getElement('pplDefaultCountrySelect')->getValue();

        return is_string($value) ? $value : null;
    }

    public function hasValidationErrorFor(string $field): bool
    {
        $elementName = $field . 'Select';
        $fieldElement = $this->getElement($elementName);
        $formGroup = $fieldElement->getParent();

        // Check for Bootstrap 5 validation classes
        return $formGroup->hasClass('is-invalid')
            || $formGroup->find('css', '.invalid-feedback') !== null
            || $formGroup->find('css', '.sylius-validation-error') !== null;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pplCheckbox' => '#sylius_admin_shipping_method_pplParcelshopsShippingMethod',
            'shippingAddress' => '[data-test-shipping-address]',
            'pplDefaultCountrySelect' => '#sylius_admin_shipping_method_pplDefaultCountry',
            'pplOptionCountriesSelect' => '#sylius_admin_shipping_method_pplOptionCountries',
        ]);
    }
}
