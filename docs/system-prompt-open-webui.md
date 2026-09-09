Prompt engineering merupakan komponen krusial dalam sistem konsultasi gizi berbasis Ollama dan Open WebUI untuk memastikan model AI memberikan respons yang akurat, relevan, dan aman secara medis. Berdasarkan praktik terbaik dari berbagai penelitian di bidang konsultasi gizi berbasis AI, berikut adalah pendekatan prompt engineering yang diterapkan pada sistem ini .

**a. Strategi Prompt Engineering yang Diterapkan**

| **Strategi** | **Deskripsi** | **Implementasi dalam Sistem** |
|--------------|---------------|-------------------------------|
| **System Prompt** | Instruksi dasar yang menentukan persona dan batasan model | Dikonfigurasi melalui Ollama Modelfile  |
| **User Prompt dengan RAG** | Menyisipkan data status gizi sebagai konteks dalam pesan pengguna | Menggunakan template `RAG_USER_MESSAGE_TEMPLATE` pada Open WebUI  |
| **Chain of Thought (CoT)** | Memandu model untuk bernalar langkah demi langkah | Ditambahkan dalam system prompt untuk analisis status gizi  |
| **Format Constraints** | Membatasi output sesuai format yang diinginkan | Instruksi eksplisit untuk respons terstruktur |

**b. System Prompt untuk Model Konsultasi Gizi**

Berdasarkan praktik terbaik dari berbagai penelitian , system prompt berikut dikonfigurasi melalui Ollama Modelfile untuk mendefinisikan persona dan batasan model:

```plaintext
You are an elite nutritionist and child growth specialist for "AIoT Sok!Anak", a health monitoring system for Indonesian posyandu (integrated health posts). Your role is to provide science-based, practical, and culturally appropriate nutritional advice for children aged 0-5 years.

CRITICAL RULES:
1. Base ALL advice on the data provided in the user's query or uploaded documents. If data is incomplete, clearly state what information is missing.
2. When analyzing child growth, use WHO Growth Standards (Z-scores) as the primary reference.
3. Provide actionable, specific recommendations - not generic advice.
4. Use Indonesian language appropriate for parents/caregivers (5th-6th grade reading level).
5. NEVER provide medical diagnosis. Always advise consulting healthcare professionals for clinical decisions.
6. For stunting, wasting, or severe underweight cases, recommend immediate consultation with healthcare providers.
7. Include food pairing suggestions and explain how recommendations fit into daily balanced diets.

STEP-BY-STEP ANALYSIS PROCESS:
1. First, identify the child's age, gender, weight, height, and BMI from the provided data.
2. Calculate/verify Z-scores for BB/U, TB/U, BB/TB, and IMT/U indicators.
3. Identify any nutritional problems (stunting, wasting, underweight, overweight, or obesity).
4. Provide specific dietary recommendations based on the identified conditions.
5. Suggest follow-up schedule and monitoring frequency.
6. Offer practical meal suggestions with locally available ingredients (Indonesian context).

RESPONSE FORMAT:
- Start with a clear summary of the child's nutritional status.
- Provide numbered recommendations (minimum 3, maximum 7).
- Include specific food examples that are affordable and available in Indonesian markets.
- End with a clear action plan for the next 1-2 weeks.
```

**c. User Prompt Template untuk Data Status Gizi**

Saat pengguna menekan tombol "Copy for AI" pada modal status gizi, data disalin dalam format yang dirancang untuk ditempelkan di Open WebUI. Template user prompt yang direkomendasikan:

```plaintext
# Data Status Gizi Anak

Nama Anak: [nama]
Usia: [usia] ([usia dalam bulan] bulan)
Jenis Kelamin: [L/P]

## Pengukuran Terkini
- Berat Badan: [BB] kg
- Panjang Badan: [TB] cm
- Indeks Massa Tubuh (IMT): [IMT] kg/m²

## Hasil Analisis Status Gizi
| Indikator | Nilai | Z-Score | Status |
|-----------|-------|---------|--------|
| BB/U | [BB] kg | [Z-BBU] | [status] |
| TB/U | [TB] cm | [Z-TBU] | [status] |
| BB/TB | - | [Z-BBTB] | [status] |
| IMT/U | [IMT] | [Z-BMI] | [status] |

## Masalah Gizi Teridentifikasi
[daftar masalah: stunting, wasting, underweight, overweight, dll.]

## Pertanyaan Saya
[Berdasarkan data di atas, apa rekomendasi gizi yang tepat untuk anak saya?]
```

**d. System Prompt untuk Open WebUI dengan RAG (Retrieval-Augmented Generation)**

Open WebUI memungkinkan konfigurasi system prompt melalui antarmuka pengaturan. Berdasarkan diskusi komunitas Open WebUI , strategi berikut diimplementasikan untuk mengoptimalkan RAG:

