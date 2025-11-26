<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PplShipmentTrait
{
    /**
     * Full PPL parcelshop data as JSON
     *
     * @var array{
     *      id: int,
     *      accessPointType: string,
     *      code: string,
     *      dhlPsId: string,
     *      depot: string,
     *      depotName: string,
     *      name: string|null,
     *      street: string|null,
     *      city: string|null,
     *      zipCode: string|null,
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
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $pplData = null;

    /** @deprecated Kept for backward compatibility. Use $pplData instead. */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMname = null;

    /** @deprecated Kept for backward compatibility. Use $pplData instead. */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMaddress = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMID = null;

    /**
     * @inheritdoc
     */
    public function getPplData(): ?array
    {
        return $this->pplData;
    }

    /**
     * @inheritdoc
     */
    public function setPplData(?array $pplData): void
    {
        $this->pplData = $pplData;
    }

    public function getPplPickupPointId(): ?string
    {
        return $this->getPplData()['code'] ?? $this->getPplKTMID();
    }

    public function pplPickupPointName(): ?string
    {
        return $this->getPplData()['name'] ?? $this->getPplKTMname();
    }

    public function pplPickupPointAddress(): ?string
    {
        $pplData = $this->getPplData();
        if ($pplData === null) {
            return $this->getPplKTMaddress();
        }

        // PPL API returns flat structure with street, city, zipCode at top level
        $parts = array_filter([
            $pplData['zipCode'] ?? null,
            $pplData['street'] ?? null,
            $pplData['city'] ?? null,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * @deprecated Kept for backward compatibility. Use getPplData() instead.
     */
    public function getPplKTMname(): ?string
    {
        return $this->pplKTMname;
    }

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMname(?string $pplKTMname): void
    {
        $this->pplKTMname = $pplKTMname;
    }

    /**
     * @deprecated Kept for backward compatibility. Use getPplData() instead.
     */
    public function getPplKTMaddress(): ?string
    {
        return $this->pplKTMaddress;
    }

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMaddress(?string $pplKTMaddress): void
    {
        $this->pplKTMaddress = $pplKTMaddress;
    }

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function getPplKTMID(): ?string
    {
        return $this->pplKTMID;
    }

    /**
     * @deprecated Kept for backward compatibility. Use setPplData() instead.
     */
    public function setPplKTMID(?string $pplKTMID): void
    {
        $this->pplKTMID = $pplKTMID;
    }
}
