<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

interface PplShippingMethodInterface
{
    public function getPplParcelshopsShippingMethod(): ?bool;

    public function setPplParcelshopsShippingMethod(?bool $pplParcelshopsShippingMethod): void;

    public function getPplOptionCountry(): ?string;

    public function setPplOptionCountry(?string $pplOptionCountry): void;
}
