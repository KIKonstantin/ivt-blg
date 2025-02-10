const header = document.querySelector('body > header');
const windowHeight = window.innerHeight;
function applyParallax() {
    const elements = document.querySelectorAll("[data-parallax]");

    function updateParallax() {
        elements.forEach((el) => {
            const speed = parseFloat(el.dataset.parallax) || 0.5; // Default speed
            const rect = el.getBoundingClientRect();
            const offset = rect.top - window.innerHeight / 2; // Centered effect
            el.style.transform = `translateY(${offset * -speed}px)`;
        });
    }

    lenis.on("scroll", (scroller) => {
        document.body.classList.toggle('scrolled', document.documentElement.scrollTop > windowHeight);
        updateParallax()
    });
    updateParallax(); // Initial run
}

// Init parallax
applyParallax();

// Start Lenis
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}
requestAnimationFrame(raf);
