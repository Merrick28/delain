<?php
/*
 * PHP Base Library
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp,
 *                    Jeffrey Galbraith
 *
 * $Id: table.inc,v 1.5 2002/04/28 08:13:58 richardarcher Exp $
 *
 * History: 990617: Modularized entire table class. Modularity breaks larger
 *                  objects into smaller, autonomous objects in order to
 *                  exercise OOP requirements of code reuse. This give
 *                  programmers the ability to use the high-level code, or
 *                  get down and dirty with the low-level code.
 *                  Everything I have changed should maintain backwards
 *                  compatibility, except for show_table_heading_row_result(),
 *                  which was unpreventable in order to get the full
 *                  magnitude of OOP.
 *          990618: Added table_cell_open(), table_cell_close(),
 *                  table_heading_cell_open() and table_heading_cell_close().
 *                  Gives better modularity to class.(JSG)
 *          990619: Added $add_extra. If set, calls extra cell functions:
 *                  table_heading_row_add_extra and table_row_add_extra.
 *                  Override these functions in a derived class to add
 *                  additional cells to a row of output data.
 *          990620: Added column name remapping. This allows the column names
 *                  of a database to be remapped into something more useful,
 *                  or, perhaps, another language. Array variable "map_cols"
 *                  added. (JSG)
 *          020424: Fix various undefined variable warnings and a typo in
 *                  show_table_heading_cells.
 *          020428: Add option to pass full row data to table_row_open.
 *                  Default is to pass column names, as was the behaviour
 *                  prior to this change.
 */

  #==========================================================================
  # Table (class)
  #--------------------------------------------------------------------------
  # Creates an HTML table based on either a PHP array or a
  # database result.
  #==========================================================================

class Table {
  var $classname = "Table";              ## Persistence Support

  var $check;                            ## if set, create checkboxes named
                                         ## to the result of $check[$key]
  var $filter = "[A-Za-z][A-Za-z0-9_]*"; ## Regexp: Field names to show
  var $fields;                           ## Array of field names to show
  var $heading;                          ## if set, create <th> section
  var $add_extra;                        ## Call extra cell functions
  var $map_cols;                         ## remap database columns to new names
  var $full_data_to_table_row_open = 0;  ## if set, pass full row data to 
                                         ## table_row_open, else just column
                                         ## names (the original behaviour)

  #==========================================================================
  ## Public functions
  #==========================================================================



  #==========================================================================
  ## Page functions
  #==========================================================================

  #==========================================================================
  # Function : start
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a two-dimensional array in an HTML table
  #            format.
  # Arguments: $ary   - The 2D array to display.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments : See function: show
  # History  :
  #==========================================================================
  function start($ary, $class="") {
    return ($this->show($ary, $class));
  }

  #==========================================================================
  # Function : start_result
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a database query result in an HTML table
  #            format.
  # Arguments: $db    - The database result.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments : See function: show_result
  # History  :
  #==========================================================================
  function start_result($db, $class="") {
    return ($this->show_result($db, $class));
  }

  #==========================================================================
  # Function : show
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a two-dimensional array in an HTML table
  #            format.
  # Arguments: $ary   - The 2D array to diaplay.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments :
  # History  : 990616 - removed redundant code.(JSG)
  #==========================================================================
  function show($ary, $class="") {
    if (!$this->verify_2d_array($ary)) {
      return 0;
    }

    $rows = 0;

    $this->table_open($class);
    if ($this->show_table_heading_row($ary, $class)) {
      $rows = $this->show_table_rows($ary, $class);
    }
    $this->table_close($class);

    return $rows;
  }

  #==========================================================================
  # Function : show_result
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a database query result in an HTML table
  #            format.
  # Arguments: $db    - The database result.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments :
  # History  :
  #==========================================================================
  function show_result($db, $class="") {
    if (!$this->verify_db($db)) {
      return 0;
    }

    $rows = 0;

    $this->table_open($class);
    if ($this->show_table_heading_row_result($db, $class)) {
      $rows = $this->show_table_rows_result($db, $class);
    }
    $this->table_close($class);

    return $rows;
  }

