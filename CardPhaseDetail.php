<?php

/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 08.02.17
 * Time: 17:48
 */

require_once "APIObject.php";

class CardPhaseDetail extends APIObject
{
    public $id;
    public $card_id;
    public $phase_id;
    public $card; // Card



    /**
     * @param null $data mixed
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }
}