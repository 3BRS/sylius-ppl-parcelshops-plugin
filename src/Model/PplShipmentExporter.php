<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Order\Model\OrderInterface;
use ThreeBRS\SyliusShipmentExportPlugin\Model\ShipmentExporterInterface;

class PplShipmentExporter implements ShipmentExporterInterface
{
    /**
     * @param array<string> $pplShippingMethodsCodes
     */
    public function __construct(
        private readonly CurrencyConverter $currencyConverter,
        private readonly array $pplShippingMethodsCodes,
    ) {
    }

    private function convert(
        int $amount,
        string $sourceCurrencyCode,
        string $targetCurrencyCode,
    ): int {
        return $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }

    /**
     * @return array<string>
     */
    public function getShippingMethodsCodes(): array
    {
        return $this->pplShippingMethodsCodes;
    }

    /**
     * @param array<mixed> $questionsArray
     *
     * @return array<mixed>
     */
    public function getRow(
        ShipmentInterface $shipment,
        array $questionsArray,
    ): array {
        assert($shipment instanceof PplShipmentInterface);

        $order = $shipment->getOrder();
        assert($order instanceof OrderInterface);
        $channel = $order->getChannel();
        assert($channel !== null);
        $address = $order->getShippingAddress();
        assert($address !== null);
        $customer = $order->getCustomer();
        assert($customer !== null);

        $shippingMethod = $shipment->getMethod();
        assert($shippingMethod instanceof PplShippingMethodInterface);
        $payment = $order->getPayments()->first();
        assert($payment instanceof PaymentInterface);
        $paymentMethod = $payment->getMethod();
        assert($paymentMethod instanceof PaymentMethodInterface);
        assert($paymentMethod->getGatewayConfig() instanceof GatewayConfigInterface);

        $isCashOnDelivery = $paymentMethod->getGatewayConfig()->getFactoryName() === 'offline';

        $currencyCode = $order->getCurrencyCode();
        assert($currencyCode !== null);

        $targetCurrencyCode = 'CZK';
        $totalAmount = $this->convert($order->getTotal(), $currencyCode, $targetCurrencyCode);

        $totalAmount = \number_format(
            $totalAmount / 100,
            0,
            '.',
            '',
        );

        $weight = 0;
        foreach ($order->getItems() as $item) {
            /** @var OrderItemInterface $item */
            $variant = $item->getVariant();
            if ($variant !== null) {
                $weight += $variant->getWeight();
            }
        }

        $pplId = $shipment->getPplKTMID();

        return [
            /* 1 - version 5 */
            '',

            /* 2 - Číslo obj.* */
            $order->getNumber(),

            /* 3 - Jméno* */
            $address->getFirstName(),

            /* 4 - Příjmení* */
            $address->getLastName(),

            /* 5 - Firma */
            $address->getCompany(),

            /* 6 - E-mail** */
            $customer->getEmail(),

            /* 7 - Mobil** */
            $address->getPhoneNumber(),

            /* 8 - Dobírka */
            $isCashOnDelivery
                ? $totalAmount
                : '',

            /* 9 - Měna */
            $targetCurrencyCode,

            /* 10 - Hodnota **/
            $totalAmount,

            /* 11 - Hmotnost */
            $weight,

            /* 12 - Cílová pobočka* */
            $pplId,

            /* 13 - Doména e-shopu*** */
            '',

            /* 14 - Obsah 18+ */
            '',

            /* 15 - Plánovaný výdej */
            '',

            /* 16 - Ulice */
            '',

            /* 17 - Č. domu */
            '',

            /* 18 - Obec */
            '',

            /* 19 - PSČ */
            '',

            /* 20 - Unique ID of the carrier pickup point */
            '',
        ];
    }

    public function getDelimiter(): string
    {
        return ',';
    }

    /**
     * @return array<mixed>|null
     */
    public function getQuestionsArray(): ?array
    {
        return null;
    }

    /**
     * @return array<mixed>|null
     */
    public function getHeaders(): ?array
    {
        return null;
    }
}
