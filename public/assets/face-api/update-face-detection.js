/*
navigator.getUserMedia
navigator.getUserMedia is now deprecated and is replaced by navigator.mediaDevices.getUserMedia. To fix this bug replace all versions of navigator.getUserMedia with navigator.mediaDevices.getUserMedia

Low-end Devices Bug
The video eventListener for play fires up too early on low-end machines, before the video is fully loaded, which causes errors to pop up from the Face API and terminates the script (tested on Debian [Firefox] and Windows [Chrome, Firefox]). Replaced by playing event, which fires up when the media has enough data to start playing.
*/
const url = document.getElementById("url-face-api").textContent;
const urlImage = document.getElementById("url-image").textContent;
const urlModel = url + "/models";
var arrDetection = [];

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
    faceapi.nets.faceExpressionNet.loadFromUri(urlModel),
]).then(start);


async function start() {
    console.log('start')
    //labeledFaceDescriptors = await loadLabeledImages();
    document.getElementById('btn-start').disabled = false
}

document.getElementById('btn-start').onclick = async function () {
    document.getElementById('btn-start').disabled = true

    var row = document.getElementsByTagName('table')[0].children[1].children

    for (let index = 0; index < row.length; index++) {
        var val = row[index].children

        var image = val[2].textContent
        const img = await faceapi.fetchImage(image);
        const detections = await faceapi
            .detectSingleFace(img)
            .withFaceLandmarks()
            .withFaceDescriptor();
        if(detections == null) continue
        val[3].textContent = 'OK'

        var descrip = {'id': val[0].textContent, 'descrip': detections.descriptor.toString()}
        arrDetection.push(descrip)
    }
    console.log(arrDetection)
    document.getElementById('btn-submit').disabled = false

}

document.getElementById('btn-submit').onclick = function () {
    document.getElementById('btn-submit').disabled = true

    $.ajax({
        type: "POST",
        cache: false,
        url: window.location.href,
        data: {
            data : arrDetection
        },
        success: function (data) {
            alert(data.message)
        },
        error: function (data) {
            alertError(data.message);
        },
    });
}

