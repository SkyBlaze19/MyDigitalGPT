<?php


    class CharacterModel {

        private int $id;
        private string $name;
        private string $description;
        private int $universe_id;
        private int $creator_id;
        private DateTime $created_at; //transformer en datetime
        private DateTime $updated_at;
        private int $order_in_universe;

        public function __construct($id, $name, $description, $universe_id, $creator_id, $created_at, $updated_at, $order_in_universe)
        {
            $this->setId($id);
            $this->setName($name);
            $this->setDescription($description);
            $this->setUniverseId($universe_id);
            $this->setCreatorId($creator_id);
            $this->setCreatedAt($created_at);
            $this->setUpdatedAt($updated_at);
            $this->setOrder_in_universe($order_in_universe);
        }

        /* Inutile je pense pour le moment
        public function toMap(): Array
        {
            $monTab = array();

            $monTab['id'] = $this->id;
            $monTab['name'] = $this->name;
            $monTab['creator_id'] = $this->creator_id;
            $monTab['created_at'] = $this->created_at;
            $monTab['updated_at'] = $this->updated_at;

            // Debug 
            //print_r($monTab);

            return $monTab;
            //return array
        }
        */
        
        
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
         * Get the value of name
         */ 
        public function getName()
        {
                return $this->name;
        }

        /**
         * Set the value of name
         *
         * @return  self
         */ 
        public function setName($name)
        {
                $this->name = $name;

                return $this;
        }

        /**
         * Get the value of description
         */ 
        public function getDescription()
        {
                return $this->description;
        }

        /**
         * Set the value of description
         *
         * @return  self
         */ 
        public function setDescription($description)
        {
                $this->description = $description;

                return $this;
        }

        /**
         * Get the value of universe_id
         */ 
        public function getUniverseId()
        {
                return $this->universe_id;
        }

        /**
         * Set the value of universe_id
         *
         * @return  self
         */ 
        public function setUniverseId($universe_id)
        {
                $this->universe_id = $universe_id;

                return $this;
        }

        /**
         * Get the value of creator_id
         */ 
        public function getCreatorId()
        {
                return $this->creator_id;
        }

        /**
         * Set the value of creator_id
         *
         * @return  self
         */ 
        public function setCreatorId($creator_id)
        {
                $this->creator_id = $creator_id;

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

        /**
         * Get the value of order_in_universe
         */ 
        public function getOrder_in_universe()
        {
                return $this->order_in_universe;
        }

        /**
         * Set the value of order_in_universe
         *
         * @return  self
         */ 
        public function setOrder_in_universe($order_in_universe)
        {
                $this->order_in_universe = $order_in_universe;

                return $this;
        }























        /*

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
                //echo 'theoriquement passws ok ';
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
        */


        
    }
