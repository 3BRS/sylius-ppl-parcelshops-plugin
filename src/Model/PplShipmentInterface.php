<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

interface PplShipmentInterface
{
    /**
     * Full PPL parcelshop data as JSON
     *
     * @return array{
     *      id: int,
     *      accessPointType: string,
     *      code: string,
     *      dhlPsId: string,
     *      depot: string,
     *      depotName: string,
     *      name: string,
     *      street: string,
     *      city: string,
     *      zipCode: string,
     *      country: string,
     *      parcelshopName: string,
     *      gps: array{
     *          latitude: float,
     *          longitude: float
     *      },
     *      www: string,
     *      ktmNote: string,
     *      openHours: list<string>,
     *      capacityStatus: string,
     *      externalNumbers: list<array{
     *          type: string,
     *          value: string
     *      }>,
     *      capacitySettings: list<array{
     *          size: string,
     *          sizeId: int,
     *          forYouDeliveryToAccessPoint: list<string>,
     *          height: int,
     *          length: int,
     *          width: int
     *      }>,
     *      visiblePs: bool,
     *      activeCardPayment: bool,
     *      tribalServicePoint: bool,
     *      dimensionForced: bool,
     *      pickupEnabled: bool,
     *      activeCashPayment: bool,
     *      newPs: bool,
     *      accessPointTypeInternal: array{
     *          id: int,
     *          name: string
     *      },
     *      distance: float,
     *      isCapacityAvailable: bool,
     *      availableCmCodes: list<string>
     *  }|null
     */
    public function getPplData(): ?array;

    public function setPplData(?array $pplData): void;

    public function getPplPickupPointId(): ?string;

    public function pplPickupPointName(): ?string;

    public function pplPickupPointAddress(): ?string;

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMname(?string $pplKTMname): void;

    /**
     * @deprecated Kept for backward compatibility. Use getPplData() instead.
     */
    public function getPplKTMname(): ?string;

    /**
     * @deprecated Kept for backward compatibility. Use getPplData() instead.
     */
    public function getPplKTMaddress(): ?string;

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMaddress(?string $pplKTMaddress): void;

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function getPplKTMID(): ?string;

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMID(?string $pplKTMID): void;
}
