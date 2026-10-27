<?php

declare(strict_types=1);

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Setup\ShippingMethodContext;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Admin\ManagingShipmentExportContext;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Admin\ManagingShippingMethodContext;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Shop\ManagingPplContext;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\Country\UpdatePage as CountryUpdatePage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShipmentExport\IndexPage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShipmentExport\IndexPageInterface;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod\UpdatePage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Checkout\SelectPaymentPage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Order\ThankYouPage;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl\PplPages;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Shop\Ppl\PplPagesInterface;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('sylius.behat.page.shop.order.thank_you.class', ThankYouPage::class)
        ->set('sylius.behat.page.admin.country.update.class', CountryUpdatePage::class);

    $services = $container->services()
        ->defaults()
            ->public();

    $services->set(UpdatePageInterface::class, UpdatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_channel_update']);

    $services->set('sylius_ppl_parcelshops_plugin.context.ui.admin.shipping_method', ManagingShippingMethodContext::class)
        ->args([
            service(UpdatePageInterface::class),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set('sylius_ppl_parcelshops_plugin.context.setup.shipping_method', ShippingMethodContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.behat.shared_storage'),
            service(StateMachineInterface::class),
        ]);

    $services->set('sylius_ppl_parcelshops_plugin.context.ui.shop.managing_ppl_context', ManagingPplContext::class)
        ->args([
            service(PplPagesInterface::class),
            service('sylius.behat.context.ui.shop.checkout.shipping'),
            service('sylius.repository.shipment'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set(PplPagesInterface::class, PplPages::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_channel_update']);

    // Override Sylius SelectPaymentPage to add waiting for payment methods to load
    $services->set('sylius.behat.page.shop.checkout.select_payment', SelectPaymentPage::class)
        ->private()
        ->parent('sylius.behat.symfony_page');

    // Override Sylius SelectPaymentPage to add waiting for payment methods to load
    $services->set('sylius.behat.page.shop.checkout.select_payment', SelectPaymentPage::class)
        ->private()
        ->parent('sylius.behat.symfony_page');

    $services->set(IndexPageInterface::class, IndexPage::class)
        ->private()
        ->parent('sylius.behat.symfony_page');

    $services->set('sylius_ppl_parcelshops_plugin.context.ui.admin.managing_shipment_export', ManagingShipmentExportContext::class)
        ->args([
            service(IndexPageInterface::class),
            service('sylius.behat.shared_storage'),
            service('behat.mink.default_session'),
        ]);
};
