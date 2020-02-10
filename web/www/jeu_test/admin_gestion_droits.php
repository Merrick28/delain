<?php
include "blocks/_header_page_jeu.php";


function cree_OuiNon($nom, $valeur, $titre)
{
    $resultat = '<tr><td class="soustitre2">';
    $resultat .= $titre;
    $resultat .= '</td><td>';
    $resultat .= "<select name='$nom'>";
    $sel_o = ($valeur == 'O') ? " selected " : "";
    $sel_n = ($valeur == 'N') ? " selected " : "";
    $resultat .= "<option value='O' $sel_o>Oui</option>";
    $resultat .= "<option value='N' $sel_n>Non</option>";
    $resultat .= "</select></td></tr>";
    return $resultat;
}


$erreur = 0;
$droit_modif = 'dcompt_gere_droits';
include "blocks/_test_droit_modif_generique.php";


$compte = $_REQUEST['compte'];

if ($erreur == 0)
{
    if (!isset($methode))
    {
        $methode = "debut";
    }
    ?>
    <div>
        <strong>Recherche d’un compte</strong>
        <form method="post">
            <input type="hidden" name="methode" value="et2"/>
            Entrez le nom du compte (caractère % pour générique) : <input type="text" name="nom"/>
            <input type="submit" value="Chercher" class='test'>
        </form>
        <hr/>
        ou <strong><a href="?methode=nouveau">créer un nouveau compte admin</a>.</strong>
        <br><br>ou <strong><a href="?methode=lister&filtre_actif=O">lister les comptes admin actifs</a>.</strong>
        <br>ou <strong><a href="?methode=lister&filtre_actif=N">lister les comptes admin inactifs</a>.</strong>
        <br>ou <strong><a href="?methode=phpPgAdmin">lister les comptes phpPgAdmin</a>.</strong>
        <br><br>
    </div>
    <script type="text/javascript" src="../scripts/manip_css.js"></script>
    <script>
        function PermutteEtage() {
            var etages = document.getElementById('choix_etage');
            var resultat = document.getElementById('liste_etages');
            var contenu = resultat.value;

            var etage_choisi = etages.value;
            var option = etages.options[etages.selectedIndex];

            if (etage_choisi != '--') {
                // On enlève l’étage sélectionné
                var nouveauContenu = EnleveValeur(contenu, etage_choisi);

                // On regarde si cela a changé quelque chose
                if (nouveauContenu !== contenu) {
                    // On a enlevé le truc, donc on agit en conséquence.
                    ManipCss.enleveClasse(option, "etage_choisi");
                }
                else {
                    // Ah ah ! C’était un cas d’ajout, en fait...
                    ManipCss.ajouteClasse(option, "etage_choisi");
                    nouveauContenu += (nouveauContenu === '') ? etage_choisi : ',' + etage_choisi;
                }
                resultat.value = nouveauContenu;
                etages.value = '--';
            }
        }

        function EnleveValeur(chaine, valeur) {
            var lesValeurs = chaine.split(",");
            var indexValeur = lesValeurs.indexOf(valeur);
            while (indexValeur > -1) {
                lesValeurs.splice(indexValeur, 1);
                indexValeur = lesValeurs.indexOf(valeur);
            }
            return lesValeurs.join(",");
        }

        function InitialiseEtages() {
            var etages = document.getElementById('choix_etage');
            var resultat = document.getElementById('liste_etages');
            var contenu = resultat.value;
            var lesEtages = contenu.split(",");
            for (var i = 0; i < lesEtages.length; i++) {
                // C’est moche mais j’ai la flemme
                etages.value = lesEtages[i];
                var option = etages.options[etages.selectedIndex];
                ManipCss.ajouteClasse(option, "etage_choisi");
            }
            etages.value = '--';
        }
    </script>
    <style>
        .etage_choisi {
            background-color: #ddd;
        }
    </style>
    <?php switch ($methode)
{
    case "debut":
        break;

    case "phpPgAdmin":
        echo "<p class=\"titre\">Liste des comptes phpPgAdmin </p>";
        echo "<table class=\"soustitre2\ cellspacing=\"2\" cellpadding=\"2\">";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>Nom du compte</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Id</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Droits</strong></p></td>";
        echo "<tr>";

        $req_pers = "SELECT u.usename ,u.usesysid ,
                                   CASE WHEN u.usesuper AND u.usecreatedb THEN 'superuser, create database'::text
                                   WHEN u.usesuper THEN 'superuser'::text
                                   WHEN u.usecreatedb THEN 'create database'::text
                                   ELSE ''::text
                                   END AS attributes
                           FROM pg_catalog.pg_user u ORDER BY usename;";
        $stmt = $pdo->query($req_pers);
        while ($result = $stmt->fetch())
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><strong>" . $result['usename'] . "</strong></td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['usesysid'] . "</td>";
            echo "<td class=\"soustitre2\">" . $result['attributes'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        break;

    case "lister":

        echo "<p class=\"titre\">Liste des comptes admin. " . ($filtre_actif == 'O' ? "actifs" : "inactifs") . " </p>";
        echo "<table class=\"soustitre2\ cellspacing=\"2\" cellpadding=\"2\">";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>Nom du compte</strong></p></td>";
        echo "<td class=\"soustitre2\" style=\"min-width: 60px;\"><p><strong>Der. Connex.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Perso</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Monstres</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Monstre<br>Gen.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Contrôle</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Logs</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Automap</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Cartes<br>AM</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Étages</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Obj<br>Gen.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Droits</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Cartes</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Logs AM</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Enchant.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Potions</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Sondages</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>News</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Anim.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Anim.</strong></p></td>";
        echo "<td class=\"soustitre2\"><p><strong>Magie</strong></p></td>";
        echo "</tr>";

        $req_pers = "select compt_cod, compt_nom, compt_mail, TO_CHAR(compt_der_connex, 'YY-MM-DD HH:MI') compt_der_connex, compt_droit.* from compte join compt_droit on dcompt_compt_cod=compt_cod where compt_actif='$filtre_actif' order by compt_nom";
        $stmt = $pdo->query($req_pers);
        while ($result = $stmt->fetch())
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><span title=\"" . $result['compt_mail'] . "\"><a href=\"admin_gestion_droits.php?methode=et3&vcompte=" . $result['compt_cod'] . "\">" . $result['compt_nom'] . "</a></span></td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['compt_der_connex'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_modif_perso'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_creer_monstre'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_modif_gmon'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_controle'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_acces_log'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_monstre_automap'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_monstre_carte'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_etage'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_objet'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_gere_droits'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_modif_carte'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_controle_admin'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_enchantements'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_potions'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_sondage'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_news'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_animations'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_magie'] . "</td>";
            echo "<td class=\"soustitre2 centrer\">" . $result['dcompt_factions'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        break;

    case "nouveau":
        ?>
        <form method="post">
            <input type="hidden" name="methode" value="nouveau_valide"/>
            <strong>Nom du compte :</strong><input type="text" name="nom"/><select name="type_compte">
                <option value="M">_monstre</option>
                <option value="C">_controle</option>
            </select><br/>
            <strong>Mot de passe :</strong><input type="text" name="mdp"/><br/>
            <strong>Droits</strong><br/>
            <table>
                <?php echo cree_OuiNon('modif_perso', 'N', 'Modification de perso');
                echo cree_OuiNon('creer_monstre', 'N', 'Créer des monstres');
                echo cree_OuiNon('modif_gmon', 'N', 'Modification de monstre générique');
                echo cree_OuiNon('controle', 'N', 'Contrôle');
                echo cree_OuiNon('acces_log', 'N', 'Accès aux logs');
                echo cree_OuiNon('automap_monstre', 'N', 'Automap complète pour les monstres');
                echo cree_OuiNon('carte_monstre', 'N', 'Accès aux cartes pour les admins monstres');
                ?>
                <tr>
                    <td class="soustitre2">Étages accessibles pour les monstres</td>
                    <td><input type="text" name="etage" value="0" id="liste_etages"/>
                        <select name="choix_etage" id='choix_etage' onchange='PermutteEtage();'>
                            <option value="--" selected='selected'>Choisissez un étage</option>
                            <option value="A">Tous les étages</option>
                            <?php echo $html->etage_select(); ?>
                        </select><br/><em>A = tous les étages, sinon séparer les étages par des virgules.<br>
                            Ex : -1,-2,0,-3</em></td>
                </tr>
                <?php echo cree_OuiNon('objet', 'N', 'Modification/création des objets générique');
                echo cree_OuiNon('gere_droits', 'N', 'Gestion des droits');
                echo cree_OuiNon('modif_carte', 'N', 'Modification des cartes');
                echo cree_OuiNon('logs_admin', 'N', 'Accès aux logs des admins/monstres');
                echo cree_OuiNon('enchantements', 'N', 'Gestion des enchantements');
                echo cree_OuiNon('potions', 'N', 'Gestion des potions');
                echo cree_OuiNon('sondage', 'N', 'Gestion des sondages');
                echo cree_OuiNon('news', 'N', 'Lancer les news');
                echo cree_OuiNon('anims', 'N', 'Gérer les animations');
                echo cree_OuiNon('magie', 'N', 'Gérer les paramètres de lancer des sorts');
                echo cree_OuiNon('factions', 'N', 'Gérer les factions');
                ?>
            </table>
            <input type="submit" value="Créer" class='test'/>
        </form>
        <script>InitialiseEtages();</script>
        <?php break;

    case "nouveau_valide":
        // Vérification du nom de compte
        $erreur = false;
        $erreur_msg = "";
        $nom = $_REQUEST['nom'];
        $mdp = $_REQUEST['mdp'];
        $type_compte = $_REQUEST['type_compte'];
        $modif_perso = $_REQUEST['modif_perso'];
        $creer_monstre = $_REQUEST['creer_monstre'];
        $modif_gmon = $_REQUEST['modif_gmon'];
        $controle = $_REQUEST['controle'];
        $acces_log = $_REQUEST['acces_log'];
        $automap_monstre = $_REQUEST['automap_monstre'];
        $etage = $_REQUEST['etage'];
        $gere_droits = $_REQUEST['gere_droits'];
        $modif_carte = $_REQUEST['modif_carte'];
        $carte_monstre = $_REQUEST['carte_monstre'];
        $logs_admin = $_REQUEST['logs_admin'];
        $objet = $_REQUEST['objet'];
        $enchantements = $_REQUEST['enchantements'];
        $potions = $_REQUEST['potions'];
        $sondage = $_REQUEST['sondage'];
        $news = $_REQUEST['news'];
        $anims = $_REQUEST['anims'];
        $magie = $_REQUEST['magie'];
        $factions = $_REQUEST['factions'];

        if ($nom=="")
        {
            $erreur = true;
            $erreur_msg = 'Veuillez donner un nom au compte à créer.';
        }
        if ($mdp=="")
        {
            $erreur = true;
            $erreur_msg = 'Veuillez donner un mot de passe au compte à créer.';
        }
        if ($etage=="")
        {
            $erreur = true;
            $erreur_msg = 'Veuillez indiquer au moins un étage.';
        } else if (strpos($etage, 'A') !== false)
        {
            // Si $etage contient A (tous les étages), on ne garde que 'A' et on vire tous les autres.
            $etage = 'A';
        }
        if (!$erreur)
        {
            if ($type_compte == 'M')
            {
                $compt_monstre = 'O';
                $compt_admin = 'N';
                if (substr($nom, -8) != '_monstre')
                    $nom .= '_monstre';
            } else
            {
                $compt_monstre = 'N';
                $compt_admin = 'O';
                if (substr($nom, -9) != '_controle')
                    $nom .= '_controle';
            }
            $req = "select count(*) as nb from compte where compt_nom = '$nom';";
            $existe = ($db->get_value($req, 'nb') > 0);
            if ($existe)
            {
                $erreur = true;
                $erreur_msg = "Le compte $nom existe déjà. Veuillez choisir un autre nom.";
            }
        }

        if (!$erreur)
        {

            // Création du compte
            $insertion = "insert into compte 
					(compt_nom, compt_password, compt_mail, compt_monstre, compt_admin, compt_validation, compt_actif, compt_dcreat, compt_acc_charte, compt_type_quatrieme)
					values ('$nom', '$mdp', '', '$compt_monstre', '$compt_admin', 0, 'O', now(), 'O', 2)
					RETURNING compt_cod";
            $vcompte = $db->get_value($insertion, 'compt_cod');

            $insertion = "insert into compt_droit
					(dcompt_compt_cod, dcompt_modif_perso, dcompt_modif_gmon, dcompt_controle, dcompt_acces_log, dcompt_monstre_automap, dcompt_etage,
					dcompt_gere_droits, dcompt_modif_carte, dcompt_controle_admin, dcompt_monstre_carte, dcompt_objet, dcompt_enchantements, dcompt_potions,
					dcompt_sondage, dcompt_news, dcompt_animations, dcompt_creer_monstre, dcompt_magie, dcompt_factions)
					values ($vcompte, '$modif_perso', '$modif_gmon', '$controle', '$acces_log', '$automap_monstre', '$etage', 
						'$gere_droits', '$modif_carte', '$logs_admin', '$carte_monstre', '$objet', '$enchantements', '$potions', 
						'$sondage', '$news', '$anims', '$creer_monstre', '$magie', '$factions')";
            $stmt = $pdo->query($insertion);

            echo "Le compte $nom a été correctement créé !";

            $req_pers = "select compt_nom from compte where compt_cod = $compt_cod ";
            $stmt = $pdo->query($req_pers);
            if($result = $stmt->fetch())
            {
                $compt_origine = $result['compt_nom'];
            }
            $log = "
				" . date("d/m/y - H:i") . " $compt_origine (compte $compt_cod) crée le compte $nom, numéro: $vcompte\n";
            $log = $log . "dcompt_modif_perso = '$modif_perso',
					dcompt_creer_monstre = '$creer_monstre',
					dcompt_modif_gmon = '$modif_gmon',
					dcompt_controle = '$controle',
					dcompt_acces_log = '$acces_log',
					dcompt_monstre_automap = '$automap_monstre',
					dcompt_etage = '$etage',
					dcompt_gere_droits = '$gere_droits',
					dcompt_modif_carte = '$modif_carte',
					dcompt_monstre_carte = '$carte_monstre',
					dcompt_controle_admin = '$logs_admin',
					dcompt_objet = '$objet',
					dcompt_enchantements = '$enchantements',
					dcompt_potions = '$potions',
					dcompt_sondage = '$sondage',
					dcompt_news = '$news',
					dcompt_animations = '$anims',
					dcompt_magie = '$magie',
					dcompt_factions = '$factions'
					---------------------------------------------------";
            writelog($log, 'droit_edit');
        }
        if ($erreur)
            echo "<p>Une erreur s’est produite ! $erreur_msg</p>";
        ?>
        <form method="post">
            <input type="hidden" name="methode" value="nouveau_valide"/>
            Entrez le nom du compte (caractère % pour générique) : <input type="text" name="nom"/>
            <input type="submit" value="Chercher" class='test'>
        </form>
        <?php break;

case "et2":
    echo '<div>';
    $nom = strtolower($nom);
    $req = "select compt_nom,compt_cod from compte where lower(compt_nom) like '$nom' ";
    $stmt = $pdo->query($req);
if ($db->nf() == 0)
{
    ?>
    Aucun compte trouvé à ce nom !<br>
    <?php
}
else
{
    ?>
    Sélectionnez le compte sur lequel vous allez changer les droits :<br>
    <form method="post">
        <input type="hidden" name="methode" value="et3">
        <select name="vcompte">
            <?php
            while ($result = $stmt->fetch())
            {
                ?>
                <option value="<?php echo $result['compt_cod']; ?>"><?php echo $result['compt_nom']; ?></option>
                <?php
            }
            ?>
        </select>
        <input type="submit" value="Sélectionner" class='test'>
        <?php
        }
        break;

        case "et3":
            $req_pers = "select compt_nom from compte where compt_cod = $vcompte ";
            $stmt = $pdo->query($req_pers);
            $compt_modif = ($result = $stmt->fetch()) ? $result['compt_nom'] : 'Compte inconnu';

            $req = "select * from compt_droit where dcompt_compt_cod = $vcompte ";
            $stmt = $pdo->query($req);
            if ($db->nf() == 0)
            {
                echo "Aucun droit particulier pour le compte « $compt_modif ».<br>
				<a href='$PHP_SELF?compte=$compte&methode=cree'>Créer des droits ?</a>";
            } else
            {
                $result = $stmt->fetch();
                echo "<p>Modification des droits pour le compte « $compt_modif ».</p>";
                ?>
                <form method="post">
                    <input type="hidden" name="methode" value="et4">
                    <input type="hidden" name="vcompte" value="<?php echo $vcompte; ?>">
                    <table>
                        <?php
                        echo cree_OuiNon('modif_perso', $result['dcompt_modif_perso'], 'Modification de perso');
                        echo cree_OuiNon('creer_monstre', $result['dcompt_creer_monstre'], 'Créer des monstres');
                        echo cree_OuiNon('modif_gmon', $result['dcompt_modif_gmon'], 'Modification de monstre générique');
                        echo cree_OuiNon('controle', $result['dcompt_controle'], 'Contrôle');
                        echo cree_OuiNon('acces_log', $result['dcompt_acces_log'], 'Accès aux logs');
                        echo cree_OuiNon('automap_monstre', $result['dcompt_monstre_automap'], 'Automap complète pour les monstres');
                        echo cree_OuiNon('carte_monstre', $result['dcompt_monstre_carte'], 'Accès aux cartes pour les admins monstres');
                        ?>
                        <tr>
                            <td class="soustitre2">Étages accessibles pour les monstres</td>
                            <td><input type="text" name="etage" value="<?php echo $result['dcompt_etage']; ?>"
                                       id="liste_etages"/>
                                <select name="choix_etage" id='choix_etage' onchange='PermutteEtage();'>
                                    <option value="--" selected='selected'>Choisissez un étage</option>
                                    <option value="A">Tous les étages</option>
                                    <?php echo $html->etage_select(); ?>
                                </select><br><em>A = tous les étages, sinon séparer les étages par des virgules.<br>
                                    Ex : -1,-2,0,-3</em></td>
                        </tr>
                        <?php
                        echo cree_OuiNon('objet', $result['dcompt_objet'], 'Modification/création des objets générique');
                        echo cree_OuiNon('gere_droits', $result['dcompt_gere_droits'], 'Gestion des droits');
                        echo cree_OuiNon('modif_carte', $result['dcompt_modif_carte'], 'Modification des cartes');
                        echo cree_OuiNon('logs_admin', $result['dcompt_controle_admin'], 'Accès aux logs des admins/monstres');
                        echo cree_OuiNon('enchantements', $result['dcompt_enchantements'], 'Gestion des enchantements');
                        echo cree_OuiNon('potions', $result['dcompt_potions'], 'Gestion des potions');
                        echo cree_OuiNon('sondage', $result['dcompt_sondage'], 'Gestion des sondages');
                        echo cree_OuiNon('news', $result['dcompt_news'], 'Lancer les news');
                        echo cree_OuiNon('anims', $result['dcompt_animations'], 'Gérer les animations');
                        echo cree_OuiNon('magie', $result['dcompt_magie'], 'Gérer les paramètres de lancer des sorts');
                        echo cree_OuiNon('factions', $result['dcompt_factions'], 'Gérer les factions');
                        ?>

                    </table>
                    <input type="submit" value="Modifier" class='test centrer'>
                </form>
                <script>InitialiseEtages();</script>
                <?php
            }
            break;

        case "et4":
            $modif_perso = $_REQUEST['modif_perso'];
            $creer_monstre = $_REQUEST['creer_monstre'];
            $modif_gmon = $_REQUEST['modif_gmon'];
            $controle = $_REQUEST['controle'];
            $acces_log = $_REQUEST['acces_log'];
            $automap_monstre = $_REQUEST['automap_monstre'];
            $etage = $_REQUEST['etage'];
            $gere_droits = $_REQUEST['gere_droits'];
            $modif_carte = $_REQUEST['modif_carte'];
            $carte_monstre = $_REQUEST['carte_monstre'];
            $logs_admin = $_REQUEST['logs_admin'];
            $objet = $_REQUEST['objet'];
            $enchantements = $_REQUEST['enchantements'];
            $potions = $_REQUEST['potions'];
            $sondage = $_REQUEST['sondage'];
            $news = $_REQUEST['news'];
            $anims = $_REQUEST['anims'];
            $magie = $_REQUEST['magie'];
            $factions = $_REQUEST['factions'];
            $vcompte = $_REQUEST['vcompte'];

            $req_pers = "select compt_nom from compte where compt_cod = $vcompte ";
            $stmt = $pdo->query($req_pers);
            if($result = $stmt->fetch())
            {
                $compt_modif = $result['compt_nom'];
            }
            $req_pers = "select compt_nom from compte where compt_cod = $compt_cod ";
            $stmt = $pdo->query($req_pers);
            if($result = $stmt->fetch())
            {
                $compt_origine = $result['compt_nom'];
            }
            $log = "
			" . date("d/m/y - H:i") . " $compt_origine (compte $compt_cod) modifie le compte $compt_modif, numero: $compte\n";

            if (strpos($etage, 'A') !== false)
            {
                // Si $etage contient A (tous les étages), on ne garde que 'A' et on vire tous les autres.
                $etage = 'A';
            }

            $req = "update compt_droit set
					dcompt_modif_perso = '$modif_perso',
					dcompt_creer_monstre = '$creer_monstre',
					dcompt_modif_gmon = '$modif_gmon',
					dcompt_controle = '$controle',
					dcompt_acces_log = '$acces_log',
					dcompt_monstre_automap = '$automap_monstre',
					dcompt_etage = '$etage',
					dcompt_gere_droits = '$gere_droits',
					dcompt_modif_carte = '$modif_carte',
					dcompt_monstre_carte = '$carte_monstre',
					dcompt_controle_admin = '$logs_admin',
					dcompt_objet = '$objet',
					dcompt_enchantements = '$enchantements',
					dcompt_potions = '$potions',
					dcompt_sondage = '$sondage',
					dcompt_news = '$news',
					dcompt_animations = '$anims',
					dcompt_magie = '$magie',
					dcompt_factions = '$factions'
				where dcompt_compt_cod = $vcompte";
            $stmt = $pdo->query($req);
            $log = $log . "dcompt_modif_perso = '$modif_perso',
				dcompt_creer_monstre = '$creer_monstre',
				dcompt_modif_gmon = '$modif_gmon',
				dcompt_controle = '$controle',
				dcompt_acces_log = '$acces_log',
				dcompt_monstre_automap = '$automap_monstre',
				dcompt_etage = '$etage',
				dcompt_gere_droits = '$gere_droits',
				dcompt_modif_carte = '$modif_carte',
				dcompt_monstre_carte = '$carte_monstre',
				dcompt_controle_admin = '$logs_admin',
				dcompt_objet = '$objet',
				dcompt_enchantements = '$enchantements',
				dcompt_potions = '$potions',
				dcompt_sondage = '$sondage',
				dcompt_news = '$news',
				dcompt_animations = '$anims',
				dcompt_magie = '$magie',
				dcompt_factions = '$factions'
				---------------------------------------------------";
            writelog($log, 'droit_edit');
            echo "<p>Le compte a bien été modifié !";
            break;

        case "cree":
        $req = "insert into compt_droit (dcompt_compt_cod) values ($vcompte) ";
        $stmt = $pdo->query($req);
        ?>
        <p>Les droits ont bien été créés !<br>
            <a href="<?php echo $PHP_SELF; ?>?methode=et3&compte=<?php echo $compte; ?>">Régler ces droits ?</a>
            <?php
            break;
            }
            ?>
        <p class="centrer"><a href="<?php echo $PHP_SELF; ?>">Retour au début</a>

<?php }
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
//include"../logs/monstre_edit.log";
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

