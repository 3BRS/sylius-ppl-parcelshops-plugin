<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
class ShippingMethod extends BaseShippingMethod implements PplShippingMethodInterface
{
    use PplShippingMethodTrait;
}
