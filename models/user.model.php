<?php

    class UserModel {

        private int $id;
        private string $username;
        private string $password;
        private string $firstname;
        private string $lastname;
        private DateTime $created_at; //transformer en datetime
        private DateTime $updated_at;

        public function __construct($id, $username, $password, $firstname, $lastname, $created_at, $updated_at)
        {
            $this->setId($id);
            $this->setUsername($username);
            $this->setPassword($password);
            $this->setFirstname($firstname);
            $this->setLastname($lastname);
            $this->setCreatedAt($created_at);
            $this->setUpdatedAt($updated_at);
        }


        public static function verifyCredentials($username, $password) {

            $database = Database::getInstance();
            $conn = $database->getConnection();
            
            // Requête pour vérifier les informations d'identification
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            // Récupération de l'utilisateur correspondant au nom d'utilisateur
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && $password == $user['password']) {
                // Les informations d'identification sont valides
                //print_r($user);
                return new UserModel(
                    $user['id'],
                    $user['username'], 
                    $user['password'],
                    $user['firstname'],
                    $user['lastname'],
                    $user['created_at'],
                    $user['updated_at'],
                );
            } else {
                if(UserModel::username_is_valid($username) === false)
                {
                    throw new Exception("Cet utilisateur est inexistant !");
                }
                else if(UserModel::password_is_valid($user['username'], $password) === false)
                {
                    throw new Exception("Mot de passe invalide !");
                }
                // Les informations d'identification sont incorrectes
                return false;
            }
        }

        public function toMap(): Array
        {
            $monTab = array();

            $monTab['id'] = $this->id;
            $monTab['username'] = $this->username;
            $monTab['password'] = $this->password;
            $monTab['firstname'] = $this->firstname;
            $monTab['lastname'] = $this->lastname;
            $monTab['created_at'] = $this->created_at;
            $monTab['updated_at'] = $this->updated_at;

            // Debug 
            //print_r($monTab);

            return $monTab;
            //return array
        }
        
        
        /*Getter(s) / Setter(s) */

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
         * Get the value of username
        */ 
        public function getUsername()
        {
                return $this->username;
        }

        /**
         * Set the value of username
         *
         * @return  self
        */ 
        public function setUsername($username)
        {
                $this->username = $username;

                return $this;
        }

        /**
         * Get the value of password
        */ 
        public function getPassword()
        {
                return $this->password;
        }

        /**
         * Set the value of password
         *
         * @return  self
        */ 
        public function setPassword($password)
        {
                $this->password = $password;

                return $this;
        }

        /**
         * Get the value of firstname
        */ 
        public function getFirstname()
        {
                return $this->firstname;
        }

        /**
         * Set the value of firstname
         *
         * @return  self
        */ 
        public function setFirstname($firstname)
        {
                $this->firstname = $firstname;

                return $this;
        }

        /**
         * Get the value of lastname
        */ 
        public function getLastname()
        {
                return $this->lastname;
        }

        /**
         * Set the value of lastname
         *
         * @return  self
        */ 
        public function setLastname($lastname)
        {
                $this->lastname = $lastname;

                return $this;
        }

        /**
         * Get the value of created_at
        */ 
        public function getCreatedAt()
        {
                return $this->created_at;
        }

        /**
         * Set the value of created_at
         *
         * @return  self
        */ 
        public function setCreatedAt($created_at)
        {
                $this->created_at = DateTime::createFromFormat('Y-m-d H:i:s', $created_at);
                return $this;
        }

        /**
         * Get the value of updated_at
        */ 
        public function getUpdatedAt()
        {
                return $this->updated_at;
        }

        /**
         * Set the value of updated_at
         *
         * @return  self
        */ 
        public function setUpdatedAt($updated_at)
        {
            $this->updated_at = DateTime::createFromFormat('Y-m-d H:i:s', $updated_at);
            return $this;
        }

        public static function password_is_valid($username, $password){
            //$conn = connexionBDD();
            //Debug
            //echo 'Tu est dans password validate';
        
            $database = Database::getInstance();
            $conn = $database->getConnection();
        
            $query = "SELECT username, `password` FROM users WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
        
            $stmt->execute();
        
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            //echo "User password = ".$user['password']." / alors que password = ".$password." !";

            if($user['password'] != $password)
            {
                //echo 'Passwd différent !';
                return false;
            }
            else 
            {
                //echo 'theoriquement passwd ok ';
                return true;
            }
        }
        
        public static function username_is_valid($username){
            //$conn = connexionBDD();
        
            $database = Database::getInstance();
            $conn = $database->getConnection();
        
            $query = "SELECT username FROM users WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
        
            $stmt->execute();
        
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            //print_r($user);
        
            if(empty($user)) 
                return false;
            else 
                return true;
        }

    }
