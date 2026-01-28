<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ThreeBRSSyliusPplParcelshopsExtension extends Extension
{
    public function load(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Expose plugin directory as parameter for test entity mapping
        $container->setParameter(
            'threebrs_sylius_ppl_parcelshops_plugin.plugin_dir',
            dirname(__DIR__, 2),
        );
    }
}
