import './bootstrap';
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.bootstrap = bootstrap;

// Modern Navigation Scroll Effects
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.modern-nav');
    const mobileCartWrapper = document.querySelector('.mobile-cart-wrapper');

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Cart button animations
    const cartButtons = document.querySelectorAll('.cart-btn');
    cartButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Navigation link animations
    const navLinks = document.querySelectorAll('.nav-link-modern');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Mobile menu toggle animation
    const navbarToggler = document.querySelector('.modern-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('.toggler-icon');
            if (icon) {
                const spans = icon.querySelectorAll('span');
                if (this.classList.contains('active')) {
                    // Transform to X
                    spans[0].style.transform = 'rotate(45deg)';
                    spans[0].style.top = '8px';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg)';
                    spans[2].style.top = '8px';
                } else {
                    // Reset to hamburger
                    spans[0].style.transform = 'rotate(0deg)';
                    spans[0].style.top = '0px';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'rotate(0deg)';
                    spans[2].style.top = '16px';
                }
            }
        });
    }

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Apply fade-in animation to cards and sections
    document.querySelectorAll('.card, section').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
});