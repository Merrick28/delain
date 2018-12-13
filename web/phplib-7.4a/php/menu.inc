<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1999 NetUSE GmbH
 *                    Kristian Koehntopp
 *
 * $Id: menu.inc,v 1.4 2002/04/26 12:29:40 richardarcher Exp $
 *
 */ 
class Menu {
  var $classname = "Menu";

  # Map of PHP_SELF URL strings to menu positions
  var $urlmap = array();
  
  # current menu position
  var $map = "";

  # Information about each menu item
  var $item = array();

  # $children["/"] = array("/1", "/2");
  var $children = array();
  
  # $visible[1] = "/1";
  var $visible = array();

  # Class to render menu items in
  var $class = "";

  # Set true if you do not want to see the main menu
  var $nomain  = false;

  # Delimiter to separate components of the page title
  var $title_delim = " : ";

  /***************************************************************************
   * public: constructor
   */
  function Menu() {
    $this->setup();
  }
  
  /*
   * public: show menu of items visible from the current page
   */
  function show() {
    print $this->get();
  }
  
  /*
   * public: build menu of items visible from the current page
   */
  function get() {
    global $_SERVER;
    
    # Determine normalized current position in tree
    $this->map = $this->normalize_pos($_SERVER["PHP_SELF"]);

    # Determine menu levels up from current position
    $r = $this->split_path($this->map);

    # set up the visible menu items
    $this->find_visible($r);
    
    # create them
    $str = "";
    
    $start    = $this->nomain?1:0;
    $len      = count($this->visible);
    $oldlevel = 0;
    $str     .= $this->start_menu();
    for($i=$start; $i<$len; $i++) {
      $level = count(explode("/", $this->visible[$i]));

      if ($level > $oldlevel) {
        $str .= $this->shift_in($oldlevel, $level);
      }
      if ($level < $oldlevel) {
        $str .= $this->shift_out($oldlevel, $level);
      }
      $oldlevel = $level;

      $str .= $this->get_cell($i, $level, $this->class);
    }
    $str .= $this->end_menu();
    
    return $str;
  }

  /*
   * public: show a menu of all items (sitemap)
   */
  function show_sitemap() {
    print $this->get_sitemap();
  }

  /*
   * public: build a menu of all items (sitemap)
   */
  function get_sitemap() {
    $this->visible = array();

    # Using PHP4, this could be faster, but we want to be portable.
    reset($this->item);
    while(list($k, $v) = each($this->item)) {
      $this->visible[] = $k;
    }
    sort($this->visible);
        
    # create them
    $str = "";
    
    $start    = $this->nomain?1:0;
    $len      = count($this->visible);
    $oldlevel = 0;
    $str     .= $this->start_menu();
    for($i=$start; $i<$len; $i++) {
      $level = count(explode("/", $this->visible[$i]));

      if ($level > $oldlevel) {
        $str .= $this->shift_in($oldlevel, $level);
      }
      if ($level < $oldlevel) {
        $str .= $this->shift_out($oldlevel, $level);
      }
      $oldlevel = $level;

      $str .= $this->get_cell($i, $level, $this->class);
    }
    $str .= $this->end_menu();
    
    return $str;
  }

  
  /*
   * public: show a single menu cell
   */
  function get_cell($n, $level, $class = "") {
    $dent = "";
    $bold = ($this->visible[$n] == $this->map);
    for ($i=1; $i<$level; $i++) {
      $dent .= "&nbsp;&nbsp;";
    }

    if (isset($this->item[$this->visible[$n]]["pseudo"])) {
      $str=sprintf("%s%s<br>\n",
           $dent,
           $this->item[$this->visible[$n]]["title"]
      );
    } else {
      $str = sprintf("%s<a href=\"%s\"%s>%s%s%s</a><br>\n", 
        $dent,
        $this->item[$this->visible[$n]]["url"],
        $class?" class=$class":"",
        $bold? '<strong><FONT COLOR="#990000">[':"",
        $this->item[$this->visible[$n]]["title"],
        $bold? "]</FONT></strong>":""
      );
    }
    return $str;
  }

