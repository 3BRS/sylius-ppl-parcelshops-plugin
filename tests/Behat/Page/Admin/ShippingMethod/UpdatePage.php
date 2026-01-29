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

        $this->waitForPageToLoad();
    }

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

    public function isSingleResourceOnPage(string $elementName): mixed
    {
        return $this->getElement($elementName)->getValue();
    }

    public function iSeePplParcelshopInsteadOfShippingAddress(): bool
    {
        $shippingAddress = $this->getElement('shippingAddress')->getText();

        return str_contains($shippingAddress, 'PPL ParcelShop') ||
            str_contains($shippingAddress, 'PPL 1') ||
            str_contains($shippingAddress, 'PPL Address');
    }

    /**
     * @param array<string> $countries
     */
    public function selectPplAllowedCountries(array $countries): void
    {
        $select = $this->getElement('pplOptionCountriesSelect');
        foreach ($countries as $country) {
            $select->selectOption($country, true); // true for multiple
        }
    }

    public function selectPplDefaultCountry(string $country): void
    {
        $select = $this->getElement('pplDefaultCountrySelect');
        $select->selectOption($country);
    }

    /**
     * @return array<string>
     */
    public function getSelectedPplAllowedCountries(): array
    {
        $select = $this->getElement('pplOptionCountriesSelect');
        $selectedOptions = $select->findAll('css', 'option[selected]');

        $result = [];
        foreach ($selectedOptions as $option) {
            $value = $option->getValue();
            if (\is_string($value)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function getSelectedPplDefaultCountry(): ?string
    {
        $select = $this->getElement('pplDefaultCountrySelect');
        $selectedOption = $select->find('css', 'option[selected]');

        if ($selectedOption === null) {
            return null;
        }

        $value = $selectedOption->getValue();

        return \is_string($value) ? $value : null;
    }

    public function hasValidationErrorFor(string $field): bool
    {
        $elementName = $field . 'Select';
        $this->waitForElement(5, $elementName);

        $fieldElement = $this->getElement($elementName);

        // Sylius 2.0 uses Bootstrap 5 validation - look for .invalid-feedback within the field container
        /** @var \Behat\Mink\Element\NodeElement|null $formGroup */
        $formGroup = $fieldElement->getParent();
        while ($formGroup !== null && !$formGroup->hasClass('field')) {
            $formGroup = $formGroup->getParent();
        }

        if ($formGroup === null) {
            return false;
        }

        return $formGroup->find('css', '.invalid-feedback') !== null;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            // Sylius 2.0 admin uses 'sylius_admin_shipping_method' form name
            'pplCheckbox' => '#sylius_admin_shipping_method_pplParcelshopsShippingMethod',
            'pplDefaultCountrySelect' => 'select#sylius_admin_shipping_method_pplDefaultCountry',
            'pplOptionCountriesSelect' => 'select#sylius_admin_shipping_method_pplOptionCountries',
            'shippingAddress' => '[data-test-shipping-address], #shipping-address',
        ]);
    }
}
