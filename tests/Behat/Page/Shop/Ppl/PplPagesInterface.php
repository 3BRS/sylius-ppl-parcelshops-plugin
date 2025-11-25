<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface PplPagesInterface extends PageInterface
{
	public function selectPplBranch(string $id, string $name, string $address): void;

	public function iSeePplBranchInsteadOfShippingAddress(): bool;
}
