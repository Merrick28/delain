<?php
/*
 * PHP Base Library
 *
 * (C) Copyright 1998 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp, Eric Ries
 *
 * $Id: sqlquery-eric.inc,v 1.2 2000/07/12 18:22:35 kk Exp $
 *
 * WARNING! This is a temporary file only and it is here to
 *          be merged with sqlquery.inc and for cleanup.
 *          NOT FOR PRODUCTION. THIS FILE WILL GO AWAY.
 */ 

class Sql_Query {
  var $classname = "Sql_Query";  ## Persistence Support
  var $persistent_slots = array(
    "conditions", "input_size", "input_max", "method", "lang", "translate", "container", "variable"
  );
  
  var $conditions = 1;      ## Allow for that many Query Conditions
  var $input_size = 20;     ## Used in text input field creation
  var $input_max  = 80;

  var $method     = "post"; ## Generate get or post form...
  var $lang       = "en";   ## HTML Widget language

  var $translate = "on";    ## If set, translate column names
  var $container = "on";    ## If set, create a container table
  var $variable  = "on";    ## if set, create variable size buttons
  var $use_js    = "on";    ## if set, use JavaScript buttons to do advanced queries

  var $BLANK = -1;          ## constant value for select statement $addblank value
  
  ## HTML Widget dictionary
  var $dict = array(
    "de" => array(
      "searchfor" => "Suchen nach:",
      "and"       => "und",
      "or"        => "oder",
      "like"      => "enthält",
      "reset"     => "Neu",
      "submit"    => "Ausführen",
      "less"      => "Weniger",
      "more"      => "Mehr"
    ),
    
    "en" => array(
      "searchfor" => "Search for:",
      "and"       => "and",
      "or"        => "or",
      "like"      => "contains",
      "reset"     => "Reset Query",
      "submit"    => "Submit Query",
      "less"      => "Fewer",
      "more"      => "More"
    )
  );

  ## SQL comparision dictionary
  var $compare = array(
        "like" => "like",
        ">"    => ">",
        "<"    => "<",

        ">="   => ">=",
        "<="   => "<=",
        "="    => "=",
        "<>"   => "<>"
      );


  function start($class = "") {
  }

  ## selection:
  ##
  ## Create a <select> tag of the class $class with the name $name.
  ## The tag contains the options named in array $option. If $trans
  ## is true, $option is exspected to be a hash of 
  ## "long name " => "sqlname" pairs. The option matching $old
  ## is created with the attribute "selected".
  ##
  ## $js is optional JavaScript code that will be put into the onChange() method
  ## $addblank, optional. if non-zero, the select's first element will be a blank entry, ( $addblank => $this->BLANK )

  function selection($name, $option, $old = "", $trans = "", $class = "", $js = "", $addblank = 0) {
    $res  = "";
    $res .= sprintf("<select%s name=\"%s\" onChange=\"%s\">\n",
              ($class)?" class=$class":"",
              $name,$js);
	
        ## Add blank value for all selects. Controlled by $addblank 

	if( $addblank )
	{	
      		$res .= sprintf("<option value=\"%s\"%s%s>%s\n",
                	$this->BLANK,
               		 ($class)?" class=$class":"",
                	 ($selected == $this->BLANK)?" selected":"",
                	 $addblank);
	}

    reset($option);
    while(list($k, $v) = each($option)) {
      if (($trans == "" && $old == $v)
       || ($trans != "" && $old == $k)) {
        $selected = " selected";
      } else {
        $selected = "";
      }
      
      $res .= sprintf("<option value=\"%s\"%s%s>%s\n",
                ($trans)?$k:$v,
                ($class)?" class=$class":"",
                ($selected)?" selected":"",
                $v);
    }
    $res .= sprintf("      </select>");
    
    return $res;
  }

  ## fieldname:
  ##
  ## Given a basename $base, and attribute name $att and an attribute
  ## number $num, this functions returns an input field name
  ## $base[$name_$num].
  ##
  ## This construct can be imported into a function namespace with a
  ## single global instruction and the field name can be easily
  ## exploded into component names and numbers.

  function makename($base, $att, $num) {
    return sprintf("%s[%s_%d]", $base, $att, $num);
  }

  ## form:
  ##
  ## Draw SQL Query selection form.
  ##
  ## if $this->use_js is set, then $option is assumed to be an associative array of table names to field names
  ##  if there are no field names for a given table, then we will query the metadata on the current database for the given table
  ##  if there are no table names specified ($option = 0) then we will query the metadata on the current database for all tables
  ##
  ## ex. $option = array( "active_sessions" => array( "sid", "name" ) ) OR $option = array( "active_sessions", "auth_user" ) 

