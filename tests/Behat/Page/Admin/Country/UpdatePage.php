<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Country\UpdatePage as BaseUpdatePage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Partials\WaitForElementTrait;

class UpdatePage extends BaseUpdatePage
{
    use WaitForElementTrait;

    public function saveChanges(): void
    {
        parent::saveChanges();

        $this->waitForPageToLoad();
    }
}
