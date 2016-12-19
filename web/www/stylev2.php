<?php header("Content-type: text/css");
?>
body {margin:0; padding:12px; background-image:url(<?php echo G_IMAGES?>fond5.gif)}
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
.bt_delain {color:#FFE081;border-color:#CE3100 #9C0000 #9C0000 #CE3100;background:url(<?php echo G_IMAGES?>bouton.jpg)}
.check_box {border:none;background-image:url(<?php echo G_IMAGES?>fondparchemin.gif)}

/* Partie FOW */
.fowtitle{background-color:#800000;color:#FFFFFF;font-size:10pt;text-align:center;font-weight:bold;margin-bottom:10px}
.fowline1{background-color:#800000;color:#FFFFFF;font-size:10pt;text-align:center;font-weight:bold}
.fowline2{background-color:#BA9C6C}

/* Partie TABLEAU */
#blockMenu {top:15px;left:15px;position:absolute;width:160px;background-image:url(<?php echo G_IMAGES?>fondparchemin.gif)}
.blockTexte {margin:0 0 0 0;background-image:url(<?php echo G_IMAGES?>fondparchemin.gif)}
.blockPierre {margin:0 0 0 0;background-image:url(<?php echo G_IMAGES?>fond5.gif)}
.barrL {height:10px;background-image:url(<?php echo G_IMAGES?>coin_bg.gif);background-repeat:no-repeat}
/*.barrC {height:10px;background-image:url(<?php echo G_IMAGES?>ligne.gif);background-repeat:repeat-x;font-size:0}*/
.barrC {height:10px;background-image:url(<?php echo G_IMAGES?>ligne_haut.gif);background-repeat:repeat-x;font-size:0}
.barrR {height:10px;padding:0 10px 0 10px;background-image:url(<?php echo G_IMAGES?>coin_bd.gif);background-repeat:no-repeat;background-position:right}
.barrLbord {padding:0 0 0 10px;background-image:url(<?php echo G_IMAGES?>ligne_gauche.gif);background-repeat:repeat-y}
.barrTitle {background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0}
.barrTitleCourt {background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;width:80%;}
.barrRbord {padding:0 10px 0 0;background-image:url(<?php echo G_IMAGES?>ligne_droite.gif);background-repeat:repeat-y;background-position:right}
.texteNorm {padding:5px 0 5px 2px}
.textNormToRight {padding:2px 5px;text-align:right}
.barrLbord {padding:0 0 0 10px;background-image:url(<?php echo G_IMAGES?>ligne_gauche.gif);background-repeat:repeat-y}
.vide {background-image:url(<?php echo G_IMAGES?>fond5.gif);}

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
.fowline1{
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	}
.fowline2{
	padding-right:2px;
	padding-left:2px;
	background-color:#BA9C6C;
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	padding-right:2pt;
	padding-left:2pt;
	}
.fowtitle{
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	}
.fowtable{
	border-color : #B5955A #9F7F47 #9F7F47 #B5955A;
	border-width: 2px;
}

body
{
		font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	padding-right:2pt;
	padding-left:2pt;
	}
li{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	padding-right:2pt;
	padding-left:2pt;
	}
.cible	{
	position: absolute;
	visibility: hidden;
	z-index: 4;
}
input.test {
	background-color : #CE6300;
	color : #FFE081;
	font-size: 13px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	border-color : #CE3100 #9C0000 #9C0000 #CE3100;
	background : url(images/bouton.jpg);
}
.bouton
{
	background-color:#B69353;
}
a.bouton
{
	background-color:#B69353;
}
input.bouton {
	color : #FFFFFF;
	font-size: 9px;
	font-family: Verdana, Helvetica, sans-serif;
	border-color : #B5955A #9F7F47 #9F7F47 #B5955A;
	background : url(images/bouton.gif);
	border-width: 2px;
	padding-right:0pt;
	padding-left:0pt;
	margin-left:0pt;
	margin-right:0pt;
	text-indent:0pt;
}

table {
	background : url(images/fondparchemin.gif);
}
.fond2 {
	background : url(images/fondparchemin.gif);
	background-color:#FFFFFF;
}
table.vide {
	background : url(images/mp_fondcarte.gif);
	background-color:#FFFFFF;
}
table.vide2 {
	background : url(none);
}

table.resume {
    background : url(images/fondparchemin2.gif);
	background-color:#FFFFFF;
}

a:link{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	text-decoration : underline;
	color:#4D0505;
	   }
a:visited{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	text-decoration : underline;
	color:#800000;
	   }
a:hover{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	text-decoration: none;
	color:#B60000;
		}

a.mail:link{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:right;
	text-decoration : underline;
	font-weight:bold;
	   }
a.mail:visited{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:right;
	text-decoration : underline;
	font-weight:bold;
	   }
a.mail:hover{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:right;
	color:#8D00F0;
	font-weight:bold;
		}
a.titre:link{
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	}
a.titre:link{
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	}
a.titre:visited{
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	}

p{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	padding-right:2pt;
	padding-left:2pt;
	}
.image{
	text-align:center;
	padding-left:0pt;
	padding-right:0pt;
	padding-top:0pt;
	padding-bottom:0pt;
}
.coord{
	font-family:verdana,helvetica,sans-serif;
	font-size:5pt;
	text-align:center;
	padding-right:0pt;
	padding-top:0pt;
	padding-bottom:0pt;
	padding-left:0pt;
	background-color:#BA9C6C;
	}
.coord2{
	font-family:verdana,helvetica,sans-serif;
	font-size:5pt;
	text-align:center;
	padding-right:0pt;
	padding-top:0pt;
	padding-bottom:0pt;
	padding-left:0pt;
	background-color:#BA9C6C;
	}
input {
	border: black 1px solid;
	font-size: 11px;
	color: black;
	font-family: verdana,helvetica,sans-serif;
	background-color: #EBE8E3;
}
input.vide {
	border-style: none;
	border: none;
	font-size: 11px;
	color: black;
	background : url(images/fondparchemin.gif);
}
textarea {
	border: black 1px solid;
	font-size: 11px;
	color: black;
	font-family: verdana,helvetica,sans-serif;
	background-color: #EBE8E3;
}
select {
	border: black 1px solid;
	font-size: 11px;
	color: black;
	font-family: verdana,helvetica,sans-serif;
	background-color: #EBE8E3;
}
.titre{
	color:#FFFFFF;
	font-size:10pt;
	text-align:center;
	font-weight:bold;
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	}

.soustitre{
	color:#FFFFFF;
	font-size:9pt;
	text-align:center;
	font-weight:bold;
	padding-right:5px;
	padding-left:5px;
	background-color:#660033;
	}

p.mail{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:justify;
	font-weight:bold;
	   }

p.droite{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:right;
	font-weight:bold;
	}
td{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
}
td.titre{
	padding-right:10px;
	padding-left:10px;
	background-color:#800000;
	}



td.soustitre2{
	padding-right:2px;
	padding-left:2px;
	background-color:#BA9C6C;
	}
.soustitre2{
	padding-right:2px;
	padding-left:2px;
	background-color:#BA9C6C;
	}
.soustitre2_rouge{
	padding-right:2px;
	padding-left:2px;
	background-color:red;
	}
.soustitre2_orange{
	padding-right:2px;
	padding-left:2px;
	background-color:orange;
	}
.soustitre2_vert{
	padding-right:2px;
	padding-left:2px;
	background-color:green;
	}
.surcharge1{
	padding-right:2px;
	padding-left:2px;
	background-color:#FFD905;
	}
.surcharge2{
	padding-right:2px;
	padding-left:2px;
	background-color:#F67004;
	}
td.coord2{
	padding-right:0px;
	padding-left:0px;
	padding-top:0px;
	padding-bottom:0px;
	background-color:#BA9C6C;
	}
td.soustitre3{
	padding-right:0px;
	padding-left:0px;
	background-color:#BA9C6C;
	}
.navoff {
border : 0px;
padding-right:2px;
padding-left:2px;
background-color:#BA9C6C;
}
.navon {
padding-right:2px;
padding-left:2px;
background-color:#BA9C6C;
border : 1px solid #800000;
font-weight:bold;
}
.vueoff {
	padding-right:0px;
padding-left:0px;
padding-top:0px;
	padding-bottom:0px;
border : 1px solid #efe7e7;
}
.vueon {
	padding-right:0px;
padding-left:0px;
padding-top:0px;
	padding-bottom:0px;
border : 1px solid #0000FF;
}
.vuesurcase {
	padding-right:0px;
padding-left:0px;
padding-top:0px;
	padding-bottom:0px;
border : 1px solid #FF0000;
}
.surligne{
	padding-right:2px;
	padding-left:2px;
	background-color:#C9B28D;
}
.onglet{
padding-right:2px;
	padding-left:2px;
	border-top-width:1px;
	border-top-style: solid;
	border-left-width:1px;
	border-left-style: solid;
	border-right-width:1px;
	border-right-style: solid;
}
.pas_onglet{
padding-right:2px;
	padding-left:2px;
	background-color:#BA9C6C;
	border-bottom-width:1px;
	border-bottom-style: solid;
}
.reste_onglet{
padding-right:2px;
	padding-left:2px;
	border-bottom-width:1px;
	border-bottom-style: solid;
	border-left-width:1px;
	border-left-style: solid;
	border-right-width:1px;
	border-right-style: solid;
}
