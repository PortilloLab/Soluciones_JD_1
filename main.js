/**
 * Lógica de Interacciones e Invocación de Terminal (Vanilla JavaScript)
 * Soluciones Informática JD & PortilloLab
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. Navegación Móvil (Toggle Menu)
    // ==========================================
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    }

    // ==========================================
    // 2. Simulación Dinámica de Consola (ITAT & DS Guardian Terminal)
    // ==========================================
    const consoleBody = document.getElementById('consoleBody');

    if (consoleBody) {
        const terminalOutput = [
            '<span class="console-prompt">$</span> <span class="console-cmd">itat doctor</span>',
            '<span class="console-info">=== ITAT Health Status: 100% OK ===</span>',
            'CPU: 12% | RAM: 3.2GB / 16GB | Disk: 34% Free',
            'MySQL Service: <span class="console-success">RUNNING (Port 3306)</span>',
            'Power BI Gateway: <span class="console-success">ACTIVE (Sync OK)</span>',
            '<br>',
            '<span class="console-prompt">$</span> <span class="console-cmd">ds_guardian audit --data train.csv</span>',
            '🛡️ <span class="console-success">DS Guardian QA Audit: 0 Nulls | 0 Leakage</span>',
            '<span class="console-success">Status: Dataset 100% Ready for Production ML</span>'
        ];

        let index = 0;
        consoleBody.innerHTML = '';

        const renderNextLine = () => {
            if (index < terminalOutput.length) {
                const line = document.createElement('div');
                line.innerHTML = terminalOutput[index];
                consoleBody.appendChild(line);
                index++;
                setTimeout(renderNextLine, 600);
            }
        };

        renderNextLine();
    }

    // ==========================================
    // 3. Envío Asíncrono del Formulario de Auditoría
    // ==========================================
    const auditForm = document.getElementById('auditForm');
    const formResponse = document.getElementById('formResponse');
    const btnSubmit = document.getElementById('btnSubmitForm');

    if (auditForm) {
        auditForm.addEventListener('submit', (e) => {
            e.preventDefault();

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando Solicitud...';
            }

            setTimeout(() => {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-check"></i> ¡Solicitud Enviada con Éxito!';
                    btnSubmit.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                }

                if (formResponse) {
                    formResponse.style.display = 'block';
                    formResponse.className = 'alert alert-success';
                    formResponse.innerHTML = '✨ ¡Gracias! Hemos recibido tu solicitud. Nos pondremos en contacto contigo en breve para coordinar el diagnóstico.';
                }

                auditForm.reset();
            }, 1200);
        });
    }

});