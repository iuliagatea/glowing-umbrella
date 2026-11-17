document.querySelectorAll('[data-slider]').forEach(initSlider);

function initSlider(root) {
    const track = root.querySelector('[data-slider-track]');
    const slides = Array.from(root.querySelectorAll('[data-slider-slide]'));
    const dots = Array.from(root.querySelectorAll('[data-slider-dot]'));
    const prevButton = root.querySelector('[data-slider-prev]');
    const nextButton = root.querySelector('[data-slider-next]');
    const autoplayDelay = Number(root.dataset.autoplay) || 0;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let index = 0;
    let timer = null;

    function goTo(newIndex) {
        index = (newIndex + slides.length) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;

        slides.forEach((slide, slideIndex) => {
            slide.setAttribute('aria-hidden', slideIndex === index ? 'false' : 'true');
        });

        dots.forEach((dot, dotIndex) => {
            dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
        });
    }

    function next() {
        goTo(index + 1);
    }

    function prev() {
        goTo(index - 1);
    }

    function startAutoplay() {
        if (autoplayDelay > 0 && !prefersReducedMotion) {
            timer = window.setInterval(next, autoplayDelay);
        }
    }

    function stopAutoplay() {
        window.clearInterval(timer);
    }

    nextButton?.addEventListener('click', () => {
        next();
        stopAutoplay();
        startAutoplay();
    });

    prevButton?.addEventListener('click', () => {
        prev();
        stopAutoplay();
        startAutoplay();
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goTo(Number(dot.dataset.sliderIndex));
            stopAutoplay();
            startAutoplay();
        });
    });

    root.addEventListener('mouseenter', stopAutoplay);
    root.addEventListener('mouseleave', startAutoplay);
    root.addEventListener('focusin', stopAutoplay);
    root.addEventListener('focusout', startAutoplay);

    startAutoplay();
}
