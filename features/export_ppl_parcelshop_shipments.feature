@export_ppl_parcelshop_shipments
Feature: Export PPL parcelshop shipments in admin panel
	In order to export PPL parcelshop shipments for batch processing
	As an Administrator
	I want to see ready PPL parcelshop shipments and export them to CSV

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "PHP T-Shirt" priced at "$19.99"
		And the store has "DHL" shipping method with "$1.99" fee
		And the store also allows shipping with "PPL parcelshop" identified by "ppl_parcel_shop"
		And this shipping method is enabled PPL parcelshops
		And the store allows paying with "Cash on Delivery"
		And the store allows paying offline

	@ui
	Scenario: Admin sees list of ready PPL parcelshop shipments
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And there is a customer "john@example.com" that placed an order "#00000002"
		And the customer bought a single "PHP T-Shirt"
		And the customer "John Doe" addressed it to "Test Street", "12345" "New York" in the "United States"
		And for the billing address of "John Doe" in the "Test Street", "12345" "New York", "United States"
		And the customer chose "DHL" shipping method with "offline" payment
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		Then I should see 1 shipment ready for export
		And I should see shipment for order "#00000001" in the list

	@ui
	Scenario: Admin can export PPL parcelshop shipments to CSV
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		And I export selected PPL parcelshop shipments to CSV
		Then I should receive a CSV file
		And the CSV should contain shipment data for order "#00000001"

	@ui
	Scenario: Admin can export multiple PPL parcelshop shipments at once
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And there is a customer "john@example.com" that placed an order "#00000002"
		And the customer bought a single "PHP T-Shirt"
		And the customer "John Doe" addressed it to "Test Street", "12345" "New York" in the "United States"
		And for the billing address of "John Doe" in the "Test Street", "12345" "New York", "United States"
		And the customer chose "PPL parcelshop" shipping method with "offline" payment
		And choose PPL parcelshop ID "456", name "PPL Branch 2" and address "Second Street 2, Brno"
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		Then I should see 2 shipments ready for export
		When I export all PPL parcelshop shipments to CSV
		Then I should receive a CSV file
		And the CSV should contain 2 shipment records

	@ui
	Scenario: Admin can mark PPL parcelshop shipments as shipped
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		And I should see 1 shipment ready for export
		When I mark selected PPL parcelshop shipments as shipped
		Then I should be notified that shipments have been marked as shipped
		And I should see 0 shipments ready for export

	@ui
	Scenario: CSV export contains correct PPL parcelshop data
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		And I export selected PPL parcelshop shipments to CSV
		Then the CSV should contain PPL parcelshop ID "123" for order "#00000001"
		And the CSV should contain customer email "lucy@teamlucifer.com"

	@ui
	Scenario: Only ready shipments with paid or COD orders are shown
		Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000001"
		And the customer bought a single "PHP T-Shirt"
		And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
		And for the billing address of "Lucifer Morningstar" in the "Seaside Fwy", "90802" "Los Angeles", "United States"
		And the customer chose "PPL parcelshop" shipping method with "Cash on Delivery" payment
		And choose PPL parcelshop ID "123", name "PPL Branch 1" and address "Main Street 1, Prague"
		And I am logged in as an administrator
		When I browse PPL parcelshop shipments ready for export
		Then I should see 1 shipment ready for export
		And I should see shipment for order "#00000001" in the list