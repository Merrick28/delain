<?php
/*
 * OPTIC TEMPLATE LIBRARY
 *
 * Jan 1999-2000 Alexander Aulbach for Main Media
 *
 * $Id: layout_html.inc,v 1.4 2002/08/07 19:31:33 layne_weathers Exp $
 *
 * This Lib is used for the default representation of a page.
 * It has to be extended for own layout-design
 *
 * The class needs the strings2.inc function-set!! include it with
 * include("strings2.inc");
 *
 *
 * This lib was written from the need to implement a user-defined
 * home page design. The user could choose how the server should looks
 * for him/her. He/she could choose, for the background color, the
 * color of the tables, the size of the fonts and so on and so on.
 * First I thought this would be possible to implement it with Style Sheets
 * but there is no way to make it as flexible as it needs to.
 * So the idea was born to make this class, which generates the design
 * on the fly.
 *
 * There is NO NEED to use it for everything. Cause it makes things
 * more difficult to handle. But it is a great help for some special
 * kind of use, especially for programming sites, where the user can
 * choose his layout, like for personalized community pages.
 *
 * It's also a great help for programmers of a web site. They can just begin
 * to make their programs and output tons of lists etc.
 * They don't have to care about the colors and placement, cause the
 * layout could be fixed later.
 *
 * The advancement to e.g. FastTemplate is, that every little piece of HTML
 * code has some default representation, which can be changed _dynamically_.
 * It produces also correct HTML code, if you only use the functions.
 *
 * For me especially the doc_picture()-function is a great help, cause
 * I never have to care about the size of the images (The WIDTH and HEIGHT
 * attributes).
 * Our designer says "Oh, Alex, before I forget: I have changes the size
 * of hugo.gif a little bit, could you fix it in your code?"
 * And I answer: "It's already fixed. Totaly automatically. :-)" 
 * 
 * The disadvantage is of course, that the pages are generated dynamically,
 * so it is not fast.
 *
 * Best efficency can be achived while using BOTH of them, mixing it
 * up as you need it.
 *
 *
 *
 * Ideas for later programming:
 * - implementing an unsorted list with the originial tags not with tables
 *
 * - It would be a nice idea to mix the both advantages of FastTemplate and
 *   Layout. There is just a need for a kind of buffer mechanism. Like this
 *   from "Story Server". If the layout exists and is uptodate, then get
 *   the template and use FastTemplate. If not create template with Layout
 *   and store it for next use.
 *
 * - Cause this is a class, you can take care, if the functions are
 *   called in the propper order and closings
 *
 */


class Layout {

	##
	##
	##
	var $Debug    = false;

	##
	## these vars are needed for generating an IMG-Tag
	## layouturl: s.th. like "http://hugo.bla/"
	## layoutpath: if empty it will be set on first call to DOCUMENT_ROOT
	##
	var $layouturl = '';
	var $layoutpath = ''; 


	##
	## In most cases only the following definition-section
	## must be changed
	##

	################################################# BEGIN
	var $docbegin =
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<HTML>
<HEAD>
';

