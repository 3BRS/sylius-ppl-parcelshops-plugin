<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Order;

use Sylius\Behat\Page\Shop\Order\ThankYouPage as BaseThankYouPage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials\WaitForElementTrait;

class ThankYouPage extends BaseThankYouPage
{
    use WaitForElementTrait;

    public function hasThankYouMessage(): bool
    {
        $this->waitForElement(5, 'thank_you');

        return parent::hasThankYouMessage();
    }
}
