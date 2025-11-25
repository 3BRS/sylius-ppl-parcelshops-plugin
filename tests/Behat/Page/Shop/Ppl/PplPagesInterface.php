<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface PplPagesInterface extends BaseUpdatePageInterface
{
	public function selectPplBranch(string $id, string $name, string $address): void;

	public function iSeePplBranchInsteadOfShippingAddress(): bool;
}
