<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>

<link rel="stylesheet" type="text/css" href="{g_url}jeu/style_vue.php">
<link rel="stylesheet" type="text/css" href="{g_url}stylev2.php">

<link rel="meta" href="http://www.jdr-delain.net/labels.rdf" type="application/rdf+xml" title="ICRA labels">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
{javascript}

<script language="javascript">
function mod()
{ 
parent.gauche.location.href="menu.php";
parent.gauche.cols="0%"; }
</script>

<body background="{img_path}fond5.gif" onload="mod();"><!--{action_onload}-->
<input name="btnp2"
type="button"
onclick="mod();"
value="modifier">
<br>
<table class="vide2" width="100%">
<tr>
	<td width="175" class="blockPierre">
	<!-- Debut du contenu gauche-->
		<div id="blockMenu">
		<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>
		<div class="barrLbord"><div class="barrRbord"><p class="barrTitle">{nom_perso}</p></div></div>
		<div class="barrLbord"><div class="barrRbord"style="text-align:center;line-height:5px">
		<p>{intangible}<br>
		<img src="{img_barre_pa}" title="{nb_pa} PA "><br><br><img src="{img_path}coeur.gif"> <img src="{img_pv}" title="{pv}/{pv_max}PV"><br><br>
		<img src="{img_path}iconexp.gif">  <img src="{img_px}" title="{px} PX, prochain niveau � {px_niveau}"><br><br>
		<img src="{img_path}att.gif" title="fourchette de d�gats"> <b>{deg_min}-{deg_max}</b><img src="{img_path}del.gif" height="8" width="16"><img src="{img_path}def.gif" title="Armure"> <b>{armure}</b><span style="line-height:100%;"><br><br>X : <b>{x}</b> Y : <b>{y}</b><br><b>{etage}</b></span></p>
		</div></div>
		<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>
		<p class="blockPierre"><br></p>

		
		<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>


		<div class="barrLbord"><div class="barrRbord">
		<p class="texteMenu">
		{lieu}
		{quete}
		{passage_niveau}
		{menu_complet}
		{gestion_compte}
		{option_monstre}
		{logout}
		
		</p>
		</div></div>
		<div class="barrL"><div class="barrR"><p class="barrC"></p></div></div>
		</div>
	<!-- Fin du contenu gauche-->
	</td>
	<td>
		<!-- Debut du contenu -->
		<div id="topdroit" style="display:{topdroit_visible};">
			<table width="100%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td width="10" background="{img_path}coin_hg.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
			<td background="{img_path}ligne_haut.gif"><img src="{img_path}del.gif" height="8" width="10"></td>

			<td width="10" background="{img_path}coin_hd.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
			</tr>
			<tr>
			<td width="10" background="{img_path}ligne_gauche.gif">&nbsp;</td>
			<td>
			{contenu_page}
		
			</td>
			<td width="10" background="{img_path}ligne_droite.gif">&nbsp;</td>

			</tr>
			<tr>
			<td height="10" width="10" background="{img_path}coin_bg.gif"><img src="{img_path}del.gif" height="2"></td>
			<td height="10" background="{img_path}ligne_bas.gif"></td>
			<td height="10" width="10" background="{img_path}coin_bd.gif"></td>
			</tr>
			</table>
		
		</div>
		<div id="topvue" height="350" style="display:{vue_visible}";>
			<div style="position:fixed;z-index: 1" width="100%">
			<table width="100%" class="vide2">
				<tr>
				<td><span style="overflow: scroll;">{vue_gauche}</span></td>
				<td><span style="overflow: scroll;">{vue_droite}</span></tD>
					<!--
					<td>{vue_gauche}</td>
					<td>{vue_droite}</td>-->
				</tr>
			</table>
					<!--
					<td>{vue_gauche}</td>
					<td>{vue_droite}</td>-->

			<!--<div style="position:relative;top:400px;">-->


	{vue_bas}
		</div>
			

		<!-- Fin du contenu -->
	</td>
</tr>
</table>
</body>
</html>