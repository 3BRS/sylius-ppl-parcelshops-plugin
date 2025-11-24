<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PplShippingMethodTrait
{
    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $pplParcelshopsShippingMethod = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
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
