@enable_ppl_parcelshops_in_shipping_method
Feature: Enable PPL parcelshops in shipping method
	In order to Enable PPL parcelshops in shipping method settings in admin panel
	As an Administrator
	I want to Enable PPL parcelshops in shipping method

	Background:
		Given the store operates on a single channel in "United States"
		And the store allows shipping with "PPL parcelshop" identified by "ppl_parcelshop"
		And I am logged in as an administrator

	@ui
	Scenario: Enable PPL parcelshops in shipping method
		Given I want to modify a shipping method "PPL parcelshop"
		When I enable PPL parcelshops
		And I save my changes
		Then I should be notified that it has been successfully edited
		And the PPL parcelshops shoul be enabled

	@ui
	Scenario: Disable PPL parcelshops in shipping method
		Given I want to modify a shipping method "PPL parcelshop"
		When I disable PPL parcelshops
		And I save my changes
		Then I should be notified that it has been successfully edited
		And the PPL parcelshops shoul be disabled
