<?php
// conversation.model.php

class ConversationModel {

    private int $id;
    private int $character_id;
    private int $user_id;
    private DateTime $created_at; //transformer en datetime
    private DateTime $updated_at;
    private int $number_of_messages;

    public function __construct($id, $character_id, $user_id, $created_at, $updated_at, $number_of_messages)
    {
        $this->setId($id);
        $this->setCharacter_id($character_id);
        $this->setUser_id($user_id);
        $this->setCreated_at($created_at);
        $this->setUpdated_at($updated_at);
        $this->setNumber_of_messages($number_of_messages);
    }

    // - Créer une conversation
    private function postNewConversation($data, $headers)
    {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $auth = new Authentication();

        $query = "INSERT INTO conversations (`conversation_name`, character_id, user_id ,created_at, updated_at, number_of_messages)
        VALUES (:convName, :persoId, :userId, :CreateDate, :UpdateDate, :numOfMessages)";

        $stmt = $conn->prepare($query);

        //Créa de la date actuelle pour insertion en bdd
        $currentDate = date('Y-m-d H:i:s');

        $token = $headers['authorization'];
        $token = explode(" ", $token);

        $userToken = $auth->decodeToken($token[1]);
        $userToken = json_decode($userToken->data, true);

        $stmt->bindParam(':convName', $data['conversation_name']);
        $stmt->bindParam(':persoId', $data['character_id']);
        $stmt->bindParam(':userId', $userToken['id']);
        $stmt->bindParam(':CreateDate', $currentDate);
        $stmt->bindParam(':UpdateDate', $currentDate);
        $stmt->bindParam(':numOfMessages', 0);

        $conversationExist = checkConversationDoublon($data['conversation_name'], $userToken['id']);

        if($conversationExist) {
            echo "Vous avez déjà créer une conversation du nom de : ".$data['conversation_name']." !";
            exit;
        }
        else 
            $stmt->execute();
        
        
        // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
        $response = array('status' => 'success', 'message' => 'L\'univers a été inséré avec succès dans la base de données');
        echo json_encode($response); 
    }


    // - Supprimer une conversation
    // - Obtenir la liste des conversations d'un utilisateur, etc.

    // Méthode pour récupérer les messages d'une conversation spécifique
    private function getMessagesInConversation($conversationId) {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $query = "SELECT * FROM messages WHERE conversation_id = :conversationId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':conversationId', $conversationId);

        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function showMessages($conversationId) {
        return $this->getMessagesInConversation($conversationId);
    }



    // Méthode pour ajouter un message à une conversation
    private function addMessageToConversation($conversationId, $userId, $message) {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $query = "INSERT INTO messages (conversation_id, user_id, message, created_at) VALUES (:conversationId, :userId, :message, NOW())";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':conversationId', $conversationId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':message', $message);
        $stmt->execute();

        // Pour la confirmation lors des tests, 
        // à commenter une fois validé car on ne souhaite pas avoir un message de confirmation à chaque message ajouté dans la conversation
        $response = array('status' => 'success', 'message' => 'Le message a bien été ajouté dans la conversation');
        echo json_encode($response); 
    }

    public function addMessage($conversationId, $userId, $message) {
        $this->addMessageToConversation($conversationId, $userId, $message);
    }



    private function check_if_conversationName_doublon($name, $creatorId) {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $query = "SELECT `conversation_name` FROM conversations WHERE `conversation_name` = :name AND `user_id` = :userId";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':userId', $creatorId);

        $stmt->execute();

        $convExits = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

        if ($convExits) {
            // Un conversation possède ce nom
            return true;
        } else {
            // Aucune conversation avec ce nom
            return false;
        }
    }

    public function checkConversationDoublon($name, $creatorId) {
        $this->check_if_conversationName_doublon($name, $creatorId);
    }   

    




    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of character_id
     */ 
    public function getCharacter_id()
    {
        return $this->character_id;
    }

    /**
     * Set the value of character_id
     *
     * @return  self
     */ 
    public function setCharacter_id($character_id)
    {
        $this->character_id = $character_id;

        return $this;
    }

    /**
     * Get the value of user_id
     */ 
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */ 
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of number_of_messages
     */ 
    public function getNumber_of_messages()
    {
        return $this->number_of_messages;
    }

    /**
     * Set the value of number_of_messages
     *
     * @return  self
     */ 
    public function setNumber_of_messages($number_of_messages)
    {
        $this->number_of_messages = $number_of_messages;

        return $this;
    }
}