  #==========================================================================
  # Function : show_page
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a two-dimensional array in an HTML table
  #            format. Only the rows $start to $start+$num are displayed.
  # Arguments: $ary   - The 2D array to diaplay.
  #            $start - The starting row to display.
  #            $num   - The number of rows to display.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments :
  # History  :
  #==========================================================================
  function show_page($ary, $start, $num, $class ="") {
    if (!$this->verify_2d_array($ary)) {
      return 0;
    }

    $rows = 0;

    $this->table_open($class);
    if ($this->show_table_heading_row($ary, $class)) {
      $rows = $this->show_table_page_rows($ary, $start, $num, $class="");
    }
    $this->table_close($class);

    return $rows;
  }

  #==========================================================================
  # Function : show_result_page
  #--------------------------------------------------------------------------
  # Purpose  : Starts the display of a database object in an HTML table
  #            format. Only the rows $start to $start+$num are displayed.
  # Arguments: $db    - The database result.
  #            $start - The starting row to display.
  #            $num   - The number of rows to display.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed.
  # Comments :
  # History  :
  #==========================================================================
  function show_result_page($db, $start, $num, $class="") {
    if (!$this->verify_db($db)) {
      return 0;
    }

    $rows = 0;

    $this->table_open($class);
    if ($this->show_table_heading_row_result($db, $class)) {
        $rows = $this->show_table_page_rows_result($db, $start, $num, $class);
    }
    $this->table_close($class);

    return $rows;
  }



  #==========================================================================
  ## Row functions
  #==========================================================================

  #==========================================================================
  # Function : show_table_heading_row
  #--------------------------------------------------------------------------
  # Purpose  : Uses the passed array to create an HTML header row.
  # Arguments: $ary   - The array to use.
  #            $class - [optional] Used for CSS control.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  :
  #==========================================================================
  function show_table_heading_row($ary, $class="") {
    if (!$this->verify_2d_array($ary)) {
      return 0;
    }

    if (isset($this->heading) && $this->heading) {
      reset($ary);
      list($key, $val) = each($ary);
      $this->table_heading_row($val, $class);
    }

    return 1;
  }

  #==========================================================================
  # Function : show_table_heading_row_result
  #--------------------------------------------------------------------------
  # Purpose  : Uses the passed database object to create an HTML header row.
  # Arguments: $db    - The database object
  #            $class - [optional] Used for CSS control.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  :
  #==========================================================================
  function show_table_heading_row_result($db, $class="") {
    if (!$this->verify_db($db)) {
      return 0;
    }

    if ($this->heading) {
      if ($db->next_record()) {
        $this->table_heading_row($db->Record, $class);
        $db->seek($db->Row-1);
        return 1;
      }
      else {
        return 0;
      }
    }
    return 1;
  }

  #==========================================================================
  # Function : table_heading_row
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to create a table heading row.
  # Arguments: $data  - The array of data that represents cells within a row.
  #            $class - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  : 990618 - Fixed return on select_colnames (JSG).
  #          : 020424 - Assume $row = 0 for header.  Avoid PHP uninitialized
  #                     variable error (LEH).
  #          : 020428 - Pass whole $data array to table_row_open if 
  #                     $this->full_data_to_table_row_open is set.
  #==========================================================================
  function table_heading_row($data, $class="") {
    $row = 0;
    if (!is_array($data)) {
      return;
    }

    if ($this->full_data_to_table_row_open) {
      $this->table_row_open($row, $data, $class);
    } else {
      $d = $this->select_colnames($data);
      $this->table_row_open($row, $d, $class);
    }
    $this->set_checkbox_heading($class);
    $this->show_table_heading_cells($data, $class);

    # call virtual function
    if (isset($this->add_extra) && $this->add_extra) {
      $this->table_heading_row_add_extra($data, $class);
    }

    $this->table_row_close(0, $class);
  }

