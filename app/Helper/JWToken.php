<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWToken{

    public static function CreateToken($userEmail, $userID):string{
        $key =env('JWT_KEY');
        $payload=[
            'iss'=>'laravel-jwt',
            'iat'=>time(),
            'exp'=>time()+60*60,
            'userEmail'=>$userEmail,
            'userID' => $userID
        ];
        return JWT::encode($payload,$key,'HS256');
    }


    public static function CreateTokenForSetPassword($userEmail, $userID):string{
        $key =env('JWT_KEY');
        $payload=[
            'iss'=>'laravel-jwt',
            'iat'=>time(),
            'exp'=>time()+60*20,
            'userEmail'=>$userEmail,
            'userID' => $userID
        ];
        return JWT::encode($payload,$key,'HS256');
    }

    public static function VerifyToken($token):string
    {
        try {
            $key =env('JWT_KEY');
            $decode=JWT::decode($token,new Key($key,'HS256'));
            //$decode=JWT::decode($token,new Key($key,'HS256'));
            return $decode->userEmail;
            //return $decodeID->userID;

        }
        catch (Exception $e){
            return 'unauthorized';
        }
    }

}
