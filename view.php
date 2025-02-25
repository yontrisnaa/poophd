<?php
// Fungsi untuk mengambil konten dari URL
function getContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

// Cek apakah ada parameter di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Artikel tidak ditemukan.");
}

// Ambil ID dari URL
$id = htmlspecialchars($_GET['id']);

// URL target untuk scraping
$targetUrl = "https://serversaya.web.id/d/" . $id;

// Ambil HTML dari halaman target
$html = getContent($targetUrl);

// Load HTML ke DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

// Gunakan DOMXPath untuk mencari elemen yang dibutuhkan
$xpath = new DOMXPath($dom);

// Ambil elemen <title>
$titleElement = $xpath->query('//title');
$pageTitle = ($titleElement->length > 0) ? trim($titleElement->item(0)->textContent) : "Tanpa Judul";

// Ambil elemen <iframe>
$iframeElement = $xpath->query('//iframe[contains(@class, "w-100")]');
$videoSrc = ($iframeElement->length > 0) ? $iframeElement->item(0)->getAttribute('src') : null;

// Jika tidak ada iframe, tampilkan error
if (!$videoSrc) {
    die("Video tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #000; /* Sama dengan index.php */
            color: #ff8000; /* Warna teks orange */
        }
        .video-container {
            max-width: 800px;
            margin: auto;
            background: #111; /* Warna background box */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(255, 128, 0, 0.5);
        }
        .video-title {
            font-size: 24px;
            font-weight: bold;
            color: #ff8000; /* Warna judul */
            text-align: center;
            margin-bottom: 15px;
        }
        .video-description {
            font-size: 16px;
            text-align: center;
            margin-top: 15px;
            color: #ddd; /* Warna teks deskripsi */
        }
        .back-button {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .btn-back {
            background: #ff8000; /* Warna button */
            border: none;
            color: black;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-back:hover {
            background: #cc6600;
        }
    </style>
    <script type='text/javascript' src='//pl25515013.effectiveratecpm.com/f8/cd/eb/f8cdeb08f15feba85dd67c3d3eb2a658.js'></script>
    <script type='text/javascript' src='//pl25956323.effectiveratecpm.com/cc/5d/c3/cc5dc39b354c7717d443f7ed621be6e5.js'></script>
</head>
<body>

<div class="container mt-5">
    <div class="video-container">
        <!-- Judul Video -->
        <h1 class="video-title"><?= htmlspecialchars($pageTitle) ?></h1>

        <!-- Pemutar Video -->
        <div class="ratio ratio-16x9">
            <iframe src="<?= htmlspecialchars($videoSrc) ?>" frameborder="0" allowfullscreen></iframe>
        </div>

        <!-- Keterangan Video -->
        <p class="video-description">
            Doods.my.id menyediakan berbagai macam video viral doodstream telegram terlengkap di update setiap harinya gratis tanpa vpn.
        </p>

        <!-- Tombol Kembali -->
        <div class="back-button">
            <a href="/" class="btn-back">⬅ Kembali ke Beranda</a>
        </div>
    </div>
</div>

</body>
   <!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,4931602,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?4931602&101" alt="" border="0"></a></noscript>
<!-- Histats.com  END  -->
</html>
