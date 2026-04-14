// ===============================
// 📊 CHART SIMPLE (si lo sigues usando)
// ===============================
const data = window.DATA_FROM_PHP || [];
const labels = data.map(d => d.action);
const values = data.map(d => d.total);

if (document.getElementById('chart')) {
    new Chart(document.getElementById('chart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values
            }]
        }
    });
}

// ===============================
// 🔔 TOAST
// ===============================
function showToast(msg) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 3000
    });
}

// ===============================
// 📊 MOCK DATA (solo si no usas backend en otras partes)
// ===============================
const mockData = {
    analytics: {
        sales: [1200, 1900, 3000, 500, 2000, 3000, 4500],
        devices: [55, 30, 15]
    },
    users: []
};

const DATA_FROM_PHP = mockData;

// ===============================
// 📊 CHARTS
// ===============================
let mainChartInstance = null;
let deviceChartInstance = null;

function initCharts() {
    const ctxMain = document.getElementById('mainChart');
    const ctxDevice = document.getElementById('deviceChart');

    if (!ctxMain || !ctxDevice) return;

    mainChartInstance = new Chart(ctxMain, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Ingresos ($)',
                data: DATA_FROM_PHP.analytics.sales,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        }
    });

    deviceChartInstance = new Chart(ctxDevice, {
        type: 'doughnut',
        data: {
            labels: ['Escritorio', 'Móvil', 'Tablet'],
            datasets: [{
                data: DATA_FROM_PHP.analytics.devices,
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b']
            }]
        }
    });
}

async function cargarUsuarios() {
    const res = await fetch('/patron_mvc/index.php?route=api-users');
    const users = await res.json();

    const tbody = document.querySelector('#usersTable tbody');
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
    cargarUsuarios();

    setInterval(cargarUsuarios, 3000); // cada 3 segundos
});