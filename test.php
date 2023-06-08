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

    echo "<br><br>";
    $testF1 = UserModel::username_is_valid('Tlemaigre');
    echo $testF1;

    echo "<br><br>";
    $testF2 = UserModel::username_is_valid('Tlemaigr');
    if($testF2 == false)
        echo "Cet utilisateur n'existe pas !";
