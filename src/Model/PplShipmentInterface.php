<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusPplParcelshopsPlugin\Model;

interface PplShipmentInterface
{
    public function getPplData(): ?string;

    public function setPplData(?string $pplData): void;

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

    public function getPplKTMID(): ?string;

    public function setPplKTMID(?string $pplKTMID): void;
}
