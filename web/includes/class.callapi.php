<?php


class callapi
{
    function call($url, $method = 'GET', $token = '', $data = '')
    {
        $curl = curl_init();

        if(is_array($data))
        {
            $data = json_encode($data);
        }

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                if ($data)
                {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($token != '')
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-delain-auth: ' . $token
            ));
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // EXECUTE:
        $result = curl_exec($curl);
        if(curl_errno($curl))
        {
            return array(false,curl_errno($curl));
        }

        $return_curl = curl_getinfo($curl);


        curl_close($curl);
        return array($return_curl,$result);
    }
}