<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page  = '';
$contenu_page4 = '';
$erreur        = 0;

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select count(perso_cod) as nombre from perso,perso_position 
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = " . $perso_cod . ")
										and perso_quete = 'quete_forgeron.php'
										and perso_cod = ppos_perso_cod";
$stmt     = $pdo->query($req_comp);
$result   = $stmt->fetch();
if ($result['nombre'] == 0 or $stmt->rowCount() == 0)
{
    $erreur        = 1;
    $contenu_page4 .= 'Vous n\'avez pas accès à cette page !';
}
$req_quete = "select pquete_quete_cod,pquete_perso_cod,pquete_date_debut,pquete_termine,pquete_param from quete_perso 
							where pquete_perso_cod = " . $perso_cod . " and pquete_quete_cod = 19";
$stmt      = $pdo->query($req_quete);
if ($stmt->rowCount() == 0 and !isset($_REQUEST['methode3']))
{
    $methode3 = 'E';
} else if ($_REQUEST['methode3'] != 'suite')
{
    $result          = $stmt->fetch();
    $methode3        = $result['pquete_termine']; /*E pas commencé, on lance - N en cours - O quête déjà réalisée*/
    $arme_reparation = $result['pquete_param'];
}

if ($erreur == 0)
{
    switch ($methode3)
    {
        case "E":
            $contenu_page4 .= "Dis, l'Ami ! Peux-tu m'aider? Mon apprenti est parti depuis le passage d'un groupe de Ménestrels ! 
				<br>Je mettrais ma main à couper qu'il a voulu suivre la belle aventure avec une des danseuses, le bougre ! 
				<br>En attendant, me voilà avec tout ce fatras non rangé sur les bras ! Si ça te tente, répare-moi un peu ces objets.
				<br><br>
				<a href=\"" . $_SERVER['PHP_SELF'] . "?methode3=suite\"><strong>J'accepte avec plaisir</strong></a>";
            break;

        case "suite":
            $contenu_page4 .= "tu trouveras un marteau, posé sur l'enclume.
			 <br>Et reviens me voir quand tu auras réussi une réparation. Pour cela il te faudra aussi exercer tes talents à identifier ce que tu as sous les yeux.
			 <br><br><em>Le forgeron vous récompensera lorsque vous aurez réussi au moins un réparation sur l'objet qu'il vous remet.</em><br>";
            $req           = "select cree_objet_perso(832," . $perso_cod . ") as arme_cassee";
            $stmt          = $pdo->query($req);
            $result        = $stmt->fetch();
            $arme          = $result['arme_cassee'];
            $req           = "update objets set obj_etat = 20 where obj_cod = " . $arme;
            $stmt          = $pdo->query($req);
            $result        = $stmt->fetch();
            $req           =
                "insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_param) values (19," . $perso_cod . "," . $arme . ")";
            $stmt          = $pdo->query($req);
            $result        = $stmt->fetch();
            break;

        case "N":
            /* On vérifie la possession d'un objet pour cette quête et son état*/
            $req    = "select obj_gobj_cod,perobj_obj_cod,obj_nom,obj_etat 
						from objets,perso_objets 
						where perobj_obj_cod = obj_cod 
						and perobj_perso_cod = $perso_cod 
						and obj_cod = " . $arme_reparation;
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            if ($stmt->rowCount() == 0)
            {
                $contenu_page4 .= "Vous ne possédez plus l'arme que je vous avez donné. Voilà qui est bien dommage. Si d'aventure vous souhaitiez recommencer cette expérience, revenez me voir, je vous fournirais une autre arme à réparer";
                $req           =
                    "delete from quete_perso where pquete_quete_cod = 19 and pquete_perso_cod = " . $perso_cod;
                $stmt          = $pdo->query($req);
            } else
                if ($result['obj_etat'] > 20)
                {
                    $contenu_page4 .= "Ah, bravo ! Je savais que je pouvais compter sur toi ! Merci l'Ami ! 
															<br>Tu peux rester et continuer si tu le souhaites, cela ne te sera pas forcément inutile dans tes aventures et te permettra d'améliorer ce nouveau savoir. En attendant, voici une petite bourse de brouzoufs en récompense. 
															<br><br>Ah, j'oubliais. Méfie-toi quand tu répareras ton propre matériel : il n'aura qu'une capacité limité à retrouver son état d'origine. Plus tu le feras sur un objet, moins il retrouvera ses capacités complètes.
															<br><br>";

                    $perso->perso_px = $perso->perso_px + 5;
                    $perso->perso_po = $perso->perso_po + 50;
                    $perso->stocke();

                    $req    =
                        "update quete_perso set pquete_termine = 'O' where pquete_quete_cod = 19 and pquete_perso_cod = " . $perso_cod;
                    $stmt   = $pdo->query($req);
                    $result = $stmt->fetch();
                } else
                {
                    $contenu_page4 .= "<br>Je pense que tu peux faire mieux que cela, soit tu as été très fainéant, soit tu as détérioré cet objet !
																<br>Dans l'un ou l'autre cas, tu dois te remettre à l'ouvrage.";
                }
            break;

        case "O":
            /*Quête déjà réalisée, donc on ferme les portes*/
            $contenu_page4 .= "Nous nous sommes déjà rencontré je pense. Et tu as bien travaillé ! Je te félicite encore.";
            break;
    }
}
include "blocks/_footer_page_jeu.php";