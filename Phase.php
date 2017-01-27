<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:01
 */

require_once "APIObject.php";


class Phase extends APIObject {
    public $id;
    public $name;
    public $description;
    public $done;
    public $draft;
    public $pipe_id;
    public $index;
    public $created_at;
    public $updated_at;
    public $can_edit;
    public $cards; // [Card]
    public $fields; // [Field],
    public $connected_pipes; // [Pipe],
    public $jump_targets; // [Phase ?]


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }


    /**
     * @param null $phase_id int
     * @return $this
     */
    public function fetch($phase_id = null) {
        if ($phase_id == null)
            $phase_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/phases/$phase_id.json", null, null);
        $this->assign_results($resp);

        $this->parse_property("cards", "Card");
        $this->parse_property("fields", "Field");
        $this->parse_property("connected_pipes", "Pipe");

        return $this;
    }


    /**
     * @param $field_id int
     * @return Field
     */
    public function get_field_by_id($field_id) {
        foreach ($this->fields as $key => $field) {

            if ($field->id == $field_id)
                return $field;
        }

        return false;
    }

    /**
     * @param $field_label string
     * @return Field
     */
    public function get_field_by_label($field_label) {
        foreach ($this->fields as $key => $field) {

            if ($field->label == $field_label)
                return $field;
        }

        return false;
    }
}
