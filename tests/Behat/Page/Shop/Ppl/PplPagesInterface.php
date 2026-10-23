<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface PplPagesInterface extends PageInterface
{
	public function selectPplBranch(string $id, string $name, string $address): void;

	public function choosePplParcelshop(string $code, string $name): void;

	public function fillPplDataField(string $value): void;

	public function getValidationMessage(): string;

	public function getShippingAddress(): string;

	public function iSeePplBranchInsteadOfShippingAddress(): bool;
}
