<?php
/*
 * Tree generation and output for PHP3
 *
 * © 1998 Alex Aulbach
 *
 * $Id: tree.inc,v 1.3 2002/04/02 23:23:12 layne_weathers Exp $
 *
 * This class needs a generated assoc array in this form:
 *
 * $tree = array(
 *               "usr" => array(
 *                 "lib"   => "",
 *                 "local" => "",
 *                 "bin"   => "",
 *                 "hugo"  => array(
 *                   "nummer1" => "",
 *                   "nummer2" => "",
 *                   "nummer3" => ""
 *                 ),
 *                 "servuz" => ""
 *               )
 *         );
 *
 * ... and can generate a tree like
 *
 *       /
 *       --+ usr
 *           --+ lib
 *             + local
 *             + bin
 *
 */

class Tree {
	var $classname = "Tree";

	var $delimiter = "^";  ## Delimiter for path-index

	var $tree;

	var $prfx=array();
	var $sufx=array();
	var $outp="";
	var $flags;

	function build_tree () {
	## This function must be overridden by user!
	## Result should fill $this->tree
	## Perhaps it is easier to use this function in a recursive manner
	## e.g. recursive scanning of directory structure.
	##
	}

	function go_through_tree ($key="",$path="",$depth=0,$lcount=0,$pcount=0) {
	STATIC $k,$t,$v,$eval2,$tmp1,$tmp2;
		# Remeber: path_to_index: $path is call by value!
		$index=$this->path_to_index($path,$key);
#DEB		echo "-------------<BR>*PATH: $path $key<BR>";
#DEB		echo "*INDEX: $depth $index<BR>";
		$eval="\$this->tree${index}";
		if ($depth) {
			eval("\$v=$eval; \$c=Count($eval);");
			$eval2 = $eval . "[0]";
			eval("if (isset(${eval2})) \$v=$eval2 ;");
			$this->growtree($key,$v,$path,$depth,$lcount,$pcount);
		} else {
			$this->starttree();
			eval("\$c=Count(\$this->tree);");
		}
		eval("\$t = gettype($eval);");
		if ("array"==$t) {         ## Just to be sure
			$evalnext="list(\$k) = each($eval);";
			eval($evalnext);			## First element
			$i=1;
			while (strlen($k)) {
				$eval2= $eval . "[\"". ereg_Replace("\"","\\\"",$k) ."\"]";
				eval("\$t=gettype($eval2); \$v=$eval2;");
#DEB			echo "<BR>$i: $t - $k -> '$v' - $c<BR>";
				if ("0" != $k) {
					switch ($t) {
						case "array" :
							eval("\$tmp1=Count($eval2); \$tmp2=isset(${eval2}[\"0\"]);");
							if ($tmp1==1 && $tmp2 ) {
								eval("\$v=${eval2}[\"0\"];");
								$this->leaftree($k,$v,$this->path_add($path,$k),$depth+1,$c,$i);
							} else {
								$this->go_through_tree($k,$path,$depth+1,$c,$i);
							}
							break;
						default :
							$this->leaftree($k,$v,$this->path_add($path,$k),$depth+1,$c,$i);
							break;
					}
				}
				eval($evalnext);				# Next element
				$i++;
			}
		}
		if ($depth) {
			$this->shrinktree($key,$depth);
		} else {
			$this->endtree();
		}			
	}

	##########################################################
	## Calculate index
	## $key is added to $path (call by reference!!!)
	## -> $path is something like "hugo^bla^nana"
	##    $index is then ["hugo"]["bla"]["nana"]


	function path_to_index (&$path,$key="") {
		$key=ereg_Replace("\"","\\\"",$key);
		if ($path && $key) {
			$path.=$this->delimiter . $key;
		} else {
			$path.=$key;
		}
		if ($path) {
			$index=implode("\"][\"",explode($this->delimiter,$path));
			$index = "[\"${index}\"]";
		} else {
			$index="";
		}
		return($index);
	}
	############################################################
	## eg. $path is "hugo^bla^wusel" then after call to this function
	## $path is "hugo^bla" and it will return '["hugo"]["bla"]'
	## if $path is empty it will return false!
	##
	function path_to_parent (&$path) {
		if ($path) {
			## calculate parent
			$path=substr($path,0,strrpos($path,$this->delimiter));
		} else {
			return(false);
		}
		if ($path) {
			$index=implode("\"][\"",explode($this->delimiter,$path));
			$index = "[\"${index}\"]";
		} else {
			$index="";
		}
		return($index);
	}

