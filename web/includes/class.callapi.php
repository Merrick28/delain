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


    /**
     * @return (compte|auth_token)[]
     */
    function verifyCall()
    {
        $headers = getallheaders();
        if (!isset($headers['X-delain-auth']))
        {
            header('HTTP/1.0 403 NoToken');
            die('Token non transmis');
        }

        $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        if(!preg_match($UUIDv4, $headers['X-delain-auth']))
        {
            {
                header('HTTP/1.0 403 NoToken');
                die('Token non UUID');
            }
        }

        $auth_token = new auth_token();

        if (!$auth_token->charge($headers['X-delain-auth']))
        {
            header('HTTP/1.0 403 TokenNotFound');
            die('Token non trouvé');
        }

        $compte = new compte;
        if (!$compte->charge($auth_token->at_compt_cod))
        {
            header('HTTP/1.0 403 AccountNotFound');
            die('Compte non trouvé');
        }

        return array("compte" =>$compte,"token" =>$auth_token);
    }
}