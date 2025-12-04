<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Entity\Shipping;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentTrait;
use Sylius\Component\Core\Model\Shipment as BaseShipment;

#[MappedSuperclass]
#[Table(name: 'sylius_shipment')]
class Shipment extends BaseShipment implements PplShipmentInterface
{
	use PplShipmentTrait;
}
