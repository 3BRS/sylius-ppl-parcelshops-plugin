<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShipmentExport;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface IndexPageInterface extends PageInterface
{
    public function countShipments(): int;

    public function hasShipmentForOrder(string $orderNumber): bool;

    public function selectAllShipments(): void;

    public function selectShipmentForOrder(string $orderNumber): void;

    public function exportSelectedShipments(): void;

    public function exportAllShipments(): void;

    public function markSelectedAsShipped(): void;
}