/*
navigator.getUserMedia
navigator.getUserMedia is now deprecated and is replaced by navigator.mediaDevices.getUserMedia. To fix this bug replace all versions of navigator.getUserMedia with navigator.mediaDevices.getUserMedia

Low-end Devices Bug
The video eventListener for play fires up too early on low-end machines, before the video is fully loaded, which causes errors to pop up from the Face API and terminates the script (tested on Debian [Firefox] and Windows [Chrome, Firefox]). Replaced by playing event, which fires up when the media has enough data to start playing.
*/
import { showModal } from "./modal.js";

const video = document.getElementById("video");
const url = document.getElementById("url-face-api").textContent;
const urlImage = document.getElementById("url-image").textContent;
const urlModel = url + "/models";
let image_ = "";

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

document.getElementById("btn-inp").onclick = function () {
    document.getElementById("btn-inp").style.display = "none";
    document.getElementById("div-inp").style.display = "block";
    document.getElementById("inp-id").focus();
};

// document.getElementById('btn-shot').onclick = function()
// {
//     let canvas = document.createElement('canvas');

//     canvas.width = video.width;
//     canvas.height = video.height;

//     let ctx = canvas.getContext('2d');
//     ctx.drawImage( video, 0, 0, canvas.width, canvas.height );

//     image_ = canvas.toDataURL('image/jpeg');
//     console.log("Screenshot")
// }

function getSnapshot() {
    let canvas = document.createElement("canvas");
    let image = "";
    canvas.width = video.width;
    canvas.height = video.height;

    let ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    image = canvas.toDataURL("image/jpeg");
    console.log("Screenshot");
    return image;
}

console.log("Loading...");
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
    // faceapi.nets.faceExpressionNet.loadFromUri('/Face-Api/models')
]).then(start);

function pause() {
    startVideo();
}

function startVideo() {
    console.log("Start");
    document.getElementById("text-loading").style.display = "none";
    navigator.getUserMedia(
        { video: {} },
        (stream) => (video.srcObject = stream),
        (err) => console.error(err)
    );
}

function loadLabeledImages() {
    const labels = Object.keys(faceRegination);
    var len_labels = labels.length;
    var success = 0;
    console.log("Start traning...");
    return Promise.all(
        labels.map(async (label) => {
            const descriptions = [];

            for (let i = 0; i < faceRegination[label].length; i++) {
                var image = faceRegination[label][i];
                if (image.search("https") == -1) {
                    image = urlImage + image;
                }
                const img = await faceapi.fetchImage(image);
                const detections = await faceapi
                    .detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                try {
                    descriptions.push(detections.descriptor);
                    console.log(image);
                    console.log(detections.descriptor);
                } catch (err) {
                    console.log(image + ": error");
                    continue;
                }
            }
            console.log(++success + "/" + len_labels);
            return new faceapi.LabeledFaceDescriptors(label, descriptions);
        })
    );
}

async function faceRecognition(faceMatcher, canvas, displaySize) {
    i = i + 1;
    console.log(i);
    const detections = await faceapi
        .detectAllFaces(video)
        .withFaceLandmarks()
        .withFaceDescriptors();
    const resizedDetections = faceapi.resizeResults(
        detections,
        displaySize
    );

    canvas
        .getContext("2d")
        .clearRect(0, 0, canvas.width, canvas.height);

    const results = resizedDetections.map((d) =>
        faceMatcher.findBestMatch(d.descriptor)
    );

    results.forEach((result, i) => {
        const box = resizedDetections[i].detection.box;
        const drawBox = new faceapi.draw.DrawBox(box, {
            label: result.toString(),
        });
        console.log(result._label);
        console.log(result._distance);
        drawBox.draw(canvas);
        if (result._label != "unknown") {
            if(RecognitionIntervalID != -1) clearInterval(RecognitionIntervalID);
            var image = getSnapshot();
            showModal(
                "Face Detecttion",
                "Please confirm your face?",
                "Yes",
                "No",
                () => {
                    alert("Attendance success");
                    submitForm(result._label, image, true);
                    RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
                },
                () => {
                    alert("Enter your ID");
                    RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
                },
                () => {
                    // alert("Wait for timekeeping again");
                    // RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
                }
            )
        } else {
            alertError("Không xác nhận được người dùng");
        }
    });
}


let i = 0;
var RecognitionIntervalID = -1;
async function start() {
    console.log("Training data.");

    const labeledFaceDescriptors = await loadLabeledImages();
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

    console.log("Completed training.");

    startVideo();

    video.addEventListener("playing", () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("webcam").append(canvas);

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
    });

    video.currentTime = 1;
}

$("#checkin-form").submit(function (e) {
    e.preventDefault();
    submitForm(document.getElementById("inp-id").value, getSnapshot(), false);
    document.getElementById("inp-id").value = "";
    document.getElementById("btn-inp").style.display = "block";
    document.getElementById("div-inp").style.display = "none";
});

function submitForm(id, image, identity) {
    $.ajax({
        type: "POST",
        cache: false,
        url: document.getElementById("checkin-form").action,
        data: {
            id: id,
            image: image,
            identity: identity,
        },
        success: function (data) {
            if (data.success == true) {
                alertSuccess(data.message);
            } else alertError(data.message);
        },
        error: function (data) {
            alertError(data.message);
        },
    });
}

function alertError(message) {
    document.getElementById("alert-message").style.backgroundColor = "red";
    document.getElementById("alert-message").textContent = message;
    setTimeout(function () {
        alertDisable();
    }, 1000);
}

function alertSuccess(message) {
    document.getElementById("alert-message").style.backgroundColor = "green";
    document.getElementById("alert-message").textContent = message;
    setTimeout(function () {
        alertDisable();
    }, 1000);
}

function alertDisable() {
    document.getElementById("alert-message").style.background = "none";
    document.getElementById("alert-message").textContent = "";
}


