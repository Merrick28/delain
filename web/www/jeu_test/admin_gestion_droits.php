<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);


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

//
//Contenu de la div de droite
//
$contenu_page = '';
$erreur = 0;
$req = "select dcompt_gere_droits from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
$compte = $_REQUEST['compte'];

if ($db->nf() == 0)
{
	$droit['gere_droits'] = 'N';
}
else
{
	$db->next_record();
	$droit['gere_droits'] = $db->f("dcompt_gere_droits");
}
if ($droit['gere_droits'] != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !</p>";
	$erreur = 1;
}
if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
?>
	<div>
		<b>Recherche d’un compte</b>
		<form method="post">
			<input type="hidden" name="methode" value="et2" />
			Entrez le nom du compte (caractère % pour générique) : <input type="text" name="nom" />
			<input type="submit" value="Chercher" class='test'>
		</form>
		<hr />
		ou <b><a href="?methode=nouveau">créer un nouveau compte admin</a>.</b>
		<br><br>ou <b><a href="?methode=lister&filtre_actif=O">lister les comptes admin actifs</a>.</b>
		<br>ou <b><a href="?methode=lister&filtre_actif=N">lister les comptes admin inactifs</a>.</b>
		<br>ou <b><a href="?methode=phpPgAdmin">lister les comptes phpPgAdmin</a>.</b>
        <br><br>
	</div>
	<script type="text/javascript" src="../scripts/manip_css.js"></script>
	<script>
		function PermutteEtage () {
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
					ManipCss.enleveClasse (option, "etage_choisi");
				}
				else
				{
					// Ah ah ! C’était un cas d’ajout, en fait...
					ManipCss.ajouteClasse (option, "etage_choisi");
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

		function InitialiseEtages () {
			var etages = document.getElementById('choix_etage');
			var resultat = document.getElementById('liste_etages');
			var contenu = resultat.value;
			var lesEtages = contenu.split(",");
			for (var i = 0; i < lesEtages.length; i++) {
				// C’est moche mais j’ai la flemme
				etages.value = lesEtages[i];
				var option = etages.options[etages.selectedIndex];
				ManipCss.ajouteClasse (option, "etage_choisi");
			}
			etages.value = '--';
		}
	</script>
	<style>
		.etage_choisi { background-color: #ddd; }
	</style>
<?php 	switch($methode)
	{
		case "debut":
		break;

		case "phpPgAdmin":
            echo "<p class=\"titre\">Liste des comptes phpPgAdmin </p>";
            echo "<table class=\"soustitre2\ cellspacing=\"2\" cellpadding=\"2\">";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><b>Nom du compte</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Id</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Droits</b></p></td>";
            echo "<tr>";

            $req_pers = "SELECT u.usename ,u.usesysid ,
                                   CASE WHEN u.usesuper AND u.usecreatedb THEN 'superuser, create database'::text
                                   WHEN u.usesuper THEN 'superuser'::text
                                   WHEN u.usecreatedb THEN 'create database'::text
                                   ELSE ''::text
                                   END AS attributes
                           FROM pg_catalog.pg_user u ORDER BY usename;";
            $db->query($req_pers);
            while($db->next_record()){
                echo "<tr>";
                echo "<td class=\"soustitre2\"><b>".$db->f("usename")."</b></td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("usesysid")."</td>";
                echo "<td class=\"soustitre2\">".$db->f("attributes")."</td>";
                echo "</tr>";
            }
            echo "</table>";
		break;

		case "lister":

            echo "<p class=\"titre\">Liste des comptes admin. ".($filtre_actif=='O' ? "actifs" : "inactifs")." </p>";
            echo "<table class=\"soustitre2\ cellspacing=\"2\" cellpadding=\"2\">";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><b>Nom du compte</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Perso</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Monstres</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Monstre<br>Gen.</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Contrôle</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Logs</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Automap</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Cartes<br>AM</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Étages</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Obj<br>Gen.</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Droits</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Cartes</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Logs AM</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Enchant.</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Potions</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Sondages</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>News</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Anim.</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Anim.</b></p></td>";
            echo "<td class=\"soustitre2\"><p><b>Magie</b></p></td>";
            echo "</tr>";

            $req_pers = "select compt_cod, compt_nom, compt_mail, compt_droit.* from compte join compt_droit on dcompt_compt_cod=compt_cod where compt_actif='$filtre_actif' order by compt_nom";
            $db->query($req_pers);
            while($db->next_record()){
                echo "<tr>";
                echo "<td class=\"soustitre2\"><span title=\"".$db->f("compt_mail")."\"><a href=\"admin_gestion_droits.php?methode=et3&vcompte=".$db->f("compt_cod")."\">".$db->f("compt_nom")."</a></span></td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_modif_perso")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_creer_monstre")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_modif_gmon")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_controle")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_acces_log")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_monstre_automap")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_monstre_carte")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_etage")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_objet")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_gere_droits")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_modif_carte")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_controle_admin")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_enchantements")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_potions")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_sondage")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_news")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_animations")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_magie")."</td>";
                echo "<td style=\"text-align: center;\" class=\"soustitre2\">".$db->f("dcompt_factions")."</td>";
                echo "</tr>";
            }
            echo "</table>";

		break;

		case "nouveau":
		?>
			<form method="post">
				<input type="hidden" name="methode" value="nouveau_valide" />
				<b>Nom du compte :</b><input type="text" name="nom" /><select name="type_compte"><option value="M">_monstre</option><option value="C">_controle</option></select><br />
				<b>Mot de passe :</b><input type="text" name="mdp" /><br />
				<b>Droits</b><br />
				<table>
		<?php 				echo cree_OuiNon('modif_perso', 'N', 'Modification de perso');
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
						</select><br /><i>A = tous les étages, sinon séparer les étages par des virgules.<br>
						Ex : -1,-2,0,-3</i></td>
				</tr>
		<?php 				echo cree_OuiNon('objet', 'N', 'Modification/création des objets générique');
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
				<input type="submit" value="Créer" class='test' />
			</form>
			<script>InitialiseEtages ();</script>
		<?php 		break;
		
		case "nouveau_valide":
			// Vérification du nom de compte
			$erreur = false;
			$erreur_msg = "";
			if (empty($nom))
			{
				$erreur = true;
				$erreur_msg = 'Veuillez donner un nom au compte à créer.';
			}
			if (empty($mdp))
			{
				$erreur = true;
				$erreur_msg = 'Veuillez donner un mot de passe au compte à créer.';
			}
			if (!isset($etage) || $etage === '')
			{
				$erreur = true;
				$erreur_msg = 'Veuillez indiquer au moins un étage.';
			}
			else if (strpos ($etage, 'A') !== false)
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
				}
				else
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
				
				// Application des droits
				$insertion = "insert into compt_droit
					(dcompt_compt_cod, dcompt_modif_perso, dcompt_modif_gmon, dcompt_controle, dcompt_acces_log, dcompt_monstre_automap, dcompt_etage,
					dcompt_gere_droits, dcompt_modif_carte, dcompt_controle_admin, dcompt_monstre_carte, dcompt_objet, dcompt_enchantements, dcompt_potions,
					dcompt_sondage, dcompt_news, dcompt_animations, dcompt_creer_monstre, dcompt_magie, dcompt_factions)
					values ($vcompte, '$modif_perso', '$modif_gmon', '$controle', '$acces_log', '$automap_monstre', '$etage', 
						'$gere_droits', '$modif_carte', '$logs_admin', '$carte_monstre', '$objet', '$enchantements', '$potions', 
						'$sondage', '$news', '$anims', '$creer_monstre', '$magie', '$factions')";
				$db->query($insertion);
				
				echo "Le compte $nom a été correctement créé !";
				
				$req_pers = "select compt_nom from compte where compt_cod = $compt_cod ";
				$db->query($req_pers);
				if($db->next_record()){
					$compt_origine = $db->f("compt_nom");
				}   
				$log = "
				".date("d/m/y - H:i")." $compt_origine (compte $compt_cod) crée le compte $nom, numéro: $vcompte\n";
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
				writelog($log,'droit_edit');
			}
			if ($erreur)
				echo "<p>Une erreur s’est produite ! $erreur_msg</p>";
		?>
			<form method="post">
				<input type="hidden" name="methode" value="nouveau_valide" />
				Entrez le nom du compte (caractère % pour générique) : <input type="text" name="nom" />
				<input type="submit" value="Chercher" class='test'>
			</form>
		<?php 		break;

		case "et2":
			echo '<div>';
			$nom = strtolower($nom);
			$req = "select compt_nom,compt_cod from compte where lower(compt_nom) like '$nom' ";
			$db->query($req);
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
				while($db->next_record())
				{
			?>
				<option value="<?php echo $db->f("compt_cod");?>"><?php echo $db->f("compt_nom");?></option>
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
			$db->query($req_pers);
			$compt_modif = ($db->next_record()) ? $db->f("compt_nom") : 'Compte inconnu';

			$req = "select * from compt_droit where dcompt_compt_cod = $vcompte ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "Aucun droit particulier pour le compte « $compt_modif ».<br>
				<a href='$PHP_SELF?compte=$compte&methode=cree'>Créer des droits ?</a>";
			}
			else
			{
				$db->next_record();
				echo "<p>Modification des droits pour le compte « $compt_modif ».</p>";
		?>
		<form method="post">
			<input type="hidden" name="methode" value="et4">
			<input type="hidden" name="vcompte" value="<?php echo $vcompte;?>">
			<table>
		<?php 
				echo cree_OuiNon('modif_perso', $db->f("dcompt_modif_perso"), 'Modification de perso');
				echo cree_OuiNon('creer_monstre', $db->f("dcompt_creer_monstre"), 'Créer des monstres');
				echo cree_OuiNon('modif_gmon', $db->f("dcompt_modif_gmon"), 'Modification de monstre générique');
				echo cree_OuiNon('controle', $db->f("dcompt_controle"), 'Contrôle');
				echo cree_OuiNon('acces_log', $db->f("dcompt_acces_log"), 'Accès aux logs');
				echo cree_OuiNon('automap_monstre', $db->f("dcompt_monstre_automap"), 'Automap complète pour les monstres');
				echo cree_OuiNon('carte_monstre', $db->f("dcompt_monstre_carte"), 'Accès aux cartes pour les admins monstres');
		?>
				<tr>
					<td class="soustitre2">Étages accessibles pour les monstres</td>
					<td><input type="text" name="etage" value="<?php echo $db->f("dcompt_etage");?>" id="liste_etages"/>
						<select name="choix_etage" id='choix_etage' onchange='PermutteEtage();'>
							<option value="--" selected='selected'>Choisissez un étage</option>
							<option value="A">Tous les étages</option>
						<?php echo $html->etage_select(); ?>
						</select><br><i>A = tous les étages, sinon séparer les étages par des virgules.<br>
						Ex : -1,-2,0,-3</i></td>
				</tr>
		<?php 
				echo cree_OuiNon('objet', $db->f("dcompt_objet"), 'Modification/création des objets générique');
				echo cree_OuiNon('gere_droits', $db->f("dcompt_gere_droits"), 'Gestion des droits');
				echo cree_OuiNon('modif_carte', $db->f("dcompt_modif_carte"), 'Modification des cartes');
				echo cree_OuiNon('logs_admin', $db->f("dcompt_controle_admin"), 'Accès aux logs des admins/monstres');
				echo cree_OuiNon('enchantements', $db->f("dcompt_enchantements"), 'Gestion des enchantements');
				echo cree_OuiNon('potions', $db->f("dcompt_potions"), 'Gestion des potions');
				echo cree_OuiNon('sondage', $db->f("dcompt_sondage"), 'Gestion des sondages');
				echo cree_OuiNon('news', $db->f("dcompt_news"), 'Lancer les news');
				echo cree_OuiNon('anims', $db->f("dcompt_animations"), 'Gérer les animations');
				echo cree_OuiNon('magie', $db->f("dcompt_magie"), 'Gérer les paramètres de lancer des sorts');
				echo cree_OuiNon('factions', $db->f("dcompt_factions"), 'Gérer les factions');
		?>
			
			</table>
			<center><input type="submit" value="Modifier" class='test'></center>
		</form>
		<script>InitialiseEtages ();</script>
		<?php 
			}
		break;

		case "et4":
			$req_pers = "select compt_nom from compte where compt_cod = $vcompte ";
			$db->query($req_pers);
			if($db->next_record()){
				$compt_modif = $db->f("compt_nom");
			}  
			$req_pers = "select compt_nom from compte where compt_cod = $compt_cod ";
			$db->query($req_pers);
			if($db->next_record()){
				$compt_origine = $db->f("compt_nom");
			}
			$log = "
			".date("d/m/y - H:i")." $compt_origine (compte $compt_cod) modifie le compte $compt_modif, numero: $compte\n";

			if (strpos ($etage, 'A') !== false)
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
			$db->query($req);
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
			writelog($log,'droit_edit');
			echo "<p>Le compte a bien été modifié !";
		break;

		case "cree":
			$req = "insert into compt_droit (dcompt_compt_cod) values ($vcompte) ";
			$db->query($req);
		?>
			<p>Les droits ont bien été créés !<br>
			<a href="<?php echo $PHP_SELF;?>?methode=et3&compte=<?php echo $compte;?>">Régler ces droits ?</a>
		<?php 
		break;
	}
?>
<p style="text-align:center"><a href="<?php echo $PHP_SELF;?>">Retour au début</a>

<?php }
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
//include"../logs/monstre_edit.log";
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