  /*
   * public: build the title of the current page based on its location 
   * in the menu hierarchy.
   */
  function get_title() {
    global $_SERVER;
    $this->title = "";
    
    # Determine normalized current position in tree
    $this->map = $this->normalize_pos($_SERVER["PHP_SELF"]);

    # Determine menu levels up from current position
    $r = $this->split_path($this->map);

    while(list($a, $b) = each($r)) {
      if (isset($this->title) && $this->title != "") {
        $this->title .= $this->title_delim;
      }
      $this->title .= $this->item[$b]["title"];
    }

    return $this->title;
  }

  /*
   * public: These four functions can be used to customise layout.
   * They are called when certain events happen during menu display.
   * They should be overridden in your subclass.
   */
  function shift_in($oldlevel, $level) { ; }
  
  function shift_out($oldlevel, $level) { ; }

  function start_menu() { ; }

  function end_menu() { ; }

  /*
   * private: normalize current menu position
   */
  function normalize_pos($pos) {
    if ( isset($this->urlmap[basename($pos)]) ) {
      return $this->urlmap[basename($pos)];
    }
    $m = $pos;
    while(strlen($m)) {
      if (isset($this->urlmap[$m])) {
        break;
      }
      $m = dirname($m);
      if ($m == "/") {
        $m = "";
      }
    }
    return $this->urlmap[$m];
  }
  
  /*
   * private: split a path /2/2 into components "", /2, /2/2
   */
  function split_path($p) {
    $path = "";

    $r = explode("/", $p);
    reset($r);
    while(list($k, $v) = each($r)) {
      if ($v) {
        $path .= "/$v";
      }
      $res[] = $path;
    }
    
    return $res;
  }

  /*
   * private: set up the visible array.
   */
  function find_visible($r) {
    # at each level, add current children to visible
    $len = count($r);
    $this->visible = array();

    for ($i=0; $i<$len; $i++) {
      # if current level has children, add them...
      if (isset($this->children[$r[$i]])) {
        reset($this->children[$r[$i]]);
        while(list($k, $v) = each($this->children[$r[$i]])) {
          $this->visible[] = $v;

          # The "pseudo" feature has not been documented in previous
          # releases. As such, it should not matter greatly that the
          # behaviour may have changed somewhat in this release.
          # Nevertheless, the changes and current behaviour are documented
          # here.
          # 
          # Previously the array item $this->item[$v]["pseudo"] should have
          # contained an array of $this->item keys. The items referenced in
          # the "pseudo" array were made visible if the "pseudo" item was
          # visible.
          # 
          # This has been changed somewhat. Now, if a "pseudo" array element
          # exists, the $this->children array is scanned and all children of
          # $this->item[$v] are made visible. The menu item $this->item[$v]
          # is displayed as text only, not as a hyperlink. The item acts as a
          # heading.

          # show children if this is a "pseudo" item
          if (isset($this->item[$v]["pseudo"]) && !ereg("^$v",$this->map)) {
            if (isset($this->children[$v]) && is_array($this->children[$v]) ) {
              reset($this->children[$v]);
              while (list($w, $ww) = each($this->children[$v])) {
                $this->visible[] = $ww;
              }
            }
          }
        }
      }
    }
    
    # Order menu items for display
    sort($this->visible);
  }

  /*
   * private: find children of each menu item
   */
  function setup() {
    reset($this->urlmap);
    while(list($k, $v) = each($this->urlmap)) {
      $base = dirname($v);
      $base = str_replace("\\","/",$base);
      if ($base == "/") {
        $base = "";
      }
      $this->children[$base][] = $v;
      $this->item[$v]["url"]   = $k;
    }
  }
}
