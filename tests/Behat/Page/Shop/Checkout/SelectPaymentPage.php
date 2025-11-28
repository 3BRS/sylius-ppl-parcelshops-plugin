<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\SelectPaymentPage as BaseSelectPaymentPage;
use Sylius\Behat\Service\DriverHelper;

final class SelectPaymentPage extends BaseSelectPaymentPage
{
    public function selectPaymentMethod(string $paymentMethod): void
    {
        // Wait for payment method elements to be available and stable
        // This prevents race conditions in CI where JavaScript hasn't finished rendering payment methods
        // or where the DOM is being updated
        if (DriverHelper::isJavascript($this->getDriver())) {
            // Retry mechanism for stale element references
            $attempts = 0;
            $maxAttempts = 3;

            while ($attempts < $maxAttempts) {
                try {
                    // Wait for element to exist
                    $this->getDocument()->waitFor(10, function () use ($paymentMethod) {
                        return $this->hasElement('payment_method_select', ['%payment_method%' => $paymentMethod]);
                    });

                    // Small additional wait for DOM to stabilize
                    $this->getSession()->wait(200);

                    // Try to click - if successful, break out of retry loop
                    parent::selectPaymentMethod($paymentMethod);
                    return;
                } catch (\Facebook\WebDriver\Exception\StaleElementReferenceException $e) {
                    $attempts++;
                    if ($attempts >= $maxAttempts) {
                        throw $e;
                    }
                    // Wait a bit before retrying
                    $this->getSession()->wait(300);
                }
            }
        } else {
            // Non-JavaScript driver
            parent::selectPaymentMethod($paymentMethod);
        }
    }
}
