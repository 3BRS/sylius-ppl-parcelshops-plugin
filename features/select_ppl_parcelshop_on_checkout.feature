@select_ppl_parcelshop_on_checkout
Feature: Select PPL parcelshop branch in checkout
	In order to select PPL parcelshop shipping method and PPL parcelshop dispensing point
	As a Customer
	I want to select PPL parcelshop shipping method and PPL parcelshop dispensing point and see it in final checkout step

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "PHP T-Shirt" priced at "$19.99"
		And the store has "DHL" shipping method with "$1.99" fee
		And the store has "PPL parcelshop" shipping method with "$0.99" fee
		And this shipping method is enabled PPL parcelshops
		And the store allows paying with "CSOB"
		And I am a logged in customer

	@ui
	Scenario: Complete order with non PPL parcelshop shipping method
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I select "DHL" shipping method
		And I complete the shipping step
		And I select "CSOB" payment method
		And I complete the payment step
		And address to "Jon Snow" should be used for both shipping and billing of my order
		When I confirm my order
		Then I should see the thank you page

	@ui
	Scenario: Unable to complete shipping step with PPL parcelshop shipping methods without selecting the PPL parcelshop
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		When I select "PPL parcelshop" shipping method
		Then I should not be able to go to the payment step again

	@ui
	Scenario: Complete order with PPL parcelshop shipping method
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I select "PPL parcelshop" shipping method
		And I choose PPL parcelshop with ID "1", name "PPL 1" and address "PPL Address 15, 123"
		And I complete the shipping step
		And I select "CSOB" payment method
		And I complete the payment step
		And I see PPL parcelshop instead of shipping address
		But my order's billing address should be to "Jon Snow"
		And I confirm my order
		And I should see the thank you page
