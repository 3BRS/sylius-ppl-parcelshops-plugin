<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Controller\Partials\GetFlashBagTrait;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;

/**
 * @deprecated This controller is deprecated and kept only for backward compatibility
 *             with old orders using the redirect-based parcelshop selection.
 *             New orders use the modal widget integration.
 */
final class PplController
{
    use GetFlashBagTrait;

    public function __construct(
        private readonly RouterInterface $router,
        /** @var ShipmentRepositoryInterface<ShipmentInterface> */
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly CartContextInterface $cartContext,
        private readonly TranslatorInterface $translator,
        /** @var ShippingMethodRepositoryInterface<ShippingMethodInterface> */
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
    ) {
    }

    public function pplReturn(
        Request $request,
        string $methodCode,
        string $redirectTo = 'sylius_shop_checkout_select_shipping',
    ): RedirectResponse {
        $order = $this->cartContext->getCart();
        assert($order instanceof OrderInterface);

        $shipmentId = $request->query->get('sessid');
        $shipment = $this->shipmentRepository->find($shipmentId);
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $methodCode]);

        if ($shippingMethod === null || $shipment === null || !$order->getShipments()->contains($shipment)) {
            $this->getFlashBag($request)->add('error', $this->translator->trans('threebrs.shop.checkout.shippingStep.pplError'));

            return new RedirectResponse($this->router->generate($redirectTo));
        }
        assert($shippingMethod instanceof ShippingMethodInterface);
        assert($shipment instanceof ShipmentInterface);
        assert($shipment instanceof PplShipmentInterface);

        $ktmId = $request->query->get('KTMID');
        $ktmAddress = $request->query->get('KTMaddress');
        $ktmName = $request->query->get('KTMname');

        $shipment->setPplKTMID($ktmId);
        $shipment->setPplKTMaddress($ktmAddress);
        $shipment->setPplKTMname($ktmName);
        $shipment->setMethod($shippingMethod);

        $this->shipmentRepository->add($shipment);

        return new RedirectResponse($this->router->generate($redirectTo));
    }
}
