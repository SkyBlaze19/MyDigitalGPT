<?php

    require 'user.model.php';

    // Test avec des informations d'identification valides
    $username = 'Tlemaigre';
    $password = 'Tlemaigre';

    echo "<h2>Test valide : verifyCredentials</h2>";

    $result = UserModel::verifyCredentials($username, $password);
    if ($result) {
        echo "Les informations d'identification sont <span style='color: green; text-transform: uppercase;'>valides</span>. L'utilisateur est connecté.";
    } else {
        echo "Les informations d'identification sont <span style='color: red; text-transform: uppercase;'>incorrectes</span>.";
    }

    echo "<hr><h2>Test invalide : verifyCredentials</h2>";

    // Test avec des informations d'identification incorrectes
    $username = 'john_doe';
    $password = 'motdepasseincorrect';

    $result = UserModel::verifyCredentials($username, $password);
    if ($result) {
        echo "Les informations d'identification sont <span style='color: green; text-transform: uppercase;'>valides</span>. L'utilisateur est connecté.";
    } else {
        echo "Les informations d'identification sont <span style='color: red; text-transform: uppercase;'>incorrectes</span>.";
    }
