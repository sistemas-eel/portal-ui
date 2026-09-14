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

    var activeModalTrigger = null;

    function openModal(modal, trigger) {
        if (!modal || modal.hasAttribute('data-portal-modal-wire')) return;

        activeModalTrigger = trigger || document.activeElement;
        modal.classList.remove('is-hidden');
        modal.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(function () {
            var focusTarget = modal.querySelector('[data-autofocus], input:not([type="hidden"]), select, textarea, button');
            if (focusTarget && typeof focusTarget.focus === 'function') focusTarget.focus();
        });
    }

    function closeModal(modal) {
        if (!modal || modal.hasAttribute('data-portal-modal-wire')) return;

        modal.classList.add('is-hidden');
        modal.setAttribute('aria-hidden', 'true');

        if (activeModalTrigger && typeof activeModalTrigger.focus === 'function') {
            activeModalTrigger.focus();
        }
        activeModalTrigger = null;
    }

    function setPersonSelectOpen(select, open) {
        if (!select) return;

        var menu = select.querySelector('[data-portal-person-menu]');
        var toggle = select.querySelector('[data-portal-person-toggle]');
        var chevron = select.querySelector('[data-portal-person-chevron]');

        if (menu) menu.classList.toggle('hidden', !open);
        if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (chevron) chevron.classList.toggle('rotate-180', open);

        if (open) {
            document.querySelectorAll('[data-portal-person-select]').forEach(function (other) {
                if (other !== select) setPersonSelectOpen(other, false);
            });
            var search = select.querySelector('[data-portal-person-search]');
            if (search) requestAnimationFrame(function () { search.focus(); });
        }
    }

    function resetPersonSelect(select) {
        var value = select.querySelector('[data-portal-person-value]');
        var label = select.querySelector('[data-portal-person-label]');
        var search = select.querySelector('[data-portal-person-search]');
        var clear = select.querySelector('[data-portal-person-clear]');
        var toggle = select.querySelector('[data-portal-person-toggle]');
        var results = select.querySelector('[data-portal-person-results]');
        var status = select.querySelector('[data-portal-person-status]');

        if (value) {
            value.value = '';
            value.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (label) label.textContent = 'Digite o nome ou número USP...';
        if (search) search.value = '';
        if (clear) clear.classList.add('hidden');
        if (toggle) {
            toggle.classList.add('text-gray-500');
            toggle.classList.remove('text-gray-900');
        }
        if (results) {
            results.replaceChildren();
            results.classList.add('hidden');
        }
        if (status) {
            status.textContent = 'Digite pelo menos 4 caracteres';
            status.classList.remove('hidden');
        }
    }

    function selectPerson(select, id, text) {
        var value = select.querySelector('[data-portal-person-value]');
        var label = select.querySelector('[data-portal-person-label]');
        var clear = select.querySelector('[data-portal-person-clear]');
        var toggle = select.querySelector('[data-portal-person-toggle]');

        if (value) {
            value.value = id;
            value.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (label) label.textContent = text;
        if (clear) clear.classList.remove('hidden');
        if (toggle) {
            toggle.classList.remove('text-gray-500');
            toggle.classList.add('text-gray-900');
        }
        setPersonSelectOpen(select, false);
    }

    function renderPersonResults(select, items) {
        var results = select.querySelector('[data-portal-person-results]');
        var status = select.querySelector('[data-portal-person-status]');
        var template = select.querySelector('[data-portal-person-result-template]');

        if (!results || !status || !template) return;

        results.replaceChildren();

        if (!items.length) {
            status.textContent = 'Nenhuma pessoa encontrada.';
            status.classList.remove('hidden');
            results.classList.add('hidden');
            return;
        }

        items.forEach(function (item) {
            var fragment = template.content.cloneNode(true);
            var button = fragment.querySelector('button');
            var id = String(item.id || '');
            var text = String(item.text || id);
            var name = text.replace(id, '').trim() || text;

            button.setAttribute('data-portal-person-result', id);
            button.setAttribute('data-portal-person-result-text', text);
            fragment.querySelector('[data-portal-person-result-id]').textContent = id;
            fragment.querySelector('[data-portal-person-result-name]').textContent = name;
            results.appendChild(fragment);
        });

        status.classList.add('hidden');
        results.classList.remove('hidden');
    }

    function searchPeople(select, term) {
        var status = select.querySelector('[data-portal-person-status]');
        var results = select.querySelector('[data-portal-person-results]');

        clearTimeout(select.__portalPersonSearchTimer);
        if (select.__portalPersonAbortController) select.__portalPersonAbortController.abort();

        if (term.length < 4) {
            if (results) {
                results.replaceChildren();
                results.classList.add('hidden');
            }
            if (status) {
                status.textContent = 'Digite pelo menos 4 caracteres';
                status.classList.remove('hidden');
            }
            return;
        }

        if (status) {
            status.textContent = 'Buscando...';
            status.classList.remove('hidden');
        }
        if (results) results.classList.add('hidden');

        select.__portalPersonSearchTimer = setTimeout(function () {
            var controller = new AbortController();
            var searchUrl = select.getAttribute('data-search-url');
            select.__portalPersonAbortController = controller;

            fetch(searchUrl + '?term=' + encodeURIComponent(term), {
                headers: { Accept: 'application/json' },
                signal: controller.signal
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Falha ao buscar pessoas.');
                    return response.json();
                })
                .then(function (data) {
                    renderPersonResults(select, Array.isArray(data.results) ? data.results : []);
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') return;
                    if (status) {
                        status.textContent = 'Erro ao buscar pessoas.';
                        status.classList.remove('hidden');
                    }
                });
        }, 500);
    }

    function openLocalUserEditor(button) {
        var modal = document.querySelector('[data-portal-local-user-modal]');
        if (!modal) return;

        var form = modal.querySelector('[data-portal-local-user-form]');
        var name = modal.querySelector('[data-portal-local-user-name]');
        var email = modal.querySelector('[data-portal-local-user-email]');
        var toggle = modal.querySelector('[data-portal-local-user-password-toggle]');

        openModal(modal, button);

        fetch(button.getAttribute('data-url'), { headers: { Accept: 'application/json' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Falha ao carregar usuário.');
                return response.json();
            })
            .then(function (user) {
                form.action = button.getAttribute('data-action');
                name.value = user.name || '';
                email.value = user.email || '';
                toggle.checked = false;
                modal.querySelectorAll('[data-portal-local-user-password]').forEach(function (field) {
                    field.value = '';
                    field.readOnly = true;
                });
            })
            .catch(function () {
                closeModal(modal);
                window.alert('Não foi possível carregar o usuário local.');
            });
    }

    function openJsonModal(button) {
        var modal = document.querySelector('[data-portal-json-modal]');
        var content = modal ? modal.querySelector('[data-portal-json-content]') : null;
        if (!modal || !content) return;

        content.textContent = 'Carregando dados...';
        openModal(modal, button);

        fetch(button.getAttribute('data-url'), { headers: { Accept: 'text/html' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Falha ao carregar JSON.');
                return response.text();
            })
            .then(function (html) { content.innerHTML = html; })
            .catch(function () { content.textContent = 'Erro ao carregar dados.'; });
    }

    function includesNamedItem(items, name, guard) {
        return items.some(function (item) {
            return item.name === name && item.guard_name === guard;
        });
    }

    function openPermissionsModal(button) {
        var modal = document.querySelector('[data-portal-permissions-modal]');
        if (!modal) return;

        var form = modal.querySelector('[data-portal-permissions-form]');
        var loading = modal.querySelector('[data-portal-permissions-loading]');
        var error = modal.querySelector('[data-portal-permissions-error]');

        loading.classList.remove('hidden');
        error.classList.add('hidden');
        form.classList.add('hidden');
        openModal(modal, button);

        fetch(button.getAttribute('data-portal-permissions-open'), { headers: { Accept: 'application/json' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Falha ao carregar permissões.');
                return response.json();
            })
            .then(function (user) {
                var permissions = Array.isArray(user.permissions) ? user.permissions : [];
                var roles = Array.isArray(user.roles) ? user.roles : [];
                var linkTypes = Array.isArray(user.permissoesVinculo) ? user.permissoesVinculo : [];
                var linkGuard = user.vinculoNs || '';
                var env = user.env || '';

                form.action = button.getAttribute('data-portal-permissions-open');
                modal.querySelector('[data-portal-permissions-user]').textContent = user.name || 'usuário';

                modal.querySelectorAll('[data-portal-permission-name]').forEach(function (input) {
                    input.checked = includesNamedItem(
                        permissions,
                        input.getAttribute('data-portal-permission-name'),
                        input.getAttribute('data-portal-permission-guard')
                    );
                });
                modal.querySelectorAll('[data-portal-role-name]').forEach(function (input) {
                    input.checked = includesNamedItem(
                        roles,
                        input.getAttribute('data-portal-role-name'),
                        input.getAttribute('data-portal-role-guard')
                    );
                });

                var envBox = modal.querySelector('[data-portal-permissions-env]');
                var hierarchy = modal.querySelector('[data-portal-permissions-hierarchy]');
                envBox.classList.toggle('hidden', !env);
                hierarchy.classList.toggle('hidden', !!env);
                modal.querySelector('[data-portal-permissions-env-label]').textContent = env ? env + ' (env)' : '';

                var links = permissions.filter(function (permission) {
                    return permission.guard_name === linkGuard && linkTypes.indexOf(permission.name.split('.')[0]) !== -1;
                }).map(function (permission) { return permission.name; });
                modal.querySelector('[data-portal-permissions-links]').textContent = links.join(', ') || 'Nenhum';

                loading.classList.add('hidden');
                form.classList.remove('hidden');
            })
            .catch(function () {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
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

        if (element.__portalAutoDismissTimer) {
            clearTimeout(element.__portalAutoDismissTimer);
            element.__portalAutoDismissTimer = null;
        }

        var container = closest(element, '[data-portal-flash-messages]');
        element.remove();
        cleanupFlashContainer(container);
    }

    function pauseAutoDismiss(el) {
        if (!el.__portalAutoDismissTimer) return;

        var elapsed = Date.now() - el.__portalAutoDismissStartedAt;
        el.__portalAutoDismissRemaining = Math.max(
            0,
            el.__portalAutoDismissRemaining - elapsed
        );

        clearTimeout(el.__portalAutoDismissTimer);
        el.__portalAutoDismissTimer = null;

        var progressBar = el.querySelector('.portal-alert-progress');

        if (progressBar) {
            var currentWidth = window.getComputedStyle(progressBar).width;
            progressBar.style.transitionDuration = '0ms';
            progressBar.style.width = currentWidth;
            void progressBar.offsetWidth;
        }
    }

    function resumeAutoDismiss(el) {
        if (
            el.__portalAutoDismissTimer
            || el.__portalAutoDismissRemaining <= 0
        ) return;

        var remaining = el.__portalAutoDismissRemaining;
        var progressBar = el.querySelector('.portal-alert-progress');

        el.__portalAutoDismissStartedAt = Date.now();
        el.__portalAutoDismissTimer = setTimeout(function () {
            el.__portalAutoDismissRemaining = 0;

            if (document.body.contains(el)) {
                dismissElement(el);
            }
        }, remaining);

        if (progressBar) {
            progressBar.style.transitionProperty = 'width';
            progressBar.style.transitionTimingFunction = 'linear';
            void progressBar.offsetWidth;

            requestAnimationFrame(function () {
                if (!el.__portalAutoDismissTimer) return;

                progressBar.style.transitionDuration = remaining + 'ms';
                progressBar.style.width = '0%';
            });
        }
    }

    function initializeAutoDismiss(el) {
        var delay = parseInt(el.getAttribute('data-portal-auto-dismiss'), 10) || 5000;
        var signature = delay + ':' + (el.textContent || '').trim();

        if (el.__portalAutoDismissSignature === signature) return;

        if (el.__portalAutoDismissTimer) {
            clearTimeout(el.__portalAutoDismissTimer);
        }

        if (el.__portalAutoDismissPauseHandler) {
            el.removeEventListener(
                'mouseenter',
                el.__portalAutoDismissPauseHandler
            );
            el.removeEventListener(
                'mouseleave',
                el.__portalAutoDismissResumeHandler
            );
        }

        el.__portalAutoDismissSignature = signature;
        el.__portalAutoDismissRemaining = delay;

        var progressBar = el.querySelector('.portal-alert-progress');

        if (progressBar) {
            progressBar.style.transitionProperty = 'width';
            progressBar.style.transitionTimingFunction = 'linear';
            progressBar.style.transitionDuration = '0ms';
            progressBar.style.width = '100%';
            void progressBar.offsetWidth;
        }

        el.__portalAutoDismissPauseHandler = function () {
            pauseAutoDismiss(el);
        };
        el.__portalAutoDismissResumeHandler = function () {
            resumeAutoDismiss(el);
        };

        el.addEventListener(
            'mouseenter',
            el.__portalAutoDismissPauseHandler
        );
        el.addEventListener(
            'mouseleave',
            el.__portalAutoDismissResumeHandler
        );

        resumeAutoDismiss(el);
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
                openModal(targetModal, modalOpenButton);
            }
            return;
        }

        var personToggle = closest(event.target, '[data-portal-person-toggle]');
        if (personToggle) {
            var personSelect = closest(personToggle, '[data-portal-person-select]');
            setPersonSelectOpen(personSelect, personToggle.getAttribute('aria-expanded') !== 'true');
            return;
        }

        var personClear = closest(event.target, '[data-portal-person-clear]');
        if (personClear) {
            resetPersonSelect(closest(personClear, '[data-portal-person-select]'));
            return;
        }

        var personResult = closest(event.target, '[data-portal-person-result]');
        if (personResult) {
            selectPerson(
                closest(personResult, '[data-portal-person-select]'),
                personResult.getAttribute('data-portal-person-result'),
                personResult.getAttribute('data-portal-person-result-text')
            );
            return;
        }

        var localUserEdit = closest(event.target, '[data-portal-local-user-edit]');
        if (localUserEdit) {
            event.preventDefault();
            openLocalUserEditor(localUserEdit);
            return;
        }

        var jsonOpen = closest(event.target, '[data-portal-json-open]');
        if (jsonOpen) {
            openJsonModal(jsonOpen);
            return;
        }

        var permissionsOpen = closest(event.target, '[data-portal-permissions-open]');
        if (permissionsOpen) {
            openPermissionsModal(permissionsOpen);
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
            closeModal(modal);
            return;
        }

        var modalSurface = closest(event.target, '[data-portal-modal-surface]');
        if (modalSurface && event.target === modalSurface) {
            var surfaceModal = closest(modalSurface, '[data-portal-modal]');
            if (surfaceModal && !surfaceModal.hasAttribute('data-portal-modal-wire')) {
                closeModal(surfaceModal);
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

        if (!closest(event.target, '[data-portal-person-select]')) {
            document.querySelectorAll('[data-portal-person-select]').forEach(function (select) {
                setPersonSelectOpen(select, false);
            });
        }
    });

    document.addEventListener('input', function (event) {
        var search = closest(event.target, '[data-portal-person-search]');
        if (!search) return;

        searchPeople(closest(search, '[data-portal-person-select]'), search.value.trim());
    });

    document.addEventListener('change', function (event) {
        var toggle = closest(event.target, '[data-portal-local-user-password-toggle]');
        if (!toggle) return;

        var modal = closest(toggle, '[data-portal-local-user-modal]');
        modal.querySelectorAll('[data-portal-local-user-password]').forEach(function (field) {
            field.readOnly = !toggle.checked;
            if (!toggle.checked) field.value = '';
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('[data-portal-layout]').forEach(closeSidebar);
        closeDropdowns(null);

        var modal = document.querySelector('[data-portal-modal]:not(.is-hidden):not([data-portal-modal-wire])');
        if (modal) closeModal(modal);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            document.querySelectorAll('[data-portal-layout]').forEach(closeSidebar);
        }
    });
}());
