# Changelog

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
