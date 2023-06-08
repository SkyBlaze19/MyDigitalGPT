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
    // Ajout des headers en minuscules
    $headers[strtolower($key)] = $value;
}
//print_r($headers);

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
        processUsers($request_method, $data, $headers, $argv);
        break;
    case 'updateUser':
        processUsers($request_method, $data, $headers, $argv);
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
            postNewUser($data);
        break;
        
        case 'PUT':
            updateUser($argv[1]/*, $headers['']*/);
        break;

        case 'DELETE':
            deleteUser($argv[1]);
        break;

    }

}

function getOneUser($id) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT username, firstname, lastname, created_at, updated_at FROM users WHERE id='".$id."' LIMIT 1";
    
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

    $query = 'SELECT username, firstname, lastname, created_at, updated_at FROM users';

    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupération des données
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    /*
    for($i = 0; $i < count($users); $i++)
        echo implode(", ", $users[$i]) . "\n";
    */
    return json_encode($users);
}

function postNewUser($data) {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    $query = "INSERT INTO users (username, `password`, email, firstname, lastname, created_at, updated_at)
    VALUES (:user, :pass, :mail, :Fname, :Lname, :CreateDate, :UpdateDate)";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $currentDate = date('Y-m-d H:i:s');


    $stmt->bindParam(':user', $data['username']);
    $stmt->bindParam(':pass', $data['password']);
    $stmt->bindParam(':mail', $data['email']);
    $stmt->bindParam(':Fname', $data['firstname']);
    $stmt->bindParam(':Lname', $data['lastname']);
    $stmt->bindParam(':CreateDate', $currentDate);
    $stmt->bindParam(':UpdateDate', $currentDate);


    //Faire un test pour voir si le username est déjà existant, si c'est le cas -> message d'erreur
    //$userExist = check_if_user_already_exist($data['username']) ? $stmt->execute() : "L'utilisateur ".$data['username']." existe déjà.";
    $userExist = check_if_user_already_exist($data['username']);

    if($userExist) {
        echo "L'utilisateur ".$data['username']." existe déjà.";
        exit;
    }
    else 
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'Le message a été inséré avec succès dans la base de données');
    echo json_encode($response);
    exit;

        /*
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
        echo $monUser = getOneUser($argv[1]);
    else
        echo $mesUsers = getAllUsers();
        /*$mesUsers = getAllUsers();
        echo $mesUsers;
        */
}

function check_if_user_already_exist($username){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT username FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($user) {
        // Un utilisateur a été trouvé
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun utilisateur trouvé avec ce nom d'utilisateur
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }

}

// Cause une erreur // Je ne sais pas pourquoi // A utiliser si possible 
function connexionBDD() {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    return $conn;
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