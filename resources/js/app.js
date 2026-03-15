import './bootstrap';

const sliderRoot = document.querySelector('[data-slider]');
if (sliderRoot) {
    const slides = sliderRoot.querySelector('.slides');
    const nextButton = sliderRoot.querySelector('[data-next]');
    const prevButton = sliderRoot.querySelector('[data-prev]');
    const dots = Array.from(sliderRoot.querySelectorAll('[data-dot]'));
    const finishButton = sliderRoot.querySelector('[data-finish]');
    const total = Number(sliderRoot.getAttribute('data-total') || '3');
    let current = 0;

    const render = () => {
        slides.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((dot, index) => dot.classList.toggle('active', index === current));
        if (prevButton) {
            prevButton.disabled = current === 0;
        }
        if (nextButton) {
            nextButton.hidden = current === total - 1;
        }
        if (finishButton) {
            finishButton.hidden = current !== total - 1;
        }
    };

    nextButton?.addEventListener('click', () => {
        current = Math.min(total - 1, current + 1);
        render();
    });

    prevButton?.addEventListener('click', () => {
        current = Math.max(0, current - 1);
        render();
    });

    let startX = 0;
    let deltaX = 0;

    slides.addEventListener('touchstart', (event) => {
        startX = event.touches[0].clientX;
    }, { passive: true });

    slides.addEventListener('touchmove', (event) => {
        deltaX = event.touches[0].clientX - startX;
    }, { passive: true });

    slides.addEventListener('touchend', () => {
        if (Math.abs(deltaX) > 60) {
            if (deltaX < 0) {
                current = Math.min(total - 1, current + 1);
            } else {
                current = Math.max(0, current - 1);
            }
        }
        deltaX = 0;
        render();
    });

    render();
}

const confirmModal = document.querySelector('[data-confirm-modal]');
if (confirmModal) {
    const messageNode = confirmModal.querySelector('[data-confirm-message]');
    const cancelButton = confirmModal.querySelector('[data-confirm-cancel]');
    const acceptButton = confirmModal.querySelector('[data-confirm-accept]');
    const forms = Array.from(document.querySelectorAll('form[data-confirm]'));
    let pendingForm = null;

    const closeModal = () => {
        confirmModal.hidden = true;
        pendingForm = null;
    };

    const openModal = (form) => {
        pendingForm = form;
        const customMessage = form.getAttribute('data-confirm-message') || '¿Deseas continuar con esta accion?';
        if (messageNode) {
            messageNode.textContent = customMessage;
        }
        confirmModal.hidden = false;
    };

    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === '1') {
                form.dataset.confirmed = '0';
                return;
            }

            event.preventDefault();
            openModal(form);
        });
    });

    acceptButton?.addEventListener('click', () => {
        if (!pendingForm) {
            return;
        }

        pendingForm.dataset.confirmed = '1';
        pendingForm.requestSubmit();
        closeModal();
    });

    cancelButton?.addEventListener('click', closeModal);

    confirmModal.addEventListener('click', (event) => {
        if (event.target === confirmModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !confirmModal.hidden) {
            closeModal();
        }
    });
}

const parseAlerts = () => {
    const raw = document.body?.getAttribute('data-limit-alerts');
    if (!raw) {
        return [];
    }

    try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const notificationStatus = document.querySelector('[data-notification-status]');
const enableNotificationsButton = document.querySelector('[data-enable-notifications]');
const limitAlerts = parseAlerts();

const setNotificationStatus = (message) => {
    if (notificationStatus) {
        notificationStatus.textContent = message;
    }
};

const getPermissionLabel = () => {
    if (!('Notification' in window)) {
        return 'Este navegador no soporta notificaciones.';
    }

    if (Notification.permission === 'granted') {
        return 'Notificaciones activas.';
    }

    if (Notification.permission === 'denied') {
        return 'Notificaciones bloqueadas por el navegador.';
    }

    return 'Notificaciones desactivadas. Activalas para recibir alertas.';
};

const notifyExceededLimits = () => {
    if (!('Notification' in window) || Notification.permission !== 'granted' || limitAlerts.length === 0) {
        return;
    }

    const userId = document.body?.getAttribute('data-auth-user') || 'guest';

    limitAlerts.forEach((alert) => {
        const key = `haxx-alert-${userId}-${alert.key || 'limit'}`;
        if (localStorage.getItem(key) === '1') {
            return;
        }

        new Notification(alert.title || 'HAXX COORP', {
            body: alert.body || 'Superaste uno de tus limites financieros.',
            icon: '/images/icono-haxx.png',
            badge: '/images/icono-haxx.png',
        });

        localStorage.setItem(key, '1');
    });
};

if (enableNotificationsButton) {
    setNotificationStatus(getPermissionLabel());

    enableNotificationsButton.addEventListener('click', async () => {
        if (!('Notification' in window)) {
            setNotificationStatus('Este navegador no soporta notificaciones.');
            return;
        }

        const permission = await Notification.requestPermission();
        setNotificationStatus(getPermissionLabel());

        if (permission === 'granted') {
            notifyExceededLimits();
        }
    });
}

if (limitAlerts.length > 0 && 'Notification' in window && Notification.permission === 'granted') {
    notifyExceededLimits();
}

document.addEventListener('DOMContentLoaded', () => {
    const openButtons = [
        document.getElementById('openQuickTxDesktop'),
        document.getElementById('openQuickTxMobile'),
    ].filter(Boolean);
    const closeButton = document.getElementById('closeQuickTx');
    const modal = document.getElementById('quickTxModal');
    const form = document.getElementById('quickTxForm');
    const submitButton = document.getElementById('quickSubmitBtn');
    const firstInput = document.getElementById('quick_amount');

    if (!openButtons.length || !closeButton || !modal) {
        return;
    }

    const openModal = () => {
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        setTimeout(() => firstInput?.focus(), 20);
    };

    const closeModal = () => {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    };

    openButtons.forEach((button) => button.addEventListener('click', openModal));
    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    form?.addEventListener('submit', () => {
        if (!submitButton) {
            return;
        }
        submitButton.disabled = true;
        submitButton.textContent = 'Guardando...';
    });
});