  #==========================================================================
  # Function : show_table_rows
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed array displaying each row of data as an HTML
  #            table row.
  # Arguments: $ary   - The array of data to display.
  #            $class - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function show_table_rows($ary, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_rows()<br>\n");
    }

    if (!$this->verify_2d_array($ary)) {
      return 0;
    }

    $row = 0;

    reset($ary);
    while(list($key, $val) = each($ary)) {
      ## Process a single row
      $this->table_row($row++, $key, $val, $class);
    }

    return $row;
  }

  #==========================================================================
  # Function : show_table_rows_result
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed database object displaying each record as an
  #            HTML table row.
  # Arguments: $db    - The database object
  #            $class - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  : 990617 - fixed return. Was "row" changed to "$row".
  #==========================================================================
  function show_table_rows_result($db, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_rows_result()<br>\n");
    }

    if (!$this->verify_db($db)) {
      return 0;
    }

    $row = 0;

    while($db->next_record()) {
      ## Process a table row
      $this->table_row($row, $row, $db->Record, $class);
      $row++;
    }

    return $row;
  }

  #==========================================================================
  # Function : show_table_page_rows
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed array displaying each row of data as an HTML
  #            table row. However, data does not start displaying until
  #            $start element and end after $num rows.
  # Arguments: $ary   - The array object.
  #            $start - Start row displaying at this element.
  #            $num   - The number of rows to display.
  #            $class - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  : 990616 - $row was incrementing (++) in for loop and within
  #                     the table_row function call.
  #==========================================================================
  function show_table_page_rows($ary, $start, $num, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_page_rows()<br>\n");
    }

    if (!$this->verify_2d_array($ary)) {
      return 0;
    }

    $row = 0;

    $max = count($ary);
    if (($start < 0 ) || ($start > $max)) {
      return 0;
    }
    $max = min($start+$num, $max);

    for ($row = $start; $row < $max; $row++) {
      ## Process a single row
      $this->table_row($row, $row, $ary[$row], $class);
    }

    return ($row - $start);
  }

  #==========================================================================
  # Function : show_table_page_rows_result
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed database object displaying each record as an
  #            HTML table row. However, data does not start displaying until
  #            $start record and ends after $num records have been displayed.
  # Arguments: $db    - The database object.
  #            $start - Start row displaying at this record.
  #            $num   - The number of rows to display.
  #            $class - [optional] Used for CSS control.
  # Returns  : The number of rows displayed
  # Comments :
  # History  :
  #==========================================================================
  function show_table_page_rows_result($db, $start, $num, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_page_rows_result()<br>\n");
    }

    if (!$this->verify_db($db)) {
      return 0;
    }

    $row = $start;
    $fin = $start + $num;

    $db->seek($start);
    while($db->next_record() && ($row < $fin)) {
      ## Process a table row
      $this->table_row($row, $row, $db->Record, $class);
      $row++;
    }

    return ($row - $start);
  }

  #==========================================================================
  # Function : table_row
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to create a table row. Calls all of the
  #            cell-related functions.
  # Arguments: $row     -
  #            $row_key -
  #            $data    - The array of data that represents cells within a row.
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  : 020428 - Pass whole $data array to table_row_open if 
  #                     $this->full_data_to_table_row_open is set.
  #==========================================================================
  function table_row($row, $row_key, $data, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>table_row()<br>\n");
    }

    if ($this->full_data_to_table_row_open) {
      $this->table_row_open($row, $data, $class);
    } else {
      $d = $this->select_colnames($data);
      $this->table_row_open($row, $d, $class);
    }
    $this->set_checkbox($row, $row_key, $data, $class);
    $this->show_table_cells($row, $row_key, $data, $class);

    # call virtual function
    if (isset($this->add_extra) && $this->add_extra) {
      $this->table_row_add_extra($row, $row_key, $data, $class);
    }

    $this->table_row_close($row, $class);
  }



  #==========================================================================
  ## Field/Cell functions
  #==========================================================================

  #==========================================================================
  # Function : set_checkbox_heading
  #--------------------------------------------------------------------------
  # Purpose  : This function creates an empty header cell to coincide with
  #            the checkbox option for that column.
  # Arguments: $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function set_checkbox_heading($class="") {
    global $debug;

    if ($debug) {
      printf("<p>set_checkbox_heading()<br>\n");
    }

    ## Checkbox handling...
    if (isset($this->check) && $this->check) {
      $this->table_heading_cell(0, "&nbsp;", $class);
    }
  }

  #==========================================================================
  # Function : set_checkbox
  #--------------------------------------------------------------------------
  # Purpose  : Creates an HTML checkbox based on the passed data, only if
  #            the member variable $check is set.
  # Arguments: $row     - The row number.
  #            $row_key - The row key.
  #            $data    - The data array.
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function set_checkbox($row, $row_key, $data, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>set_checkbox()<br>\n");
    }

    ## Checkbox handling...
    if (isset($this->check) && $this->check) {
      $this->table_checkbox_cell($row, $row_key, $data, $class);
    }
  }

  #==========================================================================
  # Function : show_table_heading_cells
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed array and displays each item in an HTML table
  #            header cell.
  # Arguments: $data    - The data array.
  #            $class   - [optional] Used for CSS control.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  : 990618 - Fixed problem with filtering headers (JSG).
  #          : 020424 - Fixed code typo - changed $cell=0 to $col=0 (LEH).
  #==========================================================================
  function show_table_heading_cells($data, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_heading_cells()<br>\n");
    }

    if (!$this->verify_array($data)) {
      return 0;
    }

    $col = 0;
    $d = $this->select_colnames($data);

    ## Create regular cells
    reset($d);
    while(list($key, $val) = each($d)) {
      $this->table_heading_cell($col++, $val, $class);
    }

    return 1;
  }

  #==========================================================================
  # Function : show_table_cells
  #--------------------------------------------------------------------------
  # Purpose  : Walks the passed array and displays each item in an HTML table
  #            cell.
  # Arguments: $row     - The row number.
  #            $row_key - The row key.                  [for derived classes]
  #            $data    - The data array.
  #            $class   - [optional] Used for CSS control.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  :
  #==========================================================================
  function show_table_cells($row, $row_key, $data, $class="") {
    global $debug;

    if ($debug) {
      printf("<p>show_table_cells()<br>\n");
    }

    if (!$this->verify_array($data)) {
      return 0;
    }

    $cell = 0;
    $d = $this->select_colnames($data);

    ## Create regular cells
    reset($d);
    while(list($key, $val) = each($d)) {
      if (isset($data[$val])) {
        $this->table_cell($row, $cell++, $val, $data[$val], $class);
      }
    }

    return 1;
  }

  #==========================================================================
  # Function : table_cell
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to render a single cell.
  # Arguments: $row   - The row number.                 [for derived classes]
  #            $col   - The column number.              [for derived classes]
  #            $key   - The key value.                  [for derived classes]
  #            $val   - The data value.
  #            $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments :
  # History  :
  #==========================================================================
  function table_cell($row, $col, $key, $val, $class="") {
    $this->table_cell_open($class);
    printf("%s", $val);
    $this->table_cell_close($class);
  }

  function table_cell_open($class="") {
    printf("  <td%s>",
      $class?" class=$class":"");
  }

  function table_cell_close($class="") {
    printf("</td>\n");
  }

  #==========================================================================
  # Function : table_heading_cell
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to render a single header cell.
  # Arguments: $col   - The column number.              [for derived classes]
  #            $val   - The data value.
  #            $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments :
  # History  : 990620 - Added column remapping.
  #==========================================================================
  function table_heading_cell($col, $val, $class="") {
    $this->table_heading_cell_open($class);

    ## Check for column name remapping
    if (isset($this->map_cols) && $this->verify_array($this->map_cols)) {
      reset($this->map_cols);
      while(list($key, $name) = each($this->map_cols)) {
        if ($key == $val) {
          $val = $name;
          break;
        }
      }
    }

    printf("%s", $val);
    $this->table_heading_cell_close($class);
  }

  #==========================================================================
  # Function : table_heading_cell_open
  #--------------------------------------------------------------------------
  # Purpose  : Starts a header cell.
  # Arguments: $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments : Low-level function for table_heading_cell()
  # History  :
  #==========================================================================
  function table_heading_cell_open($class="") {
    printf("  <th%s>", $class?" class=$class":"");
  }

  #==========================================================================
  # Function : table_heading_cell_close
  #--------------------------------------------------------------------------
  # Purpose  : Ends a header cell.
  # Arguments: $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments : Low-level function for table_heading_cell()
  # History  :
  #==========================================================================
  function table_heading_cell_close($class="") {
    printf("</th>\n");
  }

  #==========================================================================
  # Function : table_checkbox_cell
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to display a checkbox. This function runs
  #            if the member variable $check has been set. $check should be
  #            set to some key within the $data array (ex: if $data["myKey"],
  #            then set $check="myKey").
  # Arguments: $row     - The row currently being written.
  #            $row_key - If $data[$this-check] is empty, then this variable
  #                       is used instead.
  #            $data    - An array of data information.
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function table_checkbox_cell($row, $row_key, $data, $class="") {
    if ($this->check) {
      printf("  <td%s><input type=\"checkbox\" name=\"%s[%s]\" value=\"%s\"></td>\n",
        $class?" class=$class":"",
        $this->check,
        $row,
        empty($data[$this->check])?$row_key:$data[$this->check]);
    }
  }

  #==========================================================================
  ## Utility functions (used to be in util.inc, but were used only here and
  ## did create a lot of confusion on installation) -- KK
  #==========================================================================

  #==========================================================================
  # Function : verify_array
  #--------------------------------------------------------------------------
  # Purpose  : Verifies an array
  # Arguments: $ary   - The array to verify.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  :
  #==========================================================================
  function verify_array($ary) {
    if (!is_array($ary)) {
      return 0;
    }

    return 1;
  }

  #==========================================================================
  # Function : verify_2d_array
  #--------------------------------------------------------------------------
  # Purpose  : Verifies a 2D array
  # Arguments: $ary   - The array to verify.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  : 990616 - Removed "$this->" from "verify_array". (JSG)
  #==========================================================================
  function verify_2d_array($ary) {
    if (!$this->verify_array($ary)) {
      return 0;
    }

    reset($ary);
    if (!is_array(current($ary))) {
      return 0;
    }

    reset($ary);

    return 1;
  }

  #==========================================================================
  # Function : verify_db
  #--------------------------------------------------------------------------
  # Purpose  : Verifies a database object for results.
  # Arguments: $db   - The database object to verify.
  # Returns  : 1 on success, 0 on error.
  # Comments :
  # History  :
  #==========================================================================
  function verify_db($db) {
    if (!isset($db) && !$db) {
      return 0;
    }

    if ($db->num_rows() > 0) {
      return 1;
    }

    return 0;
  }

  #==========================================================================
  # Function : print_array
  #--------------------------------------------------------------------------
  # Purpose  : Debugging function that prints an array
  # Arguments: $ary   - The array to print.
  # Returns  : 
  # Comments : Recursive if an array is found within $ary
  # History  :
  #==========================================================================
  function print_array($ary) {
    if (is_array($ary)) {
      while(list($key, $val) = each($ary)) {
        echo "&nbsp;&nbsp;$key = $val<br>\n";
        if (is_array($val)) {
          $this->print_array($val);
        }
      }
    }
  }

  #==========================================================================
  ## Helper functions
  #==========================================================================

  #==========================================================================
  # Function : select_colnames
  #--------------------------------------------------------------------------
  # Purpose  : Selects the column names that should be displayed in an HTML
  #            table. This is based on the $fields variable, if set. If it
  #            is not set, then all fields names are used. This is how you
  #            filter displayed data.
  # Arguments: $data - A array containing information about the column
  #                    names. If $fields is not used, then this variable is
  #                    used instead.
  # Returns  : An array containing the column names.
  # Comments :
  # History  :
  #==========================================================================
  function select_colnames($data) {
    global $debug;

    if ($debug) {
      printf("<p>select_colnames()<br>\n");
    }

    if (!(isset($this->fields) && is_array($this->fields)) && is_array($data)) {
      reset($data);
      while(list($key, $val) = each($data)) {
        if (ereg($this->filter, $key)) {
          $this->fields[] = $key;
        }
      }
    }
    $d = $this->fields;

    if ($debug) {
      $this->print_array($d);
      printf("select_colnames() return<br>");
    }

    return $d;
  }

  #==========================================================================
  # Misc. functions
  #==========================================================================

  #--------------------------------------------------------------------------
  ## The following functions provide a very basic rendering
  ## of a HTML table with CSS class tags. Table is useable
  ## with them or the functions can be overridden for a
  ## more complex functionality.
  #--------------------------------------------------------------------------

  #--------------------------------------------------------------------------
  ## Table open and close functions.
  #--------------------------------------------------------------------------

  #==========================================================================
  # Function : table_open
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to open a table.
  # Arguments: $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments :
  # History  :
  #==========================================================================
  function table_open($class="") {
    global $debug;

    if ($debug) {
      printf("<p>table_open()<br>\n");
    }

    printf("<table%s>\n", $class?" class=$class":"");
  }

  #==========================================================================
  # Function : table_close
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to close a table.
  # Arguments: $class - [optional] Used for CSS control.
  # Returns  : Nothing
  # Comments : $class is not used by this function, but is available for
  #            derived classes that override this function.
  # History  :
  #==========================================================================
  function table_close($class="") {
    global $debug;

    if ($debug) {
      printf("<p>table_close()<br>\n");
    }

    printf("</table>\n");
  }

  ## Row open and close functions.

  #==========================================================================
  # Function : table_row_open
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to open a table row.
  # Arguments: $row -   This variable is for derived classes that override
  #                     this function that want access to the row number for
  #                     the row about to be opened.
  #            $data -  This variable is for derived classes that override
  #                     this function that want access to the row data for
  #                     the row about to be opened.
  #            $class - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function table_row_open($row, $data, $class="") {
    printf(" <tr%s>\n", $class?" class=$class":"");
  }

  #==========================================================================
  # Function : table_row_close
  #--------------------------------------------------------------------------
  # Purpose  : Outputs HTML code to close a table row.
  # Arguments: $row -     This variable is for derived classes that override
  #                       this function that want access to the row number
  #                       for the row about to be closed.
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments : $class is not used by this function, but is available for
  #            derived classes that override this function.
  # History  :
  #==========================================================================
  function table_row_close($row, $class="") {
    printf(" </tr>\n");
  }

  #==========================================================================
  ## Function overrides
  #==========================================================================

  #==========================================================================
  # Function : table_heading_row_add_extra
  #--------------------------------------------------------------------------
  # Purpose  : Virtual function for derived classes. This function is called
  #            after all header cells have been created. It allows the
  #            programmer to add additional HTML code to the header row
  #            before it is closed.
  # Arguments: $data
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function table_heading_row_add_extra($data, $class="") {
  }

  #==========================================================================
  # Function : table_row_add_extra
  #--------------------------------------------------------------------------
  # Purpose  : Virtual function for derived classes. This function is called
  #            after all cells have been created. It allows the programmer to
  #            add additional HTML code to the row before it is closed.
  # Arguments: $row
  #            $row_key
  #            $data
  #            $class   - [optional] Used for CSS control.
  # Returns  :
  # Comments :
  # History  :
  #==========================================================================
  function table_row_add_extra($row, $row_key, $data, $class="") {
  }
}
?>
