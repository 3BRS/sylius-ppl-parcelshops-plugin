# Changelog

## v2.3.0 (2026-10-23)

### Changed

- **BREAKING**: Added `PplShipmentInterface::hasPplPickupPoint()`; implementations without `PplShipmentTrait` must implement it
- **BREAKING**: `Shop/Common/Order/_addresses.html.twig` is rendered as a Twig hookable and reads the order from `hookable_metadata.context.order`; it no longer works when included with an `order` variable

### Added

- Selected PPL parcelshop is shown instead of the shipping address in the shop order summary (checkout complete step, customer account order detail); the template replaces the core `address` hookable of `sylius_shop.shared.order.show.summary.addresses.shipping.body`

### Fixed

- Changing the shipping method in checkout to a method without PPL ParcelShop enabled clears the stored parcelshop (`pplData` and legacy `pplKTM*` fields)
- Shipping step rejects parcelshop data that is not a JSON object with a non-empty string `code`, or whose `name`, `parcelshopName`, `street`, `city`, `zipCode` or `country` is not a string or null, instead of storing it
- Admin order detail and `_exporterRow.html.twig` show the parcelshop only when the shipment has a non-empty parcelshop ID and its shipping method has PPL ParcelShop enabled
- CSV export fills the target parcelshop column for parcelshops chosen in the map widget (`pplData`), not only from the legacy `pplKTMID` field

## v2.2.0 (2026-10-07)

### Changed

- Added compatibility with Sylius 2.3
- Added compatibility with Symfony 8
- Updated `3brs/sylius-shipment-export-plugin` to ^2.2

## v2.1.0 (2026-02-06)

### Changed

- **BREAKING**: Requires Sylius ^2.1 (drop support for Sylius 2.0)
- **BREAKING**: Requires PHP 8.3+
- Updated `3brs/sylius-shipment-export-plugin` to ^2.0
- Updated Symfony dependencies to ^6.4 || ^7.4
- Added compatibility with Sylius 2.2

## v2.0.0 (2026-02-06)

### Changed

- **BREAKING**: Requires Sylius 2.0.* (drop support for Sylius 1.x)
- **BREAKING**: Requires PHP 8.2+
- Migrated from `sylius_ui` events to Twig Hooks
- Updated admin templates for Bootstrap 5 (Sylius 2.0 admin UI)
- Form type extension now extends `Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType`

### Removed

- Deprecated `getPplKTMID()`, `getPplKTMname()`, `getPplKTMaddress()` methods removed
- Deprecated `setPplKTMID()`, `setPplKTMname()`, `setPplKTMaddress()` methods removed

### Fixed

- PPL parcelshop widget compatibility with Sylius 2.0 checkout flow

## v1.0.1 (2026-01-28)

### Fixed

- [SLS-97] Fixed PPL parcelshop pre-selection when returning to checkout shipping step - button now shows only parcelshop name instead of full address
- [SLS-97] Added `data-code` attribute to PPL widget for proper parcelshop pre-selection in popup
- Fixed CI prefer-lowest compatibility by requiring `symfony/cache ^6.4.17`

## v1.0.0 (2025-12-09)

### Changed

- PPL parcelshop data is now stored as complete JSON in `pplData` field instead of separate `pplKTMID`, `pplKTMname`, `pplKTMaddress` fields
- PPL parcelshop selection now uses JavaScript modal widget instead of redirect-based flow

### Deprecated

- `getPplKTMID()`, `getPplKTMname()`, `getPplKTMaddress()` methods are deprecated, use `getPplData()` instead
- `setPplKTMID()`, `setPplKTMname()`, `setPplKTMaddress()` methods are deprecated, use `setPplData()` instead
- These deprecated methods will be removed in version 2.0 (Sylius 2.0 compatibility release)

### Added

- `getPplData()` / `setPplData()` methods for storing complete PPL parcelshop data as JSON
- `getPplPickupPointId()`, `pplPickupPointName()`, `pplPickupPointAddress()` helper methods
- `Shop/_javascripts.html.twig` template for PPL widget JavaScript inclusion