	######################################################
	## Same as path_to_index and path_to_child but only
	## working on path
	##

	function path_add ($path,$key) {
		$key=ereg_Replace("\"","\\\"",$key);
		if ($path && $key) {
			$path.=$this->delimiter . $key;
		} else {
			$path.=$key;
		}
		return($path);
	}

	function path_sub ($path) {
		if ($path) {
			## calculate parent
			$path=substr($path,0,strrpos($path,$this->delimiter));
		} else {
			return(false);
		}
		return($path);
	}

	function path_index ($path) {
		if ($path) {
			$index=implode("\"][\"",explode($this->delimiter,$path));
			$index = "[\"${index}\"]";
		}
		return($index);
	}

	###################################################
	# These functions are all user definable
	# I have made a little example, of how to make a simple
	# explorer like output
	#
	# These functions attempt to be session-aware. They
	# look for a global variable called $sess and if it
	# is set they generate relative URLs containing the
	# session ID.

	function starttree () {
#DEB		echo "!!! STARTREE<BR>";
		$this->outp.= " /<BR>";
		$this->flag=true;
	}


	function growtree ($key,$value,$path,$depth,$count,$pcount) {
		global $sess;
#DEB		echo ">>> GROWTREE<BR>";
		$this->outp.= "<TT>" . join($this->prfx,"");
		if ($this->flag) {
			$this->outp.="^----";
		} elseif ($count==$pcount) {
			$this->outp.="&#160;\\---";
		} else {
			$this->outp.="O----";
		}
		if (isset($sess)) {
			$query_string = $sess->url(getenv("PHP_SELF"));
			$query_string .= $sess->mode == "get" ? "&" : "?";
			$query_string .= "val=" . urlencode($value);
		} else {
			$query_string = getenv("PHP_SELF") . "?val=" . urlencode($value);
		}
		$this->outp.= sprintf("</TT> %s->'<A HREF=\"%s\">%s</A>'".
		     " : '%s' (%s) [%s/%s]<BR>\n",
		     $key,$query_string,$value,$path,$depth,$pcount,$count);
		if ($count > $pcount) {
			$this->prfx[$depth]="|&#160;&#160;&#160;&#160;";
		} else {
			$this->prfx[$depth]="&#160;&#160;&#160;&#160;&#160;";
		}
		$this->flag=true;
	}

	function leaftree ($key,$value,$path,$depth,$count,$pcount) {
		global $sess;
#DEB	echo "--- LEAFTREE<BR>";
		$this->outp.= "<TT>" . join($this->prfx,"");
		if ($this->flag) {
			$this->outp.="*----";
		} elseif ($count==$pcount) {
			$this->outp.="&#160;\\---";
		} else {
			$this->outp.="+----";
		}
		if (isset($sess)) {
			$query_string = $sess->url(getenv("PHP_SELF"));
			$query_string .= $sess->mode == "get" ? "&" : "?";
			$query_string .= "val=" . urlencode($value);
		} else {
			$query_string = getenv("PHP_SELF") . "?val=" . urlencode($value);
		}
		$this->outp.= sprintf("</TT> %s->'<A HREF=\"%s\">%s</A>'".
		     " : '%s' (%s) [%s/%s]<BR>\n",
		     $key,$query_string,$value,$path,$depth,$pcount,$count);
		$this->flag=false;
	}


	function shrinktree ($key,$depth) {
#DEB		echo "<<< SHRINKTREE<BR>";
		unset($this->prfx[$depth]);
	}


	function endtree () {
#DEB		echo "... ENDTREE<BR>";
	}

}
