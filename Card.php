<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:02
 */

require_once "APIObject.php";


class Card extends APIObject {
    public $id;
    public $title;
    public $current_phase_id;
    public $due_date;
    public $duration;
    public $started_at;
    public $finished_at;
    public $expiration_time;
    public $index;
    public $token;
    public $pipe; // Pipe
    public $expired;
    public $late;
    public $draft;
    public $done;
    public $can_show_pipe;
    public $previous_phase; // Phase
    public $next_phase;  //Phase
    public $assignees; // [User],
    public $labels; // [Label],
    public $current_phase_detail; // PhaseDetail,
    public $other_phase_details; // [],
    public $parent_card_phase_detail; // CardPhaseDetail
    public $checklists; // [Checklist]

    private $endpoint = "https://app.pipefy.com/";


    /**
     * @param null $data mixed
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }


    /**
     * @param null $card_id mixed
     * @return $this
     */
    public function fetch($card_id = null) {
        if ($card_id == null)
            $card_id = $this->id;

        $resp = $this->send_get($this->endpoint . "cards/" . $card_id . ".json", array("Content-Type: application/json"), null);

        $this->assign_results($resp);

        $this->parse_property("pipe", "Pipe");
        $this->parse_property("previous_phase", "Phase");
        $this->parse_property("next_phase", "Phase");
        $this->parse_property("assignees", "User");
        $this->parse_property("labels", "Label");
        $this->parse_property("current_phase_detail", "PhaseDetail");
        $this->parse_property("other_phase_details", "PhaseDetail");
        $this->parse_property("parent_card_phase_detail", "CardPhaseDetail");
        $this->parse_property("checklists", "Checklist");

        return $this;
    }

    /**
     * @param $title string
     * @param $pipe_id int
     * @param $field_values array
     * @param $parent_card_id int   If not null, the connected card will be created.
     * @return $this
     */
    public function create_card ($title, $pipe_id, $field_values, $parent_card_id) {
        if ($parent_card_id == null) {
            //creating common card
            $card["card"]["title"] = $title;
            $card["card"]["field_values"] = $field_values;

            $resp = $this->send_post($this->endpoint . "cards/pipes/" . $pipe_id . "/create_card.json", array("Content-Type: application/json"), $card);

            $this->assign_results($resp);

            $this->parse_property("pipe", "Pipe");
            $this->parse_property("previous_phase", "Phase");
            $this->parse_property("next_phase", "Phase");
            $this->parse_property("assignees", "User");
            $this->parse_property("labels", "Label");
            $this->parse_property("current_phase_detail", "PhaseDetail");
            $this->parse_property("other_phase_details", "PhaseDetail");
            $this->parse_property("parent_card_phase_detail", "CardPhaseDetail");
            $this->parse_property("checklists", "Checklist");

            return $this;
        }
        else {
            //create connected card
            $resp = $this->send_post($this->endpoint . "cards/" . $parent_card_id . "/create_connected_card.json?pipe_id=" . $pipe_id, null, null);

            $this->assign_results($resp);

            $card["card"]["title"] = $title;
            $card["card"]["id"] = $this->id;


            //change title
            $this->send_put($this->endpoint . "cards/" . $this->id . ".json", array("Content-Type: application/json"), json_encode($card));


            //set field values
            foreach($field_values as $key => $field) {
                $fieldData = array(
                    "card_id" => $this->id,
                    "field_id" => $field["field_id"],
                    "new_value" => $field["value"]
                );

                $this->send_post($this->endpoint . "card_field_values/persist.json", null, http_build_query($fieldData));
            }

            //sync card data
            $this->fetch($this->id);

            //moving to the first non-draft phase
            $this->send_put($this->endpoint . "cards/".$this->id."/jump_to_phase/".$this->next_phase->id.".json", array("application/x-www-form-urlencoded"), array("source" => "connected_card"));

            //sync card data
            $this->fetch($this->id);

            return $this;
        }
    }


    public function jump_to_phase($phase_id) {
        $this->send_put($this->endpoint . "cards/".$this->id."/jump_to_phase/".$phase_id.".json", array("application/x-www-form-urlencoded"), array());

        return $this->fetch();
    }


    /**
     * @return $this
     */
    public function move_to_next_phase() {
        $resp = $this->send_put($this->endpoint . "cards/" . $this->id . "/next_phase.json", null, null);

        $this->assign_results($resp);

        return $this;
    }

    /**
     * @return $this
     */
    public function move_to_previous_phase() {
        $resp = $this->send_put($this->endpoint . "cards/" . $this->id . "/previous_phase.json", null, null);

        $this->assign_results($resp);

        return $this;
    }


    public function update_field_persist($fieldID, $newValue) {
        $fieldData = array(
            "card_id" => $this->id,
            "field_id" => $fieldID,
            "new_value" => $newValue
        );

        $this->send_post($this->endpoint . "card_field_values/persist.json", null, http_build_query($fieldData));

        return $this;
    }


    public function update_field($fieldID, $newValue) {
        $fieldData = array(
            "card_phase_detail_id" => $this->current_phase_detail->id,
            "field_id" => $fieldID,
            "new_value" => $newValue
        );

        $this->send_post($this->endpoint . "card_field_values.json", null, http_build_query($fieldData));

        return $this;
    }


    public function leave_comment($text) {
        $comment = array();
        $comment["comment"]["text"] = $text;
        $comment["comment"]["card_phase_detail_id"] = $this->current_phase_detail->id;


        $resp = $this->send_post($this->endpoint . "comments.json", null, http_build_query($comment));

        return json_decode($resp);
    }


    public function create_checklist($checklist_name, $items, $no_layout = true) {
        $checklist = (new Checklist())->create_checklist($checklist_name, $this->current_phase_detail->id, $no_layout);
        foreach ($items as $item_name) {
            $checklist->add_option($item_name, $no_layout);
        }

        return $checklist;
    }
}
