<?php

/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 07.02.17
 * Time: 19:12
 */

require_once "APIObject.php";


class Organization extends APIObject
{
    public $id;
    public $name;
    public $created_at;
    public $updated_at;
    public $pipes;  //[Pipe]


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
    public function fetch($org_id = null) {
        if ($org_id == null)
            $org_id = $this->id;

        $resp = $this->send_get("https://app.pipefy.com/organizations/$org_id.json", null, null);
        $this->assign_results($resp);

        $this->parse_property("pipes", "Pipe");

        return $this;
    }


    /**
     * @param $phase_name string
     * @return Pipe
     */
    public function get_pipe_by_name($pipe_name) {
        foreach ($this->pipes as $pipe) {

            if ($pipe->name == $pipe_name)
                return $pipe;
        }

        return false;
    }
}