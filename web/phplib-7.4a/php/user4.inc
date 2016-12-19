<?php
/**
* Session Management for PHP3
*
* @copyright  1998,1999 NetUSE GmbH Boris Erdmann, Kristian Koehntopp
*             2001, Maxim Derkachev <kot@books.ru>
* @version    $Id: user4.inc,v 1.1 2002/10/01 16:13:56 joestewart Exp $
* @package    PHPLib
* @access     public
*/ 
class User {

  /**
  *
  */
  var $classname = "User";
  
  
  /**
  * AC storage name
  *
  * @var  string
  */
  var $name = "";

  
  /**
  * AC storage  ID
  *
  * @var  string
  */
  var $id = "";
  
  
  /**
  * A name of a global array where references to registered user vars are stored.
  *
  * @var  string
  */
  var $vars_array = 'PHPLIB_USER_VARS';
  
  
  /**
  * Do we need to push user vars into global namespace?
  * 
  * (they are anyway accessible via special array, $PHPLIB_USER_VARS by default
  * 
  * @var  boolean
  */
  var $register_globals = true;		
  
  
  /**
  * Name of data storage container
  * 
  * var string
  */
  var $that_class = '';
  
  
  /**
  *
  * @var  object CT_*
  */        
  var $that;
  

  /**
  *
  * @param  string
  */
  function start($sid = '') {
    $this->get_id($sid);

    if ("" == $this->name)
      $this->name = $this->classname;
      
    $name = $this->that_class;
    $this->that = new $name;
    $this->that->ac_start();

    $this->thaw();
  } // end func start
  
  
  /**
  * registers user variables
  *
  * @param  array
  */
  function register ($things) {
  
    $things = preg_split('/\s*,\s*/', trim($things) );
    
    foreach ($things as $thing) {
      if (!isset($GLOBALS[$thing])) 
        continue;
      
      $GLOBALS[$this->vars_array][$thing] =& $GLOBALS[$thing];
      
    }
    
  } // end func register


  /**
  * find out if a var is registered user variable
  *
  * @param  string
 */
  function is_registered($name) {
    return (boolean)(isset($GLOBALS[$this->vars_array][trim($name)]));
  } // end func is_registered


  /**
  * cancel the registration of a registered user variables
  *
  */
  function unregister($things) {
  
    $things = preg_split('/\s*,\s*/', trim($things) );
    
    foreach ($things as $thing) {
      if (!isset ($GLOBALS[$this->vars_array][$thing])) 
        continue;
        
      unset ($GLOBALS[$this->vars_array][$thing]);
    }
    
  } // end func unregister
  
  /**
  *
  * @param string
  */
  function get_id($id = "") {
    $this->id = $id;
  } // end func get_id
  

  /**
  * Delete the current user record 
  */
  function delete() {
      $this->that->ac_delete($this->id, $this->name);
  } // end func delete

  
  /**
  * serializes user data (stored in $GLOBALS[$this->vars_array])
  */
  function serialize() {
    return serialize($GLOBALS[$this->vars_array]);
  } // end func serialize


  /**
  * prepare serialized user data and store it in a storage container
  *
  */
  function freeze() {
    if ($this->id == 'nobody')
      return;

    if(!$this->that->ac_store($this->id, $this->name, $this->serialize()))
      $this->that->ac_halt("User: freeze() failed.");
  } // end func freeze
    
  
  /* 
  * restore saved registered user variables
  **/
  function thaw() {
  
    $vals = $this->that->ac_get_value($this->id, $this->name);
    
    $GLOBALS[$this->vars_array] = unserialize($vals);
    
    if ($this->register_globals && is_array ($GLOBALS[$this->vars_array]) ) {
    
      reset ($GLOBALS[$this->vars_array]);
      while (list ($k, $v) = each ($GLOBALS[$this->vars_array])) {
        $GLOBALS[$k] = $v;
        $GLOBALS[$this->vars_array][$k] =& $GLOBALS[$k]; // change the entry in user vars array, so it is now a reference pointing to a global variable. 
      }
      
    }
    
  } // end func thaw


 } // end class User
?>
