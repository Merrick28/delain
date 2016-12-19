<?php

##
## Copyright (c) 1999-2000 Internet Images srl
##                    Massimiliano Masserelli <negro@interim.it>
##
## $Id: be_sql.inc,v 1.2 2000/07/12 18:22:33 kk Exp $
##
## PHPLIB Blob Engine using plain sql
## depends on db_sql
## 
## This is a "reference" class to be used only as an example. This should
## not be used in applications.
## 
## It's also a good skeleton for writing a new Blob Engine
##
## The table used by this class must contain three fields:
##  be_bid	16 chars
##  be_seq	6 chars
##  be_data     4096 chars


class BE_Sql {
    ##
    ## Define following parameters by overwriting or by deriving 
    ## your own class (recommended)
    ##
    var $database_class = "DB_Sql";     ## access table via this class
    var $database_table = "be_table";   ## write data into this table
    var $split_length   = 4096;         ## maximum amount of data for each row
                                        ## this value should be safe enough
    ## end of configuration

    var $db;  ## Internal, object used for db connection
    
    function start() {
        $name = $this->database_class;
        $this->db = new $name;
    }
    
    function blob_create() {
        if (! is_object($this->db)) {
            $this->start();
        }
        $bn = false;
        $count = 0;
        while (! $bn && ($count++ < 10)) {
            $bn = uniqid("");
            $this->db->query(sprintf("INSERT INTO %s".
                        " (be_bid, be_seq, be_data) ".
                        "   VALUES ".
                        " ('%s', '%06d', '')", $this->database_table, $bn, 0));
            if ($this->db->affected_rows() != 1) {
                $bn = "";
            }
        }
        return $bn;
    }

    function blob_open($id) {
        if (! is_object($this->db)) {
            $this->start();
        }
        $this->db->query("SELECT count(*) FROM ". $this->database_table. 
                    " WHERE be_bid = '$id'");
        if ($this->db->next_record()) {
          return ($this->db->f(0) > 0);
        } else {
          return false;
        }
    }

    function blob_close($id) {
        return true;
    }

    function blob_delete($id) {
        if (! is_object($this->db)) {
            $this->start();
        }
        $this->db->query("DELETE FROM ". $this->database_table.
                    " WHERE be_bid = '$id'");
        return true;
    }

    function blob_read($id) {
        if (! is_object($this->db)) {
            $this->start();
        }
        $str = "";
        $this->db->query("SELECT be_data, be_seq FROM ". $this->database_table.
                    " WHERE be_bid = '$id' ORDER BY be_seq");
        while ($this->db->next_record()) {
          $str .= $this->db->f(0);
        }
        return base64_decode($str);
    }

    function blob_write($id, $data) {
        if (! is_object($this->db)) {
            $this->start();
        }
        $this->db->query("BEGIN TRANSACTION");
        $this->db->query("DELETE FROM ". $this->database_table.
                    " WHERE be_bid = '$id'");
        $str = base64_encode($data);
        $seq = 0;
        while ($part = substr($str, 0, $this->split_length)) {
          $this->db->query(sprintf("INSERT INTO %s ".
                "(be_bid, be_seq, be_data) ".
                " VALUES ".
                "('%s', '%06d', '%s')",
                $this->database_table,
                $id,
                $seq++,
                $part));
          $str = substr($str, $this->split_length);
        }
        $this->db->query("END TRANSACTION");
        return true;
    }


##    function halt($s) {
##        echo "<b>$s</b>";
##        exit;
##    }

}
?>
