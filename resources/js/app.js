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
        prevButton.disabled = current === 0;
        nextButton.hidden = current === total - 1;
        finishButton.hidden = current !== total - 1;
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
