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

 - Enables sending shipments via <a href="https://www.ppl.cz/main.aspx?cls=art&art_id=1685">PPL</a> to PPL parcelshops.
 - The user can choose the PPL parcelshops from the <a href="https://www.pplbalik.cz/Main3.aspx?cls=KTMMap">map</a> during checkout in the Shipment step. 
 - See PPL parcelshop in final checkout step and also in the admin panel.
 - Export CSV with the PPL parcelshops shipments and import it easily into PPL's system.

## Installation

1. Run `$ composer require 3brs/sylius-ppl-parcelshops-plugin`.
1. Add plugin classes to your `config/bundles.php`:

    ```php
    return [
    // ...
        ThreeBRS\SyliusShipmentExportPlugin\ThreeBRSSyliusShipmentExportPlugin::class => ['all' => true],
        ThreeBRS\SyliusPplParcelshopsPlugin\ThreeBRSSyliusPplParcelshopsPlugin::class => ['all' => true],
    ];
    ```

1. Add routing to `config/routes.yaml`

    ```yaml
    threebrs_sylius_shipment_export_plugin:
        resource: "@ThreeBRSSyliusShipmentExportPlugin/Resources/config/routing.y*ml"
        prefix: '/%sylius_admin.path_name%'

    threebrs_sylius_ppl_parcelshops_plugin:
        resource: '@ThreeBRSSyliusPplParcelshopsPlugin/Resources/config/routing.y*ml'
        prefix: /
    ```

1. Add config to `config/packages/_sylius.yaml`
    ```yaml
    imports:
    # ...
        - { resource: "@ThreeBRSSyliusPplParcelshopsPlugin/config/config.y*ml" }
    ```

1. Your Entity `Shipment` has to implement `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentInterface`. 
   You can use the trait `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShipmentTrait`.
   
1. Your Entity `ShippingMethod` has to implement `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodInterface`. 
   You can use the trait `\ThreeBRS\SyliusPplParcelshopsPlugin\Model\PplShippingMethodTrait`.

1. Include `@ThreeBRSSyliusPplParcelshopsPlugin/Admin/ShippingMethod/_pplForm.html.twig` into `@SyliusAdmin/ShippingMethod/_form.html.twig`.
 
    ```twig
    {{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Admin/ShippingMethod/_pplForm.html.twig') }}
    ```
   
1. Include `@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Checkout/SelectShipping/_pplChoice.html.twig` into `@SyliusShop/Checkout/SelectShipping/_choice.html.twig`.
 
    ```twig
    {{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Checkout/SelectShipping/_pplChoice.html.twig') }}
    ```
   
1. Replace `{% include '@SyliusShop/Common/_address.html.twig' with {'address': order.shippingAddress} %}` 
   with `{{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Shop/Common/Order/_addresses.html.twig') }}` 
   in `@SyliusShop/Common/Order/_addresses.html.twig`

1. Replace `{% include '@SyliusAdmin/Common/_address.html.twig' with {'address': order.shippingAddress} %}` 
   with `{{ include('@ThreeBRSSyliusPplParcelshopsPlugin/Admin/Common/Order/_addresses.html.twig') }}` 
   in `@SyliusAdmin/Order/Show/_addresses.html.twig`

1. Override the template in `@ThreeBRSShipmentExportPlugin/_row.html.twig`
    ```twig
    {% extends '@ThreeBRSShipmentExportPlugin/_row.html.twig' %}

    {% block address %}
        {% if row.pplKTMID %}
           {{ include('@ThreeBRSSyliusPplParcelshopsPlugin/_exporterRow.html.twig') }}
        {% else %}
           {{ parent() }}
        {% endif %}
    {% endblock %}
    ```
   
1. Create and run doctrine database migrations.

For the guide how to use your own entity see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.7/customization/model.html)

## Usage

* For delivery to the PPL parcelshop, create new shipping method in the admin panel, check `To PPL ParcelShop enabled`.
* PPL CSV export will be generated for shipping method which has the code 'ppl_parcel_shop', you can change this in parameters, it is an array.

    ```yaml
    parameters:
        pplShippingMethodsCodes: ['ppl_parcel_shop']
    ```

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
