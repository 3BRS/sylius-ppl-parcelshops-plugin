<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingOrderContext implements Context
{
    public function __construct(private readonly ShowPageInterface $showPage)
    {
    }

    /**
     * @Then it should be shipped to PPL parcelshop
     */
    public function itShouldBeShippedToPplParcelshop(): void
    {
        Assert::true($this->showPage->hasPplParcelshopInShippingAddress());
    }
}
