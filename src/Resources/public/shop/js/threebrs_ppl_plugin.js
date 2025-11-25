document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('[data-ppl-input-id], [data-ppl-button-id], [data-ppl-modal-id]');
    elements.forEach(function (element) {
        initPplParcelshopWidget(element.dataset.pplInputId, element.dataset.pplButtonId, element.dataset.pplModalId)
    })
})

/**
 * PPL Parcelshop Widget Integration
 * Handles parcelshop selection via PPL's modal widget
 */

function initPplParcelshopWidget(inputId, buttonId, modalId) {
    // Listen for parcelshop selection event
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
                modalElement.style.display = 'none';
                // Remove modal backdrop if using Semantic UI
                const dimmer = modalElement.closest('.ui.dimmer');
                if (dimmer) {
                    dimmer.classList.remove('active');
                }
            }
        }
    });
}

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
