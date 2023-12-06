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
    case 'universes':
        processUniverses($request_method, $data, $headers, $argv);
        break;
    
    
    //.....
}

//Users
function processUsers($method, $data, $headers, $argv){
    switch ($method){
        case 'GET':
            handle_GET_users($argv);
        break;

        case 'POST':
            postNewUser($data);
        break;
        
        case 'PUT':
            updateUser($argv[1], $data, $headers);
        break;

        case 'DELETE':
            deleteUser($argv[1], $headers);
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
    $userExist = check_if_user_doublon($data['username'], $data['email']);

    if($userExist == 1) {
        echo "L'utilisateur ".$data['username']." existe déjà et l'email ".$data['email']." est déjà prise aussi !";
        exit;
    }
    else if($userExist == 2)
    {
        echo "L'utilisateur ".$data['username']." existe déjà.";
        exit;
    }
    else if($userExist == 3)
    {
        echo "L'email ".$data['email']." est déjà prise.";
        exit;
    }
    else 
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'L\'utilisateur a été inséré avec succès dans la base de données');
    echo json_encode($response);
    exit;

        /*
            // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
            $response = array('status' => 'success', 'message' => 'Le message a été inséré avec succès dans la base de données');
            echo json_encode($response);
            exit;


    */
    
}

function updateUser($id, $data, $headers) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();

    $query = "UPDATE users SET username = :user, `password` = :pass, email = :mail, firstname = :Fname, lastname = :Lname, updated_at = :UpdateDate 
    WHERE id = :id";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $updateDate = date('Y-m-d H:i:s');

    $stmt->bindParam(':user', $data['username']);
    $stmt->bindParam(':pass', $data['password']);
    $stmt->bindParam(':mail', $data['email']);
    $stmt->bindParam(':Fname', $data['firstname']);
    $stmt->bindParam(':Lname', $data['lastname']);
    $stmt->bindParam(':UpdateDate', $updateDate);
    $stmt->bindParam(':id', $id);


    $token = $headers['authorization'];
    $token = explode(" ", $token);
    //var_dump($token);

    //echo "\n\nrecup token ? :".$token."\n\n";
    $userToken = $auth->decodeToken($token[1]);
    //print_r("\n\nToken null ? : ".var_dump($userToken)."\n\n");
    $userToken = json_decode($userToken->data, true);
    //print_r("\n\nUser Token = : ".var_dump($userToken)."\n\n");
    //var_dump($userToken);

    //echo "\n\n Id : ".$id."\n\n";
    //echo $userToken['id']."\n\n";

    if($userToken['id'] == $id)  
    {
        $stmt->execute();
        echo "Utilisateur mis à jour !";
    }
    else {
        echo "Vous n'avez pas la permission de modifier cet utilisateur";
        exit;
    }
}

function deleteUser($id, $headers) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();


    $query = "DELETE FROM users WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);


    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    if(check_if_user_already_exist($userToken['username']) === false)
    {
        echo "L'utilisateur que vous souhaitez supprimer n'existe pas !";
        exit;
    }

    if($userToken['id'] == $id)  
    {
        $stmt->execute();
        echo "Utilisateur supprimé avec succès !";
    }
    else {
        echo "Vous n'avez pas la permission de supprimer cet utilisateur";
        exit;
    }
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

// Fonction qui sert à controler mon post 
function check_if_user_doublon($username, $mail){
    if (check_if_mail_already_used($mail) && check_if_user_already_exist($username)) 
    {
        // User déjà utilisé
        // Email déjà utilisé
        return 1;
    } else if (check_if_user_already_exist($username))
    {
        // User déjà utilisé
       return 2;
    }  else if (check_if_mail_already_used($mail))
    {    
        // Email déjà utilisé
        return 3;
    }
    else
    {
        // Pas de doublons
        return false;    
    }
}

