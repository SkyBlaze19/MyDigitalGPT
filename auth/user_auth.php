<?php

use \Firebase\JWT\JWT;

class Authentication {
    private $secretKey;
    private $expirationTime;

    
    public function __construct() {
        // Clé secrète pour signer les tokens JWT
        $this->secretKey =  __JWT_SECRET_KEY__;

        // Temps d'expiration du token (en secondes)
        $this->expirationTime =  __JWT_TOKEN_EXPIRATION_TIME__;
    }

    public function generateToken($user) {

        // Debug
        //echo '<br><br>';
        //print_r($user);
        $token = \Firebase\JWT\JWT::encode(
            array(
                    "data" => json_encode($user->toMap()),
                    "exp" => time() + __JWT_TOKEN_EXPIRATION_TIME__
                ),
            __JWT_SECRET_KEY__,
            'HS256'
        );

        return $token;
    }

    public function decodeToken($token)
    {
        $decodedToken = 'AAA';
        //print_r("Avant décodage :".$token."\n\n");
        try {
            // Validation du token et décryptage
            $decodedToken = JWT::decode($token, $this->secretKey, 'HS256');
            print_r($decodedToken);
            return $decodedToken;
            // Le token est valide
        } catch (Exception $e) {
            // Le token est invalide ou a expiré, vous pouvez renvoyer une réponse d'erreur ou non autorisée
            print_r($decodedToken);
            echo "Token invalide !";
            return null;
        }
    }
    

    /*
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    print_r($decoded);


    // We generate the token with an expiration date of 12 hour
        $token = \Firebase\JWT\JWT::encode(
            array(
                    "data" => json_encode($user->toMap()),
                    "exp" => time() + __JWT_TOKEN_EXPIRATION_TIME__
                ),
            __JWT_SECRET_KEY__,
            'HS256'
        );
    */

    /*
    public function validateToken($token) {
        try {
            $decodedToken = JWT::decode($token, $this->secretKey, array('HS256'));
            return $decodedToken;
        } catch (Exception $e) {
            // Gérer les erreurs de validation du token ici
            return false;
        }
    }
    */
}

?>
