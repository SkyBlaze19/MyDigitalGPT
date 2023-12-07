<?php
// conversation.model.php

class ConversationModel {

    private int $id;
    private int $character_id;
    private int $user_id;
    private DateTime $created_at; //transformer en datetime
    private DateTime $updated_at;

    public function __construct($id, $character_id, $user_id, $created_at, $updated_at)
        {
            $this->setId($id);
            $this->setCharacter_id($character_id);
            $this->setUser_id($user_id);
            $this->setCreated_at($created_at);
            $this->setUpdated_at($updated_at);
        }

    // Méthode pour récupérer les messages d'une conversation spécifique
    public function getMessagesInConversation($conversationId) {
        $query = "SELECT * FROM messages WHERE conversation_id = :conversationId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversationId', $conversationId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour ajouter un message à une conversation
    public function addMessageToConversation($conversationId, $userId, $message) {
        $query = "INSERT INTO messages (conversation_id, user_id, message, created_at) VALUES (:conversationId, :userId, :message, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversationId', $conversationId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    // Autres méthodes pour gérer les conversations, par exemple :
    // - Créer une conversation
    // - Supprimer une conversation
    // - Obtenir la liste des conversations d'un utilisateur, etc.




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
}



