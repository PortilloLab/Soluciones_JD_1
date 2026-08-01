/**
 * Lógica de Interacciones, Terminal Animada y Conexión Backend (AJAX/Fetch)
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
    // 3. Envío Asíncrono Real del Formulario vía fetch()
    // ==========================================
    const auditForm = document.getElementById('auditForm');
    const formResponse = document.getElementById('formResponse');
    const btnSubmit = document.getElementById('btnSubmitForm');

    if (auditForm) {
        auditForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const originalBtnText = btnSubmit ? btnSubmit.innerHTML : 'Enviar Solicitud';

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando Solicitud...';
            }

            if (formResponse) {
                formResponse.style.display = 'none';
                formResponse.className = '';
            }

            try {
                const formData = new FormData(auditForm);
                const response = await fetch('procesar_contacto.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="fa-solid fa-check"></i> ¡Solicitud Enviada!';
                        btnSubmit.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                        setTimeout(() => {
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.style.background = '';
                        }, 4000);
                    }

                    if (formResponse) {
                        formResponse.style.display = 'block';
                        formResponse.className = 'alert alert-success';
                        formResponse.style.marginTop = '15px';
                        formResponse.style.padding = '12px 16px';
                        formResponse.style.borderRadius = '8px';
                        formResponse.style.background = 'rgba(16, 185, 129, 0.15)';
                        formResponse.style.border = '1px solid #10b981';
                        formResponse.style.color = '#34d399';
                        formResponse.innerHTML = '✨ ' + data.message;
                    }

                    auditForm.reset();
                } else {
                    throw new Error(data.message || 'Error en el procesado del servidor.');
                }

            } catch (error) {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalBtnText;
                }

                if (formResponse) {
                    formResponse.style.display = 'block';
                    formResponse.className = 'alert alert-danger';
                    formResponse.style.marginTop = '15px';
                    formResponse.style.padding = '12px 16px';
                    formResponse.style.borderRadius = '8px';
                    formResponse.style.background = 'rgba(239, 68, 68, 0.15)';
                    formResponse.style.border = '1px solid #ef4444';
                    formResponse.style.color = '#f87171';
                    formResponse.innerHTML = '❌ ' + (error.message || 'Error de conexión. Intente nuevamente.');
                }
            }
        });
    }

});