/**
 * PPL Parcelshop Widget Integration for Sylius 2.0 (Bootstrap 5)
 * Handles parcelshop selection via PPL's modal widget
 * https://ppl-widget-e.apidog.io/-inserting-and-displaying-a-map-863666m0
 */

/**
 * Initialize PPL Parcelshop widget for a specific modal
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

            // Close Bootstrap 5 modal
            const modalElement = document.getElementById(modalId);
            if (modalElement && typeof bootstrap !== 'undefined') {
                const bsModal = bootstrap.Modal.getInstance(modalElement);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        }
    });
}

/**
 * Initialize PPL button states based on selected shipping method
 * Works with Sylius 2.0 Bootstrap 5 structure
 */
function initPplButtonStates() {
    // Listen for radio button changes
    document.addEventListener('change', function(event) {
        const radio = event.target;
        if (radio.type === 'radio' && radio.name && radio.name.includes('[method]')) {
            updateAllPplButtonStates();
        }
    });

    // Also listen for clicks (for custom styled radio buttons)
    document.addEventListener('click', function(event) {
        const target = event.target;

        // Check if we clicked on a label containing a radio
        const label = target.closest('label');
        if (label) {
            const radio = label.querySelector('input[type="radio"][name*="[method]"]');
            if (radio) {
                setTimeout(updateAllPplButtonStates, 50);
            }
        }
    });

    // Initialize on page load
    updateAllPplButtonStates();
}

/**
 * Update all PPL button states based on currently selected shipping methods
 */
function updateAllPplButtonStates() {
    // Find all PPL selector containers
    const pplContainers = document.querySelectorAll('.ppl-selector-container[data-ppl-method-code]');

    pplContainers.forEach(container => {
        const methodCode = container.dataset.pplMethodCode;
        const button = container.querySelector('button[data-ppl-button]');

        if (button) {
            // Find the radio button for this method
            const radio = document.querySelector(`input[type="radio"][name*="[method]"][value="${methodCode}"]`);
            const isSelected = radio && radio.checked;

            // Show/hide container based on selection
            container.style.display = isSelected ? 'block' : 'none';
            button.disabled = !isSelected;
        }
    });
}

/**
 * Initialize PPL map when modal is shown
 */
function initPplMapOnModalShow() {
    document.querySelectorAll('.modal[data-ppl-modal-id]').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const mapContainer = modal.querySelector('.ppl-map-container');
            if (mapContainer && typeof PPL !== 'undefined') {
                // Initialize PPL map if not already done
                if (!mapContainer.dataset.initialized) {
                    const config = {
                        mode: mapContainer.dataset.mode || 'default',
                        lat: parseFloat(mapContainer.dataset.lat) || 50.0755,
                        lng: parseFloat(mapContainer.dataset.lng) || 14.4378,
                        language: mapContainer.dataset.language || 'cs',
                        country: mapContainer.dataset.country || 'cz'
                    };

                    if (mapContainer.dataset.countries) {
                        config.countries = mapContainer.dataset.countries;
                    }

                    if (mapContainer.dataset.code) {
                        config.code = mapContainer.dataset.code;
                    }

                    try {
                        PPL.createMap(mapContainer.id, config);
                        mapContainer.dataset.initialized = 'true';
                    } catch (e) {
                        console.error('Failed to initialize PPL map:', e);
                    }
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Initialize widget handlers for each PPL modal
    const modals = document.querySelectorAll('.modal[data-ppl-input-id]');

    modals.forEach(function (modal) {
        const inputId = modal.dataset.pplInputId;
        const buttonId = modal.dataset.pplButtonId;
        const modalId = modal.dataset.pplModalId;

        initPplParcelshopWidget(inputId, buttonId, modalId);
    });

    // Initialize PPL button states
    initPplButtonStates();

    // Initialize map on modal show
    initPplMapOnModalShow();
});

// Legacy functions for backwards compatibility (Sylius 1.x)
function openPplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement && typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modalElement);
        bsModal.show();
    } else if (modalElement) {
        // Fallback for non-Bootstrap
        modalElement.style.display = 'block';
        const backdrop = document.getElementById(modalId + '_backdrop');
        if (backdrop) backdrop.style.display = 'block';
    }
}

function closePplModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement && typeof bootstrap !== 'undefined') {
        const bsModal = bootstrap.Modal.getInstance(modalElement);
        if (bsModal) bsModal.hide();
    } else if (modalElement) {
        // Fallback for non-Bootstrap
        modalElement.style.display = 'none';
        const backdrop = document.getElementById(modalId + '_backdrop');
        if (backdrop) backdrop.style.display = 'none';
    }
}
