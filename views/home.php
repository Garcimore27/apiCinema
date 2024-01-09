<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include_once('header.php'); ?>
    
    <div class="grid grid-cols-4 gap-8 p-8">
        <?php foreach($oks as $ok) : ?>
            <div class="border-2 border-black p-2 flex flex-col">
                <h2>id: <?= $ok['id'] ?> - <span class="font-bold text-blue-800"><?= $ok['genre'] ?> (<?= $ok['genre_id'] ?>) - <?= $ok['titre'] ?></span></h2>
                <h2>Distributeur: <?= $ok['distributeur'] ?></h2>
                <hr>
                <br>
                <img class="w-full object-cover" src=<?= $ok['img'] ?>>
                <br>
                <p class="justify-center"><?= $ok['overview'] ?></p>
                <!--<a href="/.php?id=</*?= $product['_id'] ?>" class="text-center">Ajouter au panier</a>-->
            </div>
        <?php endforeach ?>
    </div>
    <nav class="inline-flex space-x-4">
        <a class="flex items-center py-2 px-3 rounded font-medium select-none border text-white bg-black transition-colors hover:border-blue-600 hover:bg-blue-400 hover:text-white"
            href="/?pageNext=<?= ($pageNext - 1) > 0 ? $pageNext - 1 : $pageNext ?>">
            ⪻
            Previous
        </a>
        <a class="flex items-center py-2 px-3 rounded font-medium select-none border text-white bg-black transition-colors hover:border-blue-600 hover:bg-blue-400 hover:text-white"
            href="/?pageNext=<?= $pageNext + 1 ?>" class="text-center">
            Next
            ⪼
        </a>
    <br>
    </nav>
</div>
    <!-- Main js file -->
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <script type="module" src="/src/js/index.js"></script>
    <!-- Custom scripts -->
    <script type="text/javascript"></script>
</body>
</html>