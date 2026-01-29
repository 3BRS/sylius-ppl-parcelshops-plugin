<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Order\ShowPage as BaseShowPage;

final class ShowPage extends BaseShowPage implements ShowPageInterface
{
    public function hasPplParcelshopInShippingAddress(): bool
    {
        $shippingAddress = $this->getElement('shipping_address')->getText();

        return str_contains($shippingAddress, 'PPL ParcelShop') ||
            str_contains($shippingAddress, 'PPL 1') ||
            str_contains($shippingAddress, 'PPL Address');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'shipping_address' => '[data-test-shipping-address], #shipping-address',
        ]);
    }
}
