<?php
include "blocks/_header_page_jeu.php";
ob_start();


$droit_modif = 'dcompt_controle';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";


if ($erreur != 0)
{
    echo "<p>Erreur ! Vous n'êtes pas admin !";
    exit();
}
?>
    <p><a href="rech_ip.php">Recherche sur IP</a>
    <p><a href="rech_nom.php">Recherche sur nom</a>
<?php
$methode2 = get_request_var('methode2', 'entree');
switch ($methode2)
{
    case "entree":
        ?>
        <p>Visualisation des temps cumulés de <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=sit">déclarations de
                sitting
                sur 15 jours</a>
        <p>Visualisation des temps cumulés des <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=sitteur">sitteurs
        sur 15
        jours</a>
        <?php
        break;

    case "sit":
        echo '<p>Visualisation des temps cumulés des <a  href="rech_compte.php?methode2=sitteur">sitteurs sur 15 jours</a>';
        $req = "select compt_nom,csit_compte_sitte,count(csit_compte_sitte) as compteur,sum(csit_dfin - csit_ddeb) as temps_cumule from compte_sitting,compte 
										where csit_compte_sitte = compt_cod 
										and csit_dfin > (now() - '15 days'::interval)
										group by csit_compte_sitte,compt_nom";
        require "blocks/_rech_compte.php";
        echo '
					<form name="fsort" method="post" action="rech_compte.php?methode2=sit">
					<input type="hidden" name="sort">
					<input type="hidden" name="sens" value="$sens">
					<input type="hidden" name="autresens">
					<input type="hidden" name="visu">
				<table><tr><td>Nom du sitté</td>
				<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'compteur\';document.fsort.sens.value=\'' . $sens . '\';document.fsort.submit();">
					<?
					if ($sort == \'compteur\')
					{?>
					<strong>
					<?}?>
					Nombre de sitting réalisés sur ce compte
					<?
					if ($sort == \'compteur\')
					{?>
					</strong>
					<?}?>
					</a></td>
				<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'duree\';document.fsort.sens.value=\'' . $sens . '\';document.fsort.submit();">
					<?
					if ($sort == \'duree\')
					{?>
					<strong>
					<?}?>
					Durée cumulée de sitting depuis les 15 derniers jours
					<?
					if ($sort == \'duree\')
					{?>
					</strong>
					<?}?>
					</a></td>';
        while ($result = $stmt->fetch())
        {
            $sit_nom = $result['compt_nom'];
            $nombre = $result['compteur'];
            $duree_cumul = $result['temps_cumule'];
            $sitte = $result['csit_compte_sitte'];
            echo "<tr><td><a href=\"rech_compte.php?methode2=detail_sit&sit=$sitte&sit_nom=$sit_nom\">" . $sit_nom . "</a></td><td>" . $nombre . "</td><td>" . $duree_cumul . "</td></tr>";
        }
        echo "</table>";
        break;


    case "sitteur":
        echo '<p>Visualisation des temps cumulés de <a  href="rech_compte.php?methode2=sit">déclarations de sitting sur 15 jours</a>';

        $req = "select compt_nom,csit_compte_sitteur,count(csit_compte_sitteur) as compteur,sum(csit_dfin - csit_ddeb) as temps_cumule from compte_sitting,compte 
										where csit_compte_sitteur = compt_cod 
										and csit_dfin > (now() - '15 days'::interval)
										group by csit_compte_sitteur,compt_nom";
        require "blocks/_rech_compte.php";
        echo '
					<form name="fsort" method="post" action="rech_compte.php?methode2=sitteur">
					<input type="hidden" name="sort">
					<input type="hidden" name="sens" value="$sens">
					<input type="hidden" name="autresens">
					<input type="hidden" name="visu">
				<table><tr><td>Nom du sitteur</td>
				<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'compteur\';document.fsort.sens.value=\'' . $sens . '\';document.fsort.submit();">
					<?
					if ($sort == \'compteur\')
					{?>
					<strong>
					<?}?>
					Nombre de sitting réalisés par le sitteur
					<?
					if ($sort == \'compteur\')
					{?>
					</strong>
					<?}?>
					</a></td>
				<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'duree\';document.fsort.sens.value=\'' . $sens . '\';document.fsort.submit();">
					<?
					if ($sort == \'duree\')
					{?>
					<strong>
					<?}?>
					Durée cumulée de sitting réalisés par le sitteur depuis les 15 derniers jours
					<?
					if ($sort == \'duree\')
					{?>
					</strong>
					<?}?>
					</a></td>';
        while ($result = $stmt->fetch())
        {
            $sit_nom = $result['compt_nom'];
            $nombre = $result['compteur'];
            $duree_cumul = $result['temps_cumule'];
            $sitteur = $result['csit_compte_sitteur'];
            echo "<tr><td><a href=\"rech_compte.php?methode2=detail_sitteur&sitteur=$sitteur&sit_nom=$sit_nom\">" . $sit_nom . "</a></td><td>" . $nombre . "</td><td>" . $duree_cumul . "</td></tr>";
        }
        echo "</table>";
        break;

    case "detail_sit":
        ?>
        <p>Visualisation des temps cumulés de <a href="rech_compte.php?methode2=sit">déclarations de sitting sur 15
                jours</a>
            <br><br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=debut">Retour</a><br>
            <br>
        <hr><br>Liste des sittings déclarés par <strong><?php echo $sit_nom ?></strong>, présent et futurs<br>
        <table>
            <tr>
                <td><strong>Compte Sitteur</strong></td>
                <td><strong>Date de début</strong></td>
                <td><strong>Date de fin</strong></td>
            </tr>
            <?php
            $req  = "select to_char(csit_ddeb,'DD-MM-YYYY / HH24:mi') as date_debut,to_char(csit_dfin,'DD-MM-YYYY / HH24:mi') as date_fin,csit_compte_sitteur from compte_sitting
										where csit_compte_sitte = $sit
										and csit_dfin > (now() - '15 days'::interval)";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $date_deb = $result['date_debut'];
                $date_fin = $result['date_fin'];
                $compte_sitteur = $result['csit_compte_sitteur'];
                $req = "select compt_nom from compte
										where compt_cod = $compte_sitteur";
                $stmt2 = $pdo->query($req);
                $result2 = $stmt2->fetch();
                $compte_sitteur_nom = $result2['compt_nom'];
                ?>
                <tr>
                    <td class="soustitre2"><a
                                href="detail_compte.php?compte=<?php echo $compte_sitteur ?>"><?php echo $compte_sitteur_nom; ?></a>
                    </td>
                    <td class="soustitre2"><?php echo $date_deb; ?></td>
                    <td class="soustitre2"><?php echo $date_fin; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        break;

    case "detail_sitteur":
        ?>
        <p>Visualisation des temps cumulés <a href="rech_compte.php?methode2=sit">des sitteurs sur 15 jours</a>
            <br><br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=debut">Retour</a><br>
            <br>
        <hr><br>Liste des sittings réalisés par <strong><?php echo $sit_nom ?></strong>, présent et futurs<br>
        <table>
            <tr>
                <td><strong>Compte Sitté</strong></td>
                <td><strong>Date de début</strong></td>
                <td><strong>Date de fin</strong></td>
            </tr>
            <?php
            $req  = "select to_char(csit_ddeb,'DD-MM-YYYY / HH24:mi') as date_debut,to_char(csit_dfin,'DD-MM-YYYY / HH24:mi') as date_fin,csit_compte_sitte from compte_sitting
										where csit_compte_sitteur = $sitteur
										and csit_dfin > (now() - '15 days'::interval)";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $date_deb = $result['date_debut'];
                $date_fin = $result['date_fin'];
                $compte_sitte = $result['csit_compte_sitte'];
                $req = "select compt_nom from compte
										where compt_cod = $compte_sitte";
                $stmt2 = $pdo->query($req);
                $result2 = $stmt2->fetch();
                $compte_sitte_nom = $result2['compt_nom'];
                ?>
                <tr>
                    <td class="soustitre2"><a
                                href="detail_compte.php?compte=<?php echo $compte_sitte ?>"><?php echo $compte_sitte_nom; ?></a>
                    </td>
                    <td class="soustitre2"><?php echo $date_deb; ?></td>
                    <td class="soustitre2"><?php echo $date_fin; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        break;

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";