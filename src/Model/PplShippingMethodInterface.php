<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

interface PplShippingMethodInterface
{
    public function getPplParcelshopsShippingMethod(): ?bool;

    public function setPplParcelshopsShippingMethod(?bool $pplParcelshopsShippingMethod): void;

    public function getPplDefaultCountry(): ?string;

    public function setPplDefaultCountry(?string $pplDefaultCountry): void;

    /**
     * @return array<string>|null
     */
    public function getPplOptionCountries(): ?array;

    /**
     * @param array<string>|null $pplOptionCountries
     */
    public function setPplOptionCountries(?array $pplOptionCountries): void;
}
