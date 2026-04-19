let mainChartInstance = null;
let usersIntervalId = null;

function initAnalyticsChart() {
    const ctx = document.getElementById('mainChart');
    if (!ctx) return;

    const trend = window.DATA_FROM_PHP || {};
    const labels = Array.isArray(trend.labels) && trend.labels.length
        ? trend.labels
        : ['D-6', 'D-5', 'D-4', 'D-3', 'D-2', 'D-1', 'Hoy'];
    const loginSeries = Array.isArray(trend.login_success) && trend.login_success.length
        ? trend.login_success
        : new Array(labels.length).fill(0);
    const registerSuccessSeries = Array.isArray(trend.register_success) && trend.register_success.length
        ? trend.register_success
        : new Array(labels.length).fill(0);
    const registerFailedSeries = Array.isArray(trend.register_failed) && trend.register_failed.length
        ? trend.register_failed
        : new Array(labels.length).fill(0);

    if (mainChartInstance) {
        mainChartInstance.destroy();
    }

    mainChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Inicios de sesion',
                    data: loginSeries,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.12)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                },
                {
                    label: 'Registros exitosos',
                    data: registerSuccessSeries,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.12)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                },
                {
                    label: 'Registros no exitosos',
                    data: registerFailedSeries,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.12)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}

async function cargarUsuarios() {
    const tbody = document.querySelector('#usersTable tbody');
    if (!tbody) return;

    const res = await fetch('/patron_mvc/index.php?route=api-users');
    const users = await res.json();
    tbody.innerHTML = '';

    const ahora = Date.now();

    users.forEach(u => {

        let estado = '';
        let color = '';

        // ⏱ convertir expiración a timestamp
        const expira = u.otp_expiracion ? new Date(u.otp_expiracion).getTime() : null;

        const expirado = expira && expira < ahora;

        if (u.verified == 1) {
            estado = 'Activo';
            color = 'green';

        } else if (expirado) {
            estado = 'Expirado';
            color = 'red';

        } else {
            estado = 'Pendiente';
            color = 'orange';
        }

        tbody.innerHTML += `
            <tr>
                <td>${u.id}</td>
                <td>${u.usuario}</td>
                <td>${u.email}</td>
                <td style="color:${color}; font-weight:bold;">
                    ${estado}
                </td>
                <td>${u.created_at}</td>
            </tr>
        `;
    });
}

// ===============================
// 🚀 INICIALIZACIÓN GLOBAL
// ===============================
document.addEventListener('DOMContentLoaded', () => {
    initAnalyticsChart();
    const hasUsersTable = !!document.querySelector('#usersTable tbody');
    if (hasUsersTable) {
        cargarUsuarios();
        if (usersIntervalId) {
            clearInterval(usersIntervalId);
        }
        usersIntervalId = setInterval(cargarUsuarios, 3000); // solo en vista usuarios
    }
});