function check_if_mail_already_used($email){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT email FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($user) {
        // Un user possède cet email
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun utilisateur trouvé avec cet email
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
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
        // Un user possède cet email
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun utilisateur trouvé avec cet email
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
}

//Universe
function processUniverses($method, $data, $headers, $argv) {
    switch ($method){
        case 'GET':
            if(count($argv) > 2)
                handle_GET_characters($argv);
            else
                handle_GET_universes($argv);
        break;

        case 'POST':
            if(count($argv) > 2 && $argv[1] != '' && is_numeric($argv[1]))
                postNewCharacter($data, $headers, $argv[1]);
            else
                postNewUniverse($data, $headers);
        break;
        
        case 'PUT':
            if(count($argv) > 2 && $argv[1] != '' && is_numeric($argv[1]) && is_numeric($argv[3]))
                updateCharacter($argv[1], $data, $headers, $argv[3]);
            else if (count($argv) <= 2 && $argv[1] != '' && is_numeric($argv[1]))
                updateUniverse($argv[1], $data, $headers);
            else if (count($argv) > 2 && $argv[1] != '' && $argv[3] != '')
            {
                if(is_numeric($argv[1]))
                    echo "L'identifiant du personnage est incorrect, un chiffre est attendu.";
                else if (is_numeric($argv[3]))
                    echo "L'identifiant de l'univers est incorrect, un chiffre est attendu."; //Message de débug
                else 
                    echo "L'identifiant de l'univers et du personnage sont incorrect, des chiffres sont attendus."; //Message de débug       
            }
        break;

        case 'DELETE':
            if (count($argv) > 2 && $argv[1] != '' && is_numeric($argv[1]) && is_numeric($argv[3])) //Si la route contient plus de 2 elements, si le 2 n'est pas vide et est un chiffre, et si le 4 ème l'est aussi
                deleteCharacter($argv[1], $headers, $argv[3]);
            else if (count($argv) <= 2 && $argv[1] != '' && is_numeric($argv[1])) // Si la route contient 2 éléments ou moins et que le 2ème n'est pas vide et est un chiffre
                deleteUniverse($argv[1], $headers);
                else if (count($argv) > 2 && $argv[1] != '' && $argv[3] != '')
                {
                    if(is_numeric($argv[1]))
                        echo "L'identifiant du personnage est incorrect, un chiffre est attendu.";
                    else if (is_numeric($argv[3]))
                        echo "L'identifiant de l'univers est incorrect, un chiffre est attendu."; //Message de débug
                    else 
                        echo "L'identifiant de l'univers et du personnage sont incorrect, des chiffres sont attendus."; //Message de débug       
                }
        break;

    }
}

function handle_GET_universes($argv) {
    if ( isset($argv[1]) && $argv[1] != '' && is_numeric($argv[1]) )
        echo $monUnivers = getOneUniverse($argv[1]);
    else
        echo $mesUnivers = getAllUniverses();
        /*$mesUsers = getAllUsers();
        echo $mesUsers;
        */
}

function getOneUniverse($id) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `name`, creator_id, created_at, updated_at FROM universes WHERE id='".$id."' LIMIT 1";
    
    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupération des données
    $universe = $stmt->fetch(PDO::FETCH_ASSOC);
    if($universe === false)
        echo "Cet univers n'existe pas !";
    else
        return json_encode($universe);
}

function getAllUniverses() {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = 'SELECT `name`, creator_id, created_at, updated_at FROM universes';

    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupération des données
    $universes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    /*
    for($i = 0; $i < count($users); $i++)
        echo implode(", ", $users[$i]) . "\n";
    */
    return json_encode($universes);
}

function postNewUniverse($data, $headers) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();
    
    $query = "INSERT INTO universes (`name`, creator_id, created_at, updated_at)
    VALUES (:name, :creatorId, :CreateDate, :UpdateDate)";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $currentDate = date('Y-m-d H:i:s');

    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':creatorId', $userToken['id']);
    $stmt->bindParam(':CreateDate', $currentDate);
    $stmt->bindParam(':UpdateDate', $currentDate);


    //Faire un test pour voir si l'univers est déjà existant, si c'est le cas -> message d'erreur
    $universeExist = check_if_universe_doublon($data['name']);

    if($universeExist) {
        echo "L'univers ".$data['name']." existe déjà !";
        exit;
    }
    else 
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'L\'univers a été inséré avec succès dans la base de données');
    echo json_encode($response); 
}

function check_if_universe_doublon($name){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `name` FROM universes WHERE `name` = :name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    $universe = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($universe) {
        // Un univers possède ce nom
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun univers avec ce nom
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
}

function check_if_universe_exist($universeId){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `name` FROM universes WHERE `id` = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $universeId);
    $stmt->execute();

    $universe = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($universe) {
        // Un univers possède cet id
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun univers avec cet id
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
}

function updateUniverse($id, $data, $headers) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();
    
    $query = "UPDATE universes SET `name` = :name, updated_at = :UpdateDate WHERE id = :id";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $updateDate = date('Y-m-d H:i:s');

    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':UpdateDate', $updateDate);
    $stmt->bindParam(':id', $id);


    //Faire un test pour voir si l'univers est déjà existant, si c'est le cas -> message d'erreur
    $universeExist = check_if_universe_doublon($data['name']);
    
    if($universeExist) {
        echo "L'univers ".$data['name']." existe déjà !";
        exit;
    }
    else if(owns_this_universe($userToken['id'], $id) === false)
    {
        echo "L'univers que vous souhaitez modifier ne vous appartient pas !";
        exit;
    }
    else
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'L\'univers a été mis à jour avec succès.');
    echo json_encode($response); 
}

