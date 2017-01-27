<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 11:59
 */

include_once "Pipe.php";
include_once "Phase.php";
include_once "Card.php";
include_once "User.php";
include_once "Field.php";
include_once "FieldValue.php";
include_once "Label.php";
include_once "Comment.php";


class Pipefy {
    private static $user_token;
    private static $user_email;

    /**
     * @param $token string User's API Key
     * @param $email string User's email
     */
    function __construct($token, $email) {
        Pipefy::$user_token = $token;
        Pipefy::$user_email = $email;
    }

    /**
     * @param $token string User's API Key
     * @param $email string User's email
     */
    public static function init($token, $email) {
        Pipefy::$user_token = $token;
        Pipefy::$user_email = $email;
    }


    public static function get_auth_headers() {
        if (Pipefy::$user_token != null && Pipefy::$user_email != null)
            return array(
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36',
                'Connection: keep-alive',
                'Accept: */*',
                //'Accept-Encoding: gzip, deflate, sdch, br',
                'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                'X-User-Email: '.Pipefy::$user_email,
                'X-User-Token: '.Pipefy::$user_token);
        else
            throw new Exception("Pipefy API is not inited. You should create an instance of Pipefy first");
    }

    public static function get_auth_params() {
        if (Pipefy::$user_token != null && Pipefy::$user_email != null)
            return array("user_email" => Pipefy::$user_email, "user_token" => Pipefy::$user_token);
        else
            throw new Exception("Pipefy API is not inited. You should create an instance of Pipefy first");
    }
}