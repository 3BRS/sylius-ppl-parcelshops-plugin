<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Controller;

use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PplController
{
	/** @var RouterInterface */
	private $router;

	/** @var ShipmentRepositoryInterface */
	private $shipmentRepository;

	/** @var CartContextInterface */
	private $cartContext;

	/** @var FlashBagInterface */
	private $flashBag;

	/** @var TranslatorInterface */
	private $translator;

	/** @var ShippingMethodRepositoryInterface */
	private $shippingMethodRepository;

	public function __construct(
		RouterInterface $router,
		ShipmentRepositoryInterface $shipmentRepository,
		CartContextInterface $cartContext,
		FlashBagInterface $flashBag,
		TranslatorInterface $translator,
		ShippingMethodRepositoryInterface $shippingMethodRepository
	) {
		$this->router = $router;
		$this->shipmentRepository = $shipmentRepository;
		$this->cartContext = $cartContext;
		$this->flashBag = $flashBag;
		$this->translator = $translator;
		$this->shippingMethodRepository = $shippingMethodRepository;
	}

	public function pplReturn(Request $request, string $methodCode, string $redirectTo = 'sylius_shop_checkout_select_shipping'): RedirectResponse
	{
		$order = $this->cartContext->getCart();
		assert($order instanceof OrderInterface);

		$shipmentId = $request->query->get('sessid');
		$shipment = $this->shipmentRepository->find($shipmentId);
		$shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $methodCode]);

		if ($shippingMethod === null || $shipment === null || !$order->getShipments()->contains($shipment)) {
			$this->flashBag->add('error', $this->translator->trans('threebrs.shop.checkout.shippingStep.pplError'));

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