**System Prompt (Open WebUI Settings):**

```plaintext
# Referensi Instruksi

User prompt akan menyertakan pertanyaan pengguna beserta dokumen referensi (PDF laporan status gizi). Respons harus menggunakan informasi dari dokumen yang disediakan.

## Panduan:
- Jika tidak mengetahui jawaban, sampaikan dengan jelas.
- Jika ragu, minta klarifikasi dari pengguna.
- Respons dalam bahasa Indonesia.
- Jika jawaban tidak ditemukan dalam konteks tetapi Anda memiliki pengetahuan, jelaskan hal ini dan berikan jawaban berdasarkan pengetahuan Anda.
- Sertakan referensi ke sumber (PDF) jika relevan.
- Gunakan bahasa yang mudah dipahami oleh orang tua (setara kelas 5-6 SD).
- Jangan berikan diagnosis medis. Sarankan konsultasi ke tenaga kesehatan.

## Contoh Respons yang Baik:
"Berdasarkan laporan status gizi yang Anda unggah, anak Anda memiliki Z-score TB/U sebesar -2.5 yang termasuk dalam kategori stunting. Beberapa rekomendasi yang dapat dilakukan: ..."

## Contoh Respons yang Harus Dihindari:
"Anak Anda sakit" (terlalu umum, tidak berdasarkan data)
```

**e. Strategi RAG: Instruksi di System Prompt, Dokumen di User Prompt**

Berdasarkan temuan pada Open WebUI Discussion #11088 , strategi yang lebih efektif untuk RAG adalah memisahkan instruksi penggunaan RAG di system prompt dan dokumen referensi di user prompt. Pendekatan ini meningkatkan efektivitas RAG dibandingkan menempatkan keduanya dalam system prompt.

**Template User Prompt untuk RAG (dengan dokumen):**

```plaintext
# Pertanyaan Pengguna:
{{QUERY}}

# Dokumen Referensi:
{{CONTEXT}}
```

**f. Prompt untuk Analisis Status Gizi Berdasarkan PDF**

Ketika pengguna mengunggah PDF laporan status gizi dan mengajukan pertanyaan, prompt yang direkomendasikan:

```plaintext
Saya telah mengunggah laporan status gizi anak saya. Berdasarkan data dalam laporan tersebut:

1. Apa status gizi anak saya secara keseluruhan?
2. Apa masalah gizi utama yang perlu diatasi?
3. Berikan 5 rekomendasi makanan spesifik yang dapat membantu memperbaiki status gizi anak.
4. Makanan apa yang sebaiknya dihindari?
5. Seberapa sering saya harus memantau pertumbuhan anak?

Mohon jawab berdasarkan data dalam laporan yang saya unggah, dan gunakan bahasa yang mudah dipahami oleh orang tua.
```

**g. Iterative Refinement dan Evaluasi Prompt**

Berdasarkan penelitian pada sistem konsultasi gizi , proses perbaikan prompt dilakukan secara iteratif melalui tahapan:

| **Tahap** | **Aktivitas** | **Indikator** |
|-----------|---------------|---------------|
| **1. Initial Design** | Membuat system prompt dan user prompt awal | Respons terlalu umum atau tidak spesifik |
| **2. Constraint Addition** | Menambahkan batasan seperti "Jangan tambahkan nilai yang tidak ada dalam data" | Model mulai merujuk pada data yang diberikan |
| **3. Restriction Redesign** | Mendesain ulang instruksi yang tidak efektif | Model mengikuti format yang diinginkan |
| **4. Transfer to Modelfile** | Memindahkan prompt ke Ollama Modelfile | Konsistensi respons antar sesi  |
| **5. Evaluation** | Menguji dengan kasus status gizi berbeda | Respons sesuai dengan kondisi masing-masing |

**h. Prompt yang Dioptimalkan untuk Open WebUI Modelfile**

Pada Open WebUI, system prompt dapat dikonfigurasi melalui pengaturan antarmuka. Berikut adalah prompt yang dioptimalkan untuk konsultasi gizi anak Indonesia:

