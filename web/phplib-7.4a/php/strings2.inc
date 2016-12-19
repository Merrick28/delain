<?php
##
## Strings2-Functions
##
## (c) 1998 Alex 'SSilk' Aulbach
##
## These Functions are very practical and if I could program
## C a little bit better it will be placed directly in PHP3.
## But I can't... :-}
##
## $Id: strings2.inc,v 1.1.1.1 2000/04/17 16:40:14 kk Exp $


##
## Have you ever worried about such constructs like
##    echo ($faxnumber) ? sprintf("Fax: %s",$faxnumber) : "";
##
## This functionset could help you to replace those constructs by
##    p_iftrue($faxnumber,"Fax: %s");
## which is nearly the half of typing and looks more clear and solves
## an error if $faxnumber is unset.
##
function o_iftrue ($val,$str) {
	if (isset($val) && $val) {
		return(sprintf($str,$val));
	}
}
function p_iftrue ($val,$str) {
	print o_iftrue($val,$str);
}

##
## Output "One or More"
##
## This function is good if you want to differ a output by number:
##  e.g.  o_1or2($q->num_rows(),
##               "Found only one matching record",
##               "Found %s records");
## Will output if num_rows() is 1:  "Found only one matching record"
##                            200:  "Found 200 records"
##
## if $val is empty() or "" a blank string will be returned!
##
function o_1or2 ($val,$str1,$str2) {
	if (isset($val) && $val) {
		if (1==$val) {
			return(sprintf($str1,$val));
		} else {
			return(sprintf($str2,$val));
		}
	} else {
		return(false);
	}
}
function p_1or2 ($val,$str1,$str2) {
	print o_1or2 ($val,$str1,$str2);
}


##
## This is for the case, that you want to output something
## if $val is false e.g.
##
## p_0or1($faxnumber,"THERE IS NO FAXNUMBER","Faxnumber: %s");
## 
function o_0or1 ($val,$str1,$str2) {
	if (empty($val) || !$val) {
		if (isset($val)) {
			return(sprintf($str1,$val));
		} else {
			return($str1);
		}
	} else {
		return(sprintf($str2,$val));
	}
}
function p_0or1 ($val,$str1,$str2) {
	print o_0or1 ($val,$str1,$str2);
}

##
## Replaces all blank-chars with &nbsp;
## This function is used, when you are not willing to let the browser
## break your lines an can be used instead of <NOBR>-Tag
## as very compatible replacement
##
## &nbsp; can also be replaced by a true whitespace which has in
## ISO-latin-1 the code 160
##
function o_nonbsp ($val) {
	return(ereg_replace("[[:blank:]\n\r]","&nbsp;",$val));
}
function p_nonbsp ($val) {
	print o_nonbsp($val);
}
?>
