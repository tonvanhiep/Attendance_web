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


const ipnFileElement = document.getElementById('inp-face')
const resultElement = document.getElementById('div-face-upload')
// const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']

ipnFileElement.addEventListener('change', async function(e) {
    var errorAlert = document.getElementById('div-alert-error')
    errorAlert.hidden=true;
    errorAlert.innerHTML='';
    document.getElementById('processing-noti').hidden = false;

    const files = e.target.files
    resultElement.innerHTML = ''
    let image;
    var errorAlert = document.getElementById('div-alert-error')
    var deleted = 0;

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        const fileType = file['type']
        // nhan dien
        if (image) image.remove();
        image = await faceapi.bufferToImage(file);

        const detections = await faceapi.detectSingleFace(image).withFaceLandmarks().withFaceDescriptor();
        if(detections == null || detections.length == 0) {
            errorAlert.hidden=false
            var paragraph = document.createElement("p");
            paragraph.textContent = "Cannot detect face in image '" + file.name + "'";
            errorAlert.append(paragraph);
            removeFileFromFileList(i - deleted);
            deleted++;
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
    document.getElementById('processing-noti').hidden = true

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
