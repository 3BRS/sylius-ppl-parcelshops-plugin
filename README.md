<h1 align="center">
    PPL parcelshop plugin
    <br />
    <a href="https://packagist.org/packages/3BRS/sylius-ppl-parcelshop-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/3BRS/sylius-ppl-parcelshops-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/3BRS/sylius-ppl-parcelshops-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/3BRS/sylius-ppl-parcelshops-plugin.svg" />
    </a>
    <a href="https://circleci.com/gh/3BRS/sylius-ppl-parcelshops-plugin" title="Build status" target="_blank">
       <img src="https://circleci.com/gh/3BRS/sylius-ppl-parcelshops-plugin.svg?style=shield" />
   </a>
</h1>

## Features

 - Enables sending shipments via <a href="https://www.ppl.cz/co-jsou-vydejni-mista">PPL</a> to PPL parcelshops.
 - The user can choose the PPL parcelshops from the <a href="https://www.ppl.cz/mapa-vydejnich-mist?utm_source=landing_page&amp;amp;utm_medium=button_head_najit_VM&amp;amp;utm_campaign=vydejni_mista">map</a> during checkout in the Shipment step. 
 - See PPL parcelshop in final checkout step and also in the admin panel.
 - Export CSV with the PPL parcelshops shipments and import it easily into PPL's system.

## Installation

### 1. Install the plugin via Composer

```bash
composer require 3brs/sylius-ppl-parcelshops-plugin
```

### 2. Register the bundles

Add plugin classes to your `config/bundles.php`:

```php
return [
    // ...
    ThreeBRS\SyliusShipmentExportPlugin\ThreeBRSSyliusShipmentExportPlugin::class => ['all' => true],
    ThreeBRS\SyliusPplParcelshopsPlugin\ThreeBRSSyliusPplParcelshopsPlugin::class => ['all' => true],
];
```

### 3. Import routing

Add routing to `config/routes.yaml`:

```yaml
threebrs_sylius_shipment_export_plugin:
    resource: "@ThreeBRSSyliusShipmentExportPlugin/Resources/config/routing.y*ml"
    prefix: '/%sylius_admin.path_name%'

threebrs_sylius_ppl_parcelshops_plugin:
    resource: '@ThreeBRSSyliusPplParcelshopsPlugin/Resources/config/routing.yaml'
    prefix: /
```

### 4. Import plugin configuration

Add config import to `config/packages/_sylius.yaml`:

```yaml
imports:
    # ...
    - { resource: "@ThreeBRSSyliusPplParcelshopsPlugin/Resources/config/config.yaml" }
```

### 5. Configure entity models

Your Entity `Shipment` has to implement `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface`.
You can use the trait `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentTrait`.

Example:

```php
<?php

namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Shipment as BaseShipment;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipment')]
class Shipment extends BaseShipment implements PplShipmentInterface
{
    use PplShipmentTrait;
}
```

Your Entity `ShippingMethod` has to implement `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface`.
You can use the trait `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait`.

Example:

```php
<?php

namespace App\Entity\Shipping;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface;
use ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
class ShippingMethod extends BaseShippingMethod implements PplShippingMethodInterface
{
    use PplShippingMethodTrait;
}
```

For more details on entity customization, see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.13/customization/model.html).

### 6. Override templates

#### Admin Shipping Method Form

Override `templates/bundles/SyliusAdminBundle/ShippingMethod/_form.html.twig` and include the PPL form:

```twig
{# @SyliusAdmin/ShippingMethod/_form.html.twig #}
{# ... existing form content ... #}
{{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Admin/ShippingMethod/_pplForm.html.twig') }}
{# ... rest of the form ... #}
```
  - see `tests/Application/templates/bundles/SyliusAdminBundle/ShippingMethod/_form.html.twig` for example

#### Shop Checkout Shipping Choice

Override `templates/bundles/SyliusShopBundle/Checkout/SelectShipping/_choice.html.twig` and include the PPL widget at the end:

