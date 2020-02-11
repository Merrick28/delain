<?php // gestion des quêtes sur les centres de maîtrise magique.

include "blocks/_tests_appels_page_externe.php";


if (!isset($methode2))
    $methode2 = "debut";

$comp_enlumineur = 0;

//Changer les compétences nécessaires
$req = 'select pcomp_modificateur, pcomp_pcomp_cod from perso_competences
	where pcomp_perso_cod = ' . $perso_cod . '
		and pcomp_pcomp_cod in (91, 92, 93)';
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
{
    $result = $stmt->fetch();
    $comp_enlumineur = $result['pcomp_pcomp_cod'];
    $comp_enlumineur_percent = $result['pcomp_modificateur'];
}
//On vérifie que le perso ramène bien les deux objets nécessaires (un de chaque d’ailleurs)
// gobj_cod = 730 <=> Plume de scribe
// gobj_cod = 731 <=> Peau de démon travaillée
$req_nombre = "select count(obj_gobj_cod) as nombre
	from objets,perso_objets
	where perobj_obj_cod = obj_cod
		and perobj_perso_cod = $perso_cod
		and obj_gobj_cod = 730 group by obj_gobj_cod order by obj_gobj_cod ";
$stmt = $pdo->query($req_nombre);
if($result = $stmt->fetch())
    $nombre1 = $result['nombre'];
else
    $nombre1 = 0;

$req_nombre = "select count(obj_gobj_cod) as nombre
	from objets, perso_objets
	where perobj_obj_cod = obj_cod
		and perobj_perso_cod = $perso_cod
		and obj_gobj_cod = 731 group by obj_gobj_cod order by obj_gobj_cod ";
$stmt = $pdo->query($req_nombre);
if($result = $stmt->fetch())
    $nombre2 = $result['nombre'];
else
    $nombre2 = 0;

$sortie_quete = '<hr><br>';

