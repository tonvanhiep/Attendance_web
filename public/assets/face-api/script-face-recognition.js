const imageUpload = document.getElementById('imageUpload');
const url = document.getElementById('url-face-api').textContent
const urlImage = document.getElementById('url-image').textContent
const urlModel = url + '/models'

console.log("Loading model...");

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel)
]).then(start);

function loadLabeledImages() {
    const labels = Object.keys(faceRegination)
    return Promise.all(
        labels.map(async label => {
            const descriptions = [];
            for (let i = 0; i < faceRegination[label].length; i++) {
                var image = faceRegination[label][i]
                if (image.search('https') == -1) {
                    image = urlImage + image
                }
                const img = await faceapi.fetchImage(image);
                const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                try {
                    descriptions.push(detections.descriptor);
                    console.log(image)
                    console.log(detections.descriptor)
                } catch(err) {
                    console.log(image + ': error')
                    continue
                }

            }
            return new faceapi.LabeledFaceDescriptors(label, descriptions);
        })
    )
}

async function start() {
    const container = document.createElement('div');
    container.style.position = 'relative';
    document.body.append(container);

    console.log("Training data...")
    const labeledFaceDescriptors = await loadLabeledImages();
    console.log("Completed training.")

    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.4);

    let image;
    let canvas;

    document.body.append('Completed training.');

    imageUpload.addEventListener('change', async () => {
        console.log("Uploaded image")
        if (image) image.remove();
        if (canvas) canvas.remove();

        image = await faceapi.bufferToImage(imageUpload.files[0]);

        container.append(image);

        canvas = faceapi.createCanvasFromMedia(image);

        container.append(canvas);

        const displaySize = { width: image.width, height: image.height };
        faceapi.matchDimensions(canvas, displaySize);

        console.log("Processing...")
        const detections = await faceapi.detectAllFaces(image).withFaceLandmarks().withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

        results.forEach((result, i) => {
            const box = resizedDetections[i].detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
            console.log(result._label);
            console.log(result._distance);
            drawBox.draw(canvas);
        })
        console.log("Finish")
    });
}