  function form($base, $option, $class = "", $target = "") {
    global $sess;
    
    if( $this->use_js )
    {
       $table_list = array();
       $table_fields = array();


       if( is_array($option) ) 
       {
          reset( $option );
          while( list( $v, $i ) = each( $option ) )
          {

             if( is_array( $v ) )
             {
               // actually, do nothing for now
             } else {
               // we need to query metadata for table $v
               $db = new DB_Poe;
               $hash = $db->metadata( $v );
               $fields = array();

		for( $i = 0; $i < $hash["num_fields"]; $i++ )
		{
			$fields[ucwords($hash[$i]["name"])] = $hash[$i]["name"];
		}

               $table_fields[$v] = $fields;
               $table_list[$v] = ucwords($v);
             }
         }
       } else {
         // TODO: we need to query metadata for all tables
       }
       // output the relevant JavaScript stuff
       require("table_select_js.php3");
    }


    ## A hack. We cannot do language dependent initialisation of
    ## static values.
    if (isset($this->compare["like"])) {
      $this->compare["like"] = $this->dict[$this->lang]["like"];
    }
    
    ## Prepare a self-directed container form
    if ($this->container) {
      printf("<table border=1%s><tr%s><td>\n",
        ($class)?" class=$class":"",
        ($class)?" class=$class":"",
        ($class)?" class=$class":"");
    }
    printf("<form method=\"%s\" action=\"%s\">\n",
      $this->method, 
      ($target)?$target:$sess->self_url());
    
    ## Prepare the inner table, laying out the selection elements
    printf("<table%s>\n", ($class)?" class=$class":"");

    ## Build $this->conditions many selection elements    
    for ($i=1; $i<= $this->conditions; $i++) {
      printf(" <tr%s>\n",   ($class)?" class=$class":"");

      ## Build conjunction (first row does not have a conjunction)
      if ($i == 1) {
        printf("  <td%s>%s</td>\n", 
          ($class)?" class=$class":"", 
          $this->dict[$this->lang]["searchfor"]);
      } else {
        printf("  <td%s>%s</td>\n", 
          ($class)?" class=$class":"", 
          $this->selection($this->makename($base, "conj", $i),
                           array("and" => $this->dict[$this->lang]["and"], "or" => $this->dict[$this->lang]["or"]),
                           $GLOBALS[$base]["conj_".$i], 
                           "on",
                           $class));
      }
      

      if( $this->use_js )
      {
	  ## Build table selection
	  printf("  <td%s>%s</td>\n",
	    ($class)?" class=$class":"",
	    $this->selection(
	      $this->makename($base, "table_sel", $i), 
	      $table_list, 
	      $GLOBALS[$base]["table_sel_".$i],
	      $this->translate, 
	      $class, "UpdateMenu( this.form, this)", "-" ));

          ## really, we should only query the metadata for the table that is needed here, but for now
          ## we query all necessary metadata above, so we can assume that $table_list and $table_fields are set
          ## appropriately
          if( ($temp = $GLOBALS[$base]["table_sel_".$i]) && $temp != $this->BLANK )
	          $option = $table_fields[$temp];
          else
                  $option = array();

      }




      ## Build field selection
      printf("  <td%s>%s</td>\n",
        ($class)?" class=$class":"",
        $this->selection(
          $this->makename($base, "field_sel", $i), 
          $option, 
          $GLOBALS[$base]["field_sel_".$i], 
          $this->translate, 
          $class, "", '-'));
        
      ## Build comparison selection
      printf("  <td%s>%s</td>\n",
        ($class)?" class=$class":"",
        $this->selection(
          $this->makename($base, "comp", $i), 
          $this->compare, 
          $GLOBALS[$base]["comp_".$i], 
          "on", 
          $class));
      ## Create text input field.
      printf("  <td%s><input type=\"text\" name=\"%s\" value=\"%s\" size=%d maxlength=%d%s></td>\n",
        ($class)?" class=$class":"",
        $this->makename($base, "input", $i),
        $GLOBALS[$base]["input_".$i],
        $this->input_size,
        $this->input_max,
        ($class)?" class=$class":"");



      if( $this->use_js )
      {

	  ## Build table selection, again
	  printf("  <td%s>%s</td>\n",
	    ($class)?" class=$class":"",
	    $this->selection(
	      $this->makename($base, "table_sel2", $i), 
	      $table_list, 
	      $GLOBALS[$base]["table_sel2_".$i], 
	      $this->translate, 
	      $class, "UpdateMenu( this.form, this)", '-' ));

	 ## Build field selection, again
	  printf("  <td%s>%s</td>\n",
	    ($class)?" class=$class":"",
	    $this->selection(
	      $this->makename($base, "field_sel2", $i), 
	      $option, 
	      $GLOBALS[$base]["field_sel2_".$i], 
	      $this->translate, 
	      $class, "PutText( this );", '-' )); 
      }

      printf(" </tr>\n");
    }

    ## Create variable size buttons
    printf(" <tr%s>\n",  ($class)?" class=$class":"");
    printf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    
    if ($this->variable) {
      printf("  <td%s><input type=\"submit\" name=\"%s\" value=\"%s\">&nbsp;",
        ($class)?" class=$class":"",
        $this->makename($base, "more", 0),
        $this->dict[$this->lang]["more"]);
      printf("<input type=\"submit\" name=\"%s\"value=\"%s\"></td>\n",
        $this->makename($base, "less", 0),
        $this->dict[$this->lang]["less"]);
    } else {
      printf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    }
    
    printf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    printf("  <td%s><input type=\"reset\" value=\"%s\">&nbsp;",
      ($class)?" class=$class":"",
      $this->dict[$this->lang]["reset"]);
    printf("<input type=\"submit\" name=\"%s\"value=\"%s\"></td>\n",
      $this->makename($base, "submit", 0),
      $this->dict[$this->lang]["submit"]);
    
    printf(" </tr>\n");
    printf("</table>\n");
    
    printf("</form>\n");
    if ($this->container) {
      printf("</td></tr></table>\n");
    }
    printf("<!-- End %s generated query form -->\n", $this->classname);
  }
  
