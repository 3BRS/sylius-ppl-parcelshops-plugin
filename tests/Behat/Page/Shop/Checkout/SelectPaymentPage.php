<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPage as BaseSelectPaymentPage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials\WaitForElementTrait;

final class SelectPaymentPage extends BaseSelectPaymentPage
{
    use WaitForElementTrait;

    public function nextStep(): void
    {
        $this->getElement('next_step')->press();

        self::waitForPageToLoad($this->getSession());
    }

    public function selectPaymentMethod(string $paymentMethod): void
    {
        $this->waitForElement(5, 'payment_method_select');

        parent::selectPaymentMethod($paymentMethod);
    }
}
