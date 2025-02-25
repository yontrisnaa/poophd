
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

// URL sumber scraping
$sourceUrl = "https://serversaya.web.id";

// Ambil halaman target
$html = getContent($sourceUrl);

// Load HTML ke DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

// Gunakan XPath untuk mencari elemen yang dibutuhkan
$xpath = new DOMXPath($dom);
$articles = $xpath->query('//div[contains(@class, "col-6 col-md-4 mb-4")]');

// Konversi hasil ke array
$articleList = [];
foreach ($articles as $article) {
    $aTag = $article->getElementsByTagName("a")->item(0);
    $imgTag = $article->getElementsByTagName("img")->item(0);
    $titleTag = $article->getElementsByTagName("strong")->item(0);

    if ($aTag && $imgTag && $titleTag) {
        $articleList[] = [
            'url' => $aTag->getAttribute("href"),
            'image' => $imgTag->getAttribute("src"),
            'title' => trim($titleTag->textContent)
        ];
    }
}

// Pagination
$perPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil(count($articleList) / $perPage);
$offset = ($page - 1) * $perPage;
$articlesToShow = array_slice($articleList, $offset, $perPage);

// Pencarian Artikel
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $filteredArticles = array_filter($articleList, function ($article) use ($search) {
        return stripos($article['title'], $search) !== false;
    });
    $articlesToShow = array_values($filteredArticles);
    $totalPages = 1;
}

?>

    <?php include 'head.php'; ?>
    <title>Jam Malam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #ff8000;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            background: #111;
            color: #ff8000;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(255, 128, 0, 0.5);
            transition: transform 0.3s ease;
        }
        .card img {
            border-radius: 10px;
            object-fit: cover;
            height: 180px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #ff8000;
            text-align: center;
        }
        .pagination .page-link {
            background: #ff8000;
            border: none;
            color: black;
        }
        .pagination .page-link:hover {
            background: #cc6600;
            color: black;
        }
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5">


    <!-- Form Pencarian -->
    <form method="GET" action="" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari artikel..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-warning" type="submit">Cari</button>
        </div>
    </form>

    <div class="row">
        <?php if (count($articlesToShow) > 0): ?>
            <?php foreach ($articlesToShow as $article): ?>
                <div class="col-6 col-md-4 mb-4">
                    <div class="card p-3">
                        <a href="/d/<?= basename($article['url']) ?>" class="text-white">
                            <img src="<?= htmlspecialchars($article['image']) ?>" class="w-100">
                        </a>
                        <a href="/d/<?= basename($article['url']) ?>" class="title mt-2 d-block">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Artikel tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($search == '' && $totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                if ($start > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($start > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

</body>
    <?php include 'foot.php'; ?>
