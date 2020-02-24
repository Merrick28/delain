<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = $verif_connexion->perso;


$contenu_page = '';

if (isset($_GET['tobj']))
    $tobj = $_GET['tobj'];

// VERIFICATIONS D'USAGE


$boule = True;

//TEST PRESENCE

$req_matos = "select perobj_obj_cod, obj_etat from perso_objets,objets "
             . "where perobj_obj_cod = obj_cod and and perobj_perso_cod = $perso_cod and obj_gobj_cod = $tobj order by perobj_obj_cod";

$stmt   = $pdo->query($req_matos);
$result = $stmt->fetch();

$num_obj    = $result['perobj_obj_cod'];
$etat_objet = $result['obj_etat'];
if (!($result = $stmt->fetch()))
{
    // PAS D'OBJET.
    $contenu_page .= "<p>Vous avez beau chercher, il n’y a pas cet objet dans votre sac</p>";
    $boule        = false;
}

// TEST PA
if ($boule == true)
{
    if ($perso->perso_pa < 4)
    {
        $contenu_page .= "<p><strong>Vous n’avez pas assez de PA !</strong></p>";
        $boule        = false;
    }
}

// TEST PORTEUR = FAMILIER
if ($boule)
{
    if ($tobj == 269)
    {
        $is_familier  = false;
        if ($perso->perso_type_perso = 3)
        {
            $contenu_page .= "<p>Un familier ne peut pas s’occuper d’un œuf !</p>";
            $boule        = false;
        }
    }
}

// TEST PORTEUR A UN FAMILIER
if ($boule)
{
    if ($tobj == 269)
    {
        $has_familier = false;
        $req_familier =
            "select pfam_familier_cod from perso_familier,perso where pfam_perso_cod = $perso_cod and pfam_familier_cod = perso_cod and perso_actif = 'O'";
        $stmt         = $pdo->query($req_familier);
        if ($result = $stmt->fetch())
        {
            $contenu_page .= "<p>Votre familier vous fait clairement comprendre qu’il n’est pas prêt à vous laisser vous occuper de cet œuf.<br /> Peut-être faudrait-il laisser cet objet à quelqu’un qui n’a pas d’animal jaloux ?</p>";
            $boule        = false;
        }
    }
}

if ($boule)
{
    // ON ENLEVE LES PAs
    $perso->perso_pa = $perso->perso_pa - 4;
    $perso->stocke();

    // ON DIMINUE 'ETAT
    $diff_etat  = mt_rand(0, 25) + 1;
    $etat_objet = $etat_objet - $diff_etat;
    $req_etat   = "update objets set obj_etat = $etat_objet where obj_cod = $num_obj";
    $stmt       = $pdo->query($req_etat);

    switch ($tobj)
    {
        case 269:
            $contenu_page .= "<p><strong>Vous réchauffez l’œuf quelques minutes...</strong></p>";
            break;
        case 640:
            $contenu_page .= "<p><strong>Vous faites un peu de sport pour retrouver la ligne ...</strong></p>";
            break;
        default:
            $contenu_page .= "<p><strong>Vous tentez de détruire cet objet ...</strong></p>";
            break;
    }

    //ETAT NEGATIF (destruction)

    if ($etat_objet <= 0)
    {
        // ON SUPPRIME L'OBJET.
        $req_supr_obj = "select  f_del_objet($num_obj)";
        $stmt         = $pdo->query($req_supr_obj);

        switch ($tobj)
        {
            case 269:
                // POSITION DU PROPRIETAIRE
                $req_pos        = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
                $stmt           = $pdo->query($req_pos);
                $result         = $stmt->fetch();
                $perso_position = $result['ppos_pos_cod'];

                $choix = mt_rand(0, 100);
                if ($choix < 25)
                {
                    $contenu_page .= "<p><strong>Un cobra apparaît ! Il n’a pas l’air amical. </strong><br></p>";
                    $req_monstre  = "select cree_monstre_pos(16,$perso_position) as num";
                    $stmt         = $pdo->query($req_monstre);
                } else if ($choix < 50)
                {
                    $contenu_page .= "<p><strong>Un basilic apparaît ! Il n’a pas l’air amical. </strong><br></p>";
                    $req_monstre  = "select cree_monstre_pos(13,$perso_position) as num";
                    $stmt         = $pdo->query($req_monstre);
                } else
                {
                    require "../blocks/_objet_cree_fam.php";
                }
                break;
            case 640:
                $contenu_page .= "<p><strong>Ouf !  Les efforts ont porté leurs fruits: vous avez retrouvé la ligne !</strong><br></p>";
                break;
            default:
                $contenu_page .= "<p><strong>Et cela a marché: l'objet est détruit !</strong></p>";
                break;
        }
    } else
    {
        switch ($tobj)
        {
            case 269:
                require "../blocks/_objet_cree_oeuf2.php";
                break;
            case 640:
                $contenu_page .= "<p>Vous transpirez et vous sentez mieux ... mais encore un effort pour retrouver la forme !</p>";
                break;
            default:
                $contenu_page .= "<p>Des fissures apparaissent ... mais l'objet tient bon !</p>";
                break;
        }
    }
    $contenu_page .= '<form method="post" action="action_objet.php">
	<input type="hidden" name="methode" value="rechauffer">';
    switch ($tobj)
    {
        case 269:
            $contenu_page .= '<input type="submit" value="Réchauffer (4PA)"  class="test">';
            $contenu_page .= '<input type="hidden" name="tobj" value="269" />';
            break;
        case 640:
            $contenu_page .= '<input type="submit" value="Faire du sport (4PA)"  class="test">';
            $contenu_page .= '<input type="hidden" name="tobj" value="640" />';
            break;
        default:
            $contenu_page .= '<input type="submit" value="Tenter de casser cet objet (4PA)"  class="test">';
            $contenu_page .= '<input type="hidden" name="tobj" value="999" />';
            break;
    }

    $contenu_page .= '</form>';
}
// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');
$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE' => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));