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
    faceapi.nets.faceExpressionNet.loadFromUri(urlModel),
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

async function faceDetection() {
    const canvas = faceapi.createCanvasFromMedia(video);
    document.body.append(canvas);
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);
    setInterval(async () => {
        const detections = await faceapi
            .detectAllFaces(video)
            .withFaceLandmarks()
            .withFaceExpressions();
        const resizedDetections = faceapi.resizeResults(
            detections,
            displaySize
        );
        canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
        faceapi.draw.drawDetections(canvas, resizedDetections);
        faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
        faceapi.draw.drawFaceExpressions(canvas, resizedDetections);
    }, 100);
}

var faceAntiSpoofing = {
    isCheck: false,
    label: "",
    distance: 0,
    action: -1,
    actionName: "",
};

var check_attendance
async function faceRecognition(faceMatcher, canvas, displaySize) {
    canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
    // const detections = await faceapi
    //     .detectAllFaces(video)
    //     .withFaceLandmarks()
    //     .withFaceDescriptors();
    // const resizedDetections = faceapi.resizeResults(
    //     detections,
    //     displaySize
    // );
    // canvas
    //     .getContext("2d")
    //     .clearRect(0, 0, canvas.width, canvas.height);
    // const results = resizedDetections.map((d) =>
    //     faceMatcher.findBestMatch(d.descriptor)
    // );
    // faceapi.draw.drawDetections(canvas, resizedDetections)
    // faceapi.draw.drawFaceLandmarks(canvas, resizedDetections)
    // faceapi.draw.drawFaceExpressions(canvas, resizedDetections)
    // results.forEach((result, i) => {
    //     const box = resizedDetections[i].detection.box;
    //     const drawBox = new faceapi.draw.DrawBox(box, {
    //         label: result.toString(),
    //     });
    //     console.log(result._label);
    //     console.log(result._distance);
    //     drawBox.draw(canvas);
    //     if (result._label != "unknown") {
    //         if(RecognitionIntervalID != -1) clearInterval(RecognitionIntervalID);
    //         var image = getSnapshot();
    //         showModal(
    //             "Face Detecttion",
    //             "Please confirm your face?",
    //             "Yes",
    //             "No",
    //             () => {
    //                 alert("Attendance success");
    //                 submitForm(result._label, image, true);
    //                 RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
    //             },
    //             () => {
    //                 alert("Enter your ID");
    //                 RecognitionIntervalID = setInterval(faceRecognition, 7000, faceMatcher, canvas, displaySize);
    //             },
    //             () => {
    //                 // alert("Wait for timekeeping again");
    //                 // RecognitionIntervalID = setInterval(faceRecognition, 3000, faceMatcher, canvas, displaySize);
    //             }
    //         )
    //     } else {
    //         alertError("Không xác nhận được người dùng");
    //     }
    // });

    const detections = await faceapi
        .detectSingleFace(video)
        .withFaceLandmarks()
        .withFaceDescriptor()
        .withFaceExpressions();
    // console.log(detections.expressions);

    if (detections != null) {
        const resizedDetections = faceapi.resizeResults(
            detections,
            displaySize
        );

        faceapi.draw.drawDetections(canvas, resizedDetections);

        const result = faceMatcher.findBestMatch(detections.descriptor);

        if (
            result._distance < (faceAntiSpoofing.isCheck == true ? 0.55 : 0.4)
        ) {
            //kiem tra da cham cong
            //
            //
            //
            var check_result
            checkAttendance(result._label).done(() => {
                check_result = check_attendance.success
            });
            console.log(check_result);
            if(check_result == true) {
                alertSuccess("You've attended! Please come back in 5 minutes!");
                return ;
            }
            const box = resizedDetections.detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, {
                label: arrName[result._label] + ' (' + result._label +')',
            });
            drawBox.draw(canvas);

            if (faceAntiSpoofing.isCheck == true) {
                if (faceAntiSpoofing.label == result._label)
                    var check_action = checkAction(
                        detections,
                        faceAntiSpoofing.action
                    );

                if (faceAntiSpoofing.label != result._label || !check_action) {
                    removeFaceAntiSpoofing(
                        faceMatcher,
                        canvas,
                        displaySize,
                        false
                    );
                }

                if (check_action) {
                    removeFaceAntiSpoofing(
                        faceMatcher,
                        canvas,
                        displaySize,
                        true
                    );
                    //modal
                    if (RecognitionIntervalID != -1)
                        clearInterval(RecognitionIntervalID);
                    var image = getSnapshot();
                    showModal(
                        // "Please confirm",
                        arrName[result._label],
                        result._label,
                        "Yes",
                        "No",
                        () => {
                            submitForm(result._label, image, true);
                            RecognitionIntervalID = setInterval(
                                faceRecognition,
                                3000,
                                faceMatcher,
                                canvas,
                                displaySize
                            );
                        },
                        () => {
                            RecognitionIntervalID = setInterval(
                                faceRecognition,
                                7000,
                                faceMatcher,
                                canvas,
                                displaySize
                            );
                        }
                    );
                }
            } else {
                faceAntiSpoofing = {
                    isCheck: true,
                    label: result._label,
                    distance: result._distance,
                    action: Math.floor(Math.random() * 4)
                    // action: 3,
                };

                switch (faceAntiSpoofing.action) {
                    case 0:
                        alertAction("Turn your face to the Right");
                        faceAntiSpoofing.actionName = "rotateRight";
                        break;
                    case 1:
                        alertAction("Turn your face to the Left");
                        faceAntiSpoofing.actionName = "rotateLeft";
                        break;
                    case 2:
                        alertAction("Make a Smile");
                        faceAntiSpoofing.actionName = "happy";
                        break;
                    case 3:
                        alertAction("Open your Mouth");
                        faceAntiSpoofing.actionName = "surprised";
                        break;
                    default:
                        faceAntiSpoofing.actionName = "";
                        break;
                }
                // console.log(faceAntiSpoofing)

                clearInterval(RecognitionIntervalID);
                RecognitionIntervalID = setInterval(
                    faceRecognition,
                    2000,
                    faceMatcher,
                    canvas,
                    displaySize
                );
                faceAntiSpoofing.idTimeout = setTimeout(
                    removeFaceAntiSpoofing,
                    3000,
                    faceMatcher,
                    canvas,
                    displaySize,
                    false
                );
            }
        }
        else {
            alertError("Unable to confirm employee");
        }
    }
}

