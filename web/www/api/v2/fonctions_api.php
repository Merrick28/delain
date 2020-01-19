<?php
function test_perso()
{
    if (!isset($_REQUEST['visu_perso']))
    {
        header('HTTP/1.0 405 MissingArgument');
        die('visu_perso non transmis');
    }
    if (filter_var($_REQUEST['visu_perso'], FILTER_VALIDATE_INT) === false)
    {
        header('HTTP/1.0 405 MissingArgument');
        die('visu_perso non entier');
    }
    $perso = new perso;
    if (!$perso->charge($_REQUEST['visu_perso']))
    {
        header('HTTP/1.0 405 MissingArgument');
        die('perso non trouvé');
    }
    return $perso;
}

function test_offset_limit()
{
    $limit = 50;
    if(isset($_REQUEST['limit']))
    {
        if (filter_var($_REQUEST['limit'], FILTER_VALIDATE_INT) === false)
        {
            header('HTTP/1.0 405 MissingArgument');
            die('limit non entier');
        }
        if($_REQUEST['limit'] > 50)
        {
            header('HTTP/1.0 405 MissingArgument');
            die('limit sup à 50');
        }
        if($_REQUEST['limit'] < 0)
        {
            header('HTTP/1.0 405 MissingArgument');
            die('limit inf à 0');
        }
        $limit = $_REQUEST['limit'];
    }

    $offset = 0;
    if(isset($_REQUEST['offset']))
    {
        if (filter_var($_REQUEST['offset'], FILTER_VALIDATE_INT) === false)
        {
            header('HTTP/1.0 405 MissingArgument');
            die('offset non entier');
        }

        if($_REQUEST['offset'] < 0)
        {
            header('HTTP/1.0 405 MissingArgument');
            die('offset inf à 0');
        }
        $offset = $_REQUEST['offset'];
    }
    return array("offset" => $offset,"limit" => $limit);
}