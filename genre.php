<?php
$user = "root";
$pass = "";
$db = new PDO("mysql:host=localhost:3308;dbname=cinema", $user, $pass);

$sql = "SELECT * FROM genre ORDER BY nom";
$request = $db->prepare($sql);
$request->execute();

$filmsGenres = $request ->fetchAll(PDO::FETCH_ASSOC);

$dbGenres = [];
foreach($filmsGenres as $filmsGenre){
    $dbGenres[] = [
        'id' => $filmsGenre['id'],
        'genre' => $filmsGenre['nom']
    ];
}

