<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:03
 */

require_once "APIObject.php";


class FieldValue extends APIObject {
    public $id;
    public $field_id;
    public $value;
    public $display_value;
    public $created_at;
    public $updated_at;
    public $card; // Card


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }

    /**
     * @param null $field_id int
     * @return $this
     */
    public function fetch($field_value_id = null) {
        if ($field_value_id == null)
            $field_value_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/card_field_value/$field_value_id.json", null, null);
        $this->assign_results($resp);

        $this->parse_property("card", "Card");

        return $this;
    }


    /**
     * @param $value string
     * @return $this
     */
    public function set_value($value) {
        if ($this->field_id == null || $this->card == null || $this->card->id == null)
            $this->fetch();

        $resp = $this->send_post("https://app.pipefy.com/card_field_value/{$this->id}.json", null, array(
            "field_id" => $this->field_id,
            "card_phase_detail_id" => $this->card->id,
            "value" => $value
        ));

        $this->assign_results($resp);

        return $this;
    }
}
