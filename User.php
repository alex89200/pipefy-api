<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:01
 */

require_once "APIObject.php";


class User extends APIObject {
    public $id;
    public $name;
    public $username;
    public $email;
    public $created_at;
    public $updated_at;
    public $display_username;


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }


    /**
     * @param null $user_id int
     * @return $this
     */
    public function fetch($user_id = null) {
        if ($user_id == null)
            $user_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/users/$user_id.json", null, null);
        $this->assign_results($resp);

        return $this;
    }
}