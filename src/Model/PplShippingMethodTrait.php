<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait PplShippingMethodTrait
{
	/**
	 * @var bool|null
	 *
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $pplParcelshopsShippingMethod;

	/**
	 * @var string|null
	 * @ORM\Column(nullable=true, type="string")
	 */
	private $pplOptionCountry;

	public function getPplParcelshopsShippingMethod(): ?bool
	{
		return $this->pplParcelshopsShippingMethod;
	}

	public function setPplParcelshopsShippingMethod(?bool $pplParcelshopsShippingMethod): void
	{
		$this->pplParcelshopsShippingMethod = $pplParcelshopsShippingMethod;
	}

	public function getPplOptionCountry(): ?string
	{
		return $this->pplOptionCountry;
	}

	public function setPplOptionCountry(?string $pplOptionCountry): void
	{
		$this->pplOptionCountry = $pplOptionCountry;
	}
}
