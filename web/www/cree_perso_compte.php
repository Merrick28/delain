<?php 
include "includes/classes.php";
include "ident.php";
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
?>
<!DOCTYPE html>
<html>
<!-- Date de création: 06/02/2003 -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<SCRIPT LANGUAGE="JavaScript" SRC="scripts/verif_cree_perso.js"></SCRIPT>
<script language="javascript"> 
    ns4 = document.layers; 
	ie = document.all; 
	ns6 = document.getElementById && !document.all; 
 	function getObject(n,d)
 	{
 		if(typeof(n)=='object') return n;
 		if(typeof(n)=='undefined') return null;
 		var p,i,x;
 		if(!d) d = document;
 		if((p=n.indexOf("@"))>0 && parent.frames.length)
 		{
 			d=parent.frames[n.substring(p+1)].document;
 			n=n.substring(0,p);
 		}
 		if(!(x=d[n]) && d.all) x=d.all[n];
 		for (i=0;!x && i<d.forms.length;i++) x=d.forms[i][n];
 		for(i=0;!x && d.layers&&i<d.layers.length;i++) x = getObject(n,d.layers[i].document);
 		if(!x && d.getElementById) x=d.getElementById(n);
 		return x;
 	}
	var ex_poste = new Array();
		ex_poste['err'] = '';
		ex_poste['H'] = 'La loi et l’ordre y règnent, défendus par l’autorité de la Milice.<br> Ne vous avisez pas d’attaquer votre prochain, les conséquences pourraient être facheuses (cf.texte ci-dessous)';
		ex_poste['S'] = 'La seule loi qui y règne est celle du plus fort, ou du plus malin.<br> Méfiez vous de votre prochain comme de votre ombre, il n’hésitera pas à vous assassiner dans votre sommeil !  (cf.texte ci-dessous)';
	var explication = new Array();
		explication['err'] = '';
		explication['guerrier'] = 'Une épée courte et une armure légère vous seront données pour débuter votre aventure.';
		explication['bucheron'] = 'Une hache et une armure légère vous seront données pour débuter votre aventure.';
		explication['monk'] = 'Une armure légère et quelques parchemins magiques vous seront donnés pour débuter votre aventure.';
		explication['archer'] = 'Un arc léger et une bolas vous seront donnés pour débuter votre aventure.';
		explication['mage'] = 'Quelques runes des premières familles vous seront fournies, afin de pouvoir commencer à lancer les sorts indispensables à tout mage qui se respecte.';
		explication['explo'] = 'Vous entrerez dans les souterrains en connaissant déjà une partie de la carte des extérieurs. De plus, pour vous aider à explorer le reste du monde, quelques parchemins de vitesse vous seront offerts.';
		explication['mineur'] = 'Vous entrerez dans les souterrains une pioche à la main et un casque sur la tête. Ces outils vous permetttent non seulement de vous défendre, mais la pioche permet aussi de creuser certains murs pour peut être y trouver la fortune, qui sait ?';
		explication['fid_io'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_balgur'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_galthee'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_elian'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_apiera'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_falis'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
		explication['fid_ecatis'] = 'En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins).';
	function montre(id)
	{
		objet=document.getElementById(id);
		if (objet.style.display="none")
		objet.style.display="";
	}
	function setText(i,tab)
	{
		t_index = document.formuperso1.voie.options[document.formuperso1.voie.selectedIndex].value;
		document.getElementById(i).innerHTML = explication[t_index];
		
	}
	function setText_p(i,c)
	{
		t_index = document.formuperso1.poste.options[document.formuperso1.poste.selectedIndex].value;
		document.getElementById(i).innerHTML = ex_poste[t_index];
		
	}
</script>
</head>
<body background="images/fond5.gif">
<?php 
if (!$verif_auth)
{
	echo "<p>Erreur sur le passage de compte !";
	exit;
}
if (!isset($retour))
{
	$retour = 0;
}
if ($retour == 1)
{
    $def_for = $ret_for;
    $def_dex = $ret_dex;
    $def_con = $ret_con;
    $def_int = $ret_int;
}
else
{
    $def_for = 9;
    $def_dex = 9;
    $def_con = 9;
    $def_int = 9;
}
$db = new base_delain;
$creation_possible = false;
$creation_4e = false;
if (!isset($compt_cod))
{
	echo "<p><strong>Numéro de compte non défini ! </strong>Merci de contacter Merrick (page cree_perso_compte.php)!";
}
else
{
    // Recherche du type de perso en cours de création
    $requete = "select compt_ligne_perso, autorise_4e_perso(compt_quatre_perso, compt_dcreat) as autorise_quatrieme,
            compte_nombre_perso(compt_cod) as nb_perso, possede_4e_perso(compt_cod) as possede
        from compte 
        where compt_cod = $compt_cod";

    $db->query($requete);
    $db->next_record();
    $nb_perso = $db->f('nb_perso');
    $possede_4e = ($db->f('possede') == 't');
    $nb_perso_par_ligne = ($db->f('autorise_quatrieme') == 't' || $possede_4e) ? 4 : 3;
    $nb_perso_max = $db->f('compt_ligne_perso') * $nb_perso_par_ligne;

    $creation_possible = $nb_perso < $nb_perso_max;
    $creation_4e = ($nb_perso == 3 && !$possede_4e);
}
include "jeu_test/tab_haut.php";
if ($creation_possible)
{
?>
<p class="titre">Création d’un aventurier (étape 1)</p>
			
			
			<!-- début tableau Secondaire -->
				<FORM method="post" action="cree_perso_compte2.php" name="formuperso1" OnSubmit="return verifform()">
				<input type="hidden" name="compt_cod" value="<?php echo $compt_cod;?>">
				<div class="centrer"><table background="images/fondparchemin.gif" width="80%" bgcolor="#EBE7E7" border="0" cellpadding="2" cellspacing="2">
<?php     if ($creation_4e)
    {
?>
<tr><td colspan="3">
                Vous avez la possibilité de créer ce personnage car vous êtes présent sur le jeu depuis plus de deux ans.<br />
<strong>Ceci n’est pas considéré comme un droit mais comme une responsabilité.</strong> Il aura la vocation de pouvoir aider d’autres nouveaux joueurs, ou de pouvoir faire de nouvelles rencontres, ou tester des nouveautés à un plus petit niveau/
En effet, ce personnage sera volontairement limité.<br />
<strong>Il ne pourra pas se rendre en dessous du -1. Ce qui correspond à l’heure actuelle aux étages 0, -1, -1 bis et sous bassements et découvertes ainsi que le passage sous la rivière.</strong>
De plus, il est fort possible qu’il soit aussi limité en terme de niveau d’expérience.<br />
Si ces règles ne vous conviennent pas, ne gardez surtout pas ce personnage, car ne pas les respecter sera forcément sanctionné !<br />
<br />
Comprenez aussi que ce personnage n’est pas là pour vous fournir un avantage en terme de jeu, mais plus pour vous permettre d’aider d’autres aventuriers.
Dans le cas où nous aurions à gérer la moindre plainte (accaparement de matériel, menaces, 4° personnage d’une triplette ...), la sanction sera immédiate et ne touchera pas que ce personnage.
Nous comptons sur votre fair play et que cela vous permette de découvrir une nouvelle dimension du jeu. <br />
<br />
<strong>Pour une expérience de jeu encore différente, il est également possible de contrôler un monstre !</strong> Pour activer cette option, rendez-vous dans les options de votre compte, et cherchez le lien adéquat en bas de la page.<br />
                <hr></td></tr>
<?php     }
?>
                <tr><td colspan="3"><strong>Avant de créer votre personnage, nous vous invitons à consulter les <a href="http://www.jdr-delain.net/wiki/index.php/R%C3%A8gles">règles</a> qui vous donneront tout un tas d’informations utiles sur les fiches de personnage, ainsi que le <a href="http://www.jdr-delain.net/wiki/index.php">wiki</a> en général, qui contient de très nombreuses informations.<br>De plus <a href="http://www.jdr-delain.net/doc/aide.php">une aide à la création des personnages</a> est disponible. N’hésitez pas à consulter tous ces éléments fort utiles !</strong>
					<hr></td></tr>
					<tr>
						<td><p>Nom de l'aventurier : </p></td>
						<td colspan="2"><input type="text" name="nom" size="40" maxlength="25"></td>
					</tr>
					<tr><td colspan="3"><hr></td></tr>
					<tr>
						<td><p>Force : </p></td>
						<td><input type="text" name="force" size="4" maxlength="2" value="<?php echo $def_for;?>" onBlur="verif_carac(1);"></td>
						<td><p>Points restants à répartir :</p></td>
					</tr>
					<tr>
						<td><p>Dextérité : </p></td>
						<td><input type="text" name="dex" size="4" maxlength="2" value="<?php echo $def_dex;?>" onBlur="verif_carac(2);"></td>
						<td><center><input type="text" name="points" size="4" maxlength="2" onBlur="verif_carac(5);" value="9"></center></td>
					</tr>
					<tr>
						<td><p>Constitution : </p></td>
						<td colspan="2"><input type="text" name="con" size="4" maxlength="2" value="<?php echo $def_con;?>" onBlur="verif_carac(3);"></td>
					</tr>
					<tr>
						<td><p>Intelligence : </p></td>
						<td colspan="2"><input type="text" name="intel" size="4" maxlength="2" value="<?php echo $def_int;?>" onBlur="verif_carac(4);"></td>
					</tr>
					<tr><td colspan="3"><hr></td></tr>
					<tr>
						<td><p>Race : </p></td>
						<td colspan="2">
						<select name="race">
<?php 					
$recherche = "SELECT race_cod,race_nom from race where race_cod in (1,2,3)";
$db->query($recherche);
while($db->next_record())
{
	printf("<option value=\"%s\">%s</option>\n",$db->f("race_cod"),$db->f("race_nom"));
}
?>
</select></td>
</tr>
<tr>
	<td colspan="3"><hr></td>
</tr>
<tr>
	<td><p>Sexe : </p></td>
	<td colspan="2">
	<select name="sexe">
		<option value="M">Homme</option>
		<option value="F">Femme</option>
	</select>
	</td>
<!-- rajout du choix du poste de garde	-->
<tr><td colspan="3"><hr></td></tr>
<tr>
	<td width="30%"><p>Poste de garde à votre entrée : </p></td>
	<td width="30%">
	<select name="poste" onChange="javascript:setText_p('ex_poste','test');">
		<option value="err">-- </option>
		<option value="H">Dirigé par Hormandre</option>
		<option value="S">Ouvert par Sal'Morv</option>
	</select>
	</td>
	<td width="40%"><div id="ex_poste"></div></td>
</tr>
<!-- 
 rajout du choix type de l'aventurier
-->					
<tr><td colspan="3"><hr></td></tr>
<tr>
	<td width="30%"><p>Quelle voie allez vous choisir ? </p></td>
	<td width="30%">
	<select name="voie" onChange="javascript:setText('explication','explication');">
		<option value="err">-- </option>
		<option value="guerrier" onChange="javascript:setText('explication',explication['guerrier']);">La voie de l'aventurier</option>
		<option value="bucheron" onChange="javascript:setText('explication',explication['bucheron']);">La voie du barbare</option>
		<option value="monk" onChange="javascript:setText('explication',explication['monk']);">La voie du lutteur</option>
		<option value="archer" onChange="javascript:setText('explication',explication['monk']);">La voie de l'archer</option>
		<option value="mage" onChange="javascript:setText('explication',explication['mage']);">La voie du mage</option>
		<option value="explo" onClick="javascript:setText('explication',explication['explorateur']);">La voie de l'explorateur</option>
		<option value="mineur" onClick="javascript:setText('explication',explication['mineur']);">La voie du mineur</option>
	</select><br>
	
	</td>
	<td width="40%">
		<div id="explication"></div>
	</td>
</tr>		
<tr><td colspan="3"><i>N.B. : afin d’éviter tout abus, tous les objets créés lors de l’activation de l’aventurier ne peuvent pas tomber de son inventaire en cas de mort.<br>
	De plus, les runes et parchemins ne pourront pas faire l’objet de transactions vers des échoppes ou autres joueurs.</i></td></tr>		
<tr><td colspan="3"><hr></td></tr>
<tr>
	<td><input type="reset" value="Tout effacer"></td>
	<td colspan="2"><input type="submit" class="test" value="Valider et continuer"></td>
</tr>    	
<tr><td colspan="3"><hr></td></tr>
<tr><td colspan="3"><br><strong>INFORMATION IMPORTANTE :</strong>
<br>Dans l’univers que vous allez aborder, les interactions entre joueurs sont très nombreuses. Elles sont souvent le fait de l’histoire de ce monde, mais aussi des rencontres et des péripéties qui s’y sont créées et que les aventuriers ont créées par leur Role Play (et voilà, si ce terme barbare ne se trouve pas dans ton lexique, c’est que tu n’as pas lu suffisamment bien les règles. N’hésites pas à retourner les consulter). Parfois ces interactions sont violentes et se finissent dans des combats entre aventuriers.<br>
<br>
Dans notre monde sans pitié, le roi de ces contrées, Hormandre III, a mandaté une milice pour faire régner l’ordre, mais de nombreux troubles perturbent encore le secteur. Cette milice a pris en charge une partie des deux premiers niveaux uniquement, et impose la loi d’Hormandre, par tous les moyens. Faites attention, la loi est la loi.<br>
Mais un de ses voisins ne voit pas d’un très bon œil cette loi catégorique et inique qui n’est pas la sienne. Sal’Morv, a donc engagé des troupes pour faire régner sa loi, c’est à dire aucune. Vol, violence et racket sont ses jeux favoris, et que le meilleur gagne.<br>
Suite à une guerre impitoyable, un traité a été établi afin de laisser une partie des deux premiers niveaux à disposition de Sal’Morv. Ainsi, chaque souverain peut assouvir ses envies, la lutte contre Malkiar pour l’un, la lutte pour la survie des plus forts pour l’autre.<br>
<br>
Vous qui rentrez dans ces souterrains, vous aurez votre destin entre vos mains. A vous de choisir la meilleure situation pour votre départ, mais sachez que les conséquences seront immédiates, d’un côté comme de l’autre.<br>
Sur les terres d’Hormandre, la milice jouera son rôle, alors que dans le secteur de Sal'Morv, la loi du plus fort s’imposera.<br>
<br>
Maintenant, n’ayez pas peur, même si au fond, il fait noir... </td></tr>
</table></div>

<?php 
}
else
{
    echo '<p>Erreur ! Il semble que vous ayiez déjà assez de personnages comme cela...</p>';
}
include "jeu_test/tab_bas.php";
?>
</body>\
</html>
