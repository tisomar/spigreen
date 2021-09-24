<?php
$strIncludesKey = '';
$content = Config::get('popup.content');
$title = Config::get('popup.title');
include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox-page">
<header>
    <div class="container-fluid">
        <h1><?php echo $title ?></h1>
    </div>
</header>
<main role="main">
    <div class="container-fluid">
        <?php echo $content; ?>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>
</body>
</html>