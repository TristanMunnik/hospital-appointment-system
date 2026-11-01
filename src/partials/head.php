<?php
// Shared document head for every page.
// Set $pageTitle before including, and optionally $scripts (file names inside assets/js/).
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <?php foreach ($scripts ?? [] as $script): ?>
        <script src="<?= BASE_URL ?>/assets/js/<?= e($script) ?>" defer></script>
    <?php endforeach; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
