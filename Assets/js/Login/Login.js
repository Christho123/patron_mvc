   const video = document.getElementById("video");

    // INICIAR CÁMARA
    async function iniciarCamara() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (error) {
            alert("No se pudo acceder a la cámara");
        }
    }

    // CARGAR MODELOS
    async function cargarModelos() {
        const url = "https://justadudewhohacks.github.io/face-api.js/models";
        await faceapi.nets.tinyFaceDetector.loadFromUri(url);
        await faceapi.nets.faceLandmark68Net.loadFromUri(url);
        await faceapi.nets.faceRecognitionNet.loadFromUri(url);
    }

    // CAPTURAR ROSTRO
    async function capturarRostro() {
        const deteccion = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!deteccion) {
            alert("No se detectó rostro");
            return null;
        }

        return deteccion.descriptor;
    }

    // LOGIN CON ROSTRO
    async function loginFace() {
        const descriptor = await capturarRostro();

        if (!descriptor) return;

        fetch('/patron_mvc/index.php?route=face-login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ face_data: Array.from(descriptor) })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast("Acceso con reconocimiento facial");
                setTimeout(() => {
                    window.location = "/patron_mvc/index.php?route=dashboard";
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No reconocido',
                    text: 'Intenta nuevamente'
                });
            }
        });
    }

    // 🔥 TOAST
    function showToast(msg){
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: msg,
            showConfirmButton: false,
            timer: 3000
        });
    }

    // INICIALIZAR TODO
    window.addEventListener("load", async () => {

    if (typeof faceapi === "undefined") {
        console.error("faceapi no cargó");
        return;
    }

    await cargarModelos();
    await iniciarCamara();

    console.log("TODO LISTO 🔥");
});