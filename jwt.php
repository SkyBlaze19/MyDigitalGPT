<?php

include('vendor/autoload.php');

// Requires: composer require firebase/php-jwt
use Firebase\JWT\JWT;

// Get your service account's email address and private key from the JSON key file
$service_account_email = "abc-123@a-b-c-123.iam.gserviceaccount.com";
$private_key = "-----BEGIN PRIVATE KEY-----...";

function create_custom_token($uid, $is_premium_account) {
  global $service_account_email, $private_key;

  $now_seconds = time();
  $payload = array(
    "iss" => $service_account_email,
    "sub" => $service_account_email,
    "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
    "iat" => $now_seconds,
    "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
    "uid" => $uid,
    "claims" => array(
      "premium_account" => $is_premium_account
    )
  );
  return JWT::encode($payload, $private_key, "RS256");
}


/*

// We generate the token with an expiration date of 12 hour
    $token = \Firebase\JWT\JWT::encode(
        array(
                "data" => json_encode($user->toMap()),
                "exp" => time() + __JWT_TOKEN_EXPIRATION_TIME__
            ),
        __JWT_SECRET_KEY__,
        'HS256'
    );

