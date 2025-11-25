/**
 * PPL Parcelshop Widget Integration
 * Handles parcelshop selection via PPL's modal widget
 */

function initPplParcelshopWidget(inputId, buttonId, modalId) {
    console.log('PPL Widget: Initializing for modal', modalId, 'input', inputId, 'button', buttonId);

    // Listen for parcelshop selection event from PPL widget
    document.addEventListener('ppl-parcelshop-map', function (event) {
        console.log('PPL Widget: ppl-parcelshop-map event received', event.detail);
        const selectedPoint = event.detail;

        if (selectedPoint && selectedPoint.code) {
            // Store full JSON data in hidden field
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.value = JSON.stringify(selectedPoint);
                console.log('PPL Widget: Data stored in input', inputId, selectedPoint);
            } else {
                console.error('PPL Widget: Input element not found', inputId);
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
                console.log('PPL Widget: Button label updated', displayText.join(' - '));
            } else {
                console.error('PPL Widget: Button element not found', buttonId);
            }

            // Close modal
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                closePplModal(modalId);
                console.log('PPL Widget: Modal closed', modalId);
            } else {
                console.error('PPL Widget: Modal element not found', modalId);
            }
        } else {
            console.warn('PPL Widget: Invalid or empty selectedPoint', selectedPoint);
        }
    });

    console.log('PPL Widget: Event listener registered successfully');
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('PPL Widget: DOMContentLoaded - searching for modals');
    const elements = document.querySelectorAll('[data-ppl-input-id]');
    console.log('PPL Widget: Found', elements.length, 'modal(s)');

    elements.forEach(function (element) {
        const inputId = element.dataset.pplInputId;
        const buttonId = element.dataset.pplButtonId;
        const modalId = element.dataset.pplModalId;

        console.log('PPL Widget: Initializing modal', { inputId, buttonId, modalId });
        initPplParcelshopWidget(inputId, buttonId, modalId);
    });
})

function openPplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.style.display = 'block';
        // Add Semantic UI dimmer classes if needed
        const dimmer = modalElement.closest('.ui.dimmer');
        if (dimmer) {
            dimmer.classList.add('active');
        }
    }
}

function closePplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.style.display = 'none';
        const dimmer = modalElement.closest('.ui.dimmer');
        if (dimmer) {
            dimmer.classList.remove('active');
        }
    }
}
