

# This example extends the standard PHPLIB dataset, providing
# xml and xsl capability.
#
# A dataset can be returned from a database, formated into XML,
# and then transformed with a XSLT stylesheet.
#
# This class supports passing parameters to the XSLT sytlesheet,
# using add_parameter("parameter_name", "parameter_value").
#
# Both the XSL and XML that is passed into the class can be a
# string value or a file name.
#
# Example usage:
#
# $db = new DB_Sql_Xml;
# $db->query("select * from foo");
# echo $db->get_transform("xslt_file.xslt", $db->get_xml());
#
# Note: this example is PostgreSQL specific
#


class doc_reader {
  var $string = "";
  var $type = "";
  var $bignum = 1000000;
  var $uri = "";

  function doc_reader($uri) {
    $this->set_uri($uri);
    $this->set_type();

    $fp = fopen($this->get_uri(),"r");

    if($fp) {
      if ($this->get_type() == 'file') {
        $length = filesize($this->get_uri());
      } else {
        $length = $this->bignum;
      }

      $this->set_string(fread($fp,$length));
      return 1;
    } else {
      return 0;
    }
  }

  function is_file($uri) {
    if (strstr($uri,'http://') == $uri) {
      return false;
    } else {
      return true;
    }
  }

  function set_uri($string) {
    $this->uri = $string;
  }

  function get_uri() {
    return $this->uri;
  }

  function set_string($string) {
    $this->string = $string;
  }

  function get_string() {
    return $this->string;
  }

  function set_type() {
    if ($this->is_file($this->uri)) {
      $this->type = 'file';
    } else {
      $this->type = 'url';
    }
  }

  function get_type() {
    return $this->type;
  }
}

class DB_Sql_Xml extends DB_Sql {
  var $xsl = "";
  var $xml = "";
  var $xml_initialized = 0;
  var $output = "";
  var $params = array();

  function set_output($string) {
    $this->output = $string;
  }

  function get_output() {
    return $this->output;
  }

  function set_xml($uri) {
    if ( $uri[0] == "<" ) {
      $this->xml = $uri;
      return true;
    } elseif ( $uri == "" ) {
      $this->xml = '<?xml version="1.0"?><result><row></row></result>';
      return true;
    } else {
      if($doc = new doc_reader($uri)) {
        $this->xml = $doc->get_string();
        return true;
      } else {
        $this->set_error("Could not open $uri");
        return false;
      }
    }
  }

  function set_xsl($uri) {
    if ( $uri[0] == "<" ) {
      $this->xsl = $uri;
      return true;
    } else {
      if($doc = new doc_reader($uri)) {
        $this->xsl = $doc->get_string();
        return true;
      } else {
        $this->set_error("Could not open $uri");
        return false;
      }
    }
  }

  function add_parameter($name, $value) {
    $this->params[$name] = $value;
  }

  function transform() {
    if ( count($this->params) ) {
      $buffers = array("result" => $result, "template" => $this->xsl, "data" => $this->xml);
      xslt_transform("arg:/template", "arg:/data", "arg:/result", $this->params, $buffers, $result);
    } else {
      xslt_process($this->xsl, $this->xml, $result);
    }

    $this->set_output($result);
  }

  function get_transform($xsl, $xml) {
    $this->set_xsl($xsl);
    $this->set_xml($xml);
    $this->transform();
    return $this->get_output();
  }

  function set_error($string) {
    $this->error = $string;
  }

  function get_error() {
    return $this->error;
  }

  function xml_encode($text) {
    $str = $text;

    $str = str_replace("&", "&amp;", $str);
    $str = str_replace("<", "&lt;", $str);
    $str = str_replace(">", "&gt;", $str);
    $str = str_replace('"', "&quot;", $str);
    $str = str_replace("'", "&apos;", $str);

    return $str;
  }

  function get_result_xml() {
    $this->xml = "";

    $this->xml = $this->xml . '<?xml version="1.0"?>';
    $this->xml = $this->xml . '<result>';

    for ($index = 0; $index < pg_numrows($this->Query_ID); $index++)
    {
      $this->xml = $this->xml . '<row>';

      $this->Record = @pg_fetch_array($this->Query_ID, $index, 1);

      while (list($key, $val) = each($this->Record)) {
        $this->xml = $this->xml . '<' . $key . '>' . $this->xml_encode($val) . '</' . $key . '>';
      }

      $this->xml = $this->xml . '</row>';
    }

    $this->xml = $this->xml . '</result>';

    return $this->xml;
  }

  function get_simple_xml() {
    return $this->xml;
  }

  function get_xml() {
    if ( !$this->xml_initialized ) {
      $this->xml = $this->get_result_xml();
      $this->xml_initialized = 1;
    }

    return $this->xml;
  }
}