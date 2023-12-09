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

        $conversationExist = $this->checkConversationDoublon($data['conversation_name'], $userToken['id']);

        if($conversationExist) {
            echo "Vous avez déjà créer une conversation du nom de : ".$data['conversation_name']." !";
            exit;
        }
        else 
            $stmt->execute();
        
        
        // Renvoi d'une réponse pour confirmer que l'insertion a été effectuée avec succès
        $response = array('status' => 'success', 'message' => 'La conversation a été crée avec succès !');
        echo json_encode($response); 
    }

    public function createNewConversation($data, $headers) {
        return $this->postNewConversation($data, $headers);
    }


    
    // - Supprimer une conversation
    private function deleteConversation($conversationId, $headers) {
        $database = Database::getInstance();
        $conn = $database->getConnection();
    
        $auth = new Authentication();
    
        $query = "DELETE FROM conversations WHERE id = :id";
    
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $conversationId);
    
    
        $token = $headers['authorization'];
        $token = explode(" ", $token);
    
        $userToken = $auth->decodeToken($token[1]);
        $userToken = json_decode($userToken->data, true);
    
        
        if($this->does_conversation_exist($conversationId) === false)
        {
            // Théoriquement inutile car on n'aura pas la possibilité de supprimer une conversation inexistante dans l'application
            // Fait pour s'entrainer et tester dans postman
            echo "La conversation que vous souhaitez supprimer n'existe pas !";
            exit;
        }
    
        if($this->is_the_conversation_owner($userToken['id'], $conversationId))  
        {
            $stmt->execute();
            echo "Conversation supprimé avec succès !";
        }
        else {
            // Idem, comme on ne peut pas voir les conversations des autres utilisateurs, on est pas amené à pouvoir les supprimer
            // Fait pour s'entrainer et tester dans postman
            echo "Vous n'avez pas la permission de supprimer cette conversation";
            exit;
        }
    }

    public function deleteThisConversation($conversationID, $headers) {
        return $this->deleteConversation($conversationID, $headers);
    }



    // - Obtenir la liste des conversations d'un utilisateur, etc.
    private function getConversations($user_id, $character_id) {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $query = "SELECT cnv.conversation_name, c.name, cnv.number_of_messages
                FROM `conversations` as cnv
                LEFT JOIN `character` AS c ON cnv.character_id = c.id
                WHERE cnv.character_id = :charID AND cnv.user_id = :userID
        ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':charID', $character_id);
        $stmt->bindParam(':userID', $user_id);

        // Gère le cas ou un utilisateur ne possède pas de conversations
        if($this->already_have_a_conv($user_id) === false) {
            throw new Exception("Vous n'avez pas encore créer de conversations !");
            exit;
        }
        else {
            $stmt->execute();
            return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }


    
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

    

    private function check_if_conversation_exist($id){
        $database = Database::getInstance();
        $conn = $database->getConnection();
    
        $query = "SELECT `id` FROM `conversation` WHERE `id` = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur
    
        if ($conversation) {
            // Un conversation possède cet id
            // Faites quelque chose avec les données de l'utilisateur
            return true;
            // Faites ce que vous devez faire avec l'utilisateur trouvé
        } else {
            // Aucune conversation avec cet id
            // Faites ce que vous devez faire lorsque l'utilisateur n'est pas trouvé
            return false;
        }
    }

    public function does_conversation_exist($id) {
        return $this->check_if_conversation_exist($id);
    }



    private function owns_this_conversation($userID, $conversationID/*, $headers*/) {
        $database = Database::getInstance();
        $conn = $database->getConnection();
    
        $auth = new Authentication();
    
        $query = "SELECT user_id FROM conversations WHERE `id` = :conversationID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':conversationID', $conversationID);
        $stmt->execute();
    
        $conversation = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'univers
    
        //var_dump($universe);
        if($conversation['user_id'] != $userID){
            return false;
        }
        else{
            return true;
        }
    }

    public function is_the_conversation_owner($userID, $conversationID) {
        return $this->owns_this_conversation($userID, $conversationID);
    }


    private function check_if_user_have_conv($user_id) {
        $database = Database::getInstance();
        $conn = $database->getConnection();

        $query = "SELECT * FROM `conversations` as cnv WHERE cnv.user_id = :userID";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':userID', $user_id);

        $stmt->execute();

        $conv = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les données de l'utilisateur

        if ($conv)
            return true;
        else
            return false;
    }

    public function already_have_a_conv($user_id) {
        return $this->check_if_user_have_conv($user_id);
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



