<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 16;
$nom_lieu  = 'un escalier';

include "blocks/_test_lieu.php";

$perso = new perso;
$perso = $verif_connexion->perso;


$req      = "select perso_type_perso, perso_pa from perso where perso_cod = $perso_cod ";
$stmt     = $pdo->query($req);
$result   = $stmt->fetch();
$pa_perso = $perso->perso_pa;
if ($perso->perso_type_perso == 3)
{
    echo "<p>Un familier ne peut pas prendre un grand escalier tout seul !</p>";
    $erreur = 1;
}
if ($perso->perso_type_perso == 2)
{
    echo "<p>Évitons les massacres ! Un monstre de ce niveau ne devrait pas prendre un grand escalier, sauf intervention de Monstres & Cie...</p>";
    $erreur = 1;
}
if ($perso->compte_objet(86) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
    $erreur = 1;
}
if ($perso->compte_objet(87) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
    $erreur = 1;
}
if ($perso->compte_objet(88) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
    $erreur = 1;
}
if ($pa_perso < $param->getparm(43))
{
    echo("<p>Vous n’avez pas assez de PA !!!!</p>");
    $erreur = 1;
}
if ($perso->is_locked())
{
    $req    = "select fuite($perso_cod) as texte_fuite";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    $fuite  = explode('#', $result['texte_fuite']);
    if ($fuite[0] != 1)
    {
        $erreur = 0;
    } else
    {
        $erreur = 1;
    }
    echo $fuite[1];
}

if ($erreur == 0)
{
    $pa   = "update perso set perso_pa = max(perso_pa - " . $param->getparm(43) . ",0) where perso_cod = $perso_cod ";
    $stmt = $pdo->query($pa);

    $req_pos = "select ppos_pos_cod,pos_x,pos_y,pos_etage 
		from perso_position,positions 
		where ppos_perso_cod = $perso_cod 
			and ppos_pos_cod = pos_cod ";
    $stmt    = $pdo->query($req_pos);
    $result  = $stmt->fetch();

    $pos_actuelle   = $result['ppos_pos_cod'];
    $position_x     = $result['pos_x'];
    $position_y     = $result['pos_y'];
    $position_etage = $result['pos_etage'];

    $req    = "select lieu_dest from lieu,lieu_position
		where lpos_pos_cod = $pos_actuelle
			and lpos_lieu_cod = lieu_cod ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    $n_pos  = $result['lieu_dest'];

    $req     = "select pos_etage,pos_x,pos_y from positions where pos_cod = $n_pos ";
    $stmt    = $pdo->query($req);
    $result  = $stmt->fetch();
    $n_etage = $result['pos_etage'];
    $n_x     = $result['pos_x'];
    $n_y     = $result['pos_y'];


    $req  = "select update_etage_visite($perso_cod,$n_etage)";
    $stmt = $pdo->query($req);


    $deplace = "update perso_position set ppos_pos_cod = $n_pos where ppos_perso_cod = $perso_cod ";
    $stmt    = $pdo->query($deplace);


    // on cherche le lieu cod
    $req    = "select lpos_lieu_cod from lieu_position where lpos_pos_cod = $n_pos ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    $lieu   = $result['lpos_lieu_cod'];

    // On rajoute un évènement
    $texte =
        'Déplacement de ' . $position_x . ',' . $position_y . ',' . $position_etage . ' vers ' . $n_x . ',' . $n_y . ',' . $n_etage;
    $req   = "select insere_evenement($perso_cod, $perso_cod, 33, '$texte', 'O', null)";
    $stmt  = $pdo->query($req);

    // on active pour le retour
    $req  = "select pge_perso_cod from perso_grand_escalier 
		where pge_perso_cod = $perso_cod	
			and pge_lieu_cod = $lieu ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() == 0)
    {
        $req  = "insert into perso_grand_escalier (pge_perso_cod,pge_lieu_cod) values ($perso_cod,$lieu) ";
        $stmt = $pdo->query($req);
    }

    $req    =
        "select etage_libelle,etage_description from etage,positions where pos_cod = $n_pos and pos_etage = etage_numero ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    echo "<p>Vous arrivez dans le lieu : <strong>" . $result['etage_libelle'] . "</strong></p><br>";
    echo "<p><em>" . $result['etage_description'] . "</em></p>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";