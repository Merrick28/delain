
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<html>
<head>
<link rel="stylesheet" href="js/jquery/css/ui-lightness/jquery-ui-1.8rc1.custom.css" type="text/css" media="all" />
<script src="js/jquery/jquery-1.4.1.min.js" type="text/javascript"></script>
<script src="js/jquery/jquery-ui-1.8rc1.custom.min.js" type="text/javascript"></script>

<style type="text/css">

body {
	background-color: #CCC;
	font-family:Verdana, Helvetica, Arial,sans-serif;
	font-size:0.8em;
	color: #fff;
	margin: 0px;
	padding: 0px;
}

.liste_equipement {
	border: 1px solid black;
	width:500px;
}

.detaille div {	
	width:70px;
	display:inline-block;
	
}
.detaille li {
border: 1px solid blue;
	list-style: none;
	margin:0px;
	padding:1px;
}
.detaille .li_total {
	width:210px;
}

.detaille .li_img {
	display:none;
}
.detaille .head {
	font-weight:bold;
	background-color: #333;
	color: #FFF;
}


.simplifie .li_nom, .simplifie .li_type, .simplifie .li_poids, .simplifie .li_action{
	display:none;
}

.simplifie .head, .simplifie .total {
	display:none;
}

.simplifie li {
	list-style: none;
	float:left;
	display:inline;
	width:48px;
	height:48px;
	margin:2px;
	padding:2px;
	position:relative;
}
.simplifie .li_nombre {
	position: absolute;
	bottom: 2px;
	right: 5px;
	font-weight:bold;
	color:#fff;
}

</style>
<script type="text/javascript">
$(function() {
		$("#show_hide_details").click(function(){ 
			$("ul").toggleClass('detaille').toggleClass('simplifie');
		});
	});
</script>
</head>
<body>

<div class="liste_equipement ui-widget-content">
<a id="show_hide_details" href="#">Afficher/Cacher les d&eacute;tails</a>
<ul class="simplifie">
<li class="head">
<div class="li_nom">Nom</div><div class="li_type">Type</div><div class="li_nombre">Nombre</div><div class="li_poids">Poids</div>
</li>
<?php for($i = 1; $i<6; $i++){
	for($j = 1; $j<$i+2; $j++){
$nb = rand(1,10);
?>
	<li><img class="li_img" alt="Rune" src="images/rune_<?php echo $i?>_<?php echo $j?>.gif"><div class="li_nom">Male</div><div class="li_type">Rune</div><div class="li_nombre"><?php echo $nb?></div><div class="li_poids"><?php echo $nb/10.0?></div><div class="li_action"><a href="#">D&eacute;poser</a></div></li>
<?php  }
} ?>
<li class="total">
<div class="li_total">Total:</div>	<div class="li_poids">10.0</div>
</li>
</ul>
<div style="clear:both"></div>
</div>
</body>
</html>

