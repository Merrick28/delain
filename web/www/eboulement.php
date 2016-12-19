
<?php 
include "classes.php";
/*$db = new base_delain;
$db->query("insert into log_1_avr (nom) values ('$nom') ");*/
include "template.inc";
$t = new template;
$t->set_file("FileRef","template/classic/general.tpl");
// chemins
$t->set_var("g_url",$type_flux . G_URL);
$t->set_var("g_che",G_CHE);
// pack graphique
$t->set_var("img_path",G_IMAGES);
$contenu_page = '<p class="titre">Le chaos</p>
<a href="http://forum.jdr-delain.net/viewtopic.php?f=8&t=921&p=231285#p231281" target="_blank">Tous les signes étaient bien présents</a>, mais personne n\'a voulu y croire. Tant d\'années de tranquilité, impossible que cela arrive de nouveau. Et pourtant...
<br />
<br />
Tout est arrivé très vite. Une autre secousse, un peu de poussière qui tombe, quelques secondes de répit... et le chaos. Indescriptible, redoutable. Chacun est pris dans le feu du tremblement, malmené, emporté on ne sait où. Les groupes sont séparés, les individus sont seuls maintenant.
Ce n\'est plus de la poussière, mais un mur compact, palpable. On ne distingue pas à plus de quelques centimètres. 
<br />
<br />
L\'air manque, les pierres tombent du plafond, et pourtant cela continue, encore. Que trouverez vous quand le tremblement de terre aura cessé ?
';
$t->set_var("contenu_page",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
