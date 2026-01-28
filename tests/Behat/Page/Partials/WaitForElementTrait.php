<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials;

use Behat\Mink\Session;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Sylius\Behat\Service\DriverHelper;

trait WaitForElementTrait
{
    private function waitForElement(int $timeout, string $elementName): void
    {
        $this->getDocument()->waitFor($timeout, fn () => $this->hasElement($elementName));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function hasElement(string $name, array $parameters = []): bool
    {
        if (!DriverHelper::isJavascript($this->getDriver())) {
            return parent::hasElement($name, $parameters);
        }

        // Retry mechanism for stale element references and other timing issues
        $maxAttempts = 5;

        for ($attempts = 0; $attempts < $maxAttempts; $attempts++) {
            try {
                return parent::hasElement($name, $parameters);
            } catch (StaleElementReferenceException) {
                // Wait progressively longer before retrying
                $this->getSession()->wait(200 * ($attempts + 1));
            }
        }

        return parent::hasElement($name, $parameters);
    }

    private function waitForPageToLoad(?Session $session = null): void
    {
        $session ??= $this->getSession();
        if (DriverHelper::isJavascript($session->getDriver())) {
            $session->wait(500, "document.readyState === 'complete'");
        }
    }
}
