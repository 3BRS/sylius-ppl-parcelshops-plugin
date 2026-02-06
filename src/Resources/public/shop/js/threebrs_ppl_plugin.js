/**
 * PPL Parcelshop Widget Integration
 * Handles parcelshop selection via PPL's modal widget
 * https://ppl-widget-e.apidog.io/-inserting-and-displaying-a-map-863666m0
 *
 * Compatible with Sylius 2.0+ (Bootstrap 5)
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
            closePplModal(modalId);
        }
    });
}

/**
 * Initialize PPL button states based on selected shipping method
 */
function initPplButtonStates() {
    // Listen for radio button changes
    document.addEventListener('change', function(event) {
        const radio = event.target;
        if (radio.type === 'radio' && radio.name && radio.name.includes('[method]')) {
            // Find the parent form or container
            const shipmentContainer = radio.closest('form') || radio.closest('[data-sylius-test-html-attribute="shipments"]') || document;
            if (shipmentContainer) {
                updatePplButtonsForShipment(shipmentContainer);
            }
        }
    });

    // Also listen for click events on card containers (Bootstrap 5 uses cards for shipping methods)
    document.addEventListener('click', function(event) {
        const card = event.target.closest('.card');
        if (card) {
            const radio = card.querySelector('input[type="radio"][name*="[method]"]');
            if (radio && !radio.checked) {
                // Radio will be checked by the browser, wait a bit for the state to update
                setTimeout(function() {
                    const shipmentContainer = radio.closest('form') || document;
                    updatePplButtonsForShipment(shipmentContainer);
                }, 10);
            }
        }
    });

    // Initialize button states on page load
    setTimeout(function() {
        updatePplButtonsForShipment(document);
    }, 100);
}

/**
 * Update PPL button states for a specific container
 */
function updatePplButtonsForShipment(container) {
    // Get the selected shipping method code
    const selectedRadio = container.querySelector('input[type="radio"][name*="[method]"]:checked');
    const selectedMethodCode = selectedRadio?.value;

    // Find all PPL buttons and enable/disable based on selection
    const pplButtons = container.querySelectorAll('button[data-ppl-button][data-method-code]');

    pplButtons.forEach(button => {
        const buttonMethodCode = button.dataset.methodCode;
        button.disabled = buttonMethodCode !== selectedMethodCode;
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

/**
 * Open PPL modal using Bootstrap 5 Modal API
 */
function openPplModal(modalId) {
    const modalElement = document.getElementById(modalId);

    if (modalElement) {
        // Use Bootstrap 5 Modal API if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        } else {
            // Fallback: manual display
            modalElement.classList.add('show');
            modalElement.style.display = 'block';
            document.body.classList.add('modal-open');

            // Create backdrop if it doesn't exist
            let backdrop = document.querySelector('.modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }
}

/**
 * Close PPL modal using Bootstrap 5 Modal API
 */
function closePplModal(modalId) {
    const modalElement = document.getElementById(modalId);

    if (modalElement) {
        // Use Bootstrap 5 Modal API if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        } else {
            // Fallback: manual hide
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');

            // Remove backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}
