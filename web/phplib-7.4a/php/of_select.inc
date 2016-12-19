<?php
/* OOHForms: select
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_select.inc,v 1.2 2002/04/28 03:59:32 richardarcher Exp $
 */

class of_select extends of_element {

  var $options;
  var $size;
  var $valid_e;

  // Constructor
  function of_select($a) {
    $this->setup_element($a);
    if ($a["type"]=="select multiple") {
      $this->multiple=1;
    }
  }

  function self_get($val, $which, &$count) {
    $str = "";

    if ($this->multiple) {
      $n = $this->name . "[]";
      $t = "select multiple";
    } else {
      $n = $this->name;
      $t = "select";
    }
    $str .= "<$t name='$n'";
    if ($this->size) {
      $str .= " size='$this->size'";
    }
    if ($this->extrahtml) {
      $str .= " $this->extrahtml";
    }
    $str .= ">";

    reset($this->options);
    while (list($k, $o) = each($this->options)) {
      $str .= "<option";
      if (is_array($o)) {
        $str .= " value='" .  $o["value"] . "'";
      }
      if (!$this->multiple && ($this->value==$o["value"] || $this->value==$o)) {
        $str .= " selected";
      } elseif ($this->multiple && is_array($this->value)) {
        reset($this->value);
        while (list($tk, $v) = each($this->value)) {
          if ($v==$o["value"] || $v==$o) {
            $str .= " selected"; break;
          }
        }
      }
      $str .= ">" . (is_array($o) ? $o["label"] : $o) . "\n";
    }
    $str .= "</select>";

    $count = 1;
    return $str;
  }

  function self_get_frozen($val, $which, &$count) {
    $str = "";

    $x = 0;
    $n = $this->name . ($this->multiple ? "[]" : "");
    $v_array = (is_array($this->value) ? $this->value : array($this->value));
    $str .= "<table border=1>\n";
    reset($v_array);
    while (list($tk, $tv) = each($v_array)) {
      reset($this->options);
      while (list($k, $v) = each($this->options)) {
        if ((is_array($v) &&
           (($tmp=$v["value"])==$tv || $v["label"]==$tv))
         || ($tmp=$v)==$tv) {
          $x++;
          $str .= "<input type='hidden' name='$n' value='$tmp'>\n";
          $str .= "<tr><td>" . (is_array($v) ? $v["label"] : $v) . "</td></tr>\n";
        }
      }
    }
    $str .= "</table>\n";

    $count = $x;
    return $str;
  }

  function self_get_js($ndx_array) {
    $str = "";

    if (!$this->multiple && $this->valid_e) {
      $str .= "if (f.$this->name.selectedIndex == 0) {\n";
      $str .= "  alert(\"$this->valid_e\");\n";
      $str .= "  f.$this->name.focus();\n";
      $str .= "  return(false);\n";
      $str .= "}\n";
    }

    return $str;
  }

  function self_validate($val) {
    if (!$this->multiple && $this->valid_e) {
      reset($this->options);
      $o = current($this->options);
      if ($val==$o["value"] || $val==$o) {
        return $this->valid_e;
      }
    }
    return false;
  }

} // end SELECT

?>
