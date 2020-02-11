<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($methode)) {
    $methode = "entree";
}
switch ($methode) {
    case "entree":
        echo "<form name=\"rech\" method=\"post\" action=\"rech_ip.php\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
        echo "<p>Entrez l'IP à rechercher : <input type=\"text\" name=\"ip\">";
        echo "<p><center><input type=\"submit\" class=\"test\" value=\"Rechercher !\">";
        echo "</form>";
        break;
    case "valide":
        $req = "select distinct compt_cod,compt_nom,compt_mail,to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,compt_ip,compt_commentaire, to_char(ip.timestamp,'DD/MM/YYYY hh24:mi:ss') as timestamp from compte
		inner join (
		  select icompt_compt_cod, max (icompt_compt_date) as timestamp from compte_ip
		  where icompt_compt_ip = '$ip'
		  group by icompt_compt_cod
		) ip ON ip.icompt_compt_cod = compte.compt_cod
		 where compt_actif = 'O' order by compt_nom";

        $stmt = $pdo->query($req);
        echo "<p>Recherche sur l'adresse IP $ip";
        echo "<table>";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>Numéro</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>Nom</strong> (cliquez sur le nom pour détails)</td>";
        echo "<td class=\"soustitre2\"><p><strong>Mail</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>Date création</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>Dernière connexion</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>IP</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>Dernière utilisation<br />de l'IP recherchée</strong></td>";
        echo "<td class=\"soustitre2\"><p><strong>Commentaire</strong></td>";
        echo "</tr>";
        while ($result = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $result['compt_cod'] . "</td>";
            echo "<td class=\"soustitre2\"><strong><a href=\"detail_compte.php?compte=" . $result['compt_cod'] . "\">" . $result['compt_nom'] . "</a></strong></td>";
            echo "<td>" . $result['compt_mail'] . "</td>";
            echo "<td class=\"soustitre2\">" . $result['creation'] . "</td>";
            echo "<td>" . $result['connex'] . "</td>";
            echo "<td class=\"soustitre2\">" . $result['compt_ip'] . "</td>";
            echo "<td>" . $result['timestamp'] . "</td>";
            echo "<td class=\"soustitre2\">" . $result['compt_commentaire'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";