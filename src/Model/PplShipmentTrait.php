<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait PplShipmentTrait
{
	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $pplKTMname;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $pplKTMaddress;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $pplKTMID;

	public function getPplKTMname(): ?string
	{
		return $this->pplKTMname;
	}

	public function setPplKTMname(?string $pplKTMname): void
	{
		$this->pplKTMname = $pplKTMname;
	}

	public function getPplKTMaddress(): ?string
	{
		return $this->pplKTMaddress;
	}

	public function setPplKTMaddress(?string $pplKTMaddress): void
	{
		$this->pplKTMaddress = $pplKTMaddress;
	}

	public function getPplKTMID(): ?string
	{
		return $this->pplKTMID;
	}

	public function setPplKTMID(?string $pplKTMID): void
	{
		$this->pplKTMID = $pplKTMID;
	}
}
