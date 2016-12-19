<?php
/* OOHForms: text
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_text.inc,v 1.3 2005/02/19 15:21:25 layne_weathers Exp $
 */

class of_text extends of_element {

  var $maxlength;
  var $minlength;
  var $length_e;
  var $valid_regex;
  var $valid_icase;
  var $valid_e;
  var $pass;
  var $size;

  // Constructor
  function of_text($a) {
    $this->setup_element($a);
    if ($a["type"]=="password") {
      $this->pass=1;
    }
  }

  function self_get($val, $which, &$count) {
    $str = "";

    if (is_array($this->value)) {
      $v = htmlspecialchars($this->value[$which]);
    } else {
      $v = htmlspecialchars($this->value);
    }
    $n = $this->name . ($this->multiple ? "[]" : "");
    $str .= "<input name='$n' value=\"$v\"";
    $str .= ($this->pass)? " type='password'" : " type='text'";
    if ($this->maxlength) {
      $str .= " maxlength='$this->maxlength'";
    }
    if ($this->size) {
      $str .= " size='$this->size'";
    }
    if ($this->extrahtml) {
      $str .= " $this->extrahtml";
    }
    $str .= ">";

    $count = 1;
    return $str;
  }

  function self_get_frozen($val, $which, &$count) {
    $str = "";

    if (is_array($this->value)) {
      $v = $this->value[$which];
    } else {
      $v = $this->value;
    }
    $n = $this->name . ($this->multiple ? "[]" : "");
    $str .= "<input type='hidden' name='$n' value='$v'>\n";
    $str .= "<table border=1><tr><td>$v</td></tr></table>\n";

    $count = 1;
    return $str;
  }

  function self_get_js($ndx_array) {
    $str = "";

    reset($ndx_array);
    while (list($k, $n) = each($ndx_array)) {
      if ($this->length_e) {
        $str .= "if (f.elements[${n}].value.length < $this->minlength) {\n";
        $str .= "  alert(\"$this->length_e\");\n";
        $str .= "  f.elements[${n}].focus();\n";
        $str .= "  return(false);\n}\n";
      }
      if ($this->valid_e) {
        $flags = ($this->valid_icase ? "gi" : "g");
        $str .= "if (window.RegExp) {\n";
        $str .= "  var reg = new RegExp(\"$this->valid_regex\", \"$flags\");\n";
        $str .= "  if (!reg.test(f.elements[${n}].value)) {\n";
        $str .= "    alert(\"$this->valid_e\");\n";
        $str .= "    f.elements[${n}].focus();\n";
        $str .= "    return(false);\n";
        $str .= "  }\n}\n";
      }
    }

    return $str;
  }

  function self_validate($val) {
    if (!is_array($val)) {
      $val = array($val);
    }
    reset($val);
    while (list($k, $v) = each($val)) {
      if ($this->length_e && (strlen($v) < $this->minlength)) {
        return $this->length_e;
      }
      if ($this->valid_e && (($this->valid_icase &&
            !eregi($this->valid_regex, $v)) ||
           (!$this->valid_icase &&
            !ereg($this->valid_regex, $v)))) {
        return $this->valid_e;
      }
    }
    return false;
  }

} // end TEXT

?>