  ## plain_where:
  ##
  ## Given a base variable name, creates a condition suitable for
  ## the where clause of a SQL query.
  ##
  function plain_where($base) {
    for($i=1; $i<=$this->conditions; $i++) {
      ## Only create conditions for used input fields                 
      if ($GLOBALS[$base]["input_".$i] == "" || $GLOBALS[$base]["field_sel_".$i] == $this->BLANK 
          || ( $this->use_js && $GLOBALS[$base]["table_sel_".$i] == $this->BLANK ) )
        continue;

      ## If necessary, add conjunction
      if ($q != "")
        $q .= sprintf(" %s ", $GLOBALS[$base]["conj_".$i]);
      

      ## Handle "like"
      if ($GLOBALS[$base]["comp_".$i] == "like")
        $v = "%".$GLOBALS[$base]["input_".$i]."%";
      else
        $v = $GLOBALS[$base]["input_".$i];


      ## now, if the user wants to compare a table.field with another table.field
      ## we don't need the single quotes
      ## TODO: we should not use quotes for numeric data types either
      if( $this->use_js && ($c1=$GLOBALS[$base]["field_sel2_".$i]) > $this->BLANK && ($c2=$GLOBALS[$base]["table_sel2_".$i]) > $this->BLANK )
      {
         $v = sprintf( "%s.%s", $c2, $c1 );
      } else {
        $v = "'$v'";
      }


      ## Create subcondition
      if( $this->use_js )
      {
        $q .= sprintf("%s.%s %s %s",
          $GLOBALS[$base]["table_sel_".$i],
          $GLOBALS[$base]["field_sel_".$i],
          $GLOBALS[$base]["comp_".$i],
          $v);
      } else {
        $q .= sprintf("%s %s %s",
          $GLOBALS[$base]["field_sel_".$i],
          $GLOBALS[$base]["comp_".$i],
          $v);
      }
    }
    
    if (!$q) {
      $q = "1=0";
    }
    
    return "( $q )";
  }


  ## generic_submit: 
  ## Used by functions to make sure that variable conditions are handled appropriately...

  function generic_submit($what, $base, $incr = 1) {
    if (isset($GLOBALS[$base]["less_0"]))
      $this->conditions -= $incr;
    
    if (isset($GLOBALS[$base]["more_0"]))
      $this->conditions += $incr;
    
    if ($this->conditions < 1)
      $this->conditions = 1;
    
	return;
  }


  ## where:
  ##
  ## Same as plain_where(), but also inspects the submit button
  ## used to submit the query. Changes $this->conditions appropriately.
	function where( $base, $incr = 1 )
	{
		$this->generic_submit( "where", $base, $incr );
		return $this->plain_where( $base );
	}

  ## from:
  ##
  ## Only useful if using Sql_Query with $use_js turned on. Returns a string suitable for use in a
  ## query of the form "SELECT x FROM (from) WHERE (query);"

	function from( $base, $incr = 0 )
	{	
		$this->generic_submit( "from", $base, $incr );
		return $this->plain_from( $base );
	}

	function plain_from( $base )
	{
		$ret = array();
		for($i=1; $i<=$this->conditions; $i++) 
		{     
			## Only create conditions for used input fields
			if ($GLOBALS[$base]["input_".$i] == "" || $GLOBALS[$base]["table_sel_".$i] == -1 
					     || $GLOBALS[$base]["field_sel_".$i] == -1 )
      			
        			continue;


			$table = $GLOBALS[$base]["table_sel_".$i];
			$ret[$table] = $table;

			$table = $GLOBALS[$base]["table_sel2_".$i];

			if( $table && $table != -1 )
				$ret[$table] = $table;
		}
		
		return implode( ",", $ret );
	}

};

/* getQueryFields()
 * 
 * Obtains a list of fields corresponding to $table from MySQL
 * Attempts to filter out fields that are used internally (i.e. ID fields)
 * This is only temporary, should be replaced ASAP with appropriate calls to PHPLIB classes
 */
function getQueryFields( $db, $table )
{
	$fields = array();
	$result = mysql_list_fields( $db, $table );
	if( $result <= 0 )
	{
		print "Error: $db $table<br>";
		return;
	}
	while( $ob = mysql_fetch_field( $result ) )
	{
		$fields[$ob->name] = ucwords($ob->name);
	}
	
	return $fields;
}	


?>
