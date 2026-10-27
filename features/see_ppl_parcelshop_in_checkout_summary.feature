@see_ppl_parcelshop_in_checkout_summary
Feature: Seeing the selected PPL parcelshop in the checkout summary
	In order to know where my order will be delivered
	As a Customer
	I want to see the selected PPL parcelshop instead of my shipping address in the order summary

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "PHP T-Shirt" priced at "$19.99"
		And the store has "DHL" shipping method with "$1.99" fee
		And the store has "PPL parcelshop" shipping method with "$0.99" fee
		And this shipping method is enabled PPL parcelshops
		And the store allows paying with "CSOB"
		And I am a logged in customer

	@ui
	Scenario: Seeing the selected PPL parcelshop in the order summary
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I have selected "PPL parcelshop" shipping method
		And I have chosen PPL parcelshop with code "KM10833808" and name "PPL Parcelshop Frost Alley"
		When I complete the shipping step
		And I select "CSOB" payment method
		And I complete the payment step
		Then I should see PPL parcelshop "PPL Parcelshop Frost Alley" as the shipping address
		And my order's billing address should be to "Jon Snow"

	@ui
	Scenario: Changing the shipping method discards the selected PPL parcelshop
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I have selected "PPL parcelshop" shipping method
		And I have chosen PPL parcelshop with code "KM10833808" and name "PPL Parcelshop Frost Alley"
		And I complete the shipping step
		When I go back to the shipping step
		And I select "DHL" shipping method
		And I complete the shipping step
		And I select "CSOB" payment method
		And I complete the payment step
		Then address to "Jon Snow" should be used for both shipping and billing of my order
		And my shipment should not have a PPL parcelshop
