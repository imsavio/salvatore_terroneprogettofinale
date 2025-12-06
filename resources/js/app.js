import './bootstrap';
import '../scss/app.scss';

import 'bootstrap';

// Contatore caratteri per il campo body
document.addEventListener('DOMContentLoaded', function() {
    const bodyTextarea = document.getElementById('body');
    const charCount = document.getElementById('body-char-count');

    if (bodyTextarea && charCount) {
        function updateCharCount() {
            const length = bodyTextarea.value.length;
            charCount.textContent = length;

            if (length < 120) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-success');
            } else {
                charCount.classList.remove('text-danger');
                charCount.classList.add('text-success');
            }
        }

        bodyTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Inizializza il conteggio
    }

    // Validazione form articolo
    const articleForm = document.getElementById('article-form');
    if (articleForm) {
        articleForm.addEventListener('submit', function(e) {
            const body = document.getElementById('body');
            if (body && body.value.length < 120) {
                e.preventDefault();
                alert('Il contenuto deve avere almeno 120 caratteri. Attualmente hai ' + body.value.length + ' caratteri.');
                body.focus();
                return false;
            }
        });
    }

    // Effetto parallax per sezioni sovrapposte - sovrapposizione molto visibile
    const overlapSections = document.querySelectorAll('.overlap-section');
    const heroSection = document.querySelector('.hero-section');
    
    function handleScroll() {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        
        overlapSections.forEach((section, index) => {
            const rect = section.getBoundingClientRect();
            const sectionTop = rect.top;
            const sectionHeight = rect.height;
            
            // Calcola quanto la sezione è visibile nel viewport
            const viewportTop = 0;
            const viewportBottom = windowHeight;
            const sectionBottom = sectionTop + sectionHeight;
            
            // Quando la sezione entra nel viewport, la facciamo scorrere sopra la precedente con effetto molto visibile
            if (sectionTop < viewportBottom && sectionBottom > viewportTop) {
                // Calcola il progresso di scroll (0 quando entra, 1 quando è completamente visibile)
                const scrollProgress = Math.max(0, Math.min(1, (viewportBottom - sectionTop) / (windowHeight + 300)));
                
                // Applica trasformazione molto evidente: quando entra, è molto più in basso e si muove verso l'alto
                const translateY = (1 - scrollProgress) * 250;
                const scale = 0.88 + (scrollProgress * 0.12);
                const opacity = 0.7 + (scrollProgress * 0.3);
                const rotateX = (1 - scrollProgress) * 5;
                
                section.style.transform = `translateY(${translateY}px) scale(${scale}) perspective(1000px) rotateX(${rotateX}deg)`;
                section.style.opacity = opacity;
                section.style.transformOrigin = 'top center';
            } else if (sectionTop >= viewportBottom) {
                // Sezione ancora sotto il viewport - molto più in basso
                section.style.transform = 'translateY(250px) scale(0.88) perspective(1000px) rotateX(5deg)';
                section.style.opacity = '0.7';
            } else {
                // Sezione completamente sopra il viewport
                section.style.transform = 'translateY(0) scale(1) perspective(1000px) rotateX(0deg)';
                section.style.opacity = '1';
            }
        });
        
        // Anima anche la hero section con effetto più evidente
        if (heroSection) {
            const heroRect = heroSection.getBoundingClientRect();
            if (heroRect.bottom > 0) {
                const heroProgress = Math.max(0, Math.min(1, (windowHeight - heroRect.bottom) / windowHeight));
                const translateY = -heroProgress * 100;
                const scale = 1 - (heroProgress * 0.05);
                const opacity = 1 - (heroProgress * 0.4);
                
                heroSection.style.transform = `translateY(${translateY}px) scale(${scale})`;
                heroSection.style.opacity = opacity;
            }
        }
    }
    
    // Throttle per performance
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                handleScroll();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Inizializza
    handleScroll();

    // Navbar moderna con scomparsa durante lo scroll
    let lastScrollTop = 0;
    let scrollTimeout;
    const navbar = document.getElementById('mainNavbar');
    let isScrolling = false;

    if (navbar) {
        function handleNavbarScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollDelta = scrollTop - lastScrollTop;

            // Aggiungi classe "scrolled" quando si scrolla
            if (scrollTop > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }

            // Nascondi la navbar quando si scrolla verso il basso
            if (scrollDelta > 5 && scrollTop > 100) {
                navbar.classList.add('navbar-hidden');
                isScrolling = true;
            }
            // Mostra la navbar quando si scrolla verso l'alto
            else if (scrollDelta < -5) {
                navbar.classList.remove('navbar-hidden');
                isScrolling = false;
            }

            lastScrollTop = scrollTop;

            // Mostra sempre la navbar quando si raggiunge la cima
            if (scrollTop <= 10) {
                navbar.classList.remove('navbar-hidden');
                isScrolling = false;
            }

            // Reset del timeout per mostrare la navbar dopo un po' di inattività
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if (isScrolling && scrollTop > 100) {
                    navbar.classList.remove('navbar-hidden');
                    isScrolling = false;
                }
            }, 1500);
        }

        // Throttle per performance
        let navbarTicking = false;
        window.addEventListener('scroll', function() {
            if (!navbarTicking) {
                window.requestAnimationFrame(function() {
                    handleNavbarScroll();
                    navbarTicking = false;
                });
                navbarTicking = true;
            }
        }, { passive: true });

        // Mostra la navbar quando si passa il mouse sopra (se nascosta)
        navbar.addEventListener('mouseenter', function() {
            if (window.pageYOffset > 100) {
                navbar.classList.remove('navbar-hidden');
            }
        });
    }
});
