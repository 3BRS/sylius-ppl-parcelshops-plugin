<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait PplShipmentTrait
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $pplKTMname = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $pplKTMaddress = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $pplKTMID = null;

    public function getPplKTMname(): ?string
    {
        return $this->pplKTMname;
    }

    public function setPplKTMname(?string $pplKTMname): void
    {
        $this->pplKTMname = $pplKTMname;
    }

    public function getPplKTMaddress(): ?string
    {
        return $this->pplKTMaddress;
    }

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
