/* ==========================================================================
   Soluciones Informáticas JD / PortilloLab - Interactive Scripts
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // Mobile Navbar Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu on link click
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu) navMenu.classList.remove('active');
        });
    });

    // Interactive Demo Console Command Simulation
    const consoleLines = [
        { text: "$ itat doctor", type: "cmd" },
        { text: "=== ITAT Health Status: 100% OK ===", type: "success" },
        { text: "CPU: 12% | RAM: 3.2GB / 16GB | Disk: 34% Free", type: "info" },
        { text: "MySQL Service: RUNNING (Port 3306)", type: "success" },
        { text: "Power BI Gateway: ACTIVE (Sync OK)", type: "success" },
        { text: "$ ds_guardian audit --data train.csv", type: "cmd" },
        { text: "🛡️ DS Guardian QA Audit: 0 Nulls | 0 Leakage", type: "success" }
    ];

    const consoleBody = document.getElementById('consoleBody');
    if (consoleBody) {
        let index = 0;

        function renderConsole() {
            if (index < consoleLines.length) {
                const line = document.createElement('div');
                line.className = `console-line console-${consoleLines[index].type}`;
                line.textContent = consoleLines[index].text;
                consoleBody.appendChild(line);
                index++;
                setTimeout(renderConsole, 800);
            }
        }

        setTimeout(renderConsole, 500);
    }

    // Contact Form Handler
    const contactForm = document.getElementById('auditForm');
    const formResponse = document.getElementById('formResponse');

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('formName').value;
            const email = document.getElementById('formEmail').value;
            const service = document.getElementById('formService').value;

            if (formResponse) {
                formResponse.style.display = 'block';
                formResponse.innerHTML = `
                    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 14px; border-radius: 10px; margin-top: 15px; text-align: center; font-weight: 600;">
                        🎉 ¡Gracias ${name}! Hemos recibido tu solicitud de auditoría para ${service}. Nos contactaremos a ${email} a la brevedad.
                    </div>
                `;
            }
            contactForm.reset();
        });
    }
});