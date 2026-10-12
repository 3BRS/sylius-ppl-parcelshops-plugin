<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    // ORM 2 generates proxy classes without lazy ghost objects, PHP 8.4 reports them as deprecated; ORM 3 always uses lazy objects
    if (version_compare((string) InstalledVersions::getVersion('doctrine/orm'), '3.0.0', '<')) {
        $container->extension('doctrine', ['orm' => ['enable_lazy_ghost_objects' => true]]);

        return;
    }

    // symfony/var-exporter 8 has no lazy ghost objects, ORM 3 then needs native lazy objects, which DoctrineBundle 3 enables by default
    if (
        version_compare((string) InstalledVersions::getVersion('symfony/var-exporter'), '8.0.0', '>=')
        && version_compare((string) InstalledVersions::getVersion('doctrine/doctrine-bundle'), '3.0.0', '<')
    ) {
        $container->extension('doctrine', ['orm' => ['enable_native_lazy_objects' => true]]);
    }
};
