<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=0.75">
		<title>Les souterrains de Delain</title>
		<link rel="shortcut icon" href="drake_head_red.ico" type="image/gif">
		<link rel="stylesheet" type="text/css" href="{URL}style.css?v20181113" title="essai">
		<!-- Bootstrap custom CSS -->
		<link href="{URL}css/container-fluid.css?v20181113" rel="stylesheet">

		<SCRIPT language="Javascript" src="{URL}scripts/ajax.js"></script>
		<script src="{URL}/js/jquery.js"></script>
		<script src="{URL}scripts/ajax2.js?v20181113" type="text/javascript"></script>
		<script src="{URL}scripts/tools.js?v20181113_1" type="text/javascript"></script>
		<script src="{URL}scripts/delain.js?v20181113" type="text/javascript"></script>
		<script src="{URL}vendor/nok/nok.min.js" type="text/javascript"></script>

	</head>
<body style="background-image:url({URL_IMAGES}fond5.gif);" onload="tailleCadre()">
<script>//# sourceURL=general_jeu.js
    $( document ).ready(function() {
        $("div#dropdown-box").click( function (event){ switch_menu(event); });
    	$("button[class^='button-switch']").click( function (){ switch_perso(this.id); });

    	// Auto-hide/show the switches buttons (sur barre autohide)
        if ($('#colonne0').data("switch-bar")=="autohide")
        {
            $('body').on('mousemove', function(event) {
                if(10 - event.clientY >= 0) {
					$('#colonne0').slideDown();
				}
			});
		}
	});
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1534416-3', 'auto');
  ga('require', 'GTM-M2VTQQW');
  ga('send', 'pageview');

</script>
<div id="le_tout">
{BARRE_MENU_ICONE}
<div id="colonne1">
	<div id="dropdown-box">
		<div id="nom" style="background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;">{PERSO_NOM}</div>
		<div style="text-align:center;padding:5px">
			<div id="intangible" style="padding:2px">{INTANGIBLE}</div>
			<div id="pa" style="padding:2px"><img src="{URL_IMAGES}barrepa_{PERSO_PA}.gif" title="{PERSO_PA} PA " alt="{PERSO_PA} PA "></div>
			<div id="hp" style="padding:0px"><img src="{URL_IMAGES}coeur.gif" alt=""> <div title="{PERSO_PV}/{PERSO_PV_MAX}PV" alt="{PERSO_PV}/{PERSO_PV_MAX}PV" class="container-hp"><div class="barre-hp" style="width:{PERSO_BARRE_VIE}%"></div></div></div>
			<div id="enchanteur" style="padding:0px">{ENCHANTEUR}</div>
			<div id="divin" style="padding:0px">{FAM_DIVIN}</div>
			<div id="xp" style="padding:0px"><img src="{URL_IMAGES}iconexp.gif" alt=""> <div title="{PERSO_PX} PX, prochain niveau à {PERSO_PROCHAIN_NIVEAU}" alt="{PERSO_PX}/{PERSO_PROCHAIN_NIVEAU} PX" class="container-xp"><div class="barre-xp" style="width:{PERSO_BARRE_XP}%"></div></div></div>
			<div id="degats"><img src="{URL_IMAGES}att.gif" title="fourchette de dégats" alt="Att"> <b>{PERSO_DEGATS}</b><img src="{URL_IMAGES}del.gif" height="2" width="16" alt=" "><img src="{URL_IMAGES}def.gif" title="Armure" alt="Def"> <b>{PERSO_ARMURE}</b></div>
			<div id="position"><br>X : <b>{PERSO_POS_X}</b> Y : <b>{PERSO_POS_Y}</b><br><b><a href="{URL_RELATIVE}desc_etage.php"><img alt="" src="/images/iconmap.gif" style="height:12px;border:0px;" />{PERSO_ETAGE}</a></b></div>
		</div>
        <div  style="padding:0 10 0 10px; text-align:center;">
            <div id="passageniveau">{PASSAGE_NIVEAU}</div>
            <div id="quete">{PERSO_QUETE}</div>
            <div id="lieu">{PERSO_LIEU}</div>
        </div>
        <div id="dropdown-button">&or;</div>
	</div>
	<div id="dropdown-menu"class="dropdown-content">

        <hr><img src="{URL_IMAGES}ficheperso.gif" alt=""> <a href="{URL_RELATIVE}perso2.php" >Fiche de perso</a><br>
		<img src="{URL_IMAGES}vue.gif" alt=""> <b><a href="{URL_RELATIVE}frame_vue.php">Vue</a></b><br>
		<img src="{URL_IMAGES}evenements.gif" alt=""> <a href="{URL_RELATIVE}evenements.php">Événements</a><br>
        {PERSO_AUTO_QUETE}
		<hr />

		<img src="{URL_IMAGES}inventaire.gif" alt=""> <a href="{URL_RELATIVE}inventaire.php">Inventaire</a><br>
		<div id="ramasser">{RAMASSER}</div>
		<div id="transaction"><img src="{URL_IMAGES}transaction.gif" alt=""> <a href="{URL_RELATIVE}transactions2.php">{TRANSACTIONS}</a></br></div>
		<hr />
		<div id="deplacement">{TEXTE_DEP}</div>
		<img src="{URL_IMAGES}attaquer.gif" alt=""> <b><a href="{URL_RELATIVE}combat.php">Combat !</a></b><br>
		<img src="{URL_IMAGES}magie.gif" alt=""> <b><a href="{URL_RELATIVE}magie.php">Magie !</a></b><br>
		{VOIE_MAGIQUE}
        {FAVORIS}
		<hr />
		{FORGE}
		{CREUSER}
		{ENLUMINEUR}
		{POTION}
		{RELIGION}
		{ENSEIGNEMENT}
		<img src="{URL_IMAGES}concentration.gif" alt=""> <a href="{URL_RELATIVE}concentration.php">Concentration</a><br>
		{VOL}
		<hr />
		<div id="messagerie"><img src="{URL_IMAGES}messagerie.gif" alt=""> <a href="{URL_RELATIVE}messagerie2.php">{PERSO_MESSAGERIE}</a></div>
		<img src="{URL_IMAGES}guilde.gif" alt=""/> <a href="{URL_RELATIVE}guilde.php">Guilde</a><br />
		<img src="{URL_IMAGES}guilde.gif"  alt=""/> <a href="{URL_RELATIVE}groupe.php">Coterie</a>
		<hr />
		{CONTROLE}
		{CONTROLE_ADMIN}
		{DROIT_LOGS}
		{GESTION_DROITS}
		{MODIF_PERSO}
		{MODIF_MONSTRE}
		{MODIF_OBJETS}
		{DROIT_CARTE}
		{DROIT_ENCHANTEMENT}
		{DROIT_POTION}
		{QUETE_AUTO}
		{FACTIONS}
		{NEWS}
		{ANIMATIONS}
		{ECHOPPE}
		{GERANT}
		{COMMANDEMENT}
		{OPTION_MONSTRE}
		<img src="{URL_IMAGES}iconeswitch.gif" alt=""> <a href="{URL_RELATIVE}switch.php"><b>Gestion compte</b></a>
		<hr />
		<img src="{URL_IMAGES}forum.gif" alt=""> <a href="http://forum.jdr-delain.net" target="_blank">Forum</a> - {WIKI}<br>
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="{URL}" target="_top">Accueil</a><br />
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="{URL}jouer.php">Ancienne version</a><br />
		<hr />
		<img src="{URL_IMAGES}deconnection.gif" alt=""> <a href="{URL_RELATIVE}logout.php" target="_top">Se déconnecter</a>
		<hr />
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
            <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" style="border:0px; background-color:transparent"
                   name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
            <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
        </form>
	</div>
</div>
{BARRE_SWITCH_RAPIDE}
<div id="colonne2">
	<form name="visu_evt2" method="post" action="visu_evt_perso.php">
		<input type="hidden" name="visu" />
		<input type="hidden" name="cible" />
		<input type="hidden" name="num_guilde" />
		<input type="hidden" name="type" value="1" />
		<input type="hidden" name="type_at" value="0" />
		<input type="hidden" name="methode" value="attaque2" />
		<input type="hidden" id="saction" name="saction" value="action.php">
		<input type="hidden" id="sposition" name="sposition" value="action.php">
	</form>
	<form name="destdroite" id="destdroite" method="post">
		<input type="hidden" name="position" id="idposition" />
		<input type="hidden" name="destcadre" value="le_tout" id="iddestcadre" />
		<input type="hidden" name="dist" value="" />
		<input type="hidden" name="action" id="idaction" value="action.php" />
		<input type="hidden" name="methode" value="deplacement" />
	</form>
	<div id="vue_haut" style="overflow:auto;">
		<table style="background : none;">
			<tr>
				<td valign="top"><div id="vue_gauche" class="bordiv">{VUE_GAUCHE}</div></td>
				<td><div id="vue_droite" class="bordiv">{VUE_DROITE}</div></td>
			</tr>
			{VUE_RESULTAT}
		</table>
	</div>
	<div id="vue_bas" class="bordiv" style="overflow:auto;">{VUE_BAS}</div>
	<p style="font-style:italic;"><a href="#" onclick="javascript:modifierCadre();">Étendre / réduire ce cadre</a></p>
</div>
</div>
</body>
</html>

