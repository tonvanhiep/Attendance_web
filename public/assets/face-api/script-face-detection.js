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


Echo.private('office.' + document.getElementById('id-office').innerText)
.listen('UpdateImageRecognition', async (data) => {
    console.log(data)
    if (arrName[data.employee_id] == null) arrName[data.employee_id] = data.name[data.employee_id]

    updateFaceMatcher(data.employee_id, data.image[data.employee_id]);
})

async function updateFaceMatcher(label, arrImage) {
    // console.log('old labeledFaceDescriptors = ', labeledFaceDescriptors)
    // console.log('old faceMatcher = ', faceMatcher)

    const dcrt = await loadDescriptions(label, arrImage)

    var index = -1
    for (let i = 0; i < labeledFaceDescriptors.length; i++) {
        if (labeledFaceDescriptors[i]._label == label) index = i
    }

    if (index > -1) labeledFaceDescriptors.splice(index, 1);

    labeledFaceDescriptors.push(dcrt)
    console.log('update labeledFaceDescriptors = ', labeledFaceDescriptors)

    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
    console.log('update faceMatcher = ', faceMatcher)
}

document.getElementById("btn-inp").onclick = function () {
    document.getElementById("btn-inp").style.display = "none";
    document.getElementById("div-inp").style.display = "block";
    document.getElementById("inp-id").focus();
};

function getSnapshot() {
    let canvas = document.createElement("canvas");
    let image = "";
    canvas.width = video.width;
    canvas.height = video.height;

    let ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    image = canvas.toDataURL("image/jpeg");
    // console.log("Screenshot");
    return image;
}


Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel),
    faceapi.nets.faceExpressionNet.loadFromUri(urlModel),
]).then(start);

var RecognitionIntervalID = -1;
var labeledFaceDescriptors;
var faceMatcher;

async function start() {
    var imgSample = await faceapi.fetchImage('/storage/face-recognition/CppvA_Screenshot%202023-05-11%20145821.png')
    const detecSample = await faceapi.detectSingleFace(imgSample).withFaceLandmarks().withFaceDescriptor()

    labeledFaceDescriptors = await loadLabeledImages();

    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

    if (detecSample != null) {
        startVideo()
    };

    video.addEventListener("playing", () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("webcam").append(canvas);

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        playFaceRecognition(3000, canvas, displaySize);

        $("#inp-id").on("focus", function () {
            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
            stopFaceRecognition();
            setTimeout(() => {
                $("#inp-id").blur();
                document.getElementById("inp-id").value = "";
                document.getElementById("btn-inp").style.display = "block";
                document.getElementById("div-inp").style.display = "none";
                playFaceRecognition(3000, canvas, displaySize);
            }, 10000);
        });
    });

    video.currentTime = 1;
}

function startVideo() {
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
    return Promise.all(
        labels.map(async (label) => {
            return loadDescriptions(label, faceRegination[label])
        })
    );
}


async function loadDescriptions(label, arrDescrip) {
    const descriptions = [];

    for (let i = 0; i < arrDescrip.length; i++) {
        try {
            var arr32 = new Float32Array(arrDescrip[i].split(',').map(parseFloat))
            descriptions.push(arr32);
        } catch (err) {
            console.log(image + ": error");
            continue;
        }
    }
    var result = new faceapi.LabeledFaceDescriptors(label.toString(), descriptions)
    return result;
}

var faceAntiSpoofing = {
    isCheck: false,
    label: "",
    distance: 0,
    action: -1,
    actionName: "",
};
var check_attendance;
var snapshot = [];

function stopFaceRecognition() {
    if(RecognitionIntervalID != -1) clearInterval(RecognitionIntervalID);
}

function playFaceRecognition(tgian, canvas, displaySize) {
    if (video.srcObject == null) return;
    stopFaceRecognition();
    RecognitionIntervalID = setInterval(
        faceRecognition,
        tgian,
        canvas,
        displaySize
    );
    return RecognitionIntervalID;
}