	################################################# HTMLHEAD
	var $htmlhead = ARRAY (
		'TITLE'       => "<TITLE>%s</TITLE>\n",
		'stylesheet'  =>
' <style type="text/css">
 <!--
  body { font-size: 20pt  font-family: geneva,Arial,Helvetica,helv,swiss; }
  td   { font-family: geneva,Arial,Helvetica,helv,swiss; }
  a:visited { text-decoration: none; }
  a:link { text-decoration: none; }
  a:active { text-decoration: none; }
  p { font-size: 20pt ; line-height: 10pt;
      font-family: geneva,Arial,Helvetica,helv,swiss;
      fontfamily: geneva,Arial,Helvetica,helv,swiss;
    }
  lighthead { font-size: 12pt ; line-height: 10pt;
      font-family: geneva,Arial,Helvetica,helv,swiss;
      fontfamily: geneva,Arial,Helvetica,helv,swiss;
      color: #EEEEEE
    }
 -->
 </style>');

	var $htmlhd_def = ARRAY(
		'TITLE'       => '',
		'PRETITLE'    => ''
	);


		##
		## For stylesheets we must add 
		## ID, CLASS, STYLE, LANG and DIR
		## to nearly every element!
		## The good thing hereby is, that we don't need
		## to change the function-calls -
		## it is enough to put them here directly into the tag
		##
/* currently commented as idea for future use
 *	'metacontent' =>'<META TYPE=content VALUE="%s">',
 */

	################################################# BODYTAG
	var $bodytag = ARRAY(
		'endhead'     => "</HEAD>\n",
		'bodytag'     => "<BODY%s>\n<P>\n",
		'BGCOLOR'     => ' BGCOLOR="%s"',
		'BACKGROUND'  => ' BACKGROUND="%s"',
		'TEXT'        => ' TEXT="%s"',
		'LINK'        => ' LINK="%s"',
		'VLINK'       => ' VLINK="%s"',
		'ALINK'       => ' ALINK="%s"',
		'onLoad'      => ' onLoad="%s"',
		'endbody'     => "%s</P>\n</BODY>\n</HTML>\n"
	);


	var $body_def = ARRAY (
		'BGCOLOR'     => '#FFFFFF',
		'BACKGROUND'  => '',
		'TEXT'        => '#000000',
		'LINK'        => '#0000C0',
		'VLINK'       => '#0000A0',
		'ALINK'       => '#8000FF'
	);

	## Default colors for use while configuration
	## can be added as usefull
	var $color_def = ARRAY (
		'color1'       => '#AAAAAA',
		'color2'       => '#444444'
	);

	################################################# TABLE
	var $table = ARRAY(
		'tabletag'    => "<TABLE%s>\n",
		'CELLPADDING' => ' CELLPADDING="%s"',
		'CELLSPACING' => ' CELLSPACING="%s"',
		'BORDER'      => ' BORDER="%s"',
		'WIDTH'       => ' WIDTH="%s"',
		'BGCOLOR'     => ' BGCOLOR="%s"',
		'ALIGN'       => ' ALIGN="%s"',
		'endtable'    => '</TABLE>'
	);

	var $table_def = ARRAY(
		'CELLPADDING' => '2',
		'CELLSPACING' => '0',
		'BORDER'      => '0',
		'WIDTH'       => '100%',
		'BGCOLOR'     => '',
		'ALIGN'       => ''
	);

	################################################# TABROW
	var $tabrow = ARRAY(
		'trtag'       => '<TR%s>',
		'ALIGN'       => ' ALIGN="%s"',
		'VALIGN'      => ' VALIGN="%s"',
		'BGCOLOR'     => ' BGCOLOR="%s"',
		'endtrtag'    => "</TR>\n"
	);

	var $tabrow_def = ARRAY(
		'ALIGN'       => '',
		'VALIGN'      => 'top',
		'BGCOLOR'     => ''
	);

	################################################# TABCELL
	var $tabcell = ARRAY(
		'tdtag'       => '<TD%s>',
		'COLSPAN'     => ' COLSPAN="%s"',
		'ROWSPAN'     => ' ROWSPAN="%s"',
		'ALIGN'       => ' ALIGN="%s"',
		'VALIGN'      => ' VALIGN="%s"',
		'BGCOLOR'     => ' BGCOLOR="%s"',
		'WIDTH'       => ' WIDTH="%s"',
		'endtdtag'    => "</TD>\n"
	);
	## not included: HALIGN, AXES, AXIS, NOWRAP


	var $tabcell_def = ARRAY(
		'COLSPAN'     => '',
		'ROWSPAN'     => '',
		'ALIGN'       => '',
		'VALIGN'      => '',
		'BGCOLOR'     => '',
		'WIDTH'       => ''
	);


	############################################# UNSORTED LIST
## Old Style:
##	var $ulist = ARRAY(
##		'ultag'       => '<UL%s>',
##		'COMPACT'     => ' COMPACT',
##		'STYLE'       => ' STYLE="%s"',
##		'endultag'    => '</UL>'
##	);
##
##	var $ulist_def = ARRAY(
##		'COMPACT'     => '',        # '' (nothing) or '1'
##		'STYLE'       => ''         # DISC|SQUARE|CIRCLE
##	);
##	var $uitem = ARRAY(
##		'litag'       => '<LI> %s%s',
##	);

### New Style - I like this more, but it is of course more code

	var $ulist = ARRAY(
		'ultag'       => '<TABLE CELLPADDING=0 BORDER=0%s>',
		'CELLSPACING' => ' CELLSPACING=%s',
		'endultag'    => '</TABLE>'
	);

	var $ulist_def = ARRAY(
		'CELLSPACING' => '5'
	);

	var $uitem = ARRAY(
		'litag'       => "\n<TR VALIGN=top><TD>%s</TD></TR>",
		'licont'      => '%%s</TD><TD>%s'
	);


	var $uitem_def = ARRAY(	
		'IMGSRC'      => "/pics/arrowright.gif"
	);


	############################################## ANKER

	var $anker = ARRAY(
		'anker'       => '%s<A%s>%s</A>',
		'comment'     => '%s ',
		'HREF'        => ' HREF="%s"',
		'atext'       => '%s',
		'TARGET'      => ' TARGET="%s"',
		'onclick'     => ' OnClick="%s"'
	);

	var $anker_def = ARRAY(
		'comment'     => 'Link:',
		'atext'       => 'mehr Info'
	);
	## Missing: TARGET !



	############################################# PICTURE
	var $picture = ARRAY(
		'pictag'      => '<IMG SRC="%s"%s>',
		'BORDER'      => ' BORDER="%s"',
		'ALIGN'       => ' ALIGN="%s"',
		'HVSPACE'     => ' HSPACE="%s" VSPACE="%s"',
		'ALT'         => ' ALT="%s"',
		'WIDTH'       => ' WIDTH="%s"',
		'HEIGHT'      => ' HEIGHT="%s"'
	);
	
	var $picture_def = ARRAY(
		'BORDER'      => '',
		'ALIGN'       => '',
		'HVSPACE'     => '5',
		'ALT'         => '',
		'force'       => false  ## force output even if image not found
	);

	############################################ SIMPTEXT
	var $simptext = ARRAY(
		'simptext'    => "%s\n%s",
		'thead'       => '<H%d>%s</H%d>',
		'text'        => '%s',
		'ALIGN'       => '<DIV ALIGN="%s">',
		'ENDALIGN'    => '</DIV>'
	);

	var $simptext_def = ARRAY(
		'theadsize'   => '2',
		'ALIGN'       => ''
	);

	############################################ RULER
	var $ruler = ARRAY(
		'ruler'       => '<HR%s>',
		'SRC'         => '',
		'WIDTH'       => ' WIDTH="%s"',
		'SIZE'        => ' SIZE="%s"',
		'NOSHADE'     => ' NOSHADE'
	);

	var $ruler_def = ARRAY(
		'SRC'         => '',
		'WIDTH'       => '',
		'SIZE'        => '',
		'NOSHADE'     => '1'
	);


	############################################ PLACEHOLDER
	var $placeholder_def= ARRAY(
		'SRC'         => '/pics/platzhalter.gif'
	);


	############################################ BOXES
	var $box_def= ARRAY(
		'BOXCOLOR'         => '#9999DD',
		'BOXPREHEAD'       => '<BIG><FONT COLOR="#EEEEEE">&nbsp;'
	);


	#################################################
	# end of variable-definition
	#################################################

	## ---------------------------------------------- BEGIN
	function doc_begin () {
		return($this->docbegin);
	}

	## ---------------------------------------------- END
	## Pseudofunction, thougth for possible downward
	## compatibility; this function should check for possible
	## syntax errors in generation of HTML-code
	function doc_end () {
	}

	## ---------------------------------------------- HTMLHEAD
	function htmlhd ($TITLE='', $meta='',$rest='') {
		$this->defval($TITLE,   $this->htmlhd_def['TITLE']);
		if (!$TITLE) {
			$TITLE=$GOLBALS[PHP_SELF];
		}
		$TITLE = $this->htmlhd_def[PRETITLE] . $TITLE;

		$out=sprintf($this->htmlhead['TITLE'],$TITLE).
		     $this->htmlhead['stylesheet'].
		     $rest;
		$this->_debug('htmlhd',$out);
		return($out);
	}


	## ---------------------------------------------- BODYTAG
	function doc_body ($CONTENT) {
		return("$CONTENT\n");
	}

	function beg_body ($a='') {
		$BGCOLOR=$a[BGCOLOR];
		$BACKGROUND=$a[BACKGROUND];
		$TEXT=$a[TEXT];
		$VLINK=$a[VLINK];
		$ALINK=$a[ALINK];
		$onLoad=$a[onLoad];

		$this->defval($BGCOLOR,   $this->body_def['BGCOLOR']);
		$this->defval($BACKGROUND,$this->body_def['BACKGROUND']);
		$this->defval($TEXT,      $this->body_def['TEXT']);
		$this->defval($LINK,      $this->body_def['LINK']);
		$this->defval($VLINK,     $this->body_def['VLINK']);
		$this->defval($ALINK,     $this->body_def['ALINK']);
		$this->defval($onLoad,    $this->body_def['onLoad']);


		$r='';
		$r.=o_iftrue($BGCOLOR,    $this->bodytag['BGCOLOR']);
		$r.=o_iftrue($BACKGROUND, $this->bodytag['BACKGROUND']);
		$r.=o_iftrue($TEXT,       $this->bodytag['TEXT']);
		$r.=o_iftrue($LINK,       $this->bodytag['LINK']);
		$r.=o_iftrue($VLINK,      $this->bodytag['VLINK']);
		$r.=o_iftrue($ALINK,      $this->bodytag['ALINK']);
		$r.=o_iftrue($onLoad,     $this->bodytag['onLoad']);

		$out=sprintf($this->bodytag['bodytag'],$r);

		$this->_debug('beg_body',$out);
		return($out);
	}

	function end_body ($rest='') {
		$this->_debug2('end_body');
		return(sprintf($this->bodytag['endbody'],$rest));
	}


	## ---------------------------------------------- TABLE
	function doc_table ($CONTENT) {
		$this->_debug2('doc_table');
		return("$CONTENT\n");
	}


	function beg_table ($a='') {

		$WIDTH=$a[WIDTH];
		$BORDER=$a[BORDER];
		$CELLPADDING=$a[CELLPADDING];
		$CELLSPACING=$a[CELLSPACING];
		$BGCOLOR=$a[BGCOLOR];
		$ALIGN=$a[ALIGN];

		$this->defval($WIDTH,     $this->table_def['WIDTH']);
		$this->defval($BORDER,    $this->table_def['BORDER']);
		$this->defval($CELLPADDING,$this->table_def['CELLPADDING']);
		$this->defval($CELLSPACING,$this->table_def['CELLSPACING']);
		$this->defval($BGCOLOR,   $this->table_def['BGCOLOR']);
		$this->defval($ALIGN,     $this->table_def['ALIGN']);

		$r='';
		$r.=o_iftrue($WIDTH,      $this->table['WIDTH']);
		$r.=(0<=$BORDER) ? sprintf($this->table['BORDER'],$BORDER) : '';
		$r.=(0<=$CELLPADDING) ? sprintf($this->table['CELLPADDING'],$CELLPADDING) : '';
		$r.=(0<=$CELLSPACING) ? sprintf($this->table['CELLSPACING'],$CELLSPACING) : '';
		$r.=o_iftrue($BGCOLOR,    $this->table['BGCOLOR']);
		$r.=o_iftrue($ALIGN,      $this->table['ALIGN']);

		$out=sprintf($this->table['tabletag'],$r);

		$this->_debug('beg_table',$out);
		return($out);
	}
                                                 
	function end_table () {
		$this->_debug2('end_table');
		return($this->table['endtable']);
	}

	## ---------------------------------------------- TABROW
	function doc_tabrow ($CONTENT) {
		$this->_debug2('doc_tabrow');
		return("$CONTENT\n");
	}

	function beg_tabrow ($a='') {
		$ALIGN=$a[ALIGN];
		$VALIGN=$a[VALIGN];
		$BGCOLOR=$a[BGCOLOR];

		$this->defval($ALIGN,     $this->tabrow_def['ALIGN']);
		$this->defval($VALIGN,    $this->tabrow_def['VALIGN']);
		$this->defval($BGCOLOR,   $this->tabrow_def['BGCOLOR']);

		$r='';
		$r.=o_iftrue($ALIGN,      $this->tabrow['ALIGN']);
		$r.=o_iftrue($VALIGN,     $this->tabrow['VALIGN']);
		$r.=o_iftrue($BGCOLOR,    $this->tabrow['BGCOLOR']);

		$out=sprintf($this->tabrow['trtag'],$r);

		$this->_debug('beg_tabrow',$out);
		return($out);
	}

	function end_tabrow () {
		$this->_debug2('end_tabrow');
		return($this->tabrow['endtrtag']);
	}

	## ---------------------------------------------- TABCELL
	function doc_tabcell ($CONTENT) {
		$this->_debug2('doc_tabcell');
		return("\n$CONTENT");
	}

	function beg_tabcell ($a='') {
		$COLSPAN=$a[COLSPAN];
		$ROWSPAN=$a[ROWSPAN];
		$ALIGN=$a[ALIGN];
		$VALIGN=$a[VALIGN];
		$BGCOLOR=$a[BGCOLOR];
		$WIDTH=$a[WIDTH];

		$this->defval($COLSPAN,   $this->tabcell_def['COLSPAN']);
		$this->defval($ROWSPAN,   $this->tabcell_def['ROWSPAN']);
		$this->defval($ALIGN,     $this->tabcell_def['ALIGN']);
		$this->defval($VALIGN,    $this->tabcell_def['VALIGN']);
		$this->defval($BGCOLOR,   $this->tabcell_def['BGCOLOR']);
		$this->defval($WIDTH,     $this->tabcell_def['WIDTH']);
		if ($COLSPAN==1) { $COLSPAN = false; }
		if ($ROWSPAN==1) { $ROWSPAN = false; }

		$r='';
		$r.=o_iftrue($COLSPAN,    $this->tabcell['COLSPAN']);
		$r.=o_iftrue($ROWSPAN,    $this->tabcell['ROWSPAN']);
		$r.=o_iftrue($ALIGN,      $this->tabcell['ALIGN']);
		$r.=o_iftrue($VALIGN,     $this->tabcell['VALIGN']);
		$r.=o_iftrue($BGCOLOR,    $this->tabcell['BGCOLOR']);
		$r.=o_iftrue($WIDTH,      $this->tabcell['WIDTH']);

		$out=sprintf($this->tabcell['tdtag'],$r);

		$this->_debug('beg_tabcell',$out);
		return($out);
	}

	function end_tabcell () {
		$this->_debug2('end_tabcell');
		return($this->tabcell['endtdtag']);
	}

	## ---------------------------------------------- ULIST
	function doc_ulist ($CONTENT) {
		$this->_debug2('doc_ulist');
		return("$CONTENT\n");
	}

	function beg_ulist ($COMPACT='',$STYLE='',$CELLSPACING='') {
		$this->defval($COMPACT,   $this->ulist_def['COMPACT']);
		$this->defval($STYLE,     $this->ulist_def['STYLE']);
		$this->defval($CELLSPACING,$this->ulist_def['CELLSPACING']);

		$r='';
		$r.=o_iftrue($COMPACT,    $this->ulist['COMPACT']);
		$r.=o_iftrue($STYLE,      $this->ulist['STYLE']);
		$r.=o_iftrue($CELLSPACING,$this->ulist['CELLSPACING']);

		$out=sprintf($this->ulist['ultag'],$r);

		$this->_debug('beg_ulist',$out);
		return($out);
	}

	function end_ulist () {
		$this->_debug2('end_ulist');
		return($this->ulist['endultag']);
	}


	function ulistitem ($CONTENT,$IMGSRC='') {
		$this->defval($IMGSRC,   $this->uitem_def['IMGSRC']);
		$r=sprintf($this->uitem['licont'],$CONTENT);
		$r=sprintf($r,
		           $this->doc_pic(ARRAY(
		                  SRC=>$IMGSRC,
		                  HVSPACE=>'NULL')));
		$this->_debug('ulistitem',$r);
		return( sprintf($this->uitem['litag'],$r) );
	}


	## ---------------------------------------------------- ANKER
	## ANKER called with NO HREF is just simple text
	function doc_anker ($a) {
	    $comment=$a[comment];
	    $HREF=$a[HREF];
	    $atext=$a[atext];
	    $TARGET=$a[TARGET];
	    $onclick=$a[onclick];

		if ($HREF || $HREF=='NULL') {
			if ($HREF=='NULL') $HREF='';
			$this->defval($comment,   $this->anker_def['comment']);
			$this->defval($atext,     $this->anker_def['atext']);

			$out=sprintf($this->anker['anker'],
			     o_iftrue($comment,    $this->anker['comment']),
			     sprintf($this->anker['HREF'],$HREF) .
			     o_iftrue($TARGET,     $this->anker[TARGET]).
			     o_iftrue($onclick,   $this->anker[onclick]),
			     o_iftrue($atext,      $this->anker['atext'])
			);
		} else {
			$out=o_iftrue($comment,    $this->anker['comment']).
			     o_iftrue($atext,      $this->anker['atext']);
		}

		$this->_debug('doc_anker',$out);
		return($out);
	}

	## ---------------------------------------------- PICTURE
	## MISSING: - better handling of absolute and relative pathes
	##          - Handling of pictures, which are not existing on this
	##            server (hm, this is difficult!)
	function doc_pic ($a,$showdetails=false,$force=false) {
	GLOBAL $_SERVER;
		$SRC=$a[SRC];
		$path=$a[path];
		$HREF=$a[HREF];
		$BORDER=$a[BORDER];
		$ALIGN=$a[ALIGN];
		$HVSPACE=$a[HVSPACE];
		$ALT=$a[ALT];
		$WIDTH=$a[WIDTH];
		$HEIGHT=$a[HEIGHT];

		if (''==$SRC) {
			if ($showdetails) {
				$this->_debug2('doc_pic');
				return("Kein Bild");
			} else {
				$this->_debug2('doc_pic');
				return('');
			}
		}
	    if (''==$path) {
	    	$path=$SRC;
	    }
	    if (ereg("^/",$SRC)) {
	    	## if unset, assume DOCUMENT_ROOT to be the path
	    	if (!$this->layoutpath) {
	    		$this->layoutpath = $_SERVER["DOCUMENT_ROOT"];
	    	}
			$SRC= $this->layouturl . $SRC;
			$path=$this->layoutpath . $path;
		}
	    if (file_exists($path)) {
			$WH=GetImageSize($path);
		} else {
			$this->defval($force,    $this->picture_def['force']);
			if (!$force) {
				$this->_debug2('doc_pic not found');
				return("Bild: '$path' nicht vorhanden!");
			}
		}
		if ($WIDTH>0) {
			$WH[0]=$WIDTH;
		}
		if ($HEIGHT>0) {
			$WH[1]=$HEIGHT;
		}
		$this->defval($BORDER,    $this->picture_def['BORDER']);
		$this->defval($ALIGN,     $this->picture_def['ALIGN']);
		$this->defval($HVSPACE,   $this->picture_def['HVSPACE']);
		$this->defval($ALT,       $this->picture_def['ALT']);

		$r.=($BORDER!='') ? sprintf($this->picture['BORDER'],$BORDER) : '';
		$r.=o_iftrue($ALIGN,      $this->picture['ALIGN']);
		if ($HVSPACE) {
			$r.=sprintf($this->picture['HVSPACE'],$HVSPACE,$HVSPACE);
		}
		$r.=sprintf($this->picture['ALT'],HTMLSpecialChars($ALT));
		$r.=sprintf($this->picture['WIDTH'],$WH[0]);
		$r.=sprintf($this->picture['HEIGHT'],$WH[1]);

		$out=sprintf($this->picture['pictag'],$SRC,$r);

		if ($HREF) {
			$out=$this->doc_anker('NULL',$HREF,$out);
		}

		if ($showdetails) {
			$name=ereg_Replace("^.*/","",$SRC);
			$out="<SMALL>'<strong>$name</strong>'&nbsp;".
			     "$WH[0]x$WH[1]&nbsp;Pixel</SMALL>\n$out";
		}

		$this->_debug('doc_pic',$out);
		return($out);
	}

	## -------------------------------------------------- SIMPTEXT
	## 
	function simptex ($a) {
	    $text=$a[text];
	    $thead=$a[thead];
	    $theadsize=$a[theadsize];
	    $ALIGN=$a[ALIGN];

		$this->defval($theadsize, $this->simptext_def['theadsize']);
		$this->defval($ALIGN,     $this->simptext_def['ALIGN']);

		$r='';$r2='';
		$r2.=o_iftrue($thead,
	                sprintf($this->simptext['thead'],
		                    $theadsize,'%s',$theadsize) );

		$r2.=sprintf($this->simptext['text'],$text);
		if ($ALIGN) {
			$r.=sprintf($this->simptext['ALIGN'],$ALIGN);
			$r.=$r2;
			$r.=$this->simptext['ENDALIGN'];
		} else {
			$r.=$r2;
		}
		$this->_debug('simptex',$r);
		return($r);
	}

	## -------------------------------------------------- RULER
	function rula ($WIDTH='',$SIZE='',$NOSHADE='') {
		$this->defval($WIDTH,     $this->ruler_def['WIDTH']);
		$this->defval($SIZE,      $this->ruler_def['SIZE']);
		$this->defval($NOSHADE,   $this->ruler_def['NOSHADE']);
		$this->defval($SRC,       $this->ruler_def['SRC']);

		$r.=sprintf($this->ruler['SRC'],$SRC);
		$r.=o_iftrue($WIDTH,      $this->ruler['WIDTH']);
		$r.=o_iftrue($SIZE,       $this->ruler['SIZE']);
		$r.=o_iftrue($NOSHADE,    $this->ruler['NOSHADE']);

		$out=sprintf($this->ruler['ruler'], $r);
		return($out);
	}


	## -------------------------------------------------- PLACEHOLDER
	function placeholder ($WIDTH=1,$HEIGHT=1) {
		$this->_debug2('placeholder');
		return($this->doc_pic(ARRAY(
		              SRC=>$this->placeholder_def['SRC'],
		              WIDTH=>$WIDTH,
		              HEIGHT=>$HEIGHT)) );
	}

	## -------------------------------------------------- DOC_JAVASCRIPT
	function doc_javascript($js) {
		$out="<SCRIPT LANGUAGE=\"javascript\">
<!--
$js
// -->
</SCRIPT>
";
		$this->_debug('doc_javascript',$out);
		return($out);
	}


	## -------------------------------------------------- BOX_UPHD
	## Box with headings at the top of box
	function box_uphd ($a) {
	    $text=$a[text];
	    $thead=$a[thead];
	    $BOXCOLOR=$a[BOXCOLOR];
	    $ALIGN=$a[ALIGN];
	    $theadalign=$a[theadalign];
	    $INCOLOR=$a[INCOLOR];

		$this->defval($ALIGN,      $this->simptext_def['ALIGN']);
		$this->defval($theadalign, $this->simptext_def['ALIGN']);
		$this->defval($BOXCOLOR,   $this->color_def['color2']);
		$this->defval($INCOLOR,    $this->color_def['color1']);

		$this->_debug2('box_uphd');
		return(
		  $this->doc_table(
		  $this->beg_table(ARRAY(
		          WIDTH=>'NULL',
		          CELLPADDING=>2,
		          BGCOLOR=>$BOXCOLOR)).
		    $this->doc_tabrow(
		    $this->beg_tabrow().
		     $this->doc_tabcell(
		     $this->beg_tabcell(ARRAY(ALIGN=>$theadalign)).
		     $this->simptex(ARRAY(
		            text=>$this->box_def[BOXPREHEAD].$thead,
		            ALIGN=>$theadalign)).
		     $this->end_tabcell()
		    ).
		    $this->end_tabrow().
		    $this->beg_tabrow().
		     $this->doc_tabcell(
		     $this->beg_tabcell(ARRAY(
		            ALIGN=>$theadalign)).
		      $this->doc_table(
		      $this->beg_table (ARRAY(
		             WIDTH=>'100%',
		             CELLPADDING=>5,
		             BGCOLOR=>$INCOLOR)).
		       $this->doc_tabrow(
		       $this->beg_tabrow().
		        $this->doc_tabcell(
		        $this->beg_tabcell(ARRAY(
		               ALIGN=>$ALIGN)).
		        $this->simptex(ARRAY(
		               text=>$text)).
		        $this->end_tabcell()
		       ).
		       $this->end_tabrow()
		      ).
		      $this->end_table()
		     ).
		     $this->end_tabcell()
		    ).
		    $this->end_tabrow()
		    ).
		    $this->end_table()
		));
	}


	#########################################################
	## This function looks if $val is empty.
	## if yes it returns the $default_val
	## Special Rule: If $val == "NULL" then it returns an empty string
	## After this $val is searched for a string like
	## "%hugo[bla]%". If it is like this, it is replaced with
	## $this->hugo[bla]
	## IDEA: The last replacement could be done recursive
	##
	## Attention: $val is called by value!
	##
	function defval (&$val,$default_val) {
		if (!isset($val) || ''==$val) {
			$val=$default_val;
		} elseif ('NULL' == $val) {
			$val='';
		}

		if (ereg("^%([^\[]+)\[(.+)\]%$",$val,$regs) ) {
			$val=$this->{$regs[1]}[$regs[2]];
		}
	}


	function _debug ($fnname,&$val) {
		if ($this->Debug) {
			echo "<strong>$fnname</strong>:<BR><TT>".
			     nl2br(HTMLSpecialChars($val)) .
			     "</TT><BR>";
		}
	}
	function _debug2 ($fnname) {
		if ($this->Debug) {
			echo "<strong>$fname</strong><BR>";
		}
	}

}
