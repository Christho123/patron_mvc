const video = document.getElementById("video");

// 🔥 ESPERAR A QUE FACEAPI CARGUE
window.addEventListener("load", async () => {
    if (typeof faceapi === "undefined") {
        console.error("faceapi NO cargó");
        return;
    }

    await cargarModelos();
    await iniciarCamara();
});

// 🔥 iniciar cámara
async function iniciarCamara() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "No se pudo acceder a la cámara", "error");
    }
}

// 🔥 cargar modelos (RUTA CORRECTA)
async function cargarModelos() {
    const url = "https://justadudewhohacks.github.io/face-api.js/models";

    await faceapi.nets.tinyFaceDetector.loadFromUri(url);
    await faceapi.nets.faceLandmark68Net.loadFromUri(url);
    await faceapi.nets.faceRecognitionNet.loadFromUri(url);

    console.log("Modelos cargados");
}

// 🔥 guardar rostro
async function guardarRostro() {

    console.log("CLICK FUNCIONANDO");

    if (typeof faceapi === "undefined") {
        Swal.fire("Error", "face-api no cargó", "error");
        return;
    }

    const userId = typeof USER_ID !== "undefined" ? USER_ID : null;

    if (!userId) {
        Swal.fire("Error", "No hay usuario en sesión", "error");
        return;
    }

    try {
        const deteccion = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!deteccion) {
            Swal.fire("Error", "No se detectó rostro", "error");
            return;
        }

        console.log("Rostro detectado");

        const res = await fetch('../../index.php?route=save-face', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user_id: userId,
                face_data: Array.from(deteccion.descriptor)
            })
        });

        const data = await res.json();

        console.log(data);

        if (data.success) {
            Swal.fire("Éxito", "Rostro registrado", "success");
        } else {
            Swal.fire("Error", data.message || "Error al guardar", "error");
        }

    } catch (error) {
        console.error(error);
        Swal.fire("Error", "Fallo en el registro facial", "error");
    }
}

// --- LÓGICA DEL FRONTEND (Puede moverse al archivo .js externo) ---

    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otpHidden');

        // 1. Manejo de Inputs OTP
        inputs.forEach((input, index) => {
            // Manejar entrada de datos
            input.addEventListener('input', (e) => {
                // Solo permitir números
                e.target.value = e.target.value.replace(/[^0-9]/g, '');

                if (e.target.value.length === 1) {
                    // Pasar al siguiente input
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            // Manejar teclas especiales (Backspace, Flechas)
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value) {
                    // Si está vacío y borra, volver al anterior
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Manejar Pegado (Ctrl + V)
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                // Limpiar solo números
                const digits = text.replace(/[^0-9]/g, '').split('');

                if (digits.length > 0) {
                    inputs.forEach((inp, i) => {
                        if (digits[i]) {
                            inp.value = digits[i];
                        }
                    });
                    // Enfocar el último input lleno o el siguiente disponible
                    const focusIndex = Math.min(digits.length, inputs.length - 1);
                    inputs[focusIndex].focus();
                }
            });
        });

        // Función para preparar el envío
        window.submitOTP = function() {
            let otpValue = '';
            let isValid = true;

            inputs.forEach(input => {
                otpValue += input.value;
                if(input.value === '') isValid = false;
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Código Incompleto',
                    text: 'Por favor ingresa los 6 dígitos.',
                    confirmButtonColor: '#10b981'
                });
                return false;
            }

            hiddenInput.value = otpValue;
            return true; // Permite el envío del formulario
        };
    });
    