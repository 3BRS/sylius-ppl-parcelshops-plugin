# Changelog

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
