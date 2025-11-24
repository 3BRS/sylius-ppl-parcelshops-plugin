<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

interface PplShipmentInterface
{
	public function setPplKTMname(?string $pplKTMname): void;

	public function getPplKTMname(): ?string;

	public function getPplKTMaddress(): ?string;

	public function setPplKTMaddress(?string $pplKTMaddress): void;

	public function getPplKTMID(): ?string;

	public function setPplKTMID(?string $pplKTMID): void;
}
