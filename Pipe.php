<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:00
 */

require_once "APIObject.php";


class Pipe extends APIObject {
    public $id;
    public $name;

    public $created_at;
    public $updated_at;
    public $token;
    public $can_edit;
    public $labels; // [Label],
    public $users; // [User],
    public $phases; // [Phase]


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }


    /**
     * @param null $pipe_id int
     * @return $this
     */
    public function fetch($pipe_id = null) {
        if ($pipe_id == null)
            $pipe_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/pipes/$pipe_id.json", null, null);
        $this->assign_results($resp);

        $this->parse_property("labels", "Label");
        $this->parse_property("users", "User");
        $this->parse_property("phases", "Phase");

        return $this;
    }


    /**
     * @param $title string
     * @param $field_values array
     * @param $parent_card_id int   If not null, the connected card will be created.
     * @return $this
     */
    public function create_card($title, $field_values, $parent_card_id) {
        return (new Card())->create_card($title, $this->id, $field_values, $parent_card_id);
    }

    /**
     * @param $phase_name string
     * @return Phase
     */
    public function get_phase_by_name($phase_name) {
        foreach ($this->phases as $key => $phase) {

            if ($phase->name == $phase_name)
                return $phase;
        }

        return false;
    }

    /**
     * @return Phase
     */
    public function get_draft_phase() {
        foreach ($this->phases as $key => $phase) {

            if ($phase->draft)
                return $phase;
        }

        return false;
    }

}
