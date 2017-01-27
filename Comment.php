<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:04
 */

class Comment extends APIObject {
    public $id;
    public $text;
    public $created_at;
    public $updated_at;
    public $card_phase_detail_id;
    public $created_by; // User


    /**
     * @param null $data
     */
    function __construct($data = null) {
        if ($data != null) {
            $this->assign_results($data);
        }
    }


    /**
     * @param null $comment_id int
     * @return $this
     */
    public function fetch($comment_id = null) {
        if ($comment_id == null)
            $comment_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/comments/$comment_id.json", null, null);
        $this->assign_results($resp);

        return $this;
    }
}
