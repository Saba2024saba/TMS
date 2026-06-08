(function () {
    'use strict';

    function hasBootstrapModal() {
        return window.bootstrap && typeof window.bootstrap.Modal === 'function';
    }

    function getTarget(trigger) {
        var selector = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
        if (!selector || selector.charAt(0) !== '#') {
            return null;
        }
        return document.querySelector(selector);
    }

    function ensureBackdrop() {
        var backdrop = document.querySelector('.modal-backdrop.fallback-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show fallback-backdrop';
            document.body.appendChild(backdrop);
        }
        backdrop.addEventListener('click', closeOpenModal);
    }

    function removeBackdrop() {
        document.querySelectorAll('.modal-backdrop.fallback-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });
    }

    function openModal(modal) {
        modal.style.display = 'block';
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        ensureBackdrop();

        var firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea, button');
        if (firstInput) {
            firstInput.focus();
        }
    }

    function closeModal(modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        removeBackdrop();
    }

    function closeOpenModal() {
        var modal = document.querySelector('.modal.show');
        if (modal) {
            closeModal(modal);
        }
    }

    function toggleCollapse(target) {
        target.classList.toggle('show');
    }

    document.addEventListener('click', function (event) {
        var modalTrigger = event.target.closest('[data-bs-toggle="modal"]');
        if (modalTrigger && !hasBootstrapModal()) {
            event.preventDefault();
            var modal = getTarget(modalTrigger);
            if (modal) {
                openModal(modal);
            }
            return;
        }

        var closeTrigger = event.target.closest('[data-bs-dismiss="modal"]');
        if (closeTrigger && !hasBootstrapModal()) {
            event.preventDefault();
            var modalToClose = closeTrigger.closest('.modal');
            if (modalToClose) {
                closeModal(modalToClose);
            }
            return;
        }

        var collapseTrigger = event.target.closest('[data-bs-toggle="collapse"]');
        if (collapseTrigger && !(window.bootstrap && typeof window.bootstrap.Collapse === 'function')) {
            event.preventDefault();
            var collapseTarget = getTarget(collapseTrigger);
            if (collapseTarget) {
                toggleCollapse(collapseTarget);
            }
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !hasBootstrapModal()) {
            closeOpenModal();
        }
    });
}());
