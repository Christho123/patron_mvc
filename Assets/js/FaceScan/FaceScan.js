const video = document.getElementById("video");
const overlay = document.getElementById("overlay");
const btnVerify = document.getElementById("btnVerify");

let latestDescriptor = null;

async function loadModels() {
    const url = "https://justadudewhohacks.github.io/face-api.js/models";
    await faceapi.nets.tinyFaceDetector.loadFromUri(url);
    await faceapi.nets.faceLandmark68Net.loadFromUri(url);
    await faceapi.nets.faceRecognitionNet.loadFromUri(url);
}

function syncOverlay() {
    const width = video.videoWidth;
    const height = video.videoHeight;
    if (!width || !height) return;
    faceapi.matchDimensions(overlay, { width, height });
    overlay.style.width = `${video.clientWidth}px`;
    overlay.style.height = `${video.clientHeight}px`;
}

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    await new Promise((resolve) => {
        video.onloadedmetadata = resolve;
    });
    await video.play().catch(() => {});
    syncOverlay();
}

async function startTracking() {
    setInterval(async () => {
        syncOverlay();
        const displaySize = {
            width: video.videoWidth || 640,
            height: video.videoHeight || 480
        };

        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        latestDescriptor = detection ? Array.from(detection.descriptor) : null;
        const ctx = overlay.getContext("2d");
        ctx.clearRect(0, 0, overlay.width, overlay.height);

        if (detection) {
            const resized = faceapi.resizeResults(detection, displaySize);
            faceapi.draw.drawDetections(overlay, resized);
            faceapi.draw.drawFaceLandmarks(overlay, resized);
        }
    }, 220);
}

async function verifyFaceAndLogin() {
    if (!latestDescriptor) {
        Swal.fire("Rostro no detectado", "Acercate a la camara e intenta nuevamente.", "warning");
        return;
    }

    btnVerify.disabled = true;
    btnVerify.textContent = "Verificando...";
    try {
        const res = await fetch("/patron_mvc/index.php?route=verify-face-otp", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ face_data: latestDescriptor })
        });

        const data = await res.json();
        if (data.status === "success") {
            window.location.href = "/patron_mvc/index.php?route=dashboard";
            return;
        }

        if (data.status === "fail") {
            Swal.fire("No coincide", "El rostro no coincide con el usuario.", "error");
            return;
        }

        Swal.fire("Error", data.message || "No se pudo verificar el rostro.", "error");
    } catch (e) {
        Swal.fire("Error", "Error de red al verificar rostro.", "error");
    } finally {
        btnVerify.disabled = false;
        btnVerify.textContent = "Verificar rostro e ingresar";
    }
}

window.addEventListener("load", async () => {
    try {
        await loadModels();
        await startCamera();
        await startTracking();
    } catch (e) {
        Swal.fire("Error", "No se pudo iniciar el escaneo facial.", "error");
    }
});

window.addEventListener("resize", syncOverlay);
btnVerify.addEventListener("click", verifyFaceAndLogin);
