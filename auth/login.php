<?php

function checkLogin($username, $password) {
    // Créer une instance de la classe d'authentification
    $authentication = new Authentication();

    // Debug
    /*
    print_r($authentication);
    echo $username;
    echo "<br>passWD : ".$password;
    */

    $user = UserModel::verifyCredentials($username, $password);

    // Vérifier les identifiants d'authentification
    if ($user != false) {
        // Générer le token d'accès
        $token = $authentication->generateToken($user);

        // Envoyer la réponse avec le token
        echo json_encode(['token' => $token]);
    } 
    else {
        // Identifiants invalides, renvoyer une réponse d'erreur
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Invalid credentials']);
    }
}



function processLoginRequest ($request_method, $data, $headers, $argv) {
    //echo count($argv);
    if(count($argv) > 1)
    {
        echo "D'autres paramètres ont été fournis et ne sont pas attendus. Fin du programme !";
        return;
    }

    if($request_method != 'POST')
    {
        echo "Le verbe utilisé pour cette requête n'est pas le bon. Fin du programme !";
        return;
    }

    checkLogin($data['username'], $data['password']);
        
}