<div class="mb-4">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button type="button" onclick="showTab('uploadTab')" id="uploadTabBtn"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-upload mr-2"></i>Unggah Foto
            </button>
            <button type="button" onclick="showTab('webcamTab')" id="webcamTabBtn"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-camera mr-2"></i>Ambil Foto
            </button>
        </nav>
    </div>
</div>

<div id="uploadTab" class="space-y-4">
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <input type="file" name="foto_pengukuran" id="fileUpload" onchange="previewUploadedImage(this)"
                accept="image/*" class="w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="w-32 h-32 border border-gray-300 rounded overflow-hidden bg-gray-100">
            <img id="uploadPreview" src="" alt="Preview Unggahan" class="w-full h-full object-cover hidden">
            <div id="uploadPlaceholder" class="w-full h-full flex items-center justify-center text-gray-400">
                <i class="fas fa-image text-3xl"></i>
            </div>
        </div>
    </div>
    <p class="text-xs text-gray-500">Format: JPG, JPEG, PNG, GIF (max 2MB)</p>
</div>

<div id="webcamTab" class="space-y-4 hidden">
    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <div class="relative">
            <video id="webcamVideo" autoplay playsinline class="w-full h-64 object-cover"></video>

            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-4">
                <button type="button" onclick="startWebcam()" id="startWebcamBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-play mr-1"></i>Mulai Webcam
                </button>
                <button type="button" onclick="stopWebcam()" id="stopWebcamBtn"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 hidden">
                    <i class="fas fa-stop mr-1"></i>Stop Webcam
                </button>
                <button type="button" onclick="takeSnapshot()" id="takeSnapshotBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 hidden">
                    <i class="fas fa-camera mr-1"></i>Ambil Foto
                </button>
            </div>
        </div>

        <div class="p-4 bg-gray-900">
            <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1">
                    <div class="w-full h-48 md:h-32 border-2 border-gray-600 rounded overflow-hidden bg-gray-700">
                        <canvas id="webcamCanvas" class="w-full h-full object-cover hidden"></canvas>
                        <img id="snapshotPreview" src="" alt="Snapshot" class="w-full h-full object-cover hidden">
                        <div id="snapshotPlaceholder"
                            class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-camera text-4xl mb-2"></i>
                            <span class="text-sm">Foto akan muncul di sini</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col space-y-2">
                    <button type="button" onclick="retakeSnapshot()" id="retakeSnapshotBtn"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 hidden">
                        <i class="fas fa-redo mr-1"></i>Ulangi Foto
                    </button>
                    <button type="button" onclick="saveSnapshot()" id="saveSnapshotBtn"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 hidden">
                        <i class="fas fa-save mr-1"></i>Simpan Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="webcam_photo" id="webcamPhotoData">
    <p class="text-xs text-gray-500">Pastikan izin kamera sudah diberikan. Foto akan otomatis disimpan saat Anda
        mengambil foto.</p>
</div>

<script>
    let webcamStream = null;
    let snapshotData = null;

    function showTab(tabName) {
        document.getElementById('uploadTab').classList.add('hidden');
        document.getElementById('webcamTab').classList.add('hidden');

        document.getElementById('uploadTabBtn').classList.remove('border-red-500', 'text-red-600');
        document.getElementById('webcamTabBtn').classList.remove('border-red-500', 'text-red-600');

        document.getElementById(tabName).classList.remove('hidden');

        if (tabName === 'uploadTab') {
            document.getElementById('uploadTabBtn').classList.add('border-red-500', 'text-red-600');
            stopWebcam();
        } else {
            document.getElementById('webcamTabBtn').classList.add('border-red-500', 'text-red-600');
            setTimeout(startWebcam, 500);
        }
    }

    async function startWebcam() {
        try {
            if (webcamStream) {
                webcamStream.getTracks().forEach(track => track.stop());
            }

            webcamStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            const videoElement = document.getElementById('webcamVideo');
            videoElement.srcObject = webcamStream;

            document.getElementById('startWebcamBtn').classList.add('hidden');
            document.getElementById('stopWebcamBtn').classList.remove('hidden');
            document.getElementById('takeSnapshotBtn').classList.remove('hidden');

            clearSnapshot();

            console.log('Webcam started successfully');
        } catch (error) {
            console.error('Error accessing webcam:', error);
            alert('Tidak dapat mengakses webcam. Pastikan izin kamera telah diberikan dan kamera tidak sedang digunakan aplikasi lain.');
        }
    }

    function stopWebcam() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;

            const videoElement = document.getElementById('webcamVideo');
            videoElement.srcObject = null;

            document.getElementById('startWebcamBtn').classList.remove('hidden');
            document.getElementById('stopWebcamBtn').classList.add('hidden');
            document.getElementById('takeSnapshotBtn').classList.add('hidden');

            console.log('Webcam stopped');
        }
    }

    function takeSnapshot() {
        if (!webcamStream) {
            alert('Webcam belum dimulai. Klik "Mulai Webcam" terlebih dahulu.');
            return;
        }

        const videoElement = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const context = canvas.getContext('2d');

        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;

        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

        snapshotData = canvas.toDataURL('image/jpeg', 0.8);

        document.getElementById('snapshotPreview').src = snapshotData;
        document.getElementById('snapshotPreview').classList.remove('hidden');
        document.getElementById('snapshotPlaceholder').classList.add('hidden');

        document.getElementById('retakeSnapshotBtn').classList.remove('hidden');
        document.getElementById('saveSnapshotBtn').classList.remove('hidden');

        console.log('Snapshot taken');
    }

    function retakeSnapshot() {
        clearSnapshot();
        console.log('Snapshot cleared for retake');
    }

    function clearSnapshot() {
        snapshotData = null;
        document.getElementById('snapshotPreview').src = '';
        document.getElementById('snapshotPreview').classList.add('hidden');
        document.getElementById('snapshotPlaceholder').classList.remove('hidden');
        document.getElementById('retakeSnapshotBtn').classList.add('hidden');
        document.getElementById('saveSnapshotBtn').classList.add('hidden');

        const finalPhotoImage = document.getElementById('finalPhotoImage');
        const finalPhotoPlaceholder = document.getElementById('finalPhotoPlaceholder');
        const webcamPhotoData = document.getElementById('webcamPhotoData');

        if (finalPhotoImage && finalPhotoPlaceholder && webcamPhotoData) {
            finalPhotoImage.src = '';
            finalPhotoImage.classList.add('hidden');
            finalPhotoPlaceholder.classList.remove('hidden');
            webcamPhotoData.value = '';
        }
    }

    function saveSnapshot() {
        if (!snapshotData) {
            alert('Belum ada foto yang diambil. Klik "Ambil Foto" terlebih dahulu.');
            return;
        }

        const webcamPhotoData = document.getElementById('webcamPhotoData');
        if (webcamPhotoData) {
            webcamPhotoData.value = snapshotData;
        }

        const finalPhotoImage = document.getElementById('finalPhotoImage');
        const finalPhotoPlaceholder = document.getElementById('finalPhotoPlaceholder');

        if (finalPhotoImage && finalPhotoPlaceholder) {
            finalPhotoImage.src = snapshotData;
            finalPhotoImage.classList.remove('hidden');
            finalPhotoPlaceholder.classList.add('hidden');
        }

        const fileUpload = document.getElementById('fileUpload');
        const uploadPreview = document.getElementById('uploadPreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        if (fileUpload && uploadPreview && uploadPlaceholder) {
            fileUpload.value = '';
            uploadPreview.src = '';
            uploadPreview.classList.add('hidden');
            uploadPlaceholder.classList.remove('hidden');
        }

        stopWebcam();

        showTab('uploadTab');

        console.log('Snapshot saved for submission');
    }

    function previewUploadedImage(input) {
        const file = input.files[0];
        const preview = document.getElementById('uploadPreview');
        const placeholder = document.getElementById('uploadPlaceholder');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');

                const finalPhotoImage = document.getElementById('finalPhotoImage');
                const finalPhotoPlaceholder = document.getElementById('finalPhotoPlaceholder');

                if (finalPhotoImage && finalPhotoPlaceholder) {
                    finalPhotoImage.src = e.target.result;
                    finalPhotoImage.classList.remove('hidden');
                    finalPhotoPlaceholder.classList.add('hidden');
                }

                const webcamPhotoData = document.getElementById('webcamPhotoData');
                if (webcamPhotoData) {
                    webcamPhotoData.value = '';
                }
                clearSnapshot();
            }

            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }

    window.addEventListener('beforeunload', function () {
        stopWebcam();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const uploadTabBtn = document.getElementById('uploadTabBtn');
        if (uploadTabBtn && !uploadTabBtn.classList.contains('border-red-500')) {
            uploadTabBtn.classList.add('border-red-500', 'text-red-600');
        }
        console.log('Camera component loaded');
    });
</script>