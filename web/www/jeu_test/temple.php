<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur un dispensaire
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un dispensaire !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 2)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un dispensaire !!!");
	}
}

if ($erreur == 0)
{
	$tab_temple = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_temple['nom'];
	$type_lieu = $tab_temple['libelle'];
	?>
	<p><img src="../images/disp2a.png"><br /><b><?php  echo("$nom_lieu</b> - $type_lieu"); ?>

<?php 
$req = "select perso_sex from perso where perso_cod = $perso_cod";
$db->query($req);
$db->next_record();
$sexe = $db->f("perso_sex");
?>
<?php if ($sexe == 'F')
{?>
<p> <i>Vous vous trouvez dans une vaste salle entourée d’arcades élégantes. Sous ces arcades se cachent des alcôves contenant des lits. Des aventuriers de tous horizons reposent dans certaines de ces alcôves, les bandages qui entourent leur tête ou leurs membres indiquent qu’ils ont été récemment blessés. Un escalier conduit à une galerie qui fait le tour de la salle, et ouvre sur les portes de chambres particulières.

<br>Tout à coup, vos yeux se fixent sur un jeune homme qui s’approche de vous d’une démarche assurée.
	Les chausses qui habillent ses jambes semblent taillées trop étroitement et moulent le galbe de ses mollets
	et ses hanches de façon à laisser peu de place à votre imagination qui s’emballe pourtant à cette vue.
	Une chemise blanche vaporeuse met en valeur la largeur de ses épaules. Elle laisse entrevoir,
	par son échancrure négligemment ouverte, un torse mat et musclé sur lequel brille un petit pendentif en or,
	de la forme d’un calice, symbole de sa fonction de guérisseur.
<br>
L’humain si bien musclé se place juste devant vous, et baisse son visage aux traits énergiques vers vous. Il plonge son regard dont le bleu océan vous fait chavirer, dans le vôtre. Sa voix grave se fait alors entendre, provoquant de petits frissons au creux de votre nuque :
</i>
<br>
<br>« Soyez la bienvenue dans ce dispensaire, Demoiselle. Que puis-je pour vous ? »
<br>
	<p>Voici les services que nous avons à proposer :

    <form name="soins" method="post" action="temple_soins.php">
        <input type="hidden" class="vide" name="soin" value="soin_femelle">
        <p><input type="radio" name="soins" value="1" checked>Vous montrez les menues blessures qui ornent votre corps : « Pouvez-vous me soigner ? » - 20 brouzoufs.<br />
        <input type="radio" name="soins" value="2">Vous dévoilez une large plaie qui vous fait souffrir : « Pouvez-vous soigner cette blessure ? » - 35 brouzoufs.<br />
        <input type="radio" name="soins" value="3">Dans un souffle, vous murmurez « J’agonise... sauvez-moi... »  - 60 brouzoufs.<br />
        <p><input type="submit" value="Valider !" class="test">
    </form>
	<hr />
<?php 		}
	else
	{
?>
<p> <i> Vous vous trouvez dans une vaste salle entourée d’arcades élégantes.
	Sous ces arcades se cachent des alcôves contenant des lits. Des aventuriers de tous horizons reposent
	dans certaines de ces alcôves, les bandages qui entourent leur tête ou leurs membres indiquent qu’ils ont été
	récemment blessés. Un escalier conduit à une galerie qui fait le tour de la salle, et ouvre sur les portes
	de chambres particulières.
<br>
<br>Tout à coup, vos yeux se fixent sur la jeune femme qui s’approche de vous d’une démarche ondulante.
Une jupe sombre dessine le contour de ses jambes élancées.
Un tablier blanc accentue la finesse de sa taille et met en valeur la rondeur de ses hanches.
Son corsage étroit épouse les formes voluptueuses de son corps. Dans le creux de son décolleté aux formes épanouies se love un pendentif en or.
Vos yeux s’attardant à cet endroit, vous remarquez que le pendentif à la forme d’un calice, symbole de sa fonction de guérisseuse.
Arrivée à côté de vous, la sublime jeune femme plonge dans votre regard ses yeux azurés, dont la vivacité est tempérée par le rideau de ses cils allongés. La suavité de sa voix s’écoule alors de sa bouche coralline vers vous :
<br>
<br>« Soyez le bienvenu dans ce dispensaire, Messire. Que puis-je pour vous ? » </i>
    <form name="soins" method="post" action="temple_soins.php">
        <input type="hidden" class="vide" name="soin" value="soin_male">
        <p><input type="radio" name="soins" value="1" checked>Vous montrez les menues blessures qui ornent votre corps : « Pouvez-vous me soigner ? » - 20 brouzoufs.<br />
        <input type="radio" name="soins" value="2">Vous dévoilez une large plaie qui vous fait souffrir : « Pouvez-vous soigner cette blessure ? » - 35 brouzoufs.<br />
        <input type="radio" name="soins" value="3">Dans un souffle, vous murmurez « J’agonise... sauvez-moi... »  - 60 brouzoufs.<br />
        <p><input type="submit" value="Valider !" class="test">
    </form>
	<hr />
	<?php 
	}

	$req_mort = "select perso_nb_mort from perso where perso_cod = $perso_cod ";
	$db->query($req_mort);
	$db->next_record();
	$nb_mort = $db->f("perso_nb_mort");
	$tab_position = $db->get_pos($perso_cod);
	$num_etage = $tab_position['etage_reference'];
	if ($num_etage < 0)
		{$etage = abs($num_etage) + 1;}
	else {$etage = 5;}
	$prix = ($etage * $db->getparm_n(30)) + ($nb_mort * $db->getparm_n(31));

	echo("<p>« - J’aimerai m’inscrire à ce dispensaire.»
<p> <a href=\"choix_temple.php\">faire de ce dispensaire votre dispensaire par défaut ($prix

brouzoufs)</a>");
	echo(" si vous le désirez.<br /><br />");
}
include "quete.php";
?>

