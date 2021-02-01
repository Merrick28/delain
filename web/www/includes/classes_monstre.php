<?php
//ini_set('zlib.output_compression','1');
//die ('Maintenance, merci de patienter');

function writelog_class_sql($textline)
{
    //$filename='/home/sdewitte/public_html/debug/sql.log'; // or whatever your path and filename
    $filename = __DIR__ . '/../debug/sql.log'; // or whatever your path and filename
    if (!file_exists($filename))
    {
        echo '<!-- creation -->';
        if (touch($filename))
        {
            echo '<!-- creation OK -->';
        } else
        {
            echo '<!-- ECHEC SUR CREATION -->';
        }
    }
    chmod($filename, 0777);
    if (is_writable($filename))
    {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $somecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, 'a'))
        {
            echo 'Cannot open file (', $filename, ')';
            exit;
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $textline) === FALSE)
        {
            echo 'Cannot write to file (', $filename, ')';
            exit;
        }

        //echo 'Success, wrote (' . $textline . ') to file (' . $filename . ')';

        fclose($handle);

    } else
    {
        echo 'The file ', $filename, ' is not writable';
    }
}


