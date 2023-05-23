const imageUpload = document.getElementById('imageUpload');
const url = document.getElementById('url-face-api').textContent
const urlImage = document.getElementById('url-image').textContent
const urlModel = url + '/models'

console.log("Loading model...");

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
    faceapi.nets.faceExpressionNet.loadFromUri(urlModel)
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
        const detections = await faceapi.detectSingleFace(image).withFaceLandmarks().withFaceDescriptor().withFaceExpressions();

        if(detections != null) {
            console.log('detections = ', detections);
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const result = faceMatcher.findBestMatch(detections.descriptor)
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
            faceapi.draw.drawDetections(canvas, resizedDetections)
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
            faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
            const box = resizedDetections.detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
            drawBox.draw(canvas);


            // tinh khoang cach
            const pointNose = detections.landmarks.positions[30]
            const leftPoint = detections.landmarks.positions[2]
            const rightPoint = detections.landmarks.positions[14]

            const distanceLeft = Math.sqrt(Math.abs(Math.pow(pointNose.x, 2) - Math.pow(leftPoint.x, 2)) + Math.abs(Math.pow(pointNose.y, 2) - Math.pow(leftPoint.y, 2)))
            const distanceRight = Math.sqrt(Math.abs(Math.pow(pointNose.x, 2) - Math.pow(rightPoint.x, 2)) + Math.abs(Math.pow(pointNose.y, 2) - Math.pow(rightPoint.y, 2)))
            console.log('distanceLeft = ', distanceLeft);
            console.log('distanceRight = ', distanceRight);
        }
        console.log("Finish")
    });
}
