<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PplShipmentTrait
{
    /**
     * Full PPL parcelshop data as JSON
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pplData = null;

    /** @deprecated Kept for backward compatibility. Use $pplData instead. */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMname = null;

    /** @deprecated Kept for backward compatibility. Use $pplData instead. */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMaddress = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $pplKTMID = null;

    public function getPplData(): ?string
    {
        return $this->pplData;
    }

    public function setPplData(?string $pplData): void
    {
        $this->pplData = $pplData;
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

    public function getPplKTMID(): ?string
    {
        return $this->pplKTMID;
    }

    public function setPplKTMID(?string $pplKTMID): void
    {
        $this->pplKTMID = $pplKTMID;
    }
}
