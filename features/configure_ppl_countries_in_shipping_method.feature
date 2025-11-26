@configure_ppl_countries_in_shipping_method
Feature: Configure PPL countries in shipping method
	In order to control which countries' parcelshops are available
	As an Administrator
	I want to configure allowed countries and default country for PPL shipping method

	Background:
		Given the store operates on a single channel in "United States"
		And the store allows shipping with "PPL parcelshop" identified by "ppl_parcelshop"
		And I am logged in as an administrator

	@ui @javascript
	Scenario: Configure allowed countries and default country for PPL shipping method
		Given I want to modify a shipping method "PPL parcelshop"
		When I enable PPL parcelshops
		And I select "CZ, SK, PL" as allowed PPL countries
		And I select "CZ" as default PPL country
		And I save my changes
		Then I should be notified that it has been successfully edited
		And the allowed PPL countries should be "CZ, SK, PL"
		And the default PPL country should be "CZ"

	@ui @javascript
	Scenario: Default country must be within allowed countries
		Given I want to modify a shipping method "PPL parcelshop"
		When I enable PPL parcelshops
		And I select "CZ, SK" as allowed PPL countries
		And I select "DE" as default PPL country
		And I save my changes
		Then I should see a validation error for the default country field

	@ui @javascript
	Scenario: Can configure PPL without specifying countries
		Given I want to modify a shipping method "PPL parcelshop"
		When I enable PPL parcelshops
		And I save my changes
		Then I should be notified that it has been successfully edited
		And the PPL parcelshops should be enabled
