/**
 * PPL Parcelshop Widget Integration
 * Handles parcelshop selection via PPL's modal widget
 * https://ppl-widget-e.apidog.io/-inserting-and-displaying-a-map-863666m0
 */

function initPplParcelshopWidget(inputId, buttonId, modalId) {
    // Listen for parcelshop selection event from PPL widget
    document.addEventListener('ppl-parcelshop-map', function (event) {
        const selectedPoint = event.detail;

        if (selectedPoint && selectedPoint.code) {
            // Store full JSON data in hidden field
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.value = JSON.stringify(selectedPoint);
            }

            // Update button label with selected parcelshop info
            const buttonElement = document.getElementById(buttonId);
            if (buttonElement) {
                let displayText = [];

                if (selectedPoint.name) {
                    displayText.push(selectedPoint.name);
                }

                if (selectedPoint.address) {
                    const addr = selectedPoint.address;
                    const addressParts = [];

                    if (addr.street) addressParts.push(addr.street);
                    if (addr.city) addressParts.push(addr.city);

                    if (addressParts.length > 0) {
                        displayText.push(addressParts.join(', '));
                    }
                }

                buttonElement.innerHTML = displayText.join(' - ');
            }

            // Close modal
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                closePplModal(modalId);
            }
        }
    });
}

/**
 * Initialize PPL button states based on selected shipping method
 */
function initPplButtonStates() {
    // Listen for radio button changes using event delegation on click
    // We use 'click' instead of 'change' because Semantic UI might handle the change event
    document.addEventListener('click', function(event) {
        const target = event.target;

        // Check if we clicked on a radio button or its label/container
        let radio = null;
        if (target.type === 'radio' && target.name && target.name.includes('[method]')) {
            radio = target;
        } else {
            // Check if we clicked on a label or container that might contain a radio
            const closestCheckbox = target.closest('.ui.radio.checkbox');
            if (closestCheckbox) {
                radio = closestCheckbox.querySelector('input[type="radio"][name*="[method]"]');
            }
        }

        if (radio) {
            // Give Semantic UI time to update the radio state
            setTimeout(function() {
                const shipmentContainer = radio.closest('.ui.segment');
                if (shipmentContainer) {
                    updatePplButtonsForShipment(shipmentContainer);
                }
            }, 50);
        }
    });

    // Also listen for change events as backup
    document.addEventListener('change', function(event) {
        const radio = event.target;
        if (radio.type === 'radio' && radio.name && radio.name.includes('[method]')) {
            const shipmentContainer = radio.closest('.ui.segment');
            if (shipmentContainer) {
                updatePplButtonsForShipment(shipmentContainer);
            }
        }
    });

    // Initialize button states on page load for all shipments
    const shipmentContainers = document.querySelectorAll('#sylius-shipping-methods .ui.segment');
    shipmentContainers.forEach(shipmentContainer => {
        // Check if this segment contains shipping method radios
        const hasRadios = shipmentContainer.querySelector('input[type="radio"][name*="[method]"]');
        if (hasRadios) {
            updatePplButtonsForShipment(shipmentContainer);
        }
    });
}

/**
 * Update PPL button states for a specific shipment
 */
function updatePplButtonsForShipment(shipmentContainer) {
    // Get the selected shipping method code
    const selectedRadio = shipmentContainer.querySelector('input[type="radio"][name*="[method]"]:checked');
    const selectedMethodCode = selectedRadio?.value;

    // Find all PPL buttons in this shipment and enable/disable based on selection
    const pplButtons = shipmentContainer.querySelectorAll('button[data-ppl-button][data-method-code]');

    pplButtons.forEach(button => {
        const buttonMethodCode = button.dataset.methodCode;
        button.disabled = (buttonMethodCode !== selectedMethodCode);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('[data-ppl-input-id]');

    elements.forEach(function (element) {
        const inputId = element.dataset.pplInputId;
        const buttonId = element.dataset.pplButtonId;
        const modalId = element.dataset.pplModalId;

        initPplParcelshopWidget(inputId, buttonId, modalId);
    });

    // Initialize PPL button states
    initPplButtonStates();
})

function openPplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    const backdropElement = document.getElementById(modalId + '_backdrop');

    if (modalElement) {
        modalElement.style.display = 'block';
    }

    if (backdropElement) {
        backdropElement.style.display = 'block';
    }
}

function closePplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    const backdropElement = document.getElementById(modalId + '_backdrop');

    if (modalElement) {
        modalElement.style.display = 'none';
    }

    if (backdropElement) {
        backdropElement.style.display = 'none';
    }
}
