# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that enables shipping to PPL parcelshops. It integrates with the PPL parcelshop selection system and provides CSV export functionality for shipment data.

## Technology Stack

- **Framework**: Sylius 2.0+ (Symfony-based e-commerce platform)
- **PHP**: 8.2+
- **Testing**: Behat for behavioral tests via `sylius/test-application`
- **Code Quality**: PHPStan (max level), ECS (Easy Coding Standard)
- **Dependencies**: Requires `3brs/sylius-shipment-export-plugin` (v0.8.0+)

## Development Commands

### Testing
```bash
# Install dependencies
composer install

# Run Behat tests (via sylius/test-application)
vendor/bin/behat

# Run specific Behat suite/feature
vendor/bin/behat features/path/to/feature.feature
```

### Code Quality Checks
```bash
# Static analysis with PHPStan (level max)
vendor/bin/phpstan analyse

# Coding standards check with ECS
vendor/bin/ecs check

# Fix coding standards automatically
vendor/bin/ecs check --fix
```

### Console Access
The Symfony console is available via:
```bash
vendor/bin/console <command>
```

## Architecture

### Core Plugin Structure

The plugin follows Sylius plugin conventions with namespace `ThreeBRS\SyliusPplParcelshopsPlugin`:

- **Main Bundle Class**: `ThreeBRSSyliusPplParcelshopsPlugin` - extends Symfony Bundle and uses `SyliusPluginTrait`
- **Resources Location**: `src/Resources/` contains configuration, views (Twig templates), and routing

### Key Components

#### 1. Domain Models & Traits

The plugin extends Sylius entities through interfaces and traits (not direct inheritance):

- **`PplShipmentInterface`** and **`PplShipmentTrait`**: Add PPL parcelshop data as JSON to Shipment entities
- **`PplShippingMethodInterface`** and **`PplShippingMethodTrait`**: Mark shipping methods as PPL parcelshop-enabled

These must be implemented by the host application's entities (see README.md installation steps).

#### 2. Form Extensions

Form extensions augment existing Sylius forms:

- **`ShipmentPplExtension`**: Extends checkout shipment form to include PPL parcelshop selection widget
- **`AdminPplShippingMethodExtension`**: Extends admin shipping method form to configure PPL settings
  - Configured with `threebrs_sylius_ppl_parcelshops_plugin_ppl_countries` parameter

#### 3. CSV Export System

- **`PplShipmentExporter`**: Implements `ShipmentExporterInterface` from the base shipment export plugin
  - Generates CSV rows with 20 columns matching PPL's import format
  - Handles currency conversion to CZK
  - Calculates weight from order items
  - Determines cash-on-delivery status from payment method
  - Configured via `pplShippingMethodsCodes` parameter (default: `['ppl_parcel_shop']`)
  - Tagged as `threebrs.shipment_exporter_type` with type `ppl_parcel_shop`

### Service Configuration

Services are defined in `src/Resources/config/services.yaml`:
- Uses autowire and autoconfigure by default
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

This plugin uses `sylius/test-application` package for testing:
- Configuration is in `config/` directory
- Tests are in `tests/Behat/`

## Configuration Parameters

Key parameters in `src/Resources/config/services.yaml`:

- `threebrs_sylius_ppl_parcelshops_plugin_ppl_shipping_method_codes`: Array of shipping method codes that should export to PPL (default: `['ppl_parcel_shop']`)
- `threebrs_sylius_ppl_parcelshops_plugin_ppl_countries`: Array of country codes for PPL service

## PHPStan Configuration

- Level: max
- Excludes:
  - `src/DependencyInjection/Configuration.php` (causes PHPStan crash)

## Code Style

- Uses `sylius-labs/coding-standard` as base
