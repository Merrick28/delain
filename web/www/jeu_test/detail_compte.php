<?php
include "blocks/_header_page_jeu.php";

define('APPEL', 1);

ob_start();
?>
    <script language="javascript">
        ns4 = document.layers;
        ie = document.all;
        ns6 = document.getElementById && !document.all;

        function montre(id) {
            objet = document.getElementById(id);
            objet.style.display = (objet.style.display == "" ? "none" : "");
        }
    </script>
<?php

// Renvoie un texte indiquant les qualités essentielles du personnage
function tag_perso($pnj, $type_perso, $actif)
{
    $txt_perso = '';
    $txt_pnj = '';
    switch ($pnj)
    {
        case 0:
            $txt_pnj = 'standard';
            break;
        case 1:
            $txt_pnj = 'pnj';
            break;
        case 2:
            $txt_pnj = '4ème perso';
            break;
        default:
            $txt_pnj = 'inconnu';
            break;
    }
    switch ($type_perso)
    {
        case 1:
            $txt_perso = 'aventurier';
            break;
        case 2:
            $txt_perso = 'monstre';
            break;
        case 3:
            $txt_perso = 'familier';
            break;
        default:
            $txt_perso = 'inconnu';
            break;
    }
    $perso_actif = ($actif == 'N') ? ' - inactif' : '';
    return "[$txt_perso - $txt_pnj" . "$perso_actif]";
}

if (!isset($vcompte))
{
    $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);

    // Compte trouvé
    if($result = $stmt->fetch())
        $vcompte = $result['pcompt_compt_cod'];
    // Compte non trouvé ; peut-être un familier ?
    else
    {
        $req = "select pcompt_compt_cod from perso_compte
			inner join perso_familier on pfam_perso_cod = pcompt_perso_cod
			where pfam_familier_cod = $perso_cod ";
        $stmt = $pdo->query($req);
        if($result = $stmt->fetch())
            $vcompte = $result['pcompt_compt_cod'];
        else
        {
            $vcompte = -1;
        }
    }
}

$droit_modif = 'dcompt_controle';

