<?php
$user = "root";
$pass = "";
$apiKey = '14c95daccf4e2f330c3f8fc980d6c732';
$baseUrl = 'https://api.themoviedb.org/3';
$config = 'https://api.themoviedb.org/3/configuration';
$endpoint = '/movie/popular';
$byTitle = 'https://api.themoviedb.org/3/search/movie?api_key='.$apiKey.'&query=';
$db = new PDO("mysql:host=localhost:3308;dbname=cinema", $user, $pass);
$req = "*";
$pas = 50;
$pageNext = 0;
$cpt = 0;
$sql = "SELECT COUNT(*) FROM film";
$nbMovies = $db->prepare($sql);
$nbMovies->execute();

$nbFilms = $nbMovies ->fetch(PDO::FETCH_ASSOC);

//CHOIX GENRE :
$choix = "";
$i = 0;
if(isset($_POST['submit'])){
    // var_dump($_POST['submit']);die;
    if(isset($_POST['choixGenres'])){
        foreach ($_POST['choixGenres'] AS $toto)
        {
            $i++;
            if($i > 1){
                if($choix == ""){
                    $choix .= $toto;
                } else {
                    $choix .= "," .$toto;
                } 
            }
    
        }
        $sql = "SELECT f.id AS 'id', f.titre AS 'titre', f.resum AS 'resum', f.genre_id AS 'genre_id', g.nom AS 'categorie', d.nom AS 'distributeur' FROM film AS f LEFT JOIN genre AS g ON f.genre_id = g.id LEFT JOIN distrib AS d ON f.distrib_id = d.id WHERE f.genre_id IN ($choix) LIMIT 50";
        $req="genre";
    }
}elseif(isset($_POST['btnSearch'])){
    // var_dump($_POST['submit']);die;
    if(isset($_POST['search'])){
        $search = $_POST['search'];
        $sql = "SELECT f.id AS 'id', f.titre AS 'titre', f.resum AS 'resum', f.genre_id AS 'genre_id', g.nom AS 'categorie', d.nom AS 'distributeur' FROM film AS f LEFT JOIN genre AS g ON f.genre_id = g.id LEFT JOIN distrib AS d ON f.distrib_id = d.id WHERE f.titre LIKE :recherche LIMIT 50";
        $req="search";
    }
}else{

    if(empty($_GET) OR !isset($_GET['pageNext']) OR ($_GET['pageNext'] == '1')){
        $sql = "SELECT f.id AS 'id', f.titre AS 'titre', f.resum AS 'resum', f.genre_id AS 'genre_id', g.nom AS 'categorie', d.nom AS 'distributeur' FROM film AS f LEFT JOIN genre AS g ON f.genre_id = g.id LEFT JOIN distrib AS d ON f.distrib_id = d.id WHERE f.id BETWEEN 0 AND $pas";
        $pageNext = 1;
    }else{
        if((($_GET['pageNext'] - 1) * $pas) + 1 <= $nbFilms){
            $sql = "SELECT f.id AS 'id', f.titre AS 'titre', f.resum AS 'resum', f.genre_id AS 'genre_id', g.nom AS 'categorie', d.nom AS 'distributeur' FROM film AS f LEFT JOIN genre AS g ON f.genre_id = g.id LEFT JOIN distrib AS d ON f.distrib_id = d.id WHERE f.id BETWEEN ".(($_GET['pageNext'] - 1) * $pas) + 1 . " AND ".$_GET['pageNext'] * $pas;
            $pageNext = $_GET['pageNext'];
        }else{
            $sql = "SELECT f.id AS 'id', f.titre AS 'titre', f.resum AS 'resum', f.genre_id AS 'genre_id', g.nom AS 'categorie', d.nom AS 'distributeur' FROM film AS f LEFT JOIN genre AS g ON f.genre_id = g.id LEFT JOIN distrib AS d ON f.distrib_id = d.id WHERE f.id BETWEEN ".(($_GET['pageNext'] - 2) * $pas) + 1 . " AND ".($_GET['pageNext'] - 1) * $pas;
            $pageNext = $_GET['pageNext'] - 1;
        }
    }
}


//var_dump($sql); die;

$movies = $db->prepare($sql);


if($req == "*"){
    $movies->execute();
}elseif($req == "genre"){
    // var_dump($sql);die;
    $movies->execute();
}else{
    $movies->execute([
        'recherche' => '%'.$_POST['search'] .'%'
    ]);
}

$data = $movies ->fetchAll(PDO::FETCH_ASSOC);

$dbFilms = [];

foreach ($data as $donnee) {
    $cpt ++;
    $response = file_get_contents($byTitle.urlencode($donnee['titre']));
    if ($response !== false) {
        $retour = json_decode($response, true);

        if ($retour !== null) {
            if(count($retour['results']) > 0){
                $videos = $retour['results'][0];
                $dbFilms[] = [
                'id' => $donnee['id'],
                'titre' => $donnee['titre'],
                'overview' => $donnee['resum'],
                'genre' => $donnee['categorie'],
                'distributeur' => $donnee['distributeur'],
                'genre_id' => $donnee['genre_id'],
                'img' => $videos['backdrop_path']
                ];
            }else{
                $dbFilms[] = [
                    'id' => $donnee['id'],
                    'titre' => $donnee['titre'],
                    'overview' => $donnee['resum'],
                    'genre' => $donnee['categorie'],
                    'distributeur' => $donnee['distributeur'],
                    'genre_id' => $donnee['genre_id'],
                    'img' => "assets/error.jpg"
                ];
            }

        } else {
            echo "Erreur lors de la conversion de la réponse JSON.";
        }
    } else {
        echo "Erreur lors de la requête à l'API TMDb.";
    }
}

$url = "$config?api_key=$apiKey";
$response = file_get_contents($url);

if ($response !== false) {
    $data = json_decode($response, true);
    
    if ($data !== null) {
        $movies = $data['images'];
    
        foreach ($dbFilms as $film) {

            $oks[] = [
                'id' => $film['id'],
                'titre' => $film['titre'],
                'overview' => $film['overview'],
                'genre' => $film['genre'],
                'distributeur' => $film['distributeur'],
                'genre_id' => $film['genre_id'],
                'img' => $movies['secure_base_url'].$movies['backdrop_sizes'][1].$film['img']
            ];
        }

    } else {
        echo "Erreur lors de la conversion de la réponse JSON.";
    }
} else {
    echo "Erreur lors de la requête à l'API TMDb.";
}

include("genre.php");

include "views/home.php";