<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class ThreeBRSSyliusPplParcelshopsExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        // Expose plugin directory as parameter for test entity mapping
        // Must be done in prepend() so it's available for imported configs
        $container->setParameter(
            'threebrs_sylius_ppl_parcelshops_plugin.plugin_dir',
            dirname(__DIR__, 2),
        );

        // Load twig hooks configuration
        $configFile = __DIR__ . '/../Resources/config/app/twig_hooks/shipping_method.yaml';
        if (file_exists($configFile)) {
            $config = Yaml::parseFile($configFile);
            if (
                \is_array($config) &&
                isset($config['sylius_twig_hooks']) &&
                \is_array($config['sylius_twig_hooks'])
            ) {
                $container->prependExtensionConfig('sylius_twig_hooks', $config['sylius_twig_hooks']);
            }
        }
    }

    public function load(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
