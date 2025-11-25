# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that enables shipping to PPL parcelshops. It integrates with the PPL parcelshop selection system and provides CSV export functionality for shipment data.

## Technology Stack

- **Framework**: Sylius 1.13+ (Symfony-based e-commerce platform)
- **PHP**: 8.1+
- **Testing**: Behat for behavioral tests
- **Code Quality**: PHPStan (max level), ECS (Easy Coding Standard), Rector
- **Dependencies**: Requires `3brs/sylius-shipment-export-plugin` (v0.8.0+)

## Development Commands

### Testing
```bash
# Install dependencies
composer install

# Create test database schema
bin/console doctrine:schema:create -e test

# Run Behat tests (behavioral tests)
bin/behat
# or
bin/behat.sh

# Run specific Behat suite/feature
bin/behat.sh features/path/to/feature.feature
```

### Code Quality Checks
```bash
# Static analysis with PHPStan (level max)
bin/phpstan.sh

# Coding standards check with ECS
bin/ecs.sh

# Fix coding standards automatically
vendor/bin/ecs check --config=ecs.php --fix

# Run Rector for automated refactoring
vendor/bin/rector process
```

### Console Access
The Symfony console is available via symlink:
```bash
bin/console <command>
```

## Architecture

### Core Plugin Structure

The plugin follows Sylius plugin conventions with namespace `ThreeBRS\SyliusPplParcelshopsPlugin`:

- **Main Bundle Class**: `ThreeBRSSyliusPplParcelshopsPlugin` - extends Symfony Bundle and uses `SyliusPluginTrait`
- **Resources Location**: `src/Resources/` contains configuration, views (Twig templates), and routing

### Key Components

#### 1. Domain Models & Traits

The plugin extends Sylius entities through interfaces and traits (not direct inheritance):

- **`PplShipmentInterface`** and **`PplShipmentTrait`**: Add PPL parcelshop data (KTMID, KTMaddress, KTMname) to Shipment entities
- **`PplShippingMethodInterface`** and **`PplShippingMethodTrait`**: Mark shipping methods as PPL parcelshop-enabled

These must be implemented by the host application's entities (see README.md installation steps).

#### 2. Controllers

- **`PplController`**: Handles callback from PPL parcelshop selection widget
  - `pplReturn()`: Processes the parcelshop selection and saves it to the shipment
  - Receives KTMID, KTMaddress, KTMname from query parameters
  - Validates shipment belongs to current cart before saving

#### 3. Form Extensions

Form extensions augment existing Sylius forms:

- **`ShipmentPplExtension`**: Extends checkout shipment form to include PPL parcelshop selection widget
- **`AdminPplShippingMethodExtension`**: Extends admin shipping method form to configure PPL settings
  - Configured with `threebrs_sylius_ppl_parcelshops_plugin_ppl_countries` parameter (defaults: CZ, PL)

#### 4. CSV Export System

- **`PplShipmentExporter`**: Implements `ShipmentExporterInterface` from the base shipment export plugin
  - Generates CSV rows with 20 columns matching PPL's import format
  - Handles currency conversion to CZK
  - Calculates weight from order items
  - Determines cash-on-delivery status from payment method
  - Configured via `pplShippingMethodsCodes` parameter (default: `['ppl_parcel_shop']`)
  - Tagged as `mango_sylius.shipment_exporter_type` with type `ppl_parcel_shop`

### Service Configuration

Services are defined in `src/Resources/config/services.yml`:
- All services have `autowire: false` and `autoconfigure: false` by default
- Dependencies are explicitly configured
- Form extensions are registered via `form.type_extension` tags

### View Templates

Templates in `src/Resources/views/` are designed to be included in host application templates:

- **Admin Templates**:
  - `Admin/ShippingMethod/_pplForm.html.twig`: PPL configuration form
  - `Admin/Common/Order/_addresses.html.twig`: Order addresses with PPL parcelshop info

- **Shop Templates**:
  - `Shop/Checkout/SelectShipping/_pplChoice.html.twig`: Parcelshop selector widget
  - `Shop/Common/Order/_addresses.html.twig`: Order confirmation with parcelshop details

- **Export Template**:
  - `_exporterRow.html.twig`: CSV export row template

### Test Application

The `tests/Application/` directory contains a minimal Sylius application for testing:
- **`tests/Application/src/Entity/`**: Contains example entity implementations with PPL traits
- **`tests/Application/config/`**: Symfony configuration for the test app
- Uses environment variable `APP_ENV=test`

## Configuration Parameters

Key parameters in `src/Resources/config/services.yml`:

- `pplShippingMethodsCodes`: Array of shipping method codes that should export to PPL (default: `['ppl_parcel_shop']`)
- `threebrs_sylius_ppl_parcelshops_plugin_ppl_countries`: Array of country codes for PPL service (default: `['CZ', 'PL']`)

## PHPStan Configuration

- Level: max
- Container XML path: `tests/Application/var/cache/test/Tests_ThreeBRS_SyliusPplParcelshopsPlugin_KernelTestDebugContainer.xml`
- Excludes:
  - `src/DependencyInjection/Configuration.php` (too slow to analyze)
  - Test files in `tests/Behat/`

## Code Style

- Uses `sylius-labs/coding-standard` as base
- Additional rules: `NoUnusedImportsFixer`, `SingleImportPerStatementFixer`
- Visibility not required in Spec files