include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0 && $vcompte != -1)
{
    $methode2 = $_REQUEST['methode2'];
    switch ($methode2)
    {
        case 'mise_a_jour'://MAJ du compte lié associé à un compte
            $req = "update compte set compt_compte_lie = (select compt_cod from compte where compt_nom = '$foo') where compt_cod = $vcompte";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $req = "select compt_compte_lie from compte
				where compt_cod = $vcompte ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $vcompte_lie = $result['compt_compte_lie'];
            break;
    }
    $nom_pers = $perso->perso_nom;

    // Détails du compte
    $req = "select compt_admin, compt_nom, compt_password, compt_nom, compt_mail, to_char(compt_dcreat, 'DD/MM/YYYY hh24:mi:ss') as creation,
			to_char(compt_der_connex, 'DD/MM/YYYY hh24:mi:ss') as connex, compt_ip, compt_commentaire, compt_confiance,
			compt_hibernation, to_char(compt_dfin_hiber, 'DD/MM/YYYY hh24:mi:ss') as fin_hiber, compt_compte_lie
		from compte
		where compt_cod = $vcompte ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $vcompte_lie = $result['compt_compte_lie'];
    echo "<p class=\"titre\">Détail du compte " . $result['compt_nom'] . "</p>";
    echo "<table cellspacing=\"2\" cellpadding=\"2\">";
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p><strong>Nom du compte</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Password</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Adresse mail</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Date de création</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Dernière connexion</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Dernière IP</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Hibernation ?</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Date de fin d’hibernation</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Commentaire <a href=\"modif_detail_compte.php?compte=$vcompte\">(Ajouter ?)</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class=\"soustitre2\"><p><strong>" . $result['compt_nom'] . "</strong></td>";
    if (($vcompte != 4) && ($vcompte != 353))
    {
        if ($result['compt_admin'] == 'O')
        {
            echo "<td class=\"soustitre2\"><p>Mot de passe contrôle....</td>";
        } else
        {
            echo "<td class=\"soustitre2\"><p>" . $result['compt_password'] . "</td>";
        }
    } else
    {
        echo "<td><p>Vous pensez sérieusement pas voir le mot de passe de ce compte, non ? ;-) </td>";
    }
    echo "<td class=\"soustitre2\"><p>" . $result['compt_mail'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['creation'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['connex'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['compt_ip'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['compt_hibernation'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['fin_hiber'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['compt_commentaire'] . "</td>";
    echo "</tr>";

    echo "</table>";
    $compt_conf = $result['compt_confiance'];

    // Détails des persos
    $req = "select perso_cod, perso_nom, perso_px, perso_niveau, to_char(perso_dcreat, ' DD/MM/YYYY hh24:mi:ss') as crea, perso_type_perso, perso_pnj, perso_actif,
			pos_x, pos_y, pos_etage, etage_libelle
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		inner join etage on etage_numero = pos_etage
		where perso_cod in (select pcompt_perso_cod
		from perso_compte where pcompt_compt_cod = $vcompte) order by perso_cod";
    $stmt = $pdo->query($req);
    echo "<table>";
    echo "<tr><td colspan=\"6\"><p class=\"titre\">Persos de ce compte : </p></td></tr>";
    while ($result = $stmt->fetch())
    {
        $visu_pos = true;
        require "blocks/_detail_compte.php";
    }
    echo "</table>";

    // Détail des familiers
    $req = "select fam.perso_cod, fam.perso_nom, fam.perso_px, fam.perso_niveau, 
		to_char(fam.perso_dcreat, ' DD/MM/YYYY hh24:mi:ss') as crea,
		fam.perso_type_perso, fam.perso_pnj, fam.perso_actif
		from perso as fam
		inner join perso_familier on pfam_familier_cod = fam.perso_cod
		inner join perso per on per.perso_cod = pfam_perso_cod
		inner join perso_compte on pcompt_perso_cod = per.perso_cod
		where pcompt_compt_cod = $vcompte
		order by fam.perso_cod";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() > 0)
    {
        echo "<table>";
        echo "<tr><td colspan=\"6\"><p class=\"titre\">Familiers de ce compte : </p></td></tr>";
        while ($result = $stmt->fetch())
        {
            $visu_pos = false;
            require "blocks/_detail_compte.php";
        }
        echo "</table>";
    }

    // historique des sittings
    $req = "select sitteur.compt_nom as sitteur_nom, csit_compte_sitte, sitte.compt_nom as sitte_nom, csit_compte_sitteur, 
		to_char(csit_ddeb, 'DD/MM/YYYY hh24:mi:ss') as ddeb, to_char(csit_dfin, 'DD/MM/YYYY hh24:mi:ss') as dfin,
		csit_dfin - csit_ddeb as duree
		from compte_sitting
		inner join compte sitteur on sitteur.compt_cod = csit_compte_sitteur
		inner join compte sitte on sitte.compt_cod = csit_compte_sitte
		where $vcompte IN (csit_compte_sitteur, csit_compte_sitte)
		order by csit_ddeb desc limit 50";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() > 0)
    {
        echo '<p class="titre">Historique des 50 derniers sittings :
			<a class="titre" href="javascript:montre(\'sitting\')">(Montrer/Cacher)</a></p>';
        echo '<table id="sitting" style="display:none">';
        echo "<tr><td class=\"soustitre2\">Compte sitteur</td>
				<td class=\"soustitre2\">Compte sitté</td>
				<td class=\"soustitre2\">Date de début</td>
				<td class=\"soustitre2\">Date de fin</td>
				<td class=\"soustitre2\">Durée</td></tr>";
        while ($result = $stmt->fetch())
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><a href='?compte=" . $result['csit_compte_sitteur'] . "'>" . $result['sitteur_nom'] . "</a></p></td>";
            echo "<td class=\"soustitre2\"><p><a href='?compte=" . $result['csit_compte_sitte'] . "'>" . $result['sitte_nom'] . "</a></p></td>";
            echo "<td class=\"soustitre2\"><p>" . $result['ddeb'] . "</p></td>";
            echo "<td class=\"soustitre2\"><p>" . $result['dfin'] . "</p></td>";
            echo "<td class=\"soustitre2\"><p>" . $result['duree'] . "</p></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // IP utilisées
    $req = 'select icompt_compt_ip , to_char(icompt_compt_date , ';
    $req .= '\'YYYY-MM-DD hh24:mi:ss\') as timestamp from compte_ip ';
    $req .= 'where icompt_compt_cod = ' . $vcompte;
    $req .= ' order by timestamp desc ';
    $req .= 'limit 50';
    $stmt = $pdo->query($req);
    ?>
    <p class="titre">Dernières IP utilisées :
        <a class="titre" href="javascript:montre('ip')">(Montrer/Cacher)</a>
    </p>
    <table id="ip" style="display:none">
        <?php while ($result = $stmt->fetch())
        { ?>
            <tr>
                <td class="soustitre2"><?php echo $result['timestamp']; ?></td>
                <td class="soustitre2"><?php echo $result['icompt_compt_ip']; ?></td>
            </tr>
        <?php } ?>
    </table>
    <?php
    if ($compt_conf != 'O')
    {
        echo "<p><a href=\"multi_trace2.php?v_compte=$vcompte\">Cherche dans les multi ? </a>";
    }

    if ($compt_conf == 'N')
    {
        echo "<p><a href=\"compt_confiance.php?etat=N&compte=$vcompte\">Passer ce compte en <strong>confiant</strong> ?</a> (ne plus le faire apparaître dans les multi)";
        echo "<p><a href=\"compt_confiance.php?etat=S&compte=$vcompte\">Passer ce compte en <strong>surveillé</strong> ?</a> (Alertes en fins d'hibernation)";
    } else
    {
        echo "<p><a href=\"compt_confiance.php?etat=O&compte=$vcompte\">Passer ce compte en NON confiant ?</a> (le faire apparaitre dans les multi)";
    }

    echo "<p><a href=\"trc_connex.php?compte=$vcompte\">Logs de connexion</a><br><br>";
    include 'sadmin.php';
    if ($vcompte_lie != null)
    {
        $req2 = "select compt_nom from compte
			where compt_cod = $vcompte_lie ";
        $stmt = $pdo->query($req2);
        $result = $stmt->fetch();
        $vcompte_lie_nom = $result['compt_nom'];
    } else
    {
        $vcompte_lie_nom = "";
    }
    echo '<form name="login2" method="post" action="' . $_SERVER['PHP_SELF'] . '">
		<input type="hidden" name="methode2" value="mise_a_jour">
		<br>Compte lié : <strong>' . $vcompte_lie_nom . '</strong>     <input type="text" name="foo" id="foo" value="' . $vcompte_lie . '" onkeyup="loadData2();document.getElementById(\'zoneResultats\').style.visibility = \'hidden\'" />          <input type="submit" name="maj" value="Mettre à jour">   <em>indiquer le nouveau compte lié si nécessaire, avec vérification assistée</em>
		<ul id="zoneResultats" style="visibility: hidden;"></ul>

		</form>';
    echo "<strong><a href=\"invalide_compte.php?compte=$vcompte\" class='centrer'>INVALIDER CE COMPTE ???</a><br><br>";
} else if ($vcompte == -1)
{
    echo "<p>Ce personnage n’est relié à aucun compte</p>";
} else
{
    echo "<p>Erreur ! Vous n’êtes pas administrateur !</p>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