function deleteUniverse($universeID, $headers) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();

    $query = "DELETE FROM universes WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $universeID);


    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    if(check_if_universe_exist($universeID) === false)
    {
        echo "L'univers que vous souhaitez supprimer n'existe pas !";
        exit;
    }

    if(owns_this_universe($userToken['id'], $universeID))  
    {
        $stmt->execute();
        echo "Univers supprimé avec succès !";
    }
    else {
        echo "Vous n'avez pas la permission de supprimer cet univers";
        exit;
    }
}


function owns_this_universe($userID, $universeID/*, $headers*/) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();

    $query = "SELECT creator_id FROM universes WHERE `id` = :universeID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':universeID', $universeID);
    $stmt->execute();

    $universe = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'univers

    //var_dump($universe);
    if($universe['creator_id'] != $userID){
        return false;
    }
    else{
        return true;
    }
}


//Characters
function handle_GET_characters($argv) {
    if ( isset($argv[3]) && $argv[3] != '' && is_numeric($argv[3]) )
        echo $monCharacter = getOneCharacter($argv[3], $argv[1]);
    else
        echo $mesCharacters = getAllCharacters($argv[1]);
        /*$mesUsers = getAllUsers();
        echo $mesUsers;
        */
}

function getOneCharacter($id, $universeID) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `name`, `description`, universe_id, creator_id, created_at, updated_at, number_in_universe FROM `character` WHERE id= :id AND universe_id= :univID LIMIT 1";
    $composedID = $universeID.'-'.$id;
    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':univID', $universeID);
    $stmt->bindParam(':id', $composedID);
    $stmt->execute();

    // Récupération des données
    $character = $stmt->fetch(PDO::FETCH_ASSOC);
    if($character === false)
        echo "Ce personnage n'existe pas !";
    else if (check_if_universe_exist($universeID) === false)
        echo "L'univers n'existe pas.";
    else
        return json_encode($character);
}

function getAllCharacters($universeID) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = 'SELECT `name`, `description`, universe_id, creator_id, created_at, updated_at, number_in_universe FROM `character` WHERE universe_id = :univID';

    // Exécution de la requête SQL
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':univID', $universeID);
    $stmt->execute();

    // Récupération des données
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($characters == false)
        echo "Aucun personnage crée !";
    else if (check_if_universe_exist($universeID) === false)
        echo "L'univers n'existe pas.";
    else
        return json_encode($characters);
}

function postNewCharacter($data, $headers, $univID) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();
    

    $query = "INSERT INTO `character` (`name`, `description`, universe_id, creator_id, created_at, updated_at, number_in_universe)
    VALUES (:name, :desc, :univID, :creatorId, :CreateDate, :UpdateDate, :numberInUniverse)";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $currentDate = date('Y-m-d H:i:s');

    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    // Identifie le nombre de personnage dans l'univers actuel
    $nbPersoExistant_query = "SELECT COUNT(*) AS nb_perso FROM `character` WHERE universe_id = $univID";
    $stmtNbPerso = $conn->prepare($nbPersoExistant_query);
    $stmtNbPerso->execute();

    $nbPerso = $stmtNbPerso->fetch(PDO::FETCH_ASSOC);

    //Débug var_dump($nbPerso);
    $newPersoNumber = $nbPerso['nb_perso'] + 1;
    //Débug var_dump($newPersoNumber);
    
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':desc', $data['description']);
    $stmt->bindParam(':univID', $univID);
    $stmt->bindParam(':creatorId', $userToken['id']);
    $stmt->bindParam(':CreateDate', $currentDate);
    $stmt->bindParam(':UpdateDate', $currentDate);
    $stmt->bindParam(':numberInUniverse', $newPersoNumber);


    //Faire un test pour voir si l'univers est déjà existant, si c'est le cas -> message d'erreur
    $characterExist = check_if_character_doublon($data['name']);
    $isYourUniverse = owns_this_universe($userToken['id'] ,$univID);

    if($characterExist) {
        echo "Le personnage ".$data['name']." existe déjà !";
        exit;
    }
    if($isYourUniverse != true){
        echo "Ce n'est pas votre univers, vous ne pouvez pas y créer de personnages.";
        exit;
    }
    else 
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'Le personnage a été crée avec succès');
    echo json_encode($response); 
}