```twig
{# @SyliusShop/Checkout/SelectShipping/_choice.html #}
{# ... existing shipping choice content ... #}
{{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Checkout/SelectShipping/_pplChoice.html.twig') }}
```
  - see `tests/Application/templates/bundles/SyliusShopBundle/Checkout/SelectShipping/_choice.html.twig` for example

#### Shop Order Addresses

Override `templates/bundles/SyliusShopBundle/Common/Order/_addresses.html.twig` and replace the shipping address section:

```twig
{# @SyliusAdmin/Order/Show/_addresses.html.twig #}
<div class="ui segment">
    <div class="ui two column divided stackable grid">
        <div class="column" id="sylius-billing-address" {{ sylius_test_html_attribute('billing-address') }}>
            <div class="ui small dividing header">{{ 'sylius.ui.billing_address'|trans }}</div>
            {% include '@SyliusShop/Common/_address.html.twig' with {'address': order.billingAddress} %}
        </div>
        <div class="column" id="sylius-shipping-address" {{ sylius_test_html_attribute('shipping-address') }}>
            <div class="ui small dividing header">{{ 'sylius.ui.shipping_address'|trans }}</div>
            {{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Common/Order/_addresses.html.twig') }}
        </div>
    </div>
</div>
```

#### Admin Order Addresses

Override `templates/bundles/SyliusAdminBundle/Order/Show/_addresses.html.twig` and replace the shipping address section:

```twig
{# @ThreeBRSShipmentExportPlugin/_row.html.twig #}
{% if order.shippingAddress is not null %}
    <h4 class="ui top attached styled header">
        {{ 'sylius.ui.shipping_address'|trans }}
    </h4>
    <div class="ui attached segment" id="shipping-address">
        {{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Admin/Common/Order/_addresses.html.twig') }}
    </div>
{% endif %}
{# ... billing address and other content ... #}
```

### 7. Create and run database migrations

Generate and run doctrine migrations to add the required database columns:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

## Configuration

The plugin can be configured using the following parameters in your `config/packages/_sylius.yaml` or `config/services.yaml`:

```yaml
parameters:
    # Shipping method codes that should be exported to PPL CSV format
    # Default: ['ppl_parcel_shop']
    pplShippingMethodsCodes: ['ppl_parcel_shop', 'your_custom_ppl_method']

    # Country codes where PPL parcelshop service is available
    # Default: ['CZ', 'PL']
    threebrs_sylius_ppl_parcelshops_plugin_ppl_countries: ['CZ', 'PL']
```

## Usage

### 1. Create a PPL Parcelshop Shipping Method

1. Go to the admin panel: **Shipping Methods** → **Create**
2. Fill in the basic information (code, zone, calculator, etc.)
3. Check the **"To PPL ParcelShop enabled"** option
4. Select the countries where this shipping method should be available
5. Save the shipping method

### 2. Customer Checkout Flow

When a customer selects a PPL parcelshop shipping method during checkout:

1. A PPL parcelshop selector widget will appear
2. The customer can choose their preferred parcelshop from an interactive map
3. The selected parcelshop information (ID, name, address) is saved with the shipment
4. The parcelshop details are visible in the order confirmation

### 3. Export Shipments to PPL

1. Go to the admin panel: **Shipment Export**
2. Select the PPL ParcelShop exporter type
3. Choose the shipments you want to export
4. Click **Export** to download a CSV file
5. Import the CSV file into PPL's system

The CSV export includes all necessary information for PPL processing:
- Parcelshop ID and details
- Recipient information
- Package weight and dimensions
- Cash on delivery amount (if applicable)
- Order reference

## Development

### Usage

- Develop your plugin in `/src`
- See `bin/` for useful commands

### Testing


After your changes you must ensure that the tests are still passing.

```bash
$ composer install
$ bin/console doctrine:schema:create -e test
$ bin/behat
$ bin/phpstan.sh
$ bin/ecs.sh
```

License
-------
This library is under the MIT license.

Credits
-------
Developed by [manGoweb](https://www.threebrs.eu/).
