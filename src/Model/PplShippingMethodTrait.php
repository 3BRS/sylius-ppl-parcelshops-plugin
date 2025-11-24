<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait PplShippingMethodTrait
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::BOOLEAN, nullable: true)]
    private ?bool $pplParcelshopsShippingMethod = null;

    #[ORM\Column(nullable: true, type: \Doctrine\DBAL\Types\Types::STRING)]
    private ?string $pplOptionCountry = null;

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
