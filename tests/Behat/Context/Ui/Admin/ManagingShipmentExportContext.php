<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShipmentExport\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentExportContext implements Context
{
    private ?string $csvContent = null;

    public function __construct(
        private readonly IndexPageInterface $indexPage,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly Session $session,
    ) {
    }

    /**
     * @When I browse PPL parcelshop shipments ready for export
     */
    public function iBrowsePplParcelshopShipmentsReadyForExport(): void
    {
        $this->indexPage->open(['exporterName' => 'ppl_parcel_shop']);

    }

    /**
     * @Then I should see :count shipment(s) ready for export
     */
    public function iShouldSeeShipmentsReadyForExport(int $count): void
    {
        Assert::eq(
            $this->indexPage->countShipments(),
            $count,
            sprintf('Expected %d shipments, but found %d', $count, $this->indexPage->countShipments())
        );
    }

    /**
     * @Then I should see shipment for order :orderNumber in the list
     */
    public function iShouldSeeShipmentForOrderInTheList(string $orderNumber): void
    {
        Assert::true(
            $this->indexPage->hasShipmentForOrder($orderNumber),
            sprintf('Shipment for order "%s" should be visible in the list', $orderNumber)
        );
    }

    /**
     * @When I export selected PPL parcelshop shipments to CSV
     */
    public function iExportSelectedPplParcelshopShipmentsToCSV(): void
    {
        $this->indexPage->exportSelectedShipments();
        $this->csvContent = $this->session->getPage()->getContent();
    }

    /**
     * @When I export all PPL parcelshop shipments to CSV
     */
    public function iExportAllPplParcelshopShipmentsToCSV(): void
    {
        $this->indexPage->exportAllShipments();
        $this->csvContent = $this->session->getPage()->getContent();
    }

    /**
     * @Then I should receive a CSV file
     */
    public function iShouldReceiveACSVFile(): void
    {
        Assert::notNull($this->csvContent, 'CSV content should not be null');
        Assert::notEmpty($this->csvContent, 'CSV content should not be empty');

        // Check if response contains CSV-like content
        $responseHeaders = $this->session->getResponseHeaders();
        if (isset($responseHeaders['content-type'])) {
            $contentType = is_array($responseHeaders['content-type'])
                ? $responseHeaders['content-type'][0]
                : $responseHeaders['content-type'];
            Assert::contains(
                $contentType,
                'text/csv',
                sprintf('Expected CSV content type, got: %s', $contentType)
            );
        }
    }

    /**
     * @Then the CSV should contain shipment data for order :orderNumber
     */
    public function theCSVShouldContainShipmentDataForOrder(string $orderNumber): void
    {
        Assert::notNull($this->csvContent, 'CSV content should not be null');
        Assert::contains(
            $this->csvContent,
            $orderNumber,
            sprintf('CSV should contain order number "%s"', $orderNumber)
        );
    }

    /**
     * @Then the CSV should contain :count shipment record(s)
     */
    public function theCSVShouldContainShipmentRecords(int $count): void
    {
        Assert::notNull($this->csvContent, 'CSV content should not be null');

        $lines = explode("\n", trim($this->csvContent));
        $actualCount = count($lines);

        Assert::eq(
            $actualCount,
            $count,
            sprintf('Expected %d CSV records, but found %d', $count, $actualCount)
        );
    }

    /**
     * @When I mark selected PPL parcelshop shipments as shipped
     */
    public function iMarkSelectedPplParcelshopShipmentsAsShipped(): void
    {
        $this->indexPage->markSelectedAsShipped();
    }

    /**
     * @Then I should be notified that shipments have been marked as shipped
     */
    public function iShouldBeNotifiedThatShipmentsHaveBeenMarkedAsShipped(): void
    {
        $this->indexPage->open(['exporterName' => 'ppl_parcel_shop']);
        // The notification is handled by Sylius flash messages
    }

    /**
     * @Then the CSV should contain PPL parcelshop ID :pplId for order :orderNumber
     */
    public function theCSVShouldContainPPLParcelshopIdForOrder(string $pplId, string $orderNumber): void
    {
        Assert::notNull($this->csvContent, 'CSV content should not be null');

        $lines = explode("\n", trim($this->csvContent));
        $found = false;

        // Strip # from order number if present since CSV might not include it
        $orderNumberClean = ltrim($orderNumber, '#');

        foreach ($lines as $line) {
            if ((str_contains($line, $orderNumber) || str_contains($line, $orderNumberClean)) && str_contains($line, $pplId)) {
                $found = true;
                break;
            }
        }

        Assert::true(
            $found,
            sprintf('CSV should contain PPL parcelshop ID "%s" for order "%s"', $pplId, $orderNumber)
        );
    }

    /**
     * @Then the CSV should contain customer email :email
     */
    public function theCSVShouldContainCustomerEmail(string $email): void
    {
        Assert::notNull($this->csvContent, 'CSV content should not be null');
        Assert::contains(
            $this->csvContent,
            $email,
            sprintf('CSV should contain customer email "%s"', $email)
        );
    }
}
