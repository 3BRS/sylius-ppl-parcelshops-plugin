<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/enable_ppl_parcelshops_in_shipping_method.php',
        'suites/configure_ppl_countries_in_shipping_method.php',
        'suites/admin_sees_ppl_parcelshop_in_order.php',
        // JavaScript tests (require panther/selenium) - skipped for now
        // 'suites/select_ppl_parcelshop_on_checkout.php',
        'suites/export_ppl_parcelshop_shipments.php',
    ]);
