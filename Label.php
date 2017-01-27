<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:04
 */

require_once "APIObject.php";


class Label extends APIObject {
    public $id;
    public $name;
    public $color;


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }
}