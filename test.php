<?php

    include('vendor/autoload.php');

    include('controllers/user.controller.php');
    include('models/ConnexionBdd.php');

    include('auth/login.php');

    require 'auth/config.php';
    require 'auth/user_auth.php';
    include('models/user.model.php');

    require 'vendor/autoload.php';


    //phpinfo();
    /*echo date_default_timezone_get();

    echo "<br><br>";

    $currentDate = date('Y-m-d H:i:s');
    echo $currentDate;

    echo "<br><br>";

    date_default_timezone_set('Europe/Paris');
    */
    echo date_default_timezone_get();

    echo "<br><br>";

    $currentDate = date('Y-m-d H:i:s');
    echo $currentDate;

    echo "<hr><br><br>";
    $testF1 = UserModel::username_is_valid('Tlemaigre');
    echo $testF1;

    echo "<br><br>";
    $testF2 = UserModel::username_is_valid('Tlemaigr');
    if($testF2 == false)
        echo "Cet utilisateur n'existe pas !";

    echo "<hr><br><br>";
    $testMail1 = check_if_mail_already_used('theolemaigre@hotmail.fr');
    echo $testMail1;

    echo "<br><br>";
    $testMail2 = check_if_mail_already_used('a');
    if($testMail2 == false)
        echo "Cet email n'est pas utilisé !";


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