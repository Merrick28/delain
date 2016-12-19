
# This example extends the standard PHPLIB dataset, providing
# a single updateable record
#
# Usage would be:
#
# $db = new DB_Sql_Updateable;
# $db->get_record($id);
# $db->set_field_1($new_value);
# $db->save_record();
#
# Note: this example is PostgreSQL specific
# Note: table specific information is hard coded
#

class DB_Sql_Updateable extends DB_Sql {
  var $id = 0;
	var $dirty_fields = array();

  function get_record($id) {
		$this->connect();
		$query = sprintf("select * from players where player_id = '%s'", $id);
		$this->Query_ID = pg_Exec($this->Link_ID, $query);
   	$this->Row = 0;

   	$this->Error = pg_ErrorMessage($this->Link_ID);
   	$this->Errno = ($this->Error == "")?0:1;
   	if (!$this->Query_ID) {
     		$this->halt("Invalid SQL: ".$Query_String);
   	}

		$this->next_record();
		$this->id = $this->f("player_id");

    return $this->Query_ID;
  }

  function save_record() {
    if ( count($this->dirty_fields) ) {
      $db = new DB_Sql;

	  	$str = "update players set ";

  		$i = 1;

	  	while (list($key, $val) = each($this->dirty_fields)) {
  			$str = $str . " " . $key . " = '" . $val . "'";

		  	if ( $i < count($this->dirty_fields) ) {
	  			$str = $str . ", ";
  			}

	  		$i++;
  		}

  		$str = $str . " where player_id = '$this->id'";

  		$db->query($str);

      $this->dirty_fields = array();
    }
  }

  function set_field_1($n) {
		$this->dirty_fields["field_1"] = $n;
  }

  function set_field_2($n) {
		$this->dirty_fields["field_2"] = $n;
  }
}
