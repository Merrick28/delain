<?php 
// chemins
$t->set_var("g_url",$type_flux.G_URL);
$t->set_var("g_che",G_CHE);
// chemin des images
$t->set_var("img_path",G_IMAGES);
$t->set_var("img_pv",G_IMAGES . 'hp' . $barre_hp . '.gif');
$t->set_var("img_px",G_IMAGES . 'xp' . $barre_xp . '.gif');
$t->set_var("deg_min",$degats_min);
$t->set_var("deg_max",$degats_max);
$t->set_var("armure",$armure);
$t->set_var("x",$position_x);
$t->set_var("y",$position_y);
$t->set_var("etage",$position_etage);
$t->set_var("quete",$quete);
$t->set_var("passage_niveau",$passage_niveau);
$t->set_var("gestion_compte",'<img src="' . G_IMAGES. 'iconeswitch.gif"> <a href="switch.php">Gestion compte</a><br></p><p class="texteMenu">');
$t->set_var("option_monstre",$option_monstre);
$t->set_var("logout",'<img src="' . G_IMAGES . 'deconnection.gif"> <a href="logout.php">Se déconnecter</a>');
$t->set_var("menu_complet",$menu_complet);
$t->set_var("topdroit_visible",$topdroit_visible);
$t->set_var("javascript",$javascript);
$t->set_var("contenu_page",$contenu_page);
$t->set_var("vue_visible",$vue_visible);
$t->set_var("vue_gauche",$vue_gauche);
$t->set_var("vue_droite",$vue_droite);
$t->set_var("vue_bas",$vue_bas);


$t->parse("Sortie","FileRef");
$t->p("Sortie");
