<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 Cameron Taggart (cameront@wolfenet.com)
 *        Modified by Guarneri carmelo (carmelo@melting-soft.com)
 *					 
 * $Id: db_odbc.inc,v 1.6 2004/07/23 20:36:29 layne_weathers Exp $
 */ 

class DB_ODBC {
  
  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";

  /* public: configuration parameters */
  var $Auto_Free     = 0;     ## Set to 1 to automatically free results
  var $PConnect      = 0;     ## Set to 1 to use persistent database connections
  var $Debug         = 0;     ## Set to 1 for debugging messages
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore error, but spit a warning)
  var $Seq_Table     = "db_sequence"; ## Name of the sequence table
  var $Seq_ID_Col    = "p_nextid";    ## Name of the Sequence ID column in $Seq_Table
  var $Seq_Name_Col  = "p_seq_name";  ## Name of the Sequence Name column in $Seq_Table
  var $Lock_Table    = "db_sequence"; ## Name of the lock table
  var $Lock_Name_Col = "p_seq_name";  ## Name of the lock Name column
  var $Lock_ID_Col   = "p_nextid";    ## Name of the lock Name column
  var $Lock_Timeout  = 5;             ## Wait this long for a lock
  var $Lock_Override = 1;             ## Set to 1 to override lock after $Lock_Timeout seconds
  var $UseODBCCursor = 0;             ## Set to 1 to enable execution of stored procedures on the server

  /* public: result array and current row number */
  var $Record   = array();
  var $Row      = 0;
  var $RowCount = -1;     ## used to remember num_rows() return value

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "odbc";
  var $revision = "1.2";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;


  /* public: constructor */
  function DB_ODBC($query = "") {
    if($query) {
      $this->query($query);
    }
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
  }

  /* public: connection management */
  function connect($Database = "", $Host = "", $User = "", $Password = "") {
    /* Handle defaults */
    if ("" == $Database)
      $Database = $this->Database;
    if ("" == $Host)
      $Host     = $this->Host;
    if ("" == $User)
      $User     = $this->User;
    if ("" == $Password)
      $Password = $this->Password;
      
    /* establish connection, select database */
    if ( 0 == $this->Link_ID ) {
      if(!$this->PConnect) {
        $this->Link_ID = odbc_connect($this->Database, $this->User, $this->Password, $this->UseODBCCursor);
      } else {
        $this->Link_ID = odbc_pconnect($this->Database, $this->User, $this->Password, $this->UseODBCCursor);
      }
      if (!$this->Link_ID) {
        $this->connect_failed();
      }
    }
    
    return $this->Link_ID;
  }

  function connect_failed() {
    $this->Halt_On_Error = "yes";
    $this->halt("connect ($this->User, \$Password, $this->Database, $this->UseODBCCursor) failed");
  }

  /* public: discard the query result */
  function free() {
      odbc_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }

  /* public: perform a query */
  function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

    if ($this->Debug)
      printf("Debug: query = %s<br>\n", $Query_String);

#   rei@netone.com.br suggested that we use this instead of the odbc_exec().
#   He is on NT, connecting to a Unix MySQL server with ODBC. -- KK
#    $this->Query_ID = odbc_prepare($this->Link_ID,$Query_String);
#    $this->Query_Ok = odbc_execute($this->Query_ID);
        
    $this->Query_ID = odbc_exec($this->Link_ID, $Query_String);
    $this->RowCount = -1; # reset num_rows() return value
    $this->Row   = 0;
    $this->Errno = 0;
    $this->Error = "";
    odbc_binmode($this->Query_ID, 1);
    odbc_longreadlen($this->Query_ID, 4096);
    
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: walk result set */
  function next_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = array();
    $this->Row   += 1;
    $stat = odbc_fetch_row($this->Query_ID, $this->Row);
    $this->Errno = 0;
    $this->Error = "";

    if (!$stat) {
      if ($this->Auto_Free) {
        $this->free();
	  }
    } else {
      $count = odbc_num_fields($this->Query_ID);
      for ($i=1; $i<=$count; $i++) {
        $this->Record[strtolower(odbc_field_name($this->Query_ID, $i))] = odbc_result($this->Query_ID, $i);
      }
    }
    return $stat;
  }

  /* public: position in result set */
  function seek($pos = 0) {
    $this->Row = $pos;
    return 1;
  }

  /* public: get the time in msecs */
  function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return (float)$usec + (float)$sec;
  }

  /* public: table locking */