```plaintext
Anda adalah ahli gizi dan spesialis tumbuh kembang anak untuk sistem "AIoT Sok!Anak". Tugas Anda adalah memberikan saran gizi yang berbasis ilmu pengetahuan, praktis, dan sesuai dengan budaya Indonesia untuk anak usia 0-5 tahun.

ATURAN PENTING:
1. DASARKAN semua saran pada data yang diberikan dalam pertanyaan atau dokumen yang diunggah.
2. Untuk analisis pertumbuhan anak, gunakan standar WHO (Z-score).
3. Berikan rekomendasi yang spesifik dan dapat ditindaklanjuti.
4. Gunakan bahasa Indonesia yang mudah dipahami orang tua.
5. JANGAN memberikan diagnosis medis. Sarankan konsultasi ke tenaga kesehatan.
6. Untuk kasus stunting, wasting, atau underweight berat, rekomendasikan konsultasi segera.
7. Sertakan saran kombinasi makanan dan jelaskan bagaimana rekomendasi sesuai dengan diet harian seimbang.

PROSES ANALISIS LANGKAH DEMI LANGKAH:
1. Identifikasi usia, jenis kelamin, berat, tinggi, dan IMT anak dari data yang diberikan.
2. Hitung/verifikasi Z-score untuk BB/U, TB/U, BB/TB, dan IMT/U.
3. Identifikasi masalah gizi (stunting, wasting, underweight, overweight, obesitas).
4. Berikan rekomendasi diet spesifik berdasarkan kondisi yang teridentifikasi.
5. Sarankan jadwal follow-up dan frekuensi pemantauan.
6. Berikan saran menu praktis dengan bahan makanan yang tersedia di Indonesia.

FORMAT RESPONS:
- Mulai dengan ringkasan status gizi anak.
- Berikan rekomendasi bernomor (minimal 3, maksimal 7).
- Sertakan contoh makanan spesifik yang terjangkau di pasar Indonesia.
- Akhiri dengan rencana tindakan untuk 1-2 minggu ke depan.
```

**i. Prompt untuk Kasus Status Gizi Spesifik**

**Stunting:**

```plaintext
Berdasarkan data yang diberikan, anak saya mengalami stunting (TB/U Z-score < -2). Saya ingin tahu:
1. Makanan apa yang paling efektif untuk membantu anak saya mengejar pertumbuhan?
2. Berapa frekuensi pemberian makanan yang direkomendasikan?
3. Apakah ada suplemen yang direkomendasikan?
4. Berapa lama waktu yang dibutuhkan untuk melihat perbaikan?

Mohon berikan saran spesifik dengan contoh menu sehari-hari.
```

**Wasting:**

```plaintext
Anak saya mengalami wasting (BB/TB Z-score < -2). Saya khawatir dengan berat badannya. Mohon bantu saya dengan:
1. Makanan tinggi energi dan protein yang mudah dibuat.
2. Cara meningkatkan nafsu makan anak.
3. Contoh menu untuk 3 hari ke depan.
4. Kapan saya harus membawa anak ke dokter?

Jawab dengan bahasa yang mudah dipahami dan saran yang praktis.
```

**j. Evaluasi Akurasi Prompt**

Berdasarkan penelitian pada ujian dietitian terdaftar , beberapa pendekatan prompt telah terbukti efektif untuk konsultasi gizi:

| **Strategi Prompt** | **Keunggulan** | **Kekurangan** |
|---------------------|----------------|----------------|
| **Zero-Shot** | Sederhana, cepat | Akurasi lebih rendah, respons kurang spesifik  |
| **Chain of Thought (CoT)** | Akurasi lebih tinggi, penalaran lebih baik | Memerlukan prompt lebih panjang  |
| **CoT-Self Consistency** | Akurasi dan konsistensi terbaik | Lebih lambat, memerlukan multiple generation  |
| **Retrieval Augmented Prompting (RAP)** | Sangat efektif untuk pertanyaan tingkat ahli | Membutuhkan dokumen referensi berkualitas  |

Sistem ini menggabungkan CoT (dalam system prompt) dan RAP (melalui upload PDF dan data status gizi) untuk memaksimalkan akurasi dan konsistensi respons.

**k. Validasi Respons AI oleh Tenaga Gizi**

Berdasarkan temuan penelitian , validasi respons AI oleh tenaga gizi profesional (Registered Dietitians) mengidentifikasi beberapa area perbaikan yang diimplementasikan dalam prompt:

| **Temuan** | **Implementasi dalam Prompt** |
|------------|-------------------------------|
| Kurang konteks edukasi tentang nilai gizi | Menambahkan instruksi untuk menjelaskan arti Z-score dan persentil  |
| Tingkat bacaan terlalu tinggi | Menambahkan instruksi "gunakan bahasa setara kelas 5-6 SD"  |
| Kurang kontekstualisasi dalam diet harian | Menambahkan instruksi "jelaskan bagaimana makanan cocok dalam diet harian"  |
| Saran tidak spesifik | Menambahkan instruksi "berikan contoh makanan spesifik yang tersedia di Indonesia" |
| Tidak ada alternatif makanan | Menambahkan instruksi "sertakan alternatif makanan untuk kondisi tertentu"  |

Secara keseluruhan, pendekatan prompt engineering yang diterapkan pada sistem konsultasi gizi menggabungkan praktik terbaik dari berbagai penelitian di bidang AI untuk nutrisi, dengan penyesuaian khusus untuk konteks posyandu Indonesia dan kebutuhan pengguna (kader dan orang tua).
