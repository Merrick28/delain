<?php
class logfile
{
    function writelog_class_sql($textline,$namefile = 'sql.log')
    {
        $filename = G_CHE . '/debug/' . $namefile; // or whatever your path and filename
        if (!file_exists($filename))
        {
            if (!touch($filename))
            {
                echo 'Echec sur création du fichier de log ' . $filename;
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

            fclose($handle);
        }
        else
        {
            echo 'The file ', $filename, ' is not writable';
        }
    }
}