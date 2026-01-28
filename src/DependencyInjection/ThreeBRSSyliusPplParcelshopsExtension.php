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

        $env = $container->getParameter('kernel.environment');
        if ($env === 'test' && file_exists(__DIR__ . '/../Resources/config/services_test.yaml')) {
            $loader->load('services_test.yaml');
        }
    }
}