switch ($methode2)
{
    case "debut":
        //On regarde si le perso est déjà un enlumineur, et quel est son niveau
        if ($comp_enlumineur == 0) //Nouvel enlumineur potentiel
        {
            $req = "select pquete_param_texte, pquete_nombre from quete_perso
				where pquete_quete_cod = 16
					and pquete_perso_cod = $perso_cod";
            $stmt = $pdo->query($req);
            if($result = $stmt->fetch())
            {
                $position_quete = $result['pquete_param_texte'];

                // On est déjà face à un enlumineur potentiel, on doit donc vérifier à quel stade il est
                if ($result['pquete_nombre'] == 2) // Le perso a bien réalisé sa quête en tuant le monstre
                {
                    $sortie_quete .= '« <em>Vous voilà de nouveau ? Et je vois que vous revenez victorieux !
						J’espère que vous avez remarqué que vous aviez vaincu votre propre peur !
						<br>Regardons maintenant si vous me rapportez aussi les objets qui me permettront
						de vous montrer comment réaliser vos propres enluminures...</em> »';
                    //On vérifie que le perso possède bien les objets nécessaires
                    if ($nombre1 > 0 && $nombre2 > 0)
                    {
                        $sortie_quete .= '« <em><br>Voilà qui est parfait, vous me ramenez donc tous les éléments
							pour faire de vous un grand enlumineur !</em>»
							<br><br>Encore ce dernier pas à réaliser avant d’être un bon enlumineur :
							<a href="' . $PHP_SELF . '?methode2=niv1_conf"><strong>Allez, je me lance !</strong></a><br><br>
							Il vous en coûtera <strong>12 PA</strong>';
                    } else //Pas le bon nombre d’objet
                    {
                        $sortie_quete .= '« <em>Je crois bien que vous n’avez pas compris ce que vous deviez me ramener.
							Il s’agissait d’une plume et d’un papier vierge, mais très spéciaux !
							Apprenez donc à compter !</em> »';
                    }
                } else //Pas encore tué le monstre
                {
                    $sortie_quete .= '« <em>Vous voilà déjà ? Mais vous n’avez rien réalisé !
						Cherchez donc un peu mieux, et revenez me voir lorsque vous aurez combattu
						votre âme noire et récupéré les objets nécessaires.</em> »';
                    // On vérifie que la position de la quête est au même étage.
                    // Sinon on permet au perso d’en reprendre une.
                    $tab = $db->get_pos($perso_cod);
                    $pos_cod = $tab['pos_cod'];
                    $req_position = "select etage_numero from etage, positions
						where pos_cod = $pos_cod
							and pos_etage = etage_numero
							and etage_numero in
								(select etage_numero from etage, positions
								where pos_cod = cast($position_quete as integer)
								and pos_etage = etage_numero)";
                    $stmt = $pdo->query($req_position);
                    if ($stmt->rowCount() == 0)
                    {
                        $sortie_quete .= '<br />Je vois que malheuseument, vous n’êtes plus à l’étage
							vous permettant de réaliser cette quête.
							<br>Voici une nouvelle chance de vous rendre maître dans l’art de l’enluminure.
							<br><br><em>L’annulation de la quête précédente a été automatiquement faite.</em>';
                        $req_delete = "update positions set pos_fonction_dessus = '' where pos_cod = $position_quete";
                        $stmt = $pdo->query($req_delete);
                        $req_delete = "delete from quete_perso
							where pquete_quete_cod = 16
								and pquete_perso_cod = $perso_cod";
                        $stmt = $pdo->query($req_delete);
                    }
                }
            } else // Pas encore engagé dans la quête
            {
                $sortie_quete .= '<br>Un magicien présent dans ces lieux vous regarde avec insistance.
					Il semble vous analyser, provoquant chez vous quelques frissons.
					<br>D’un coup, il se rapproche de vous et vous aborde : <br>
					<br>« <em>Voilà un moment que je vous observe, et si vous êtes en ces lieux,
					c’est qu’il doit y avoir une bonne raison et que vous êtes intéressé par les arts magiques.
					<br><strong>Je peux faire de vous un magicien important, versé dans l’art de l’enluminure !</strong></em> »
					<br><br>Hum, voilà quelque chose de tentant !
					<a href="' . $PHP_SELF . '?methode2=niv1"><strong>Allez, je me lance !</strong></a><br><br>';
            }
        } //Fin d’un perso sans enluminure
        else if ($comp_enlumineur == 91) // Le perso est au premier niveau d’enluminure
        {
            if ($comp_enlumineur_percent < 90) // Pas le niveau comp suffisant
            {
                $sortie_quete .= '« <em>Vous revoilà déjà ?
					<br>Vous manquez de pratique pour prétendre à ce que je vous apprenne autre chose !
					Revenez donc lorsque vous serez un peu plus expérimenté.
					<br>L’enseignement est une chose, la pratique et l’expérience une autre !</em> »
					<br><br>Un niveau minimum de <strong>90%</strong> dans votre compétence d’enlumineur est nécessaire
					avant de pouvoir passer au niveau 2.<br><br>';
            } else // La première condition est remplie
            {
                $sortie_quete .= '« <em>Ah, je vois que vous avez investi dans l’enseignement que je vous avais donné !
					C’est une bonne chose, et je me verrais ravi de vous en apprendre un peu plus.
					<br>Bon, malheureusement, je manque un peu de moyen en ce moment, et il faudra que
					vous me fournissiez quelques brouzoufs pour que puisse acheter des composants.
					<br>Donnez moi <strong>10 000 brouzoufs</strong>, et je ferais de vous un enlumineur accompli !</em> »
					<br><br>Hum, voilà quelque chose de tentant !
					<a href="' . $PHP_SELF . '?methode2=niv2"><strong>Allez, je me lance !</strong></a><br><br>';
            }
        } else if ($comp_enlumineur == 92) // Le perso est au deuxième niveau d’enluminure
        {
            if ($comp_enlumineur_percent < 100)
            {
                $sortie_quete .= '« <em>Vous revoilà déjà ?
					<br>Vous manquez de pratique pour prétendre à ce que je vous apprenne autre chose !
					Revenez donc lorsque vous serez un peu plus expérimenté.
					<br>L’enseignement est une chose, la pratique et l’expérience une autre !</em> »
					<br><br>Un niveau minimum de <strong>100%</strong> dans votre compétence d’enlumineur est nécessaire
					avant de pouvoir passer au niveau 2<br><br>';
            } else
            {
                $sortie_quete .= '« <em>Ah, je vois que vous avez investi dans l’enseignement que je vous avais donné !
					C’est une bonne chose, et je me verrais ravi de vous en apprendre un peu plus.
					<br>Bon, malheureusement, je manque un peu de moyen en ce moment, et il faudra que vous me fournissiez
					quelques brouzoufs pour que puisse acheter des composants.
					<br>Donnez moi <strong>20000 brouzoufs</strong>, et je ferais de vous un enlumineur expérimenté !</em> »
					<br><br>Hum, voilà quelque chose de tentant !
					<a href="' . $PHP_SELF . '?methode2=niv3"><strong>Allez, je me lance !</strong></a><br><br>';
            }
        } else if ($comp_enlumineur == 93) // Le perso est au troisième niveau d’enluminure
        {
            $sortie_quete .= '« <em>Cher confrère ! Nous pouvons deviser si vous le souhaitez des meilleurs
				parchemins que vous avez pu trouver !
				<br>Je suis toujours friand de ces informations qui me permettent de mieux capturer la magie...</em> »
				<br><br>Et l’enlumineur se lance dans des palabres sans fin...<br><br>';
        }
        break; //Fin du case début

    case "niv1": //Le perso veut devenir enlumineur, donc on va lui donner la quête qui va bien
        $req = "select pquete_param_texte from quete_perso
			where pquete_quete_cod = 16 and pquete_perso_cod = " . $perso_cod;
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() == 0)
        {
            $req = "select enlumineur($perso_cod, 0) as resultat";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $sortie_quete .= $result['resultat'];
        } else
        {
            $sortie_quete .= 'Tnuuuuuuuuuuut !!!!!!';
        }
        break;

    case "niv1_conf": //Le perso va devenir enlumineur niveau 1
        $req = "select pquete_param_texte from quete_perso
			where pquete_quete_cod = 16 and pquete_perso_cod = " . $perso_cod;
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() == 0 || $nombre1 = 0 || $nombre2 = 0)
        {
            $sortie_quete .= 'Tnuuuuuuuuuuut !!!!!!';
        } else
        {
            $req = "select enlumineur($perso_cod, 1) as resultat";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $sortie_quete .= $result['resultat'];
        }
        break;

    case "niv2": // 10000 brouzoufs et limite comp
        $req = "select enlumineur($perso_cod, $comp_enlumineur) as resultat";
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        $sortie_quete .= $result['resultat'];
        break;

    case "niv3": // 20000 brouzoufs et limite comp
        $req = "select enlumineur($perso_cod, $comp_enlumineur) as resultat";
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        $sortie_quete .= $result['resultat'];
        break;
}
