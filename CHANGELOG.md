# Changelog

## v1.0.0 (2025-12-08)

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
