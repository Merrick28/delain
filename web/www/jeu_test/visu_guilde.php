<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on vérifie qu'on soit dans la guilde visitée 
$req_guilde = "select pguilde_meta_caravane, pguilde_meta_noir, guilde_cod, guilde_nom, rguilde_libelle_rang, pguilde_rang_cod, rguilde_admin, pguilde_message
    from guilde
    inner join guilde_perso on pguilde_guilde_cod = guilde_cod
    inner join guilde_rang on rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod
    where pguilde_perso_cod = $perso_cod 
        and pguilde_valide = 'O' 
        and guilde_cod = $num_guilde";

$stmt = $pdo->query($req_guilde);

$is_guilde = ($stmt->rowCount() != 0);

if ($is_guilde)
{
    $result = $stmt->fetch();

    $perso_meta_noir     = $result['pguilde_meta_noir'];
    $perso_meta_caravane = $result['pguilde_meta_caravane'];
    if ($result['rguilde_admin'] == 'O')
    {
        $is_admin = true;
    } else
    {
        $is_admin = false;
    }
} else
{
    $perso_meta_noir     = 'N';
    $perso_meta_caravane = 'N';
    $is_admin            = false;
}

$req_guilde    =
    "select guilde_meta_caravane,guilde_meta_noir,guilde_cod,guilde_nom,guilde_description from guilde where guilde_cod = $num_guilde ";
$stmt          = $pdo->query($req_guilde);
$result        = $stmt->fetch();
$description   = $result['guilde_description'];
$meta_noir     = $result['guilde_meta_noir'];
$meta_caravane = $result['guilde_meta_caravane'];
$guilde_nom    = $result['guilde_nom'];
echo "<p class=\"titre\">" . $guilde_nom;
if ($param->getparm(74) == 1)
{
    if ($is_guilde)
    {
        if ($meta_noir == 'O')
        {
            echo "<hr>";
            echo "<p>Votre guilde est rattachée en meta guildage à la guilde <strong>envoyés de Salm'o'rv</strong>.<br>";
            if ($perso_meta_noir == 'O')
            {
                echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=n&r=N\">Annuler ce meta-guildage ?</a>";
            } else
            {
                echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=n&r=O\">Se rattacher à ce meta guildage ?</a>";
            }
            echo "<hr>";
        }
        if ($meta_caravane == 'O')
        {
            echo "<hr>";
            echo "<p>Votre guilde est rattachée en meta guildage à la guilde <strong>Corporation marchande du R.A.D.I.S</strong>.<br>";
            if ($perso_meta_caravane == 'O')
            {
                echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=c&r=N\">Annuler ce meta-guildage ?</a>";
            } else
            {
                echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=c&r=O\">Se rattacher à ce meta guildage ?</a>";
            }
            echo "<hr>";
        }
    }
}
// on regarde s'il y a des révolutions en cours
if ($is_guilde)
{
    $req  = "select revguilde_cod from guilde_revolution where revguilde_guilde_cod = $num_guilde ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        echo "<hr>";
        echo "<p style=\"text-align:center;\"><strong>Révolution en cours !</strong><br>";
        echo "<p style=\"text-align:center;\">Pour en savoir plus, <a href=\"guilde_revolution.php\">cliquez ici !</a>";
        echo "<hr>";
    }
}
echo '<table width="100%"><tr><td class="titre"><p class="titre">',
$guilde_nom, '</td></tr></table>';
echo "<p>" . str_replace(chr(127), ";", $description);


$guilde = new guilde;
$guilde->charge($num_guilde);


$stat_guilde = $guilde->get_stats_guilde();
$req_membre  = "select perso_nom,rguilde_libelle_rang,perso_cod,rguilde_admin ";
$req_membre  = $req_membre . "from guilde_perso,perso,guilde_rang ";
$req_membre  = $req_membre . "where pguilde_guilde_cod = $num_guilde ";
$req_membre  = $req_membre . "and pguilde_perso_cod = perso_cod ";
$req_membre  = $req_membre . "and pguilde_valide = 'O' ";
$req_membre  = $req_membre . "and perso_actif != 'N' ";
$req_membre  = $req_membre . "and pguilde_rang_cod = rguilde_rang_cod ";
$req_membre  = $req_membre . "and rguilde_guilde_cod = $num_guilde ";
$req_membre  = $req_membre . "order by rguilde_admin desc,perso_nom ";

