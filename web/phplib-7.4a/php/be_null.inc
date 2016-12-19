<?php

##
## Copyright (c) 1999-2000 Internet Images srl
##                    Massimiliano Masserelli <negro@interim.it>
##
## $Id: be_null.inc,v 1.2 2000/07/12 18:22:33 kk Exp $
##
## PHPLIB Blob Engine using nothing
## 
## This is a "reference" class to be used only as an example. This should
## not be used in applications.
## 
## It's also a good skeleton for writing a new Blob Engine


class BE_Null {
    function blob_create() {
        return false;
    }

    function blob_open($id) {
        return false;
    }

    function blob_close($id) {
        return false;
    }

    function blob_delete($id) {
        return false;
    }

    function blob_read($id) {
        return false;
    }

    function blob_write($id, $data) {
        return false;
    }


    function halt($s) {
        echo "<b>$s</b>";
        exit;
    }

}
?>
