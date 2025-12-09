<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PplShippingMethodTrait
{
    /** @ORM\Column(type="boolean", nullable=true) */
    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $pplParcelshopsShippingMethod = null;

    /** @ORM\Column(type="string", nullable=true) */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplDefaultCountry = null;

    /**
     * @var array<string>|null
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $pplOptionCountries = null;

    public function getPplParcelshopsShippingMethod(): ?bool
    {
        return $this->pplParcelshopsShippingMethod;
    }

    public function setPplParcelshopsShippingMethod(?bool $pplParcelshopsShippingMethod): void
    {
        $this->pplParcelshopsShippingMethod = $pplParcelshopsShippingMethod;
    }

    public function getPplDefaultCountry(): ?string
    {
        return $this->pplDefaultCountry;
    }

    public function setPplDefaultCountry(?string $pplDefaultCountry): void
    {
        $this->pplDefaultCountry = $pplDefaultCountry;
    }

    /**
     * @return array<string>|null
     */
    public function getPplOptionCountries(): ?array
    {
        return $this->pplOptionCountries;
    }

    /**
     * @param array<string>|null $pplOptionCountries
     */
    public function setPplOptionCountries(?array $pplOptionCountries): void
    {
        $this->pplOptionCountries = $pplOptionCountries;
    }
}
