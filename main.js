/**
 * Lógica de Interacciones del Cliente (Vanilla JavaScript)
 * Soluciones Informática JD
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. Efecto Scroll en la Cabecera (Header)
    // ==========================================
    const header = document.getElementById('header');
    
    const handleScroll = () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };
    
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Ejecutar al cargar la página por si inicia con scroll


    // ==========================================
    // 2. Menú de Navegación Móvil (Toggle)
    // ==========================================
    const mobileToggle = document.getElementById('mobile-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu ul li a');

    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            // Cambiar el icono de barras (bars) a cerrar (times)
            const icon = mobileToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Cerrar el menú cuando se hace clic en cualquier enlace
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });
    }


    // ==========================================
    // 3. Enlaces Activos al hacer Scroll (Intersection Observer)
    // ==========================================
    const sections = document.querySelectorAll('section[id]');
    
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -60% 0px', // Activar cuando esté en la zona central de la pantalla
        threshold: 0
    };

    const observerCallback = (entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                const activeLink = document.querySelector(`.nav-menu ul li a[href="#${id}"]`);
                
                if (activeLink) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    activeLink.classList.add('active');
                }
            }
        });
    };

    const observer = new IntersectionObserver(observerCallback, observerOptions);
    sections.forEach(section => observer.observe(section));


    // ==========================================
    // 4. Envío Asíncrono del Formulario de Contacto (AJAX / Fetch)
    // ==========================================
    const contactForm = document.getElementById('contact-form');
    const contactAlert = document.getElementById('contact-alert');
    const btnSubmit = document.getElementById('btn-submit-contact');

    if (contactForm && contactAlert) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Preparar UI
            contactAlert.className = 'alert d-none';
            btnSubmit.disabled = true;
            const originalBtnText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = 'Enviando Mensaje... <i class="fa fa-spinner fa-spin"></i>';

            const formData = new FormData(contactForm);

            try {
                const response = await fetch(contactForm.getAttribute('action'), {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }

                const result = await response.json();

                if (result.success) {
                    // Mostrar mensaje de éxito
                    contactAlert.className = 'alert alert-success';
                    contactAlert.innerHTML = `<i class="fa fa-check-circle"></i> ${result.message}`;
                    contactForm.reset(); // Limpiar formulario
                } else {
                    // Mostrar mensaje de error del backend
                    contactAlert.className = 'alert alert-danger';
                    contactAlert.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${result.message}`;
                }

            } catch (error) {
                console.error('Error de contacto:', error);
                contactAlert.className = 'alert alert-danger';
                contactAlert.innerHTML = '<i class="fa fa-exclamation-circle"></i> Ocurrió un fallo en la conexión. Por favor, intente nuevamente.';
            } finally {
                // Restaurar botón
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnText;
                
                // Hacer scroll hasta la alerta
                contactAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});