async function faceRecognition(canvas, displaySize) {
    canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

    const detections = await faceapi
        .detectSingleFace(video)
        .withFaceLandmarks()
        .withFaceDescriptor()
        .withFaceExpressions();

    if (detections != null) {
        checkOpenMouth(detections)
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        faceapi.draw.drawDetections(canvas, resizedDetections);

        const result = faceMatcher.findBestMatch(detections.descriptor);

        if (result._distance < (faceAntiSpoofing.isCheck == true ? 0.6 : 0.5)) {
            if (faceAntiSpoofing.isCheck == false || faceAntiSpoofing.label == result._label) {
                const box = resizedDetections.detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, {
                    label: arrName[result._label] + " (" + result._label + ")",
                });
                drawBox.draw(canvas);
            }

            if (faceAntiSpoofing.isCheck == true) {
                if (checkAction(detections, faceAntiSpoofing.action)) {
                    stopFaceRecognition();
                    alertDisable()
                    clearTimeout(faceAntiSpoofing.idTimeout);

                    var image = snapshot[0];
                    var RemoveShowModalID = setTimeout(() => {
                        $(".modal").remove();
                        $(".modal-backdrop").remove();
                        submitForm(faceAntiSpoofing.label, image, true);
                        canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

                        playFaceRecognition(3000, canvas, displaySize);
                        removeFaceAntiSpoofing(canvas, displaySize, true);
                    }, 6000);

                    showModal(
                        arrName[parseInt(faceAntiSpoofing.label)],
                        faceAntiSpoofing.label,
                        "Yes",
                        "No",
                        () => {
                            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
                            submitForm(faceAntiSpoofing.label, image, true);
                            playFaceRecognition(3000, canvas, displaySize);
                            removeFaceAntiSpoofing(canvas, displaySize, true);
                            clearTimeout(RemoveShowModalID);
                        },
                        () => {
                            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
                            removeFaceAntiSpoofing(canvas, displaySize, true);
                            playFaceRecognition(3000, canvas, displaySize);
                            clearTimeout(RemoveShowModalID);
                        }
                    );

                    snapshot = [];
                } else {
                    canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
                    removeFaceAntiSpoofing(canvas, displaySize, false, true);
                }
            } else {
                stopFaceRecognition()
                var check_result = false;
                checkAttendance(result._label).done(() => {
                    check_result = check_attendance.success;
                });

                if (check_result == true) {
                    alertSuccess("You've attended! Please come back in 1 minutes!");
                     playFaceRecognition(3000, canvas, displaySize);
                    return;
                }

                snapshot.push(getSnapshot());

                faceAntiSpoofing = {
                    isCheck: true,
                    label: result._label,
                    distance: result._distance,
                    action: Math.floor(Math.random() * 4),
                };

                switch (faceAntiSpoofing.action) {
                    case 0:
                        alertAction("Make a Smile");
                        faceAntiSpoofing.actionName = "happy";
                        break;
                    case 1:
                        alertAction("Open your Mouth");
                        faceAntiSpoofing.actionName = "surprised";
                        break;
                    case 2:
                        alertAction("Turn your face to the Right");
                        faceAntiSpoofing.actionName = "rotateRight";
                        break;
                    case 3:
                        alertAction("Turn your face to the Left");
                        faceAntiSpoofing.actionName = "rotateLeft";
                        break;
                    default:
                        faceAntiSpoofing.actionName = "";
                        break;
                }

                playFaceRecognition(3000, canvas, displaySize);

                faceAntiSpoofing.idTimeout = setTimeout(
                    removeFaceAntiSpoofing,
                    6000,
                    canvas, displaySize, false, true);
            }
        } else {
            alertError("Unable to confirm employee");
        }
    }
}

function removeFaceAntiSpoofing(canvas, displaySize, isSuccess, playRecog = false) {
    clearTimeout(faceAntiSpoofing.idTimeout);

    if (!isSuccess) {
        console.log('time out')
        alertError("Unable to confirm employee")};

    faceAntiSpoofing = {
        isCheck: false,
        label: "",
        distance: 0,
        action: -1,
        actionName: "",
        idTimeout: -1,
    };

    if (playRecog) playFaceRecognition(3000, canvas, displaySize);
}

function rotateFaceToLeftRight(detections) {
    const pointNose = detections.landmarks.positions[30];
    const leftPoint = detections.landmarks.positions[2];
    const rightPoint = detections.landmarks.positions[14];

    const distanceLeft = Math.abs(pointNose.x - leftPoint.x);
    const distanceRight = Math.abs(pointNose.x - rightPoint.x);

    if (distanceLeft / 5 >= distanceRight) {
        return "rotateLeft";
    } else if (distanceRight / 5 >= distanceLeft) {
        return "rotateRight";
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
        return result_expression[0];
    }
    return "";
}

function checkOpenMouth(detections) {
    const pointTop = detections.landmarks.positions[63];
    const pointBottom = detections.landmarks.positions[67];
    const pointLeft = detections.landmarks.positions[61];
    const pointRight = detections.landmarks.positions[65];

    const y = Math.abs(pointTop.y - pointBottom.y);
    const x = Math.abs(pointLeft.x - pointRight.x);
    if (x / y < 0.55) return true;
    return false;
}

function checkAction(detections, action) {
    switch (action) {
        case 0:
            if (getExpression(detections) == "happy") {
                return true;
            }
            break;
        case 1:
            return checkOpenMouth(detections)

        case 2:
            if (rotateFaceToLeftRight(detections) == "rotateRight") {
                return true;
            }
            break;
        case 3:
            if (rotateFaceToLeftRight(detections) == "rotateLeft") {
                return true;
            }
            break;
    }
    return false;
}

function checkAttendance(id) {
    var k = $.Deferred();
    $.ajax({
        async: false,
        type: "POST",
        url: window.location.toString() + "/check-attendance",
        data: {
            id: id,
        },
    }).done(function (data) {
        check_attendance = data;
        k.resolve();
    });
    return k.promise();
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

function alertAction(message) {
    document.getElementById("alert-message").style.backgroundColor = "blue";
    document.getElementById("alert-message").style.textAlign = "center";
    document.getElementById("alert-message").textContent = message;
}

function alertError(message) {
    document.getElementById("alert-message").style.backgroundColor = "red";
    document.getElementById("alert-message").style.textAlign = "left";
    document.getElementById("alert-message").textContent = message;
    setTimeout(function () {
        alertDisable();
    }, 2000);
}

function alertSuccess(message) {
    document.getElementById("alert-message").style.backgroundColor = "green";
    document.getElementById("alert-message").style.textAlign = "left";
    document.getElementById("alert-message").textContent = message;
    setTimeout(function () {
        alertDisable();
    }, 2000);
}

function alertDisable() {
    document.getElementById("alert-message").style.background = "none";
    document.getElementById("alert-message").style.textAlign = "left";
    document.getElementById("alert-message").textContent = "";
}
