<?php

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials;

use Behat\Mink\Session;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Sylius\Behat\Service\DriverHelper;

trait WaitForElementTrait
{
    private function waitForElement(
        int    $timeout,
        string $elementName,
    ): void {
        $this->getDocument()->waitFor($timeout, fn() => $this->hasElement($elementName));
    }

    protected function hasElement(
        string $name,
        array  $parameters = [],
    ): bool {
        if (!DriverHelper::isJavascript($this->getDriver())) {
            return parent::hasElement($name, $parameters);
        }

        // Retry mechanism for stale element references and other timing issues
        $attempts    = 0;
        $maxAttempts = 5;

        while ($attempts < $maxAttempts) {
            try {
                return parent::hasElement($name, $parameters);
            } catch (StaleElementReferenceException) {
                // Wait progressively longer before retrying
                $this->getSession()->wait(200 * $attempts);
            }
        }
        return parent::hasElement($name, $parameters);
    }

    public static function waitForPageToLoad(Session $session): void
    {
        if (DriverHelper::isJavascript($session->getDriver())) {
            $session->wait(500, "document.readyState === 'complete'");
        }
    }
}
