<?php
/**
 * Created by PhpStorm.
 * User: ExE
 * Date: 27.01.17
 * Time: 12:00
 */

require_once "Pipefy.php";


class APIObject {

    /**
     * @param $url string
     * @param $headers array
     * @param $getData array
     * @return mixed
     * @throws Exception
     */
    protected function send_get($url, $headers, $getData) {
        $authHeaders = Pipefy::get_auth_headers();
        $headers = array_merge($authHeaders, (array)$headers);

        if ($getData != null)
            $url .= "?" . http_build_query($getData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $url string
     * @param $headers array
     * @param $postData mixed
     * @return mixed
     * @throws Exception
     */
    protected function send_post($url, $headers, $postData) {
        $authHeaders = Pipefy::get_auth_headers();
        $headers = array_merge($authHeaders, (array)$headers);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $url string
     * @param $headers array
     * @param $putData mixed
     * @return mixed
     * @throws Exception
     */
    protected function send_put($url, $headers, $putData) {
        $putData = (is_array($putData)) ? http_build_query($putData) : $putData;

        $authHeaders = Pipefy::get_auth_headers();
        $headers = array_merge($authHeaders, (array)$headers, array('Content-Length: ' . strlen($putData)));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $putData);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $resp mixed
     */
    final protected function assign_results ($resp)
    {
        if (is_string($resp))
            $resp = json_decode($resp);

        foreach ($resp as $key => $val)
        {
            if (property_exists(get_called_class(), $key))
            {
                $this->$key = $val;
            }
            else {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * @param $propName string
     * @param $className string
     */
    protected function parse_property($propName, $className) {
        if (property_exists(get_called_class(), $propName) && $this->$propName != null) {
            if (is_array($this->$propName)) {
                $tmp_val = array();
                foreach ($this->$propName as $key => $value) {
                    $tmp_val[] = new $className($value);
                }
                $this->$propName = $tmp_val;
            }
            else {
                $this->$propName = new $className($this->$propName);
            }
        }
    }
}
