<?php

/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 08.02.17
 * Time: 17:49
 */

require_once "APIObject.php";

class PhaseDetail extends APIObject
{
    public $id;
    public $duration;
    public $created_at;
    public $phase; // Phase
    public $connected_cards; // [Card]
    public $checklists; // [Checklist]
    public $field_values; // [FieldValue]
    public $automated_messages; // []
    public $comments; // [Comment]



    /**
     * @param null $data mixed
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);

            $this->parse_property("phase", "Phase");
            $this->parse_property("checklists", "Checklist");
        }
    }
}