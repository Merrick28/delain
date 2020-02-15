<style>
    #formulaire {
        padding: 5px;
        margin: 10px 0 0 10px;
        border: 1px dashed #999;
        width: 590px;
    }

    #formulaire fieldset {
        border: 0;
        margin: 0;
        padding: 0;
    }

    #formulaire fieldset label {
        display: block;
    }

    #formulaire legend {
        margin: 0 0 5px;
    }

    #formulaire p {
        display: block;
        padding: 5px 0 0;
        margin: 10px 0 0;
        width: 580px;
    }

    #zoneResultats {
        border: 1px solid #000;
        background-color: #fff;
        display: block;
        overflow: auto;
        margin-left: 200;
        padding: 0;
        position: absolute;
        width: 400px;
    }

    #zoneResultats li {
        background: #fff;
        display: block;
        margin: 0;
        padding: 0;
    }

    #zoneResultats li a {
        display: block;
        padding: 2px;
        text-decoration: none;
    }

    #zoneResultats li a:hover {
        background-color: #ffffc0;
    }

    input {
        margin: 0;
    }
</style>
<?php
$methode = get_request_var('methode', 'entree');
$compte  = new compte;
$compte->charge($compt_cod);
if (!$compte->is_admin())
{
    echo "<p>Erreur ! Vous n'êtes pas admin !";
    exit();
}
switch ($methode)
{
    case "entree":

        include 'sadmin.php';
        /*echo "<form name=\"login2\" method=\"post\" action=\"../jouer.php\">";
        echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
        echo "<p>Entrez directement le numéro de perso : <input type=\"text\" name=\"num_perso\">";
        echo "<input type=\"submit\" value=\"Voir !\" class=\"test\">";
        echo "</form>";
        echo "<p style=\"text-align:center;\"><a href=\"switch.php?methode=tout\">Afficher toute la liste</a></p>";*/
        echo "<form name=\"login2\" method=\"post\" id=\"login2\" action=\"index.php\">";
        echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
        echo "<input type=\"hidden\" name=\"idsessadm\" value=\"$compt_cod\">";
        echo "<p>Entrez directement le numéro de perso : <input type=\"text\" id=\"num_perso\" name=\"num_perso\"> <input type=\"submit\" value=\"Voir !\" class=\"test\">";
        echo '<p>Tapez un nom de perso pour trouver son numéro : 
			<input type="text" name="foo" id="foo" value="" onkeyup="loadData();document.getElementById(\'zoneResultats\').style.visibility = \'hidden\'" />
			<ul id="zoneResultats" style="visibility: hidden;"></ul>';
        echo "";
        echo "</form>";
        break;
    case "tout":
        $admin = 'O';
        echo "<form name=\"login\" method=\"post\" action=\"index.php\">";
        echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
        echo "<input type=\"hidden\" name=\"idsessadm\" value=\"$compt_cod\">";
        $req  =
            "select perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss'),compt_nom from perso,perso_compte,compte where perso_type_perso = 1 and perso_actif = 'O' ";
        $req  = $req . "and perso_cod = pcompt_perso_cod ";
        $req  = $req . "and pcompt_compt_cod = compt_cod ";
        $req  = $req . "order by perso_cod";
        $stmt = $pdo->query($req);
        echo "<select name=\"num_perso\">";
        while ($result = $stmt->fetch())
        {
            echo "<option value=\"" . $result['perso_cod'] . "\">" . $result['perso_cod'] . "-" . $result['perso_nom'] . " [" . $result['compt_nom'] . "]</option>";
        }
        echo "</select>";
        echo "<input type=\"submit\" value=\"Voir !\" class=\"test\">";
        echo "</form>";

        echo "<form name=\"login2\" method=\"post\" action=\"index.php\">";
        echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
        echo "<input type=\"hidden\" name=\"idsessadm\" value=\"$compt_cod\">";
        echo "<p>Ou entrez directement le numéro de perso : <input type=\"text\" name=\"num_perso\">";
        echo "<input type=\"submit\" value=\"Voir !\" class=\"test\">";
        echo "</form>";
        break;
}
?>

<input type="button" class="test" value="Rechercher un perso !"
       onClick="window.open('http://www.jdr-delain.net/rech_perso.php','rech','width=500,height=300');">
<br><br>
<p style="text-align:center"><a href="http://www.jdr-delain.net/jeu_test/change_pass.php">Changer de mot de passe
        !</a><br/>
    <a href="http://www.jdr-delain.net/jeu_test/options_clef_forum.php">Demander une clef d’accès au forum</a><br/></p>
