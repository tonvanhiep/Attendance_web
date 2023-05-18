const url = document.getElementById('url-face-api').textContent
const urlModel = url + '/models'
const urlImage = document.getElementById('url-image').textContent

const folderName = ''; // co '/' cuoi thu muc
const arrLabel = [
    {'label': 1, 'sl': 10},
    {'label': 2, 'sl': 10},
    {'label': 3, 'sl': 10},
    {'label': 4, 'sl': 10},
    {'label': 5, 'sl': 10},
];




console.log("Loading model...");

// Promise.all([
//     faceapi.nets.tinyFaceDetector.loadFromUri(urlModel),
//     faceapi.nets.faceRecognitionNet.loadFromUri(urlModel),
//     faceapi.nets.faceLandmark68Net.loadFromUri(urlModel),
//     faceapi.nets.ssdMobilenetv1.loadFromUri(urlModel)
// ]).then(start);

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
    console.log("Training data...")
    const labeledFaceDescriptors = await loadLabeledImages();
    console.log("Completed training.")
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.4);

    document.getElementById('btn-start').onclick = startTest(faceMatcher);
}

var arrResult = {};
var stt = 1;
async function startTest(faceMatcher) {
    document.getElementById('btn-start').disabled = true;
    var count = 0;
    var _true = 0;
    var _false = 0;
    for (let i = 0; i < arrLabel.length; i++) {
        for (let j = 0; j < arrLabel[i].sl; j++) {
            var arr = {
                'stt' : stt++,
                'image' : urlImage + folderName + j + '.jpeg',
                'label' : arrLabel[i]['label'],
                'recognition' : '',
                'distance' : 0,
                'result': ''
            };

            image = await faceapi.fetchImage(arr['image']);
            const detections = await faceapi.detectAllFaces(image).withFaceLandmarks().withFaceDescriptor();

            if(!(detections == null || detections.length == 0)) {
                const displaySize = { width: image.width, height: image.height };
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
                results.forEach((result, i) => {
                    arr['recognition'] = result._label;
                    arr['distance'] = result._distance;
                })
                arrResult[i + 'count'] != null ? int(arrResult[i + 'count']) + 1 : arrResult[i + 'count'] = 1;
            }

            if(arr['label'] == arr['recognition']) {
                arr['result'] = 'True';
                arr['recognition'] = arr['recognition'] + ' - ' + arr['distance']
                arrResult[i + 'true'] != null ? int(arrResult[i + 'true']) + 1 : arrResult[i + 'true'] = 1;
            }
            else if (arr['recognition'] == '') arr['result'] = 'None';
            else {
                arr['result'] = 'False';
                arr['recognition'] = arr['recognition'] + ' - ' + arr['distance']
                arrResult[i + 'false'] != null ? int(arrResult[i + 'false']) + 1 : arrResult[i + 'false'] = 1;
            };

            document.getElementById('tbl-detail').innerHTML +=
            '<tr>' +
                '<th>' + arr['stt'] + '</th>' +
                '<td><a href="' + arr['image'] + '">' + arr['image'] + '</a></td>' +
                '<td>' + arr['label'] + '</td>' +
                '<td>' + arr['recognition'] + '</td>' +
                '<td>' + arr['image'] + '</td>' +
            '</tr>';

        }
        document.getElementById('tbl-result').innerHTML +=
            '<tr>' +
                '<td>' + arr['label'] + '</td>' +
                '<td>' + arrResult[i + 'count'] + '</td>' +
                '<td>' + arrResult[i + 'true'] + '</td>' +
                '<td>' + arrResult[i + 'false'] + '</td>' +
            '</tr>';
        count += arrResult[i + 'count'];
        _true += arrResult[i + 'true'];
        _false += arrResult[i + 'false'];
        var dochinhxac = int(arrResult[i + 'true']) / int(arrResult[i + 'count']);
        document.getElementById('total-count').textContent = count;
        document.getElementById('total-true').textContent = _true;
        document.getElementById('total-false').textContent = _false;
        document.getElementById('phan-tram').textContent = dochinhxac + '%' ;
    }
}
