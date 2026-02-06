<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\AfterSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;

final class PplShippingMethodFixtureListener extends AbstractListener implements AfterSuiteListenerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function afterSuite(SuiteEvent $suiteEvent, array $options): void
    {
        $connection = $this->entityManager->getConnection();

        // Update PPL shipping method settings
        $connection->executeStatement("
            UPDATE sylius_shipping_method
            SET ppl_parcelshops_shipping_method = 1,
                ppl_default_country = 'CZ',
                ppl_option_countries = 'a:2:{i:0;s:2:\"CZ\";i:1;s:2:\"SK\";}'
            WHERE code = 'ppl_parcel_shop'
        ");
    }

    public function getName(): string
    {
        return 'ppl_shipping_method';
    }
}
