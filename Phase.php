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



    public function move_card($from_index, $dest_index, $before) {
        $cards_count = count($this->cards);

        if ($from_index < 0 || $from_index >= $cards_count || $dest_index < 0 || $dest_index >= $cards_count)
            return false;

        $card_to_move = $this->cards[$from_index];

        $putData = array();
        if ($before) {

            if ($dest_index != 0) {
                $putData["previous_id"] = $this->cards[$dest_index - 1]->id;
            }

            $putData["next_id"] = $this->cards[$dest_index]->id;
        }
        else {

            if ($dest_index != $cards_count - 1) {
                $putData["next_id"] = $this->cards[$dest_index + 1]->id;
            }

            $putData["previous_id"] = $this->cards[$dest_index]->id;
        }

        $this->send_put(sprintf("https://app.pipefy.com/cards/%d/move", $card_to_move->id), null, $putData);

        //move card in array
        $new_cards = array();

        for ($i = 0; $i < $cards_count; $i++) {
            if ($i == $dest_index) {
                $new_cards[] = $card_to_move;
            }
            else {
                if ($i != $from_index)
                    $new_cards[] = $this->cards[$i];
            }
        }

        $this->cards = $new_cards;

        return $this;
    }


    public function move_card_by_id($card_id, $prev_card_id, $next_card_id, $fetch_updated = true) {
        $putData = array();

        if ($prev_card_id != null)
            $putData["previous_id"] = $prev_card_id;

        if ($next_card_id != null)
            $putData["next_id"] = $next_card_id;

        $this->send_put(sprintf("https://app.pipefy.com/cards/%d/move", $card_id), null, $putData);


        if ($fetch_updated)
            return $this->fetch();
        else
            return $this;
    }


    public function sort_cards_by_due_date() {
        foreach ($this->cards as $card) {
            $card->fetch();
        }

        usort($this->cards, function ($a, $b) {
            $ta = strtotime($a->due_date);
            $tb = strtotime($b->due_date);

            if ($ta == $tb) {
                return 0;
            }

            return ($ta < $tb) ? -1 : 1;
        });


        for ($i = 1; $i < count($this->cards); $i++) {
            $card_id = $this->cards[$i]->id;
            $prev_card_id = $this->cards[$i - 1]->id;
            $this->move_card_by_id($card_id, $prev_card_id, null, false);
        }

        return $this->fetch();
    }
}
