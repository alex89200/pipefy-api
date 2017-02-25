<?php

/**
 * Created by PhpStorm.
 * User: zende
 * Date: 17.02.2017
 * Time: 14:23
 */

require_once "APIObject.php";


class Checklist extends APIObject
{
    public $id;
    public $name;
    public $items;
    public $card_phase_detail_id;



    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }



    /**
     * @param null $checklist_id int
     * @return $this
     */
    public function fetch($checklist_id = null) {
        if ($checklist_id == null)
            $checklist_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/checklists/$checklist_id.json", null, null);
        $this->assign_results($resp);

        return $this;
    }

    /**
     * @param string $checklist_name
     * @param int $card_phase_detail_id
     * @param bool $no_layout
     * @return $this
     */
    public function create_checklist($checklist_name, $card_phase_detail_id, $no_layout = true) {
        $checklist["checklist"]["name"] = $checklist_name;
        $checklist["checklist"]["card_phase_detail_id"] = $card_phase_detail_id;
        $checklist["no_layout"] = $no_layout;

        $resp = $this->send_post("https://app.pipefy.com/checklists.json", null, http_build_query($checklist));
        $this->assign_results($resp);

        return $this;
    }


    /**
     * @param string $new_name
     * @return $this
     */
    public function rename($new_name) {
        $checklist["checklist"]["name"] = $new_name;

        $resp = $this->send_put("https://app.pipefy.com/checklists/" . $this->id, null, http_build_query($checklist));
        $this->assign_results($resp);

        return $this;
    }

    /**
     * @param string $item_name
     * @param bool $no_layout
     * @return array
     */
    public function add_option($item_name, $no_layout = true) {
        $checklist["checklist_item"]["text"] = $item_name;
        $checklist["checklist_item"]["checklist_id"] = $this->id;
        $checklist["no_layout"] = $no_layout;

        $resp = $this->send_post("https://app.pipefy.com/checklist_items.json", null, http_build_query($checklist));
        //$this->assign_results($resp);

        return json_decode($resp);
    }
}