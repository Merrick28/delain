<html>
<head>
		<title>Les souterrains de Delain</title>
		<link rel="shortcut icon" href="drake_head_red.ico" type="image/gif">
		<link rel="stylesheet" type="text/css" href="{URL}style.css" title="essai">
		<link rel="stylesheet" type="text/css" href="{URL}style.php">
		<style>
			div#colonne1 {
				float: left;
				width: 175px;
				height : 100%;
				background-image:url({URL_IMAGES}fondparchemin.gif);
				border-color:#800000;
				border-style:solid;
				border-radius: 10px;
				height:auto;
				overflow:auto;
				}
			div#colonne2 {
				padding:10px;
				margin-left: 205px;
				}
</style>
<SCRIPT language="Javascript" src="{URL}scripts/ajax.js"></script>
</head>
<body style="background-image:url({URL_IMAGES}fond5.gif);">

<div id="colonne1">
	<div id="nom" style="background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;">{PERSO_NOM}</div>
	<div style="text-align:center;padding:5px">
		<div id="intangible" style="padding:2px">{INTANGIBLE}</div>
		<div id="pa" style="padding:2px"><img src="{URL_IMAGES}barrepa_{PERSO_PA}.gif" title="{PERSO_PA} PA " alt="{PERSO_PA} PA "></div>
		<div id="hp" style="padding:2px"><img src="{URL_IMAGES}coeur.gif" alt=""><img src="{URL_IMAGES}hp{PERSO_BARRE_VIE}.gif" title="{PERSO_PV}/{PERSO_PV_MAX}PV" alt="{PERSO_PV}/{PERSO_PV_MAX}PV"></div>
		<div id="enchanteur" style="padding:2px">{ENCHANTEUR}</div>
		<div id="xp" style="padding:2px"><img src="{URL_IMAGES}iconexp.gif" alt=""><img src="{URL_IMAGES}xp{PERSO_BARRE_XP}.gif" title="{PERSO_PX} PX, prochain niveau à {PERSO_PROCHAIN_NIVEAU}" alt="{PERSO_PX}/{PERSO_PROCHAIN_NIVEAU} PX"></div>
		<div id="degats"><img src="{URL_IMAGES}att.gif" title="fourchette de dégats" alt="Att"> <b>{PERSO_DEGATS}</b><img src="{URL_IMAGES}del.gif" height="2" width="16" alt=" "><img src="{URL_IMAGES}def.gif" title="Armure" alt="Def"> <b>{PERSO_ARMURE}</b></div>
		<div id="position"><br>X : <b>{PERSO_POS_X}</b> Y : <b>{PERSO_POS_Y}</b><br><b><a href="desc_etage.php"><img alt="" src="/images/iconmap.gif" style="height:12px;border:0px;" />{PERSO_ETAGE}</a></b></div>
		</div>
<div style="padding:10px;">
		<div id="passageniveau">{PASSAGE_NIVEAU}</div>
		<div id="quete">{PERSO_QUETE}</div>
		<div id="lieu">{PERSO_LIEU}</div>
		<div id="messagerie"><img src="{URL_IMAGES}messagerie.gif" alt=""> <a href="messagerie2.php">{PERSO_MESSAGERIE}</a></p></div>
		<img src="{URL_IMAGES}vue.gif" alt=""> <a href="frame_vue.php">Vue</a><br>
		<div id="deplacement">{TEXTE_DEP}</div>
		<div id="ramasser">{RAMASSER}</div>
		
		
		<img src="{URL_IMAGES}forum.gif" alt=""> <a href="http://forum.jdr-delain.net" target="_blank">Forum</a> - {WIKI}<br>
		
		
		<img src="{URL_IMAGES}ficheperso.gif" alt=""> <a href="perso2.php" >Fiche de perso</a><br>

		<img src="{URL_IMAGES}evenements.gif" alt=""> <a href="evenements.php">Evenements</a><br>
		{CONTROLE}
		{MODIF_PERSO}
		{MODIF_MONSTRE}
		{DROIT_CARTE}
		{CONTROLE_ADMIN}
		{GESTION_DROITS}
		{MODIF_OBJETS}
		{DROIT_ENCHANTEMENT}
		{DROIT_POTION}
		{DROIT_LOGS}
	   {QUETE_AUTO}
	   {NEWS}
    	<hr />
    	
    	<img src="{URL_IMAGES}evenements.gif" alt=""> <a href="concours_barde.php">Concours de barde</a><br>
		<hr />

		{ECHOPPE}
		{GERANT}
		{DIEU}
		{FIDELE_GERANT}
		<img src="{URL_IMAGES}guilde.gif" alt=""> <a href="guilde.php">Guilde</a><BR />
		<img src="{URL_IMAGES}guilde.gif"  alt=""/> <a href="groupe.php">Coterie</a>
		<hr />		

		<img src="{URL_IMAGES}inventaire.gif" alt=""> <a href="inventaire.php">Inventaire</a><br>
		<div id="ramasser"></div>
		<hr />
		
		<img src="{URL_IMAGES}concentration.gif" alt=""> <a href="concentration.php">Concentration</a><br>
		<hr />
		
		<img src="{URL_IMAGES}attaquer.gif" alt=""> <a href="combat.php">Combat !</a><br>
		<hr />
		
		<img src="{URL_IMAGES}magie.gif" alt=""> <a href="magie.php">Magie !</a><br>
		{VOIE_MAGIQUE}
		{FORGE}
		{ENLUMINEUR}
		{POTION}
		{RELIGION}
		{COMMANDEMENT}
		{ENSEIGNEMENT}
		{CREUSER}
		{VOL}
		<div id="transaction"><img src="{URL_IMAGES}transaction.gif" alt=""> <a href="transactions2.php">Transactions</a></br></div>
		<hr />
		
		<img src="{URL_IMAGES}iconeswitch.gif" alt=""> <a href="switch.php">Gestion compte</a><br>
		{OPTION_MONSTRE}
		<hr />
		
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="{URL}" target="_top">Accueil</a><br />
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="{URL}jouer.php">Version normale</a><br />
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="logout.php" target="_top">Se déconnecter</a>
	</div>
</div>

<div id="colonne2">
<div id="vue_haut" style="height:350px;overflow:auto;position:fixed;"><table style="background : none;"><tr><td><div id="vue_gauche" class="bordiv">{VUE_GAUCHE}</span></td><td><div id="vue_droite" class="bordiv">{VUE_DROITE}</span></td></tr></table></div>
<div id="vue_bas" class="bordiv" style="margin-top:350px;">{VUE_BAS}</div>


</div>
</html>

