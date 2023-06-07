<?php

/*error_reporting(0);*/

include('vendor/autoload.php');

include('controllers/user.controller.php');
include('models/ConnexionBdd.php');

include('auth/login.php');

require 'auth/config.php';
require 'auth/user_auth.php';
include('models/user.model.php');

require 'vendor/autoload.php';

// Method received
$request_method = $_SERVER["REQUEST_METHOD"]; // GET / POST / PUT / DELETE

// Received data
$data = json_decode(file_get_contents('php://input'), true);
//print_r($data);

// Headers of the requests
$headers_tmp = apache_request_headers();
$headers = array();

// Remplissage des headers
foreach ($headers_tmp as $key => $value){
    $headers[$key] = $value;
    // Ajout des headers en minuscules
    $headers[strtolower($key)] = $value;
}

// URL requested
$url = isset($_GET['url']) && $_GET['url'] != "" ? $_GET['url'] : null;

// Debug
//print_r("URL =".$url."</br></br>");

// Explode the URL
$argv_tmp = explode("/", $url);
$argv = array();

// Fill up argv
foreach ($argv_tmp as $key => $value) {
    if(trim($value) != "")
    {
        $argv[] = $value;
    }
}

///////////////////////////////////////

//if (count($argv) > 0)
    /*Debug *///echo $argv[0]."<br>";
//else 
    /* Debug *///echo "Argv est vide donc on ne peut pas l'afficher<br>";

// Debug
//print_r($argv);

if (is_null($argv) || count($argv) == 0) {
    exit;
}

switch($argv[0]){
    case 'users':
        processUsers($request_method, $data, $headers, $argv);
        break;
    case 'login':
        processLoginRequest($request_method, $data, $headers, $argv);
        //checkLogin($data['username']);
        break;
    case 'createUser':

        break;
    
    
    
    //.....
}


function processUsers($method, $data, $headers, $argv){
    switch ($method){
        case 'GET':
            handle_GET_users($argv);
            /*
            if($argv[1] != '')
                getOneUser($argv[1]);
            else
                getAllUsers();
            */
        break;

        case 'POST':
            postNewUser();
        break;
        
        case 'PUT':
            updateUser($argv[1]);
        break;

        case 'DELETE':
            deleteUser($argv[1]);
        break;

    }

}

function getOneUser($id) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT * FROM users WHERE id='".$id."' LIMIT 1";
    
    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupération des données
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return json_encode($user);
}

function getAllUsers() {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = 'SELECT * FROM users';

    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupération des données
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo $users[0];
    return json_encode($users);
}

function postNewUser() {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    
    /*

        // Préparation de la requête SQL
            echo $query = "INSERT INTO users (username, `password`, mail, registrationDate)
                VALUES (:username, :pass, :mail, :registrationDate)";
                
            // Récupération des données depuis le payload
            echo $user = $payload['username'];
            echo $password = $payload['password'];
            echo $mail = $payload['mail'];
            echo $registrationDate = $payload['registrationDate'];
            
            // Exécution de la requête SQL
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':pass', $password);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':registrationDate', $registrationDate);
            $stmt->execute();
            
            // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
            $response = array('status' => 'success', 'message' => 'Le message a été inséré avec succès dans la base de données');
            echo json_encode($response);
            exit;


    */
    
}

function updateUser($id) {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    $query = '';
}

function deleteUser($id) {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    $query = '';
}

// handle = gerer (gestion des users / GET)
function handle_GET_users($argv) {
    if ( isset($argv[1]) && $argv[1] != '' && is_numeric($argv[1]) )
        getOneUser($argv[1]);
    else
        getAllUsers();
}

exit;

















include('controllers/controller.user.php');
include('models/ConnexionBdd.php');
    const SEPARATEUR_ENDPOINT = '/';
    // Récupere juste le endpoint 
    //(si en local ignore le dossier parent)
    $endpoint = $_GET['url'];
    
        $headers = getallheaders();
        $method = $_SERVER['REQUEST_METHOD'];
        $payload = json_decode(file_get_contents('php://input'),true);

    
    // Gérer les routes en comparant l'endpoint en fonction de son contenu 
    // Puis le diriger vers son controlleur 
    // Faire un singleton pour connecter les controlleurs à la bdd 
    // users/2
    
    // Sépare l'endpoint afin d'identifier ou l'on est 
    $urlExploded = explode(SEPARATEUR_ENDPOINT,$endpoint);

    switch($urlExploded[0]){
        case 'users':
            //echo "Vous êtes sur la partie utilisateur"; 
            // Ci-dessous urlExploded = mon endpoint;
            processUsersRequests($headers, $method, $urlExploded, $payload);
            break;
        case 'message':
            echo "Vous êtes sur la partie message";
            break;
        default;
            echo "Bonjour !";
            break;
    }

    exit;

    //print_r($_GET);

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $monUrl = "https"; 
    else
        $monUrl = "http";   
    
    // Ajoutez // à l'URL.
    $monUrl .= "://"; 
    
    // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
    $monUrl .= $_SERVER['HTTP_HOST']; 
    
    // Ajouter l'emplacement de la ressource demandée à l'URL
    $monUrl .= $_SERVER['REQUEST_URI']; 
      
    // Afficher l'URL
    //echo $monUrl; 

    echo("Hello World"); echo("<br/>");echo("<br/>");

    // Sert à envoyer une requete http avec les headers or je dois les récuperer
    //print_r(get_headers($monUrl)); echo("<br/>");
    //print_r(get_headers($monUrl, true));

    echo "Headers de la requête : \n";
    print_r($headers);
    echo("<br/>");echo("<br/>");
    echo "Méthode utilisée : $method"."<br/><br/>";

    echo "L'url est : $monUrl"."<br/><br/>"; 

    // Vérifie que la méthode de la requête est POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérez le corps de la requête
        $body = file_get_contents('php://input');
    
        $array = json_decode($body, true);
        // Affichez le corps de la requête
        echo strrev($array["message"]);
        echo "\n".$array["message"];
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        // Récupérez le corps de la requête GET
        $body = $_GET['message'];

        $array = json_decode($body, true);
        // Affichez le corps de la requête
        echo strrev($array["message"]);
        echo "\n".$array["message"];
    }

    echo "<br><br>".$endpoint;

    if($endpoint == "/Api/users")
    {
        echo("<br><br>"."Vous êtes sur la partie authentification utilisateur.");
    }


/*
?>

<!--
<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TP API REST</title>
    </head>
    <body>
        
    </body>
</html>
-->*/