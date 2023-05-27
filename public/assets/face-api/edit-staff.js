const input = document.getElementById('file-input');
const image = document.getElementById('img-preview');
const video = document.getElementById("video");
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


input.addEventListener('change', (e) => {
    if (e.target.files.length) {
        const src = URL.createObjectURL(e.target.files[0]);
        image.src = src;
    }
});

$('#myModal2').on('shown.bs.modal', function (e) {
    displayActionName('')
})

$('#myModal2').on('hide.bs.modal', function (e) {
    stopFaceDetection(null, true)
})

var btnStart = document.getElementById('btn-start')
btnStart.onclick = function () {
    btnStart.disabled = true
    btnStart.textContent = "Scanning..."
    faceScan.innerHTML = ""
    action = 0
    faceScanning()
}

var action = 0;
var arrAction = {
    0 : {
        'message' : 'Vui long giu im khuon mat',
        'success' : false
    },
    1 : {
        'message' : 'Vui long cuoi',
        'success' : false
    },
    2 : {
        'message' : 'Vui long ha mieng',
        'success' : false
    },
    3 : {
        'message' : 'Vui long xoay mat sang trai',
        'success' : false
    },
    4 : {
        'message' : 'Vui long xoay mat sang phai',
        'success' : false
    }
}
var idSetIntervalDetection = -1;
async function faceScanning() {
    navigator.getUserMedia(
        { video: {} },
        (stream) => (video.srcObject = stream),
        (err) => console.error(err)
    );

    video.currentTime = 1;
}

video.addEventListener("playing", async () => {
    console.log("Playing")

    const canvas = faceapi.createCanvasFromMedia(video);
    document.getElementById("webcam").append(canvas);

    if(stopFaceDetection(canvas) == true) {
        return
    }

    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    await faceapi
        .detectSingleFace(video)
        .withFaceLandmarks()
        .withFaceExpressions();

    idSetIntervalDetection = setInterval(
        faceDetection,
        1000,
        canvas,
        displaySize
    );
});

function stopFaceDetection(canvas = null, stop = false) {
    if(action > 4 || stop == true) {
        if (idSetIntervalDetection != -1) {
            clearInterval(idSetIntervalDetection)
            idSetIntervalDetection = -1
        };
        displayActionName('');
        video.srcObject = null
        btnStart.disabled = false
        btnStart.textContent = "Re-scan"
        if(canvas != null) {
            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height)
            var removeCanvas = document.getElementById('webcam')
            removeCanvas.removeChild(removeCanvas.children[1])
        }
        return true
    }
    return false
}

async function faceDetection(canvas, displaySize) {
    console.log("face detection")
    if(stopFaceDetection(canvas) == true) {
        return
    }
    canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

    displayActionName()

    const detections = await faceapi
        .detectSingleFace(video)
        .withFaceLandmarks()
        .withFaceExpressions();

    if (detections == null) return

    const resizedDetections = faceapi.resizeResults(
        detections,
        displaySize
    );

    faceapi.draw.drawDetections(canvas, resizedDetections);

    switch (action) {
        case 0:
            if(getExpression(detections) == "neutral" && Math.abs(rotateFaceToLeftRight(detections) - 1) <= 0.5  ) getSnapshotAndAddToFileList()
            break;
        case 1:
            if(getExpression(detections) == "happy") getSnapshotAndAddToFileList()
            break;
        case 2:
            if(getExpression(detections) == "surprised") getSnapshotAndAddToFileList()
            break;
        case 3:
            if(rotateFaceToLeftRight(detections) <= 1/4) getSnapshotAndAddToFileList()
            break;
        case 4:
            if(rotateFaceToLeftRight(detections) >= 4) getSnapshotAndAddToFileList()
            break;
        default:
            return
    }

}

function getExpression(detections) {
    let result_expression = [];
    if (detections.expressions) {
        let arr = Object.values(detections.expressions);
        let max = Math.max(...arr);
        let index = arr.indexOf(max);
        let key = Object.keys(detections.expressions);
        result_expression.push(key[index], max);
        // console.log(result_expression);
        return result_expression[0]
    }
    return "";
}

function rotateFaceToLeftRight(detections) {
    const pointNose = detections.landmarks.positions[30];
    const leftPoint = detections.landmarks.positions[2];
    const rightPoint = detections.landmarks.positions[14];

    const distanceLeft = Math.abs(pointNose.x - leftPoint.x);
    const distanceRight = Math.abs(pointNose.x - rightPoint.x);

    return distanceLeft / distanceRight
}

const actionName = document.getElementById('action-name');
function displayActionName(message = null) {
    actionName.textContent = message != null ? message : arrAction[action].message
}


const ipnFileElement = document.getElementById('inp-face')
const faceUpload = document.getElementById('div-face-upload')
const faceScan = document.getElementById('div-face-scan')


function getSnapshotAndAddToFileList() {
    displayActionName('OK');

    let canvas = document.createElement("canvas");
    let image = "";
    canvas.width = video.width;
    canvas.height = video.height;

    let ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    image = canvas.toDataURL("image/jpeg");

    addFileFromFileList(dataURLtoFile(image, arrAction[action].message + '.jpeg'))

    faceScan.insertAdjacentHTML(
        'beforeend',
        `<img src="${image}" alt="" class="rounded img-preview" />`
    )
    action++;
}

function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[arr.length - 1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type:mime});
}

ipnFileElement.addEventListener('change', async function(e) {
    var errorAlert = document.getElementById('div-alert-error')
    errorAlert.hidden=true;
    errorAlert.innerHTML='';
    document.getElementById('processing-noti').hidden = false;

    const files = e.target.files
    faceUpload.innerHTML = ''
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
            faceUpload.insertAdjacentHTML(
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

function addFileFromFileList(item) {
    const dt = new DataTransfer()
    const input = document.getElementById('inp-face')
    const { files } = input

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        dt.items.add(file) // here you exclude the file. thus removing it.
    }

    dt.items.add(item)

    input.files = dt.files // Assign the updates list
}
