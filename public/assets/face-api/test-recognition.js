const url = document.getElementById('url-face-api').textContent
const urlModel = url + '/models'
const urlImage = document.getElementById('url-image').textContent

const folderName = 'storage/img-test/'; // co '/' cuoi thu muc
const arrLabel = [
    {'label': 1, 'sl': 10},
    {'label': 25, 'sl': 10},
    {'label': 30, 'sl': 10},
    {'label': 31, 'sl': 10},
    {'label': 33, 'sl': 10},
    {'label': 34, 'sl': 10},
    {'label': 35, 'sl': 10},
    {'label': 36, 'sl': 10},
    {'label': 37, 'sl': 10},
    {'label': 38, 'sl': 10},
    {'label': 39, 'sl': 10},
    {'label': 40, 'sl': 10},
    {'label': 42, 'sl': 10},
    {'label': 43, 'sl': 10},
    {'label': 44, 'sl': 10},
    {'label': 45, 'sl': 10},
    {'label': 46, 'sl': 10},
    {'label': 47, 'sl': 10},
    {'label': 48, 'sl': 10},
    {'label': 49, 'sl': 10},
    {'label': 50, 'sl': 10},
    {'label': 'unknown', 'sl': 30},
];




console.log("Loading model...");

Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
    faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
    faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
    faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel)
]).then(start);

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

async function start() {
    console.log("Training data...")
    const labeledFaceDescriptors = await loadLabeledImages();
    console.log("Completed training.")
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

    document.getElementById('btn-start').onclick = startTest(faceMatcher);
}

var arrResult = {};
var stt = 1;
async function startTest(faceMatcher) {
    document.getElementById('btn-start').disabled = true;
    var count = 0;
    var _true = 0;
    var _false = 0;
    var _unknown = 0;
    var _none = 0;
    for (let i = 0; i < arrLabel.length; i++) {
        arrResult[i + 'true'] = 0;
        arrResult[i + 'false'] = 0;
        arrResult[i + 'none'] = 0;
        arrResult[i + 'unknown'] = 0;
        for (let j = 0; j < arrLabel[i].sl; j++) {
            var arr = {
                'stt' : stt++,
                'image' : urlImage + folderName + arrLabel[i]['label'] + '/' + j + '.png',
                'label' : arrLabel[i]['label'],
                'recognition' : '',
                'result': 'None'
            };
            var image = await faceapi.fetchImage(arr['image']);
            const detections = await faceapi.detectSingleFace(image).withFaceLandmarks().withFaceDescriptor();

            if(detections != null) {
                const result = faceMatcher.findBestMatch(detections.descriptor);
                arr['recognition'] = result._label.toString() + ' - ' + result._distance.toString()

                if(arr['label'] == result._label) {
                    arr['result'] = 'True';
                    arrResult[i + 'true']++
                }
                else if(result._label == 'unknown') {
                    arr['result'] = 'Unknown';
                    arrResult[i + 'unknown']++
                }
                else {
                    arr['result'] = 'False';
                    arrResult[i + 'false']++
                }
            } else {
                arrResult[i + 'none']++
            }

            document.getElementById('tbl-detail').innerHTML +=
            '<tr>' +
                '<th>' + arr['stt'] + '</th>' +
                '<td><a target="_blank" href="' + arr['image'] + '">' + arr['image'] + '</a></td>' +
                '<td>' + arr['label'] + '</td>' +
                '<td>' + arr['recognition'] + '</td>' +
                '<td>' + arr['result'] + '</td>' +
            '</tr>';

        }
        document.getElementById('tbl-result').innerHTML +=
            '<tr>' +
                '<td>' + arr['label'] + '</td>' +
                '<td>' + arrLabel[i].sl + '</td>' +
                '<td>' + arrResult[i + 'true'] + '</td>' +
                '<td>' + arrResult[i + 'false'] + '</td>' +
                '<td>' + arrResult[i + 'unknown'] + '</td>' +
                '<td>' + arrResult[i + 'none'] + '</td>' +
            '</tr>';
        count += arrLabel[i].sl
        arrResult[i + 'true'] == null ? _true += 0 : _true += arrResult[i + 'true'];
        arrResult[i + 'false'] == null ? _false += 0 : _false += arrResult[i + 'false'];
        arrResult[i + 'unknown'] == null ? _unknown += 0 : _unknown += arrResult[i + 'unknown'];
        arrResult[i + 'none'] == null ? _none += 0 : _none += arrResult[i + 'none'];
    }

    var accuracy = parseInt(_true) / parseInt(count);
    document.getElementById('total-count').textContent = count;
    document.getElementById('total-true').textContent = _true;
    document.getElementById('total-false').textContent = _false;
    document.getElementById('total-unknown').textContent = _unknown;
    document.getElementById('total-none').textContent = _none;
    document.getElementById('accuracy-metric').textContent = accuracy * 100 + '%' ;
}