function removeFaceAntiSpoofing(faceMatcher, canvas, displaySize, isSuccess) {
    clearTimeout(faceAntiSpoofing.idTimeout);

    if (isSuccess) alertSuccess("Right action");
    else alertError("Wrong action");

    faceAntiSpoofing = {
        isCheck: false,
        label: "",
        distance: 0,
        action: -1,
        actionName: "",
        idTimeout: -1,
    };

    clearInterval(RecognitionIntervalID);
    RecognitionIntervalID = setInterval(
        faceRecognition,
        3000,
        faceMatcher,
        canvas,
        displaySize
    );
}

function rotateFaceToLeftRight(detections) {
    const pointNose = detections.landmarks.positions[30];
    const leftPoint = detections.landmarks.positions[2];
    const rightPoint = detections.landmarks.positions[14];

    const distanceLeft = Math.abs(pointNose.x - leftPoint.x);
    const distanceRight = Math.abs(pointNose.x - rightPoint.x);
    if (distanceLeft / 4 >= distanceRight) {
        console.log("Left", distanceLeft / distanceRight);
        return "rotateLeft";
    } else if (distanceRight / 4 >= distanceLeft) {
        console.log("Right", distanceRight / distanceLeft);
        return "rotateRight";
    }
}

function useEmotion(detections) {
    let result_expression = [];
    if (detections.expressions) {
        let arr = Object.values(detections.expressions);
        let max = Math.max(...arr);
        let index = arr.indexOf(max);
        let key = Object.keys(detections.expressions);
        result_expression.push(key[index], max);
        // console.log(result_expression);
        if (result_expression[0] == "happy") {
            return "happy";
        } else if (result_expression[0] == "surprised") {
            return "surprised";
        }
    }
    return "nothing";
}

function checkAction(detections, action) {
    if (action == 0) {
        if (rotateFaceToLeftRight(detections) == "rotateRight") {
            return true;
        }
    }
    if (action == 1) {
        if (rotateFaceToLeftRight(detections) == "rotateLeft") {
            return true;
        }
    }
    if (action == 2) {
        if (useEmotion(detections) == "happy") {
            return true;
        }
    }
    if (action == 3) {
        if (useEmotion(detections) == "surprised") {
            return true;
        }
    }
    return false;
}

function checkAttendance(id) {
        var k = $.Deferred()
        $.ajax({
            async: false,
            type: "POST",
            // cache: false,
            url: 'http://127.0.0.1:8000/check-in/check-attendance',
            data: {
                id: id,
            },
        }).done(function (data) {
            // console.log(data)
            check_attendance = data;
            k.resolve();
            });
        return k.promise()
}
let i = 0;
var RecognitionIntervalID = -1;
async function start() {
    console.log("Training data.");

    const labeledFaceDescriptors = await loadLabeledImages();
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

    console.log("Completed training.");

    startVideo();

    // video.addEventListener("playing", faceDetection)

    video.addEventListener("playing", () => {
        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("webcam").append(canvas);

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        RecognitionIntervalID = setInterval(
            faceRecognition,
            3000,
            faceMatcher,
            canvas,
            displaySize
        );
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
    }, 1000);
}

function alertSuccess(message) {
    document.getElementById("alert-message").style.backgroundColor = "green";
    document.getElementById("alert-message").style.textAlign = "left";
    document.getElementById("alert-message").textContent = message;
    setTimeout(function () {
        alertDisable();
    }, 1000);
}

function alertDisable() {
    document.getElementById("alert-message").style.background = "none";
    document.getElementById("alert-message").style.textAlign = "left";
    document.getElementById("alert-message").textContent = "";
}
