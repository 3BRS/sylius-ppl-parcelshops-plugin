<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Page\Admin\ShipmentExport;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'threebrs_admin_Shipment_export';
    }

    public function countShipments(): int
    {
        $rows = $this->getDocument()->findAll('css', 'table tbody tr');

        return count($rows);
    }

    public function hasShipmentForOrder(string $orderNumber): bool
    {
        $rows = $this->getDocument()->findAll('css', 'table tbody tr');

        foreach ($rows as $row) {
            if (str_contains($row->getText(), $orderNumber)) {
                return true;
            }
        }

        return false;
    }

    public function selectAllShipments(): void
    {
        $checkboxes = $this->getDocument()->findAll('css', 'input[type="checkbox"][name="ids[]"]');

        foreach ($checkboxes as $checkbox) {
            $checkbox->check();
        }
    }

    public function selectShipmentForOrder(string $orderNumber): void
    {
        $rows = $this->getDocument()->findAll('css', 'table tbody tr');

        foreach ($rows as $row) {
            if (str_contains($row->getText(), $orderNumber)) {
                $checkbox = $row->find('css', 'input[type="checkbox"][name="ids[]"]');
                if ($checkbox) {
                    $checkbox->check();

                    return;
                }
            }
        }

        throw new \RuntimeException(sprintf('Could not find shipment for order "%s"', $orderNumber));
    }

    public function exportSelectedShipments(): void
    {
        // Try different selectors to find checkboxes
        $checkboxes = $this->getDocument()->findAll('css', 'input[type="checkbox"][name="ids[]"]');

        if (empty($checkboxes)) {
            $checkboxes = $this->getDocument()->findAll('css', 'input.exportCheckbox');
        }

        if (empty($checkboxes)) {
            $checkboxes = $this->getDocument()->findAll('css', 'input[type="checkbox"]');
        }

        $ids = [];
        foreach ($checkboxes as $checkbox) {
            $value = $checkbox->getAttribute('value');
            if (!empty($value) && is_numeric($value)) {
                $ids[] = $value;
            }
        }

        // Build export URL properly
        $exporterName = 'ppl_parcel_shop';
        $baseUrl = rtrim($this->getParameter('base_url'), '/');
        $url = sprintf('%s/admin/shipment-exports/export/%s', $baseUrl, $exporterName);

        if (!empty($ids)) {
            $queryString = http_build_query(['ids' => $ids]);
            $url .= '?' . $queryString;
        }

        $this->getDriver()->visit($url);
    }

    public function exportAllShipments(): void
    {
        $this->exportSelectedShipments();
    }

    public function markSelectedAsShipped(): void
    {
        // Try different selectors to find checkboxes
        $checkboxes = $this->getDocument()->findAll('css', 'input[type="checkbox"][name="ids[]"]');

        if (empty($checkboxes)) {
            $checkboxes = $this->getDocument()->findAll('css', 'input.exportCheckbox');
        }

        if (empty($checkboxes)) {
            $checkboxes = $this->getDocument()->findAll('css', 'input[type="checkbox"]');
        }

        $ids = [];
        foreach ($checkboxes as $checkbox) {
            $value = $checkbox->getAttribute('value');
            if (!empty($value) && is_numeric($value)) {
                $ids[] = $value;
            }
        }

        // Build mark-as-shipped URL properly
        $exporterName = 'ppl_parcel_shop';
        $baseUrl = rtrim($this->getParameter('base_url'), '/');
        $url = sprintf('%s/admin/shipment-exports/mark/%s', $baseUrl, $exporterName);

        if (!empty($ids)) {
            $queryString = http_build_query(['ids' => $ids]);
            $url .= '?' . $queryString;
        }

        $this->getDriver()->visit($url);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'export_button' => 'button:contains("Export")',
            'mark_as_sent_button' => 'button:contains("Mark as sent")',
        ]);
    }
}