/*
ODBC is not guaranteed do table locking natively. This function emulates 
locking with certain constraints. The intention is to provide at least the
minimum functionality required to implement sequences via nextid().

This function maintains a list of locked tables in a lock table. To lock 
a table it inserts a row into the lock table with the locked table name 
suffixed with '_p_lock' as a primary key and with a value of 0. 
The suffix and value are used so that the sequence table can be used as 
the lock table if desired.
While this row exists a lock will not be granted to another process.
To protect against threads failing to release a lock, if a lock is not
obtained within $this->Lock_Timeout seconds the lock may be deleted. 
The timeout value needs to be set to a reasonable length based on the 
expected transaction time on the locked table.

This method will only be effective if the locked table is accessed 
exclusively via this class. This is not a database-enforced lock and 
there is nothing preventing other applications from modifying a table 
while it is 'locked'.

The function uses microtime to prevent multiple threads all hitting the 
table simultaneously on the tick of a second.

The function halt()s if a lock cannot be obtained.
*/
  function lock($table, $mode="write") {
    
    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    $getsql   = sprintf("INSERT into %s (%s, %s) VALUES ('%s', 0)",
                 $this->Lock_Table,
                 $this->Lock_Name_Col,
                 $this->Lock_ID_Col,
                 strtolower($table)."_p_lock");
    $delsql   = sprintf("DELETE FROM %s where %s='%s' AND %s=0",
                 $this->Lock_Table,
                 $this->Lock_Name_Col,
                 strtolower($table)."_p_lock",
                 $this->Lock_ID_Col);
    $selsql   = sprintf("SELECT * FROM %s where %s='%s' AND %s=0",
                 $this->Lock_Table,
                 $this->Lock_Name_Col,
                 strtolower($table)."_p_lock",
                 $this->Lock_ID_Col);
    $timeout  = $this->getmicrotime() + $this->Lock_Timeout;
    $got_lock = 0;
    $override = $this->Lock_Override;
    while (!$got_lock) {
      $got_lock = @odbc_exec($this->Link_ID, $getsql);
      if ($this->Debug && !$got_lock) {
        echo "missed lock... looping\n";
        flush();
      }
      $currtime = $this->getmicrotime();
      if (!$got_lock) {
        if ($timeout < $currtime) {
          if (!$override) {
            # try to select existing lock
            if (!@odbc_exec($this->Link_ID, $selsql)) {
              # lock select failed. Either the table does not exist or the lock was
              # released just this instant. Try to get a lock to see which...
              $got_lock = @odbc_exec($this->Link_ID, $getsql);
              if (!$got_lock) {
                $this->halt("Lock select failed. Does the table $this->Lock_Table exist?");
              }
              return $got_lock;
            }
            $this->halt("lock() failed.");
            return 0;
          } else {
            # delete existing lock
            if ($this->Debug) {
              echo "overriding lock\n";
            }

            if (!@odbc_exec($this->Link_ID, $delsql)) {
              # lock override failed. Either the table does not exist or the lock was
              # released just this instant. Try to get a lock to see which...
              $got_lock = @odbc_exec($this->Link_ID, $getsql);
              if (!$got_lock) {
                $this->halt("Lock override failed. Does the table $this->Lock_Table exist?");
              }
              return $got_lock;
            } else {
              # just deleted the lock so try to get it straight away
              $got_lock = @odbc_exec($this->Link_ID, $getsql);
              $timeout  = $currtime + $this->Lock_Timeout; # reset the timer
              $override = 0; # override once only
              # fall through to wait loop
            }
          }
        }
      }
           
      if (!$got_lock) {
        $waittime = $currtime + 0.5;
        while ($waittime > $this->getmicrotime()) {
          ;
        }
      }
    }
    if ($this->Debug && !$got_lock) {
      echo "missed lock... bug!\n";
    } else {
      echo "got lock\n";
      flush();
    }
    return $got_lock;
  }
  
  function unlock($table = "") {
    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # Note: this unlocks ALL tables if $table is blank!
    if ($table == "") {
      $delsql   = sprintf("DELETE FROM %s where %s LIKE '%%_p_lock' AND %s=0",
                   $this->Lock_Table,
                   $this->Lock_Name_Col,
                   $this->Lock_ID_Col);
    } else {
      $delsql   = sprintf("DELETE FROM %s where %s='%s' AND %s=0",
                   $this->Lock_Table,
                   $this->Lock_Name_Col,
                   strtolower($table)."_p_lock",
                   $this->Lock_ID_Col);
    }

    $res = @odbc_exec($this->Link_ID, $delsql);
    if (!$res) {
      $this->halt("unlock() failed.");
    }
    return $res;
  }

  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return odbc_num_rows($this->Query_ID);
  }

  function num_rows() {
    # Due to a strange problem with the odbc_fetch_row function it is only 
    # possible to walk through the result set once. By storing the row count
    # this problem is avoided.
    # Once the number of rows has been calculated it is stored in $RowCount.
    if ($this->RowCount != -1) {
      return $this->RowCount;
    }

    # Many ODBC drivers don't support odbc_num_rows() on SELECT statements.
    $num_rows = odbc_num_rows($this->Query_ID);

    # This is a workaround. It is intended to be ugly.
    if ($num_rows < 0) {
      $i=10;
      while (odbc_fetch_row($this->Query_ID, $i)) 
        $i*=10;

      $j=0;
      while ($i!=$j) {
        $k= $j+intval(($i-$j)/2);
        if (odbc_fetch_row($this->Query_ID, $k))
          $j=$k;
        else 
          $i=$k;
        if (($i-$j)==1) {
          if (odbc_fetch_row($this->Query_ID, $i)) 
            $j=$i;
          else 
            $i=$j; 
        };
        //printf("$i $j $k <br>");
      };
      $num_rows=$i;
    }

    $this->RowCount = $num_rows;
    return $num_rows;
  }

  function num_fields() {
    # NOTE: this only works after next_record has been called!
    return count($this->Record)/2;
  }

  /* public: shorthand notation */
  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  function f($Name) {
    if (isset($this->Record[$Name])) {
      return $this->Record[strtolower($Name)];
    }
    return "";
  }

  function p($Name) {
    print $this->f($Name);
  }

  /* public: sequence numbers */
  function nextid($seq_name) {
    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };
    
    if ($this->lock($this->Seq_Table)) {
      /* get sequence number (locked) and increment */
      $q  = sprintf("select %s from %s where %s = '%s'",
                $this->Seq_ID_Col,
                $this->Seq_Table,
                $this->Seq_Name_Col,
                $seq_name);
      $id = odbc_exec($this->Link_ID, $q);
      $res = 0;
      if (odbc_fetch_row($id, 1)) {
        $res = array();
        $count = odbc_num_fields($id);
        for ($i=1; $i<=$count; $i++) {
          $res[strtolower(odbc_field_name($id, $i))] = odbc_result($id, $i);
        }
      }

      /* No current value, make one */
      if (!is_array($res)) {
        $currentid = 0;
        $q = sprintf("insert into %s ( %s, %s ) values('%s', %s)",
                $this->Seq_Table,
                $this->Seq_Name_Col,
                $this->Seq_ID_Col,
                $seq_name,
                $currentid);
        $id = odbc_exec($this->Link_ID, $q);
      } else {
        $currentid = $res[$this->Seq_ID_Col];
      }
      $nextid = $currentid + 1;
      $q = sprintf("update %s set %s = '%s' where %s = '%s'",
                $this->Seq_Table,
                $this->Seq_ID_Col,
                $nextid,
                $this->Seq_Name_Col,
                $seq_name);
      $id = odbc_exec($this->Link_ID, $q);
      $this->unlock();
    } else {
      $this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
      return 0;
    }
    return $nextid;
  }

  /* public: return table metadata */
  function metadata($table = "", $full = false) {
    $count = 0;
    $id    = 0;
    $res   = array();

    /*
     * Due to compatibility problems with Table we changed the behavior
     * of metadata();
     * If $full is set, metadata returns additional information
     *
     * This information is always returned:
     * $result[]:
     *   [0]["table"]  table name
     *   [0]["name"]   field name
     *   [0]["type"]   field type
     *   [0]["len"]    field length
     *   [0]["flags"]  field flags
     *
     * If $full is set this information is also returned:
     * $result[]:
     *   ["num_fields"] number of metadata records
     *   [0]["php_type"] the corresponding PHP-type 
     *   [0]["php_subtype"] the subtype of PHP-type
     *   ["meta"][field name] index of field named "field name"
     *   This one could be used if you have a field name, but no index.
     *   Test:  if (isset($result['meta']['myfield'])) { ...
     *   [unique] = field names which have an unique key, separated by space
     */

    // if no $table specified, assume that we are working with a query result
    if ($table) {
      $this->connect();
      $id = odbc_exec($this->Link_ID, "select * from $table");
      if (!$id) {
        $this->halt("Metadata query failed.");
        return false;
      }
    } else {
      $id = $this->Query_ID;
      if (!$id) {
        $this->halt("No query specified.");
        return false;
      }
    }

    $count = odbc_num_fields($id);

    for ($i=1; $i<=$count; $i++) {
      $res[$i]["table"] = $table;
      $res[$i]["name"]  = odbc_field_name ($id, $i);
      $res[$i]["type"]  = odbc_field_type ($id, $i);
      $res[$i]["len"]   = odbc_field_len   ($id, $i);
      $res[$i]["flags"] = ""; // any optional flags to report?
    }

    if ($full) {
      $uniq = array();
      $res["num_fields"] = $count;
    
      # ODBC result set starts at 1
      for ($i=1; $i<=$count; $i++) {
        $res["meta"][$res[$i]["name"]] = $i;
        switch ($res[$i]["type"]) {
          case "var string":
          case "string" :
          case "char" :
            $res[$i]["php_type"]="string";
            $res[$i]["php_subtype"]="";
            break;
          case "timestamp" :
          case "datetime" :
          case "date" :
          case "time" :
            $res[$i]["php_type"]="string";
            $res[$i]["php_subtype"]="date";
            break;
          case "blob" :
            $res[$i]["php_type"]="string";
            $res[$i]["php_subtype"]="blob";
            break;
         case "real" :
            $res[$i]["php_type"]="double";
            $res[$i]["php_subtype"]="";
            break;
         case "long" :
         default :
            $res[$i]["php_type"]="int";
            $res[$i]["php_subtype"]="";
            break;
        }
        if ( ereg("(unique_key|primary_key)",$res[$i]["flags"]) ) {
            $uniq[]=$res[$i]["name"];
        }
      }
      $res["unique"]=join(" ",$uniq);
    }
    
    // free the result only if we were called on a table
    if ($table) {
      odbc_free_result($id);
    }
    return $res;
  }

  /* public: find available table names */
  function table_names() {
    $this->connect();
    $h = odbc_tables($this->Link_ID);
    $i = 0;
    while(odbc_fetch_row($h)) {
      if (odbc_result($h, 4) == "TABLE") {
        $return[$i]["table_name"]      = odbc_result($h, 3);
        $return[$i]["tablespace_name"] = odbc_result($h, 1);
        $return[$i]["database"]        = odbc_result($h, 1);
        $i += 1;
      }
    }
    odbc_free_result($h);
    return $return;
  }

  /* private: error handling */
  function halt($msg) {
    $this->Errno = 1;
    $this->Error = "General Error (The ODBC interface cannot return detailed error messages).";
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session halted.");
  }

  function haltmsg($msg) {
    printf("<p><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>ODBC Error</b>: %s (%s)</p>\n",
      $this->Errno,
      $this->Error);
  }

}
$_php_major_version = substr(phpversion(), 0, 1);
if((4 > $_php_major_version) or !class_exists("DB_Sql"))
	{
	class DB_Sql extends DB_ODBC
		{
		function DB_Sql($query = "")
			{
			$this->DB_ODBC($query);
			}
		}
	}
unset($_php_major_version);
?>
