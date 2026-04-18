'use strict';

export function mainNavigation(bootstrap) {
    return {
        init: function (selector) {
            selector = selector ?? '.main-nav';
            window.addEventListener('load', () => this.setup(selector));
        },

        setup: function (selector) {

            // Handle dropdown opening (after it's visible)
            document.querySelectorAll(selector + ' .dropdown').forEach(dropdown => {
                if (this.isMobileView()) {
                    return; // Don't auto-close on mobile
                }
                dropdown.addEventListener('shown.bs.dropdown', () => {
                    // Close all other dropdowns
                    this.closeOtherDropdowns(dropdown, selector);
                });

                // Handle dropdown closing
                dropdown.addEventListener('hide.bs.dropdown', (e) => {
                    if (e.clickEvent) {
                        // Prevent close if clickEvent is set
                        e.preventDefault();
                    }
                });
            });
        },

        // Utility: check if we are in mobile mode
        isMobileView() {
            const toggler = document.querySelector('.navbar-toggler');
            return toggler && window.getComputedStyle(toggler).display !== 'none';
        },

        // Utility: close all other dropdowns except the one passed
        closeOtherDropdowns(currentDropdown, selector) {
            if (this.isMobileView()) {
                return; // Don't auto-close on mobile
            }
            document.querySelectorAll(`${selector} .dropdown-menu.show`).forEach(openMenu => {
                const openDropdown = openMenu.closest('.dropdown');
                if (openDropdown && openDropdown !== currentDropdown) {
                    const openToggle = openDropdown.querySelector('.dropdown-toggle');
                    bootstrap.Dropdown.getOrCreateInstance(openToggle).hide();
                }
            });
        }
    }
};
