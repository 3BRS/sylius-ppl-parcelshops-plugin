@validate_ppl_parcelshop_on_checkout
Feature: Validating PPL parcelshop data in checkout
	In order to have my order delivered to an existing PPL parcelshop
	As a Customer
	I want to be prevented from completing the shipping step with invalid PPL parcelshop data

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "PHP T-Shirt" priced at "$19.99"
		And the store has "DHL" shipping method with "$1.99" fee
		And the store has "PPL parcelshop" shipping method with "$0.99" fee
		And this shipping method is enabled PPL parcelshops
		And the store allows paying with "CSOB"
		And I am a logged in customer

	@ui
	Scenario Outline: Unable to complete the shipping step with invalid PPL parcelshop data
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I have selected "PPL parcelshop" shipping method
		And the PPL parcelshop field contains '<data>'
		When I complete the shipping step
		Then I should be notified that I have to choose a PPL parcelshop

		Examples:
			| data                             |
			| {"code":123}                     |
			| {"code":""}                      |
			| {"name":"PPL 1"}                 |
			| {"code":"KM1","name":5}          |
			| {"code":"KM1","city":["Prague"]} |
			| 5                                |
			| not json                         |
