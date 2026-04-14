async function startFace() {
    const video = document.getElementById("video");

    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
}

async function captureFace() {
    const detections = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();

    return detections.descriptor;
}
async function iniciarCamara() {
    const video = document.getElementById("video");

    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
}

async function capturarRostro() {
    const deteccion = await faceapi
        .detectSingleFace(video)
        .withFaceLandmarks()
        .withFaceDescriptor();

    return deteccion.descriptor;
}