<?php 
define("NOGOOGLE", "1");
// calc an offset of 24 hours
$offset = 3600 * 24 * 10;
// calc the string in GMT not localtime and add the offset
$expire = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
//output the HTTP header
Header($expire);
$gmt_mtime = gmdate('D, d M Y H:i:s', time() ) . ' GMT';
header("Last-Modified: " . $gmt_mtime );
header('Content-type: text/css');
// Bleda 2/2/11 dÃ©jÃ  fait en footer. ob_start();
?>
body {margin:0; padding:12px; background-image:url(http://images.jdr-delain.net/fond5.gif)}
* {font-family:verdana,helvetica,sans-serif;font-size:9pt}
p {margin:0;padding:0}
form {margin:0;padding:2px 5px}
input {border:black 1px solid;background-color:#EBE8E3}
a {text-decoration:underline;color:#4D0505}
a:hover {text-decoration:none;color:#B60000}
a:visited {color:#800000}
img {border:none}
/*hr {border-top:0 hidden black;border-right:0 hidden black;border-bottom:0.1em solid black;border-left:0 hidden black}*/

.texteMenu {padding:5px 0 5px 2px;border-bottom:1px solid black;}
.bt_delain {color:#FFE081;border-color:#CE3100 #9C0000 #9C0000 #CE3100;background:url(http://images.jdr-delain.net/bouton.jpg)}
.check_box {border:none;background-image:url(http://images.jdr-delain.net/fondparchemin.gif)}


/* Partie TABLEAU */
#blockMenu {top:15px;left:15px;position:absolute;width:160px;background-image:url(http://images.jdr-delain.net/fondparchemin.gif)}
.blockTexte {margin:0 0 0 0;background-image:url(http://images.jdr-delain.net/fondparchemin.gif)}
.blockPierre {margin:0 0 0 0;background-image:url(http://images.jdr-delain.net/fond5.gif)}
.barrL {height:10px;background-image:url(http://images.jdr-delain.net/coin_bg.gif);background-repeat:no-repeat}
/*.barrC {height:10px;background-image:url(http://images.jdr-delain.net/ligne.gif);background-repeat:repeat-x;font-size:0}*/
.barrC {height:10px;background-image:url(http://images.jdr-delain.net/ligne_haut.gif);background-repeat:repeat-x;font-size:0}
.barrR {height:10px;padding:0 10px 0 10px;background-image:url(http://images.jdr-delain.net/coin_bd.gif);background-repeat:no-repeat;background-position:right}
.barrLbord {padding:0 0 0 10px;background-image:url(http://images.jdr-delain.net/ligne_gauche.gif);background-repeat:repeat-y}
.barrTitle {background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0}
.barrTitleCourt {background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;width:80%;}
.barrRbord {padding:0 10px 0 0;background-image:url(http://images.jdr-delain.net/ligne_droite.gif);background-repeat:repeat-y;background-position:right}
.texteNorm {padding:5px 0 5px 2px}
.textNormToRight {padding:2px 5px;text-align:right}
.barrLbord {padding:0 0 0 10px;background-image:url(http://images.jdr-delain.net/ligne_gauche.gif);background-repeat:repeat-y}
.vide {background-image:url(http://images.jdr-delain.net/fond5.gif);}


dl, dd, ul {
margin: 0;
padding: 0;
list-style-type: none;
}
dt {
margin: 0;
padding: 5px;
list-style-type: none;
font-family: "comic sans ms";
font-size: 13px;
font-weight: bold;
font-style: italic;
}
li {
margin: 0;
padding: 2px;
list-style-type: none;
}
#menu {
position: absolute;
top: 0em;
left: 1em;
width: 10em;
}
#menu dt {
cursor: pointer;
}
#menu dd {
background : url(images/fondparchemin.gif);
position: absolute;
z-index: 100;
left: 12em;
margin-top: -4em;
width: 20em;
border: 1px solid gray;
}
#menu ul {
	background : url(images/fondparchemin.gif);
	padding-right:2px;
	padding-left:2px;

}
#menu li {
text-align: center;
font-size: 85%;
height: 20px;
line-height: 20px;
}
#menu li a, #menu dt a {
color: #000;
text-decoration: none;
display: block;
}
#menu li a:hover {
text-decoration: underline;
}
#mentions {
font-family: verdana, arial, sans-serif;
position: absolute;
bottom : 200px;
left : 50px;
color: #000;
background-color: #ddd;
}
#mentions a {text-decoration: none;
color: #222;
}
#mentions a:hover{text-decoration: underline;
}
.fond2 {
	background : url(images/fondparchemin.gif);
	background-color:#FFFFFF;
}
#nom {background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0}
<?php 
header('Content-Length: ' . ob_get_length());
 // Bleda 2/2/11 dÃ©jÃ  fait en footer. ob_end_flush();
?>
