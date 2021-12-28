<style>
#result {
    display:none;
    text-align: center;
    z-index: 10002;
    color: rgba(255,255,255,0.8);
    max-width: 450px;
    width:85%;
    background: rgba(0,0,0,0.5);
    padding: 10px;
    border-radius: 8px;
    position: fixed;
    bottom: 20px;
    left: 50%;
    margin-bottom:40px;
    transform: translate(-50%, 50%);
}
.fullscreen {
    z-index: 10000;
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
    transform:translateX(calc((100% - 100vw) / 2));
}
</style>
<div>
    <video autoplay="true" id="video" muted></video>
    <div id="result">Memuat Kamera...</div>
</div>
<script src="js/face-api/face-api.js"></script>
<script src="facesamples/sample-<?=$_GET['pegawai_id']?>.js"></script>
<script>
var descriptor = new Float32Array(Object.values(employee_sample.descriptor))
var mystream, schedule_id,interval;
var video = document.getElementById('video')
document.getElementById('result').style.display = "block";
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/js/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/js/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/js/models'),
    faceapi.nets.ssdMobilenetv1.loadFromUri('/js/models')
]).then(startVideo)

function startVideo() {
    navigator.getUserMedia({ 
        video: {} 
    },
        stream => {
            video.srcObject = stream
            mystream = stream
        },
        err => console.error(err)
    )
}

video.addEventListener('play', () => {
    video.classList.add('fullscreen')
    document.getElementById('result').innerHTML = "Sedang Memindai!"
    setTimeout(async e => {
        video.pause()
        mystream.getTracks().forEach(track => {
            track.stop();
        });
        document.getElementById('result').innerHTML = "Mendeteksi Wajah..."
        const detection = await faceapi.detectSingleFace(video,new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor()
        console.log({det:detection})
        if(detection != undefined)
        {
            document.getElementById('result').innerHTML = "Wajah Terdeteksi! Mengenali Wajah..."
            clearInterval(interval)
            video.pause()
            mystream.getTracks().forEach(track => {
                track.stop();
            });
            
            try {
                const faceMatcher = new faceapi.FaceMatcher([
                    new faceapi.LabeledFaceDescriptors('<?=$_GET['pegawai_id']?>', [descriptor])
                ], 0.6)
                var results = faceMatcher.findBestMatch(detection.descriptor)
                console.log({res:results})
                if(results.distance <= 0.5)
                {
                    document.getElementById('result').innerHTML = "Wajah Cocok! Absensi Berhasil"
                    var canvas = document.createElement('canvas')
                    var context = canvas.getContext('2d')
                    canvas.height = video.videoHeight // offsetHeight
                    canvas.width = video.videoWidth // offsetWidth
                    context.drawImage(video,0,0,canvas.width,canvas.height)
                    var pic = canvas.toDataURL("image/jpeg")

                    var formData = new FormData;
                    formData.append('file_absensi',pic)
                    formData.append('jenis_absen','<?=$_GET['jenis_absen']?>')
                    formData.append('pegawai_id','<?=$_GET['pegawai_id']?>')
                    formData.append('opd_id','<?=$_GET['opd_id']?>')
                    formData.append('keterangan','<?=$_GET['keterangan']?>')

                    var req = await fetch('/apiabsen/saveAbsen',{
                        method:'POST',
                        body:formData
                    })
                    var res = await req.json()
                    document.getElementById('result').innerHTML = res.message
                    if(res.status == 'success')
                    {
                        alert('Absen Berhasil')
                        window.close()
                        // if (typeof(Android) != "undefined")
                        //     location='file:///android_asset/index.html#/absen/<?=$_GET['type']?>'
                        // else
                        //     location='http://localhost:9000/#/absen/<?=$_GET['type']?>'
                    }
                    return
                }
                else
                {
                    document.getElementById('result').innerHTML = "Wajah tidak cocok! Pemindaian ulang dalam 3 detik"
                    setTimeout(() => {
                        startVideo()
                    }, 3000);
                }
            } catch (err) {
                document.getElementById('result').innerHTML = 'Error! Silahkan Refresh untuk mengulangi'
            }
        }
        else
        {
            document.getElementById('result').innerHTML = "Wajah tidak ditemukan! Pemindaian ulang dalam 3 detik"
            setTimeout(() => {
                startVideo()
            }, 3000);
        }
    }, 4000);
})
</script>