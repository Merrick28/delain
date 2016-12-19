<?php
// require_once($_PHPLIB["libdir"]."session/session4.inc");
/**
* PHPLib Sessions using PHP 4 build-in sessions and PHPLib storage container
* 
* @copyright  (c) 1998,1999 NetUSE GmbH Boris Erdmann, Kristian Koehntopp, 
*             2000 Maxim Derkachev <kot@books.ru>,
*             2000 Teodor Cimpoesu <teo@digiro.net>
* @author     Maxim Derkachev <kot@books.ru>, Teodor Cimpoesu <teo@digiro.net>,
*             Ulf Wendel <uw@netuse.de>
* @version    $Id: session4_custom.inc,v 1.2 2002/10/01 16:22:03 joestewart Exp $
* @package    PHPLib
* @access     public
*/ 

class Session_Custom extends Session {


  /**
  * session storage module - user, files or mm
  * 
  * @var  string
  */
  var $module = 'user';
  
  
  /**
  * where to save session files if module == files
  * 
  * @var string
  */
  var $save_path;
  
  
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
  * Purge all session data older than 1440 minutes.
  * 
  * @var int
  */
  var $gc_time = 1440;


  /**
  * Garbaga collection probability
  *
  * Set this in php.ini or httpd.conf (.htaccess)
  * 
  * @var  int
  */
  var $gc_probability;
  
  
  /**
  * initialization
  */
  function start() {
  
    $this->set_container();

    return Session::start();
  } // end func
  
  // the following functions used in session_set_save_handler
  
  /** 
  * Open callback
  * 
  * abstract
  */
  function open() {
    return true;
  } // end func open

  
  /**
  * Close callback
  * 
  * @abstract
  */
  function close() {
    return true;
  } // end func close
  
  
  /**
  * Delete callback
  */
  function del() {
    
    if ($this->module == 'user') {
      $this->that->ac_delete($this->id, $this->name);
      $this->put_id();
    }
    
    return true;
  } // end func del

  
  /*
  * Write callback.
  *
  */
  function freeze() {

    if ($this->module == 'user') {
    
      $r = $this->that->ac_store($this->id, $this->name, session_encode());
      $this->release_lock();
      
       if(!$r) 
        $this->that->ac_halt("Session: freeze() failed.");
    }
    
    return Session::freeze(); 
  } // end func freeze

  
  /**
  * Read callback.
  */
  function thaw() {
  
      if ($this->module == 'user') {
        # $this->get_lock();
        return $this->that->ac_get_value(session_id(), $this->name);
      }
      
      return true;
  }
    
  /**
  * gc callback.
  *
  * Destroy all session data older than $this->gc_time
  *
  */
  function gc() {

    if ($this->module == 'user') {
    
      if (empty($this->gc_time)) 
        $this->gc_time = get_cfg_var("session.gc_maxlifetime");
    
      return $this->that->ac_gc($this->gc_time, $this->name);
    }
    
    return true;
  } // end func gc


  // helper functions used in initialization

  /**
  * ?
  *
  */
  function set_container(){
  
    switch ($this->module) {
      case "user" :
        
        session_module_name('user');
        
        $name = $this->that_class;
        $this->that = new $name;
        $this->that->ac_start();
        
        // set custom session handlers
        session_set_save_handler(array (&$this, 'open'),
            array (&$this, 'close'),
            array (&$this, 'thaw'),
            array (&$this, 'freeze'),
            array (&$this, 'del'),
            array (&$this, 'gc')
        );
        break;
    
      case "mm":
        session_module_name('mm');
        break;
        
      case "files" :
      default:
        if ($this->save_path) 
          session_save_path($this->save_path);
          
        session_module_name('files');
        break;
    }
    
  } // end func set_container

  
  /**
  * ? 
  */
  function get_lock() {
    $this->that->ac_get_lock();
  } // end func get_clock

  
  /**
  * ?
  */
  function release_lock() {
    $this->that->ac_release_lock();
  } // end func release_lock

  
} // end class Session4_Custom
?>
