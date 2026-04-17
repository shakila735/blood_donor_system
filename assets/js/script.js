/* ===================================
   BLOODNET - BLOOD DONATION PLATFORM
   Interactive JavaScript Features
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== SCROLL TO TOP FUNCTIONALITY =====
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    // Show/hide scroll to top button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    });
    
    // Scroll to top functionality
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // ===== NAVBAR SCROLL EFFECT =====
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.style.padding = '0.5rem 0';
            navbar.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        } else {
            navbar.style.padding = '1rem 0';
            navbar.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
        }
    });
    
    // ===== SMOOTH SCROLLING FOR ANCHOR LINKS =====
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const navbarHeight = navbar.offsetHeight;
                const targetPosition = targetSection.offsetTop - navbarHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(navLink => {
                    navLink.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });
    
    // ===== ACTIVE NAVIGATION LINK BASED ON SCROLL POSITION =====
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    function updateActiveNavLink() {
        const scrollPosition = window.scrollY + navbar.offsetHeight + 100;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveNavLink);
    
    // ===== ANIMATION ON SCROLL (FADE IN UP) =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Add animation classes to elements
    const animatedElements = document.querySelectorAll('.feature-card, .stat-card, .blood-group-card, .step-card');
    
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    // ===== STAT COUNTER ANIMATION =====
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start) + '+';
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target + '+';
            }
        }
        
        updateCounter();
    }
    
    // Trigger counter animation when stats section is visible
    const statsNumbers = document.querySelectorAll('.stat-number');
    const statsSection = document.querySelector('.stats-section');
    
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numbers = entry.target.querySelectorAll('.stat-number');
                numbers.forEach((number, index) => {
                    setTimeout(() => {
                        const target = parseInt(number.textContent);
                        animateCounter(number, target);
                    }, index * 200);
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // ===== BLOOD TYPE CARD INTERACTIONS =====
    const bloodGroupCards = document.querySelectorAll('.blood-group-card');
    
    bloodGroupCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all cards
            bloodGroupCards.forEach(c => c.classList.remove('active'));
            // Add active class to clicked card
            this.classList.add('active');
            
            // You could show more info about this blood type here
            const bloodType = this.querySelector('.blood-type-badge span').textContent;
            console.log(`Selected blood type: ${bloodType}`);
        });
    });
    
    // ===== FEATURE CARD HOVER EFFECTS =====
    const featureCards = document.querySelectorAll('.feature-card');
    
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.icon-circle').style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.icon-circle').style.transform = 'scale(1) rotate(0deg)';
        });
    });
    
    // ===== MOBILE MENU ENHANCEMENTS =====
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    // Close mobile menu when clicking on a link
    const mobileNavLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                navbarCollapse.classList.remove('show');
                navbarToggler.classList.remove('active');
            }
        });
    });
    
    // ===== FORM VALIDATION (for future forms) =====
    function validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    }
    
    // ===== LOADING STATES FOR BUTTONS =====
    function setButtonLoading(button, loading = true) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText;
        }
    }
    
    // ===== TOAST NOTIFICATIONS =====
    function showToast(message, type = 'info', duration = 3000) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.innerHTML = toastHtml;
        
        document.body.appendChild(toastContainer);
        
        const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'), {
            autohide: true,
            delay: duration
        });
        
        toast.show();
        
        toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
            toastContainer.remove();
        });
    }
    
    // ===== SEARCH FUNCTIONALITY =====
    function initializeSearch() {
        const searchInput = document.querySelector('#searchInput');
        const searchResults = document.querySelector('#searchResults');
        
        if (searchInput && searchResults) {
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    // Simulate search - replace with actual search logic
                    performSearch(query);
                }, 300);
            });
        }
    }
    
    function performSearch(query) {
        // This is a placeholder - implement actual search logic
        console.log('Searching for:', query);
        // You would typically make an AJAX call here
    }
    
    // ===== EMERGENCY CONTACT MODAL =====
    function initializeEmergencyModal() {
        const emergencyBtn = document.querySelector('#emergencyBtn');
        
        if (emergencyBtn) {
            emergencyBtn.addEventListener('click', function() {
                // Show emergency contact information
                showToast('Emergency Hotline: 1-800-BLOOD-HELP', 'info', 5000);
            });
        }
    }
    
    // ===== BLOOD REQUEST FORM HANDLING =====
    function initializeBloodRequestForm() {
        const requestForm = document.querySelector('#bloodRequestForm');
        
        if (requestForm) {
            requestForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateForm(this)) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    setButtonLoading(submitBtn, true);
                    
                    // Simulate form submission
                    setTimeout(() => {
                        setButtonLoading(submitBtn, false);
                        showToast('Blood request submitted successfully!', 'success');
                        this.reset();
                    }, 2000);
                }
            });
        }
    }
    
    // ===== DONOR REGISTRATION FORM HANDLING =====
    function initializeDonorRegistrationForm() {
        const donorForm = document.querySelector('#donorRegistrationForm');
        
        if (donorForm) {
            donorForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateForm(this)) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    setButtonLoading(submitBtn, true);
                    
                    // Simulate form submission
                    setTimeout(() => {
                        setButtonLoading(submitBtn, false);
                        showToast('Registration successful! Welcome to BloodNet!', 'success');
                        this.reset();
                    }, 2000);
                }
            });
        }
    }
    
    // ===== DASHBOARD SPECIFIC FUNCTIONALITY =====
    function initializeDashboard() {
        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const dashboardSidebar = document.getElementById('dashboardSidebar');
        
        if (sidebarToggle && dashboardSidebar) {
            sidebarToggle.addEventListener('click', function() {
                dashboardSidebar.classList.toggle('show');
            });
        }
        
        // Sidebar collapse/expand functionality
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const dashboardMain = document.getElementById('dashboardMain');
        
        if (sidebarCollapseBtn && dashboardSidebar && dashboardMain) {
            sidebarCollapseBtn.addEventListener('click', function() {
                dashboardSidebar.classList.toggle('collapsed');
                dashboardMain.classList.toggle('expanded');
                
                // Update icon
                const icon = this.querySelector('i');
                if (dashboardSidebar.classList.contains('collapsed')) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                } else {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                }
            });
        }
        
        // Dashboard fade-in-up animations
        const dashboardObserverOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const dashboardObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    dashboardObserver.unobserve(entry.target);
                }
            });
        }, dashboardObserverOptions);
        
        // Observe all dashboard fade-in-up elements
        document.querySelectorAll('.fade-in-up').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            dashboardObserver.observe(element);
        });
        
        // Dashboard stats counter animation
        const dashboardStatNumbers = document.querySelectorAll('.stat-number');
        
        if (dashboardStatNumbers.length > 0) {
            const dashboardStatsObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.textContent);
                        animateDashboardCounter(entry.target, target);
                        dashboardStatsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            dashboardStatNumbers.forEach(number => {
                dashboardStatsObserver.observe(number);
            });
        }
        
        // Notification badge animation
        const notificationBadges = document.querySelectorAll('.notification-badge');
        notificationBadges.forEach(badge => {
            badge.style.animation = 'pulse 2s infinite';
        });
        
        // Action card hover effects
        const actionCards = document.querySelectorAll('.action-card');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Profile card interactions
        const profileCard = document.querySelector('.profile-card');
        if (profileCard) {
            profileCard.addEventListener('mouseenter', function() {
                this.querySelector('.profile-avatar').style.transform = 'scale(1.1) rotate(5deg)';
            });
            
            profileCard.addEventListener('mouseleave', function() {
                this.querySelector('.profile-avatar').style.transform = 'scale(1) rotate(0deg)';
            });
        }
    }
    
    // Dashboard specific counter animation
    function animateDashboardCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }
        
        updateCounter();
    }
    
    // ===== INITIALIZE ALL FEATURES =====
    function initializeAll() {
        initializeSearch();
        initializeEmergencyModal();
        initializeBloodRequestForm();
        initializeDonorRegistrationForm();
        initializeDashboard();
        
        // Add some entrance animations
        setTimeout(() => {
            const heroSection = document.querySelector('.hero-section');
            if (heroSection) {
                heroSection.querySelector('h1').style.animation = 'fadeInUp 1s ease';
                heroSection.querySelector('p').style.animation = 'fadeInUp 1s ease 0.3s both';
                heroSection.querySelector('.hero-buttons').style.animation = 'fadeInUp 1s ease 0.6s both';
            }
        }, 100);
    }
    
    // Call initialization
    initializeAll();
    
    // ===== KEYBOARD NAVIGATION =====
    document.addEventListener('keydown', function(e) {
        // ESC key to close modals/menus
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.hide();
            });
            
            // Close mobile menu if open
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                if (navbarToggler) navbarToggler.classList.remove('active');
            }
            
            // Close sidebar if open on mobile
            const dashboardSidebar = document.getElementById('dashboardSidebar');
            if (dashboardSidebar && dashboardSidebar.classList.contains('show')) {
                dashboardSidebar.classList.remove('show');
            }
        }
        
        // Ctrl+K for search (if implemented)
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('#searchInput');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
    
    // ===== PERFORMANCE OPTIMIZATION =====
    // Debounce scroll events
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(function() {
            updateActiveNavLink();
        });
    });
    
    // Lazy load images (if any)
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('BloodNet application initialized successfully!');
});
