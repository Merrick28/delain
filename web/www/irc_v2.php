<?php 
include "classes.php";
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = montre_formulaire_connexion($verif_auth, ob_get_contents());
ob_end_clean();
$t->set_var("IDENT",$ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page .= '<div class="titre">IRC</div>
			Avec un client IRC :<br><br>
			Serveur : irc.netrusk.net<br>
			Canal : #SD<br><br>
			Si vous êtes derrière un firewall passez par <a href="http://www.netrusk.net/pjirc/">http://www.netrusk.net/pjirc/</a> (java) ou <a href="http://www.netrusk.net/cgi-bin/irc.cgi">http://www.netrusk.net/cgi-bin/irc.cgi</a> (cgi), et tapez <b>/join #sd</b> une fois la connection effectuée.<br>
			Les conversations sur le chan sont publiques, sont loguées, et des extraits aléatoires sont diffusées sur le forum.';
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>

