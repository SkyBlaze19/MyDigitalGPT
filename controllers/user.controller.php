<?php 

// Classe/fonction php de controller qui gère les interactions de l'utilisateur

function processUsersRequests($headers, $method, $urlExploded, $payload){
    
    
    $database = Database::getInstance();
    $conn = $database->getConnection();

    // identifier la methode -> pour faire les actions en conséquence
    // Si post fais de l'add en bdd, si put fait update

    //switch 
    //case en fonction de la methode
    switch ($method) {
        case 'GET':
            // Préparation de la requête SQL
            $query = "SELECT * FROM users";

            // Exécution de la requête SQL
            $stmt = $conn->prepare($query);
            $stmt->execute();

            // Récupération des données
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Encodage des données en JSON pour l'envoi au client
            echo json_encode($users);
        break;
            
        case 'POST':
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
            
        break;
            

        case 'PUT':
            // Non fini
            //$query = "UPDATE users (user, password, mail, registrationDate) VALUES (?, ?, ?, ?)";
        break;

        case 'DELETE':
            
        break;
    }

}