<?php
/*
 * PHP Base Library
 *
 * (c) 1999-2000 Carmelo Guarneri
 *
 * $Id: tmpl_table.inc,v 1.2 2000/07/12 18:22:35 kk Exp $
 *
 */ 

Class Template_Table extends Table {

  var $header;
  var $footer;
  var $rowform;
  var $uppercase;

  var $first_row;
  var $page_rows;
  var $num_rows;

  function start($rowform, $header="", $footer="") {
	$this->header=$header;
	$this->footer=$footer;
	$this->rowform=$rowform;
	$uppercase=0;
  }

  function show_result_page($db, $start, $num) {
	$this->first_row=$start;
	$this->page_rows=$num;
	$this->num_rows=$db->num_rows();
    if (($start > $db->num_rows()+0) || ($start < 0)) {
      return;
    }
    $row = $start;
    $fin = $start + $num;
    
    $this->table_open($class);
    if ($this->heading) {
      if ($db->next_record()) {
        $db->seek($start);
        $this->table_heading_row($db->Record, $class);
      } else {
        $this->table_close();
        return;
      }
    }
    
    $db->seek($start);
    while($db->next_record() && ($row < $fin)) {
      ## Process a table row
      $this->table_row($row, $row, $db->Record, $class);
      $row += 1;
    }
    $this->table_close();
  }

  ############################################################

  ## The following functions provide a very basic rendering
  ## of a HTML table with CSS class tags. Table is useable
  ## with them or the functions can be overridden for a
  ## more complex functionality.

  ## Table open and close functions.

  function table_row($row, $row_key, $data, $class = "") {
    $cell = 0;

    $d = $this->select_colnames($data);    

    reset($d);
    while(list($key, $val) = each($d)) {
	  $lvar=$val;
	  if ($uppercase) $lvar=strtoupper($val);
	  $$lvar=$data[$val];
	  //printf($lvar);
    }
    include "$this->rowform";
  }

  function table_open($class = "") {
      if ($this->header!="") 
	    include $this->header;
  }

  function table_close() {
      if ($this->footer!="") 
	    include $this->footer;
  }

}

?>
