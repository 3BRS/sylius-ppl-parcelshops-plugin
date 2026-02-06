<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ThreeBRSSyliusPplParcelshopsExtension extends Extension implements PrependExtensionInterface
{
    public function load(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('sylius_twig_hooks')) {
            return;
        }

        $container->prependExtensionConfig('sylius_twig_hooks', [
            'hooks' => [
                // Shop: Add PPL widget after shipping method choice details
                'sylius_shop.checkout.select_shipping.content.form.shipments.shipment.choice' => [
                    'ppl_widget' => [
                        'template' => '@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Checkout/SelectShipping/_pplChoice.html.twig',
                        'priority' => -200,
                    ],
                ],
                // Shop: Add PPL JavaScript to the page
                'sylius_shop.checkout.select_shipping' => [
                    'ppl_javascripts' => [
                        'template' => '@ThreeBRSSyliusPplParcelshopsPlugin/Shop/_javascripts.html.twig',
                        'priority' => -100,
                    ],
                ],
                // Admin: Add PPL settings to shipping method create form
                'sylius_admin.shipping_method.create.content.form#left' => [
                    'ppl_settings' => [
                        'template' => '@ThreeBRSSyliusPplParcelshopsPlugin/Admin/ShippingMethod/_pplForm.html.twig',
                        'priority' => -100,
                    ],
                ],
                // Admin: Add PPL settings to shipping method update form
                'sylius_admin.shipping_method.update.content.form#left' => [
                    'ppl_settings' => [
                        'template' => '@ThreeBRSSyliusPplParcelshopsPlugin/Admin/ShippingMethod/_pplForm.html.twig',
                        'priority' => -100,
                    ],
                ],
                // Admin: Add PPL parcelshop info after shipping address in order show
                'sylius_admin.order.show.content.sections.customer' => [
                    'ppl_parcelshop_address' => [
                        'template' => '@ThreeBRSSyliusPplParcelshopsPlugin/Admin/Common/Order/_addresses.html.twig',
                        'priority' => 95,
                    ],
                ],
            ],
        ]);
    }
}