$stmt      = $pdo->query($req_membre);
$nb_membre = $stmt->rowCount();
echo("<p>Il y a <strong>$nb_membre</strong> inscrits à cette guilde<br />");

printf(" - Renommee moyenne de la guilde : <strong>%s</strong><br />", $stat_guilde['renommee']);
printf(" - Karma moyen de la guilde : <strong>%s</strong><br />", $stat_guilde['karma']);
$moy_perso_tue   = round(($stat_guilde['joueur_tue'] / $nb_membre), 2);
$moy_monstre_tue = round(($stat_guilde['monstre_tue'] / $nb_membre), 2);
$moy_mort        = round(($stat_guilde['nb_mort'] / $nb_membre), 2);
printf(" - Nombre d'aventuriers tués : <strong>%s</strong> (moyenne par membre: %s)<br />", $stat_guilde['joueur_tue'], $moy_perso_tue);
printf(" - Nombre de monstres tués : <strong>%s</strong> (moyenne par membre: %s)<br />", $stat_guilde['monstre_tue'], $moy_monstre_tue);
printf(" - Nombre de morts : <strong>%s</strong> (moyenne par membre: %s)<br /><br />", $stat_guilde['nb_mort'], $moy_mort);
?>
    <table>
        <form name="visu_perso" method="post" action="visu_desc_perso.php"><input type="hidden" name="visu">
<?php
$tab_admin['O'] = ' - Administrateur';
$tab_admin['N'] = '';
while ($result = $stmt->fetch())
{
    $adm = $result['rguilde_admin'];
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.visu_perso.visu.value=" . $result['perso_cod'] . ";document.visu_perso.submit();\">" . $result['perso_nom'] . "</a></p>";
    echo "</td><td><p>" . $result['rguilde_libelle_rang'];
    echo $tab_admin[$adm];

    echo "</td>";
    if (($is_guilde) && !$is_admin && ($result['rguilde_admin'] == 'O'))
    {
        $req2  = "select revguilde_cod from guilde_revolution where revguilde_cible = " . $result['perso_cod'] . " ";
        $stmt2 = $pdo->query($req2);
        if ($stmt2->rowCount() == 0)
        {
            echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.visu_perso.visu.value=" . $result['perso_cod'] . ";document.visu_perso.action='revolution.php';document.visu_perso.submit();\">Révolution !!</a></td>";
        } else
        {
            echo "<td class=\"soustitre2\"><p>Révolution en cours !</td>";
        }
    } else
    {
        echo "<td></td>";
    }
    if ($is_guilde)
    {
        $req   = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau ";
        $req   = $req . "where dper_perso_cod = " . $result['perso_cod'] . " ";
        $req   = $req . "and dper_dieu_cod = dieu_cod ";
        $req   = $req . "and dper_niveau = dniv_niveau ";
        $req   = $req . "and dniv_dieu_cod = dieu_cod ";
        $req   = $req . "and dniv_niveau >= 1 ";
        $stmt2 = $pdo->query($req);
        if ($stmt2->rowCount() != 0)
        {
            $result2  = $stmt2->fetch();
            $religion = " </strong>(" . $result2['dniv_libelle'] . " de " . $result2['dieu_nom'] . ")<strong> ";
            echo "<td>$religion</td>";
        } else
        {
            echo "<td></td>";
        }
        $requete   = "select etage_cod,etage_libelle  ";
        $requete   = $requete . "from perso_position,positions,etage ";
        $requete   = $requete . "where ppos_perso_cod = " . $result['perso_cod'] . " ";
        $requete   = $requete . "and ppos_pos_cod = pos_cod ";
        $requete   = $requete . "and etage_numero = pos_etage ";
        $stmt2     = $pdo->query($requete);
        $result2   = $stmt2->fetch();
        $lib_etage = $result2['etage_libelle'];
        $etage_cod = $result2['etage_cod'];
        if ($etage_cod == 10 or $etage_cod == 14)
        {
            $lib_etage = "Localisation indéterminée";
        }
        echo "<td>", $lib_etage, "</td>";
    } else
    {
        echo "<td></td><td></td>";
    }
    echo "</tr>";
}
echo "</table>";

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";