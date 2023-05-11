const input = document.getElementById('file-input');
const image = document.getElementById('img-preview');

input.addEventListener('change', (e) => {
    if (e.target.files.length) {
        const src = URL.createObjectURL(e.target.files[0]);
        image.src = src;
    }
});

function val() {
    d = document.getElementById("select-status").value;
    if(d == 0) {
        document.getElementById('div-left-day').style.display = 'block';
    } else {
        document.getElementById('div-left-day').style.display = 'none';
        document.getElementById('inp-left-day').value = '';
    }
}
    const url = document.getElementById("url-face-api").textContent;
    const urlModel = url + "/models";
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
    faceapi.nets.faceExpressionNet.loadFromUri(urlModel)
]).then(
    console.log('Module is loaded')
)
var modelsIsLoaded = false;
// function loadModel() {
//     const url = document.getElementById("url-face-api").textContent;
//     const urlModel = url + "/models";
//     Promise.all([
//         faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
//         faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
//         faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
//         faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
//         faceapi.nets.faceExpressionNet.loadFromUri(urlModel)
//     ])
//     modelsIsLoaded = true;
// }


const ipnFileElement = document.getElementById('inp-face')
const resultElement = document.getElementById('div-face-upload')
// const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']

ipnFileElement.addEventListener('change', async function(e) {
    // if(!modelsIsLoaded) {
    //     loadModel()
    // }
    const files = e.target.files
    resultElement.innerHTML = ''
    let image;

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        const fileType = file['type']
        // nhan dien
        if (image) image.remove();
        image = await faceapi.bufferToImage(file);

        const detections = await faceapi.detectAllFaces(image).withFaceLandmarks().withFaceDescriptors();
        console.log(detections);

        if(detections.length == 0) {
            // thong bao anh khong co khuon mat
            removeFileFromFileList(i);
            continue;
        }

        const fileReader = new FileReader()
        fileReader.readAsDataURL(file)

        fileReader.onload = function() {
            const url = fileReader.result
            resultElement.insertAdjacentHTML(
                'beforeend',
                `<img src="${url}" alt="${file.name}" class="rounded img-preview" />`
            )}
        }
})

function removeFileFromFileList(index) {
    const dt = new DataTransfer()
    const input = document.getElementById('inp-face')
    const { files } = input

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        if (index !== i) dt.items.add(file) // here you exclude the file. thus removing it.
    }

    input.files = dt.files // Assign the updates list
}
