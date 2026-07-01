import '../css/portal-ui.css';

(function () {
    'use strict';

    function closest(element, selector) {
        return element && element.closest ? element.closest(selector) : null;
    }

    function layoutFor(element) {
        return closest(element, '[data-portal-layout]') || document.querySelector('[data-portal-layout]');
    }

    function withThemeSwitchingDisabled(callback) {
        document.documentElement.classList.add('portal-ui-switching');
        callback();
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                document.documentElement.classList.remove('portal-ui-switching');
            });
        });
    }

    function applyThemeMode(isDark) {
        const root = document.documentElement;
        const themeColor = document.querySelector('#portal-ui-color-meta');
        const targetMode = isDark ? 'dark' : 'light';

        if (root.dataset.portalUiMode === targetMode && document.body) {
            document.body.style.colorScheme = targetMode;
            return;
        }

        root.classList.toggle('dark', isDark);
        root.style.colorScheme = targetMode;
        root.dataset.portalUiMode = targetMode;

        if (document.body) {
            document.body.style.colorScheme = targetMode;
        }

        if (themeColor) {
            themeColor.setAttribute(
                'content',
                getComputedStyle(root).getPropertyValue(isDark ? '--portal-ui-primary-dark' : '--portal-ui-primary').trim()
            );
        }
    }

    function toggleDarkMode() {
        const isDark = !document.documentElement.classList.contains('dark');
        withThemeSwitchingDisabled(function () {
            applyThemeMode(isDark);
        });
        localStorage.setItem('portal-ui-dark', isDark);
    }

    function closeSidebar(layout) {
        if (layout) {
            layout.classList.remove('is-sidebar-open');
        }
    }

    function initializeLayout(layout) {
        if (window.innerWidth >= 1024) {
            closeSidebar(layout);
        }
    }

    function closeDropdowns(except) {
        document.querySelectorAll('[data-portal-dropdown-menu].is-open').forEach(function (menu) {
            if (menu !== except) {
                menu.classList.remove('is-open');
                var owner = closest(menu, '[data-portal-dropdown]');
                var trigger = owner ? owner.querySelector('[data-portal-dropdown-toggle]') : null;
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function cleanupFlashContainer(container) {
        if (!container) return;

        var remainingAlerts = container.querySelectorAll('[data-portal-dismissible], [role="alert"]');
        if (remainingAlerts.length === 0) {
            container.remove();
        }
    }

    function dismissElement(element) {
        if (!element || !element.parentNode) return;

        var container = closest(element, '[data-portal-flash-messages]');
        element.remove();
        cleanupFlashContainer(container);
    }

    function initializeAutoDismiss(el) {
        var delay = parseInt(el.getAttribute('data-portal-auto-dismiss'), 10) || 5000;
        var signature = delay + ':' + (el.textContent || '').trim();

        if (el.__portalAutoDismissSignature === signature) return;

        if (el.__portalAutoDismissTimer) {
            clearTimeout(el.__portalAutoDismissTimer);
        }

        el.__portalAutoDismissSignature = signature;
        var progressBar = el.querySelector('.portal-alert-progress');

        if (progressBar) {
            progressBar.style.transitionProperty = 'width';
            progressBar.style.transitionTimingFunction = 'linear';
            progressBar.style.transitionDuration = '0ms';
            progressBar.style.width = '100%';
            void progressBar.offsetWidth;
            requestAnimationFrame(function () {
                progressBar.style.transitionDuration = delay + 'ms';
                progressBar.style.width = '0%';
            });
        }

        el.__portalAutoDismissTimer = setTimeout(function () {
            if (document.body.contains(el)) {
                dismissElement(el);
            }
            el.__portalAutoDismissTimer = null;
        }, delay);
    }

    function initializeAutoDismissInRoot(root) {
        var scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll('[data-portal-auto-dismiss]').forEach(initializeAutoDismiss);
    }

    function observeAutoDismiss() {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.hasAttribute && node.hasAttribute('data-portal-auto-dismiss')) {
                        initializeAutoDismiss(node);
                    }
                    if (node.querySelectorAll) {
                        node.querySelectorAll('[data-portal-auto-dismiss]').forEach(initializeAutoDismiss);
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function registerLivewireAutoDismissHooks() {
        if (!window.Livewire || window.__portalLivewireAutoDismissHooksRegistered) return;

        window.__portalLivewireAutoDismissHooksRegistered = true;

        ['morph.added', 'morphed', 'message.processed'].forEach(function (hook) {
            if (typeof window.Livewire.hook !== 'function') return;

            window.Livewire.hook(hook, function (payload) {
                initializeAutoDismissInRoot(payload && payload.el ? payload.el : document);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var prefersDark = localStorage.getItem('portal-ui-dark') === 'true' ||
            (!('portal-ui-dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

        applyThemeMode(prefersDark);

        document.querySelectorAll('[data-portal-layout]').forEach(initializeLayout);

        initializeAutoDismissInRoot();
        observeAutoDismiss();
        registerLivewireAutoDismissHooks();
    });

    document.addEventListener('livewire:init', registerLivewireAutoDismissHooks);

    document.addEventListener('click', function (event) {
        var modalOpenButton = closest(event.target, '[data-portal-modal-open]');
        if (modalOpenButton) {
            var modalId = modalOpenButton.getAttribute('data-portal-modal-open');
            var targetModal = modalId ? document.getElementById(modalId) : null;
            if (targetModal && targetModal.hasAttribute('data-portal-modal') && !targetModal.hasAttribute('data-portal-modal-wire')) {
                targetModal.classList.remove('is-hidden');
            }
            return;
        }

        var openButton = closest(event.target, '[data-portal-sidebar-open]');
        if (openButton) {
            var openLayout = layoutFor(openButton);
            if (openLayout) openLayout.classList.add('is-sidebar-open');
            return;
        }

        var closeButton = closest(event.target, '[data-portal-sidebar-close]');
        if (closeButton) {
            closeSidebar(layoutFor(closeButton));
            return;
        }

        var collapseButton = closest(event.target, '[data-portal-sidebar-collapse]');
        if (collapseButton) {
            var collapseLayout = collapseButton.closest('[data-portal-layout]');
            if (collapseLayout) {
                var collapsed = collapseLayout.classList.toggle('is-sidebar-collapsed');
                collapseButton.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                var icon = collapseButton.querySelector('[data-portal-collapse-icon]');
                if (icon) {
                    icon.classList.toggle('fa-chevron-left', !collapsed);
                    icon.classList.toggle('fa-chevron-right', collapsed);
                }
            }
            return;
        }

        var submenuButton = closest(event.target, '[data-portal-submenu-toggle]');
        if (submenuButton) {
            var submenuOwner = closest(submenuButton, '[data-portal-sidebar-item]');
            var submenu = submenuOwner ? submenuOwner.querySelector('[data-portal-submenu]') : null;
            var submenuIcon = submenuButton.querySelector('[data-portal-submenu-icon]');
            if (submenu) {
                var submenuIsOpen = submenu.classList.toggle('hidden') === false;
                submenuButton.setAttribute('aria-expanded', submenuIsOpen ? 'true' : 'false');
                if (submenuIcon) submenuIcon.classList.toggle('rotate-180', submenuIsOpen);
            }
            return;
        }

        var dropdownButton = closest(event.target, '[data-portal-dropdown-toggle]');
        if (dropdownButton) {
            var dropdown = closest(dropdownButton, '[data-portal-dropdown]');
            var menu = dropdown ? dropdown.querySelector('[data-portal-dropdown-menu]') : null;
            if (menu) {
                var opening = !menu.classList.contains('is-open');
                closeDropdowns(menu);
                menu.classList.toggle('is-open', opening);
                dropdownButton.setAttribute('aria-expanded', opening ? 'true' : 'false');
            }
            return;
        }

        var dismissButton = closest(event.target, '[data-portal-dismiss]');
        if (dismissButton) {
            var dismissible = closest(dismissButton, '[data-portal-dismissible]');
            if (dismissible) dismissElement(dismissible);
            return;
        }

        var modalCloseButton = closest(event.target, '[data-portal-modal-close]');
        if (modalCloseButton) {
            var modal = closest(modalCloseButton, '[data-portal-modal]');
            if (modal && !modal.hasAttribute('data-portal-modal-wire')) modal.classList.add('is-hidden');
            return;
        }

        var modalSurface = closest(event.target, '[data-portal-modal-surface]');
        if (modalSurface && event.target === modalSurface) {
            var surfaceModal = closest(modalSurface, '[data-portal-modal]');
            if (surfaceModal && !surfaceModal.hasAttribute('data-portal-modal-wire')) {
                surfaceModal.classList.add('is-hidden');
            }
            return;
        }

        var darkModeToggle = closest(event.target, '[data-portal-dark-mode-toggle]');
        if (darkModeToggle) {
            toggleDarkMode();
            return;
        }

        if (!closest(event.target, '[data-portal-dropdown]')) {
            closeDropdowns(null);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('[data-portal-layout]').forEach(closeSidebar);
        closeDropdowns(null);

        var modal = document.querySelector('[data-portal-modal]:not(.is-hidden):not([data-portal-modal-wire])');
        if (modal) modal.classList.add('is-hidden');
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            document.querySelectorAll('[data-portal-layout]').forEach(closeSidebar);
        }
    });
}());