function check_if_character_doublon($name){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `name` FROM `character` WHERE `name` = :name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    $character = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($character) {
        // Un personnage possède ce nom
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun personnage avec ce nom
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
}

function updateCharacter($universeID, $data, $headers, $characterID) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();
    
    $query = "UPDATE `character` SET `name` = :name, `description` = :desc, updated_at = :UpdateDate WHERE id = :id";

    $stmt = $conn->prepare($query);

    //Créa de la date actuelle pour insertion en bdd
    $updateDate = date('Y-m-d H:i:s');

    //$composedID = $universeID.'-'.$characterID;

    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':desc', $data['description']);
    $stmt->bindParam(':UpdateDate', $updateDate);
    $stmt->bindParam(':id', $characterID);


    //Faire un test pour voir si l'univers est déjà existant, si c'est le cas -> message d'erreur
    if(owns_this_universe($userToken['id'], $universeID) === false)
    {
        echo "Le personnage que vous souhaitez modifier ne vous appartient pas !";
        exit;
    }
    else if (check_if_character_exist($characterID) === false)
    {
        echo "Le personnage que vous souhaitez mettre à jour n'existe pas !";
        exit;
    }
    else if (check_if_character_doublon($data['name']))
    {
        echo "Le personnage ".$data['name']." existe déjà.";
        exit;
    }
    else
        $stmt->execute();
    
    
    // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'Le personnage a été mis à jour avec succès.');
    echo json_encode($response); 
}

function deleteCharacter($universeID, $headers, $characterID) {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $auth = new Authentication();

    $query = "DELETE FROM `character` WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $characterID);


    $token = $headers['authorization'];
    $token = explode(" ", $token);

    $userToken = $auth->decodeToken($token[1]);
    $userToken = json_decode($userToken->data, true);

    if(owns_this_universe($userToken['id'], $universeID) === false)
    {
        echo "Le personnage que vous souhaitez supprimer ne vous appartient pas !";
        exit;
    }
    else
        // Récupère le numéro du personnage que l'on va supprimer
        $numDuPersoSupp_query = "SELECT number_in_universe FROM `character` WHERE universe_id = $universeID AND id = $characterID";
        $stmtNumPerso = $conn->prepare($numDuPersoSupp_query);
        $stmtNumPerso->execute();

        $numPersoSuppResult = $stmtNumPerso->fetch(PDO::FETCH_ASSOC);
        var_dump($numPersoSuppResult);
        $numPersoSupp = $numPersoSuppResult['number_in_universe'];


        $stmt->execute();

        
        $updateOtherCharacterQuery = "
            UPDATE `character` SET `number_in_universe` = `number_in_universe` - 1 WHERE universe_id = $universeID AND `number_in_universe` > $numPersoSupp
        ";
        $stmtUpdateOthers = $conn->prepare($updateOtherCharacterQuery);
        $stmtUpdateOthers->execute();

    // Renvoi d'une réponse pour confirmer que la suppression a été effectuée avec succès
    $response = array('status' => 'success', 'message' => 'Le personnage a été supprimé avec succès.');
    echo json_encode($response);
}

function check_if_character_exist($id){
    $database = Database::getInstance();
    $conn = $database->getConnection();

    $query = "SELECT `id` FROM `character` WHERE `id` = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $character = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

    if ($character) {
        // Un personnage possède cet id
        // Faites quelque chose avec les données de l'utilisateur
        return true;
        // Faites ce que vous devez faire avec l'utilisateur trouvé
    } else {
        // Aucun personnage avec cet id
        // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
        return false;
    }
}



/* Utile si on utilise PATCH
function handle_UPDATE_query($query, $data, $id){
    $setStatements = array();
    foreach ($data as $field => $value) {
    // Vérifiez si le champ doit être exclu ou s'il n'y a pas de nouvelle valeur fournie
    if ($field === 'created_at' || $value === null) {
        continue; // Passe à la prochaine itération de la boucle
    }

    // Ajoute le fragment de mise à jour pour ce champ
    $setStatements[] = $field . " = '" . $value . "'";
}

// Vérifiez si au moins un champ doit être mis à jour
if (count($setStatements) > 0) {
    // Ajoute les fragments de mise à jour à la requête
    $query .= ' ' . implode(', ', $setStatements);

    // Ajoutez la condition pour identifier la ligne à mettre à jour, par exemple :
    $query .= ' WHERE id = '.$id;

    // Exécutez la requête SQL ici
    // ...
} else {
    // Aucun champ à mettre à jour, vous pouvez gérer cette situation en conséquence
    // ...
}

}

*/

// Cause une erreur // Je ne sais pas pourquoi // A utiliser si possible 
function connexionBDD() {
    $database = Database::getInstance();
    $conn = $database->getConnection();

    return $conn;
}

exit;














