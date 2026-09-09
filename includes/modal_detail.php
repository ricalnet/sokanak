<!-- Modal for Detail View - Professional Version -->
<div id="detailModal"
    class="fixed inset-0 bg-gray-900 bg-opacity-70 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-4 mx-auto p-4 w-full max-w-3xl">
        <div class="bg-white rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0"
            id="modalContainer">
            <div
                class="flex justify-between items-center p-6 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center shadow-sm">
                        <i class="fas fa-child text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Detail Data Anak</h3>
                        <p class="text-sm text-gray-500 mt-1">Informasi lengkap anak posyandu</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="closeDetail()"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]" id="detailContent">
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Terakhir diperbarui: <span id="lastUpdated"></span>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="closeDetail()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Tutup
                        </button>
                        <button id="editButton" onclick="editData()"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-edit mr-2"></i>Edit Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(data) {
        const tgl_lahir = new Date(data.tgl_lahir);
        const formattedDate = tgl_lahir.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        const today = new Date();
        const birthDate = new Date(data.tgl_lahir);
        let usiaTahun = today.getFullYear() - birthDate.getFullYear();
        let usiaBulan = today.getMonth() - birthDate.getMonth();

        if (usiaBulan < 0 || (usiaBulan === 0 && today.getDate() < birthDate.getDate())) {
            usiaTahun--;
            usiaBulan = 12 + usiaBulan;
        }

        const usiaHari = Math.abs(today.getDate() - birthDate.getDate());
        let usiaText = '';

        if (usiaTahun > 0) {
            usiaText = `${usiaTahun} tahun`;
            if (usiaBulan > 0) usiaText += ` ${usiaBulan} bulan`;
        } else if (usiaBulan > 0) {
            usiaText = `${usiaBulan} bulan`;
            if (usiaHari > 0) usiaText += ` ${usiaHari} hari`;
        } else {
            usiaText = `${usiaHari} hari`;
        }

        const updatedAt = data.updated_at || data.created_at;
        const lastUpdated = new Date(updatedAt).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        document.getElementById('lastUpdated').textContent = lastUpdated;

        let detailHTML = `
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-start gap-6 pb-6 border-b border-gray-100">
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 rounded-xl overflow-hidden border-4 border-red-50 shadow-sm mx-auto md:mx-0">
    `;

        if (data.foto_pengukuran && data.foto_pengukuran !== '') {
            detailHTML += `<img src="${data.foto_pengukuran}" alt="${data.nama_anak}" class="w-full h-full object-cover">`;
        } else {
            detailHTML += `
            <div class="w-full h-full bg-gradient-to-br from-red-50 to-gray-50 flex items-center justify-center">
                <i class="fas fa-child text-5xl text-red-200"></i>
            </div>
        `;
        }

        detailHTML += `
                    </div>
                    <div class="mt-4 text-center md:text-left">
                        <div class="bg-red-50 rounded-lg p-3">
                            <p class="text-sm text-gray-600 mb-1">NIK</p>
                            <p class="font-mono text-base font-bold text-red-700">${data.NIK}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1">
                    <div class="text-center md:text-left">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">${data.nama_anak}</h2>
                        <div class="flex flex-wrap gap-2 mb-4 justify-center md:justify-start">
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${data.jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-pink-100 text-pink-700 border border-pink-200'}">
                                <i class="fas ${data.jenis_kelamin == 'L' ? 'fa-mars' : 'fa-venus'} mr-1"></i>
                                ${data.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}
                            </span>
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                <i class="fas fa-calendar-alt mr-1"></i>${formattedDate}
                            </span>
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                <i class="fas fa-birthday-cake mr-1"></i>${usiaText}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                            <p class="text-xs text-blue-600 mb-1">Nomor KK</p>
                            <p class="text-sm font-medium text-gray-900 font-mono">${data.nomor_KK}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                            <p class="text-xs text-green-600 mb-1">RW</p>
                            <p class="text-sm font-medium text-gray-900">RW ${data.rw}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center mr-3">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Alamat</h4>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Desa/Kelurahan</p>
                                <p class="text-base font-medium text-gray-900">${data.desa}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Kecamatan</p>
                                <p class="text-base font-medium text-gray-900">${data.kecamatan}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">RW</p>
                                <p class="text-base font-medium text-gray-900">${data.rw}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mr-3">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Data Orang Tua</h4>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-500">Nama Orang Tua</p>
                                <p class="text-base font-medium text-gray-900">${data.nama_ortu}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-500">NIK Orang Tua</p>
                                <p class="text-base font-medium text-gray-900 font-mono">${data.nik_ortu}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-500">Nomor Telepon</p>
                                <p class="text-base font-medium text-gray-900">${data.hp_ortu}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${data.nama_wali && data.nama_wali.trim() !== '' ? `
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center mr-3">
                            <i class="fas fa-user-friends text-purple-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Data Wali</h4>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-500">Nama Wali</p>
                                <p class="text-base font-medium text-gray-900">${data.nama_wali}</p>
                            </div>
                        </div>
                        ${data.hp_wali && data.hp_wali.trim() !== '' ? `
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 text-gray-400">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-500">Nomor Telepon</p>
                                <p class="text-base font-medium text-gray-900">${data.hp_wali}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-gray-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Informasi Sistem</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-plus text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Dibuat pada</p>
                                <p class="font-medium text-gray-700">${new Date(data.created_at).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-sync-alt text-gray-400 mr-3 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Terakhir diupdate</p>
                                <p class="font-medium text-gray-700">${new Date(updatedAt).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-gradient-to-r from-red-50 to-blue-50 rounded-lg border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div class="mb-3 md:mb-0">
                        <h5 class="font-medium text-gray-900">Ingin melihat riwayat pengukuran?</h5>
                        <p class="text-sm text-gray-600 mt-1">Akses data lengkap pengukuran anak ini</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="riwayat-pengukuran.php?anak_id=${data.id}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-chart-line mr-2"></i>Riwayat
                        </a>
                        <a href="input-pengukuran.php?anak_id=${data.id}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>Input Pengukuran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;

        document.getElementById('detailContent').innerHTML = detailHTML;
        document.getElementById('editButton').setAttribute('onclick', `window.location.href='anak-form.php?action=edit&id=${data.id}'`);

        document.getElementById('detailModal').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('modalContainer').classList.remove('scale-95', 'opacity-0');
            document.getElementById('modalContainer').classList.add('scale-100', 'opacity-100');
        }, 10);

        document.addEventListener('keydown', handleEscapeKey);
    }

    function closeDetail() {
        const modalContainer = document.getElementById('modalContainer');
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailContent').innerHTML = '';
            document.removeEventListener('keydown', handleEscapeKey);
        }, 300);
    }

    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeDetail();
        }
    }

    function editData() {
    }

    document.getElementById('detailModal').addEventListener('click', function (e) {
        if (e.target.id === 'detailModal') {
            closeDetail();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const detailContent = document.getElementById('detailContent');
        if (detailContent) {
            detailContent.addEventListener('wheel', function (e) {
                if (this.scrollHeight > this.clientHeight) {
                    e.stopPropagation();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            const modal = document.getElementById('detailModal');
            if (!modal.classList.contains('hidden')) {
                if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                }
            }
        });
    });

    function smoothScrollToTop(element) {
        element.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function (e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusableElement) {
                        e.preventDefault();
                        lastFocusableElement.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusableElement) {
                        e.preventDefault();
                        firstFocusableElement.focus();
                    }
                }
            }
        });

        setTimeout(() => {
            firstFocusableElement.focus();
        }, 100);
    }
</script>

<style>
    #detailModal {
        backdrop-filter: blur(4px);
    }

    #modalContainer {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }

    #detailContent {
        scrollbar-width: thin;
        scrollbar-color: #e5e7eb transparent;
    }

    #detailContent::-webkit-scrollbar {
        width: 6px;
    }

    #detailContent::-webkit-scrollbar-track {
        background: #f9fafb;
        border-radius: 3px;
    }

    #detailContent::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 3px;
    }

    #detailContent::-webkit-scrollbar-thumb:hover {
        background: #d1d5db;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    @media (max-width: 768px) {
        #detailModal .max-w-3xl {
            max-width: 95%;
            margin: 1rem;
        }

        #detailModal .p-4 {
            padding: 0.5rem;
        }
    }
</style>