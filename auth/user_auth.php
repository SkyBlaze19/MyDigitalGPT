<?php

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        /*$key = __JWT_SECRET_KEY__;
        $testKey = new Key(__JWT_SECRET_KEY__, 'HS256');
        var_dump($testKey);*/

        //print_r("\n\n".__JWT_SECRET_KEY__."\n\n");
        $decodedToken = '';
        //print_r("Avant décodage :".$token."\n\n");
        try {
            // Validation du token et décryptage
            $decodedToken = JWT::decode($token, new Key(__JWT_SECRET_KEY__, 'HS256'));
            //var_dump($decodedToken);
           // print_r("\n\nDecoded token dans la fonction decoded token : ".$decodedToken."\n\n");
            return $decodedToken;
            // Le token est valide
        } catch (Exception $e) {
            // Le token est invalide ou a expiré, vous pouvez renvoyer une réponse d'erreur ou non autorisée
            //var_dump($decodedToken);
            //print_r("\n\nDecoded token dans la fonction decoded token : ".$decodedToken."\n\n");
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
