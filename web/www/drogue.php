
<?php 
include "classes.php";
/*$db = new base_delain;
$db->query("insert into log_1_avr (nom) values ('$nom') ");*/
include "template.inc";
$t = new template;
$t->set_file("FileRef","template/classic/general.tpl");
// chemins
$t->set_var("g_url",G_URL);
$t->set_var("g_che",G_CHE);
// pack graphique
$t->set_var("img_path",G_IMAGES);
$contenu_page = '<p class="titre">Un drogue....</p>
Père de famille incapable de s\'empêcher de surfer sur le Net tous les jours, amateur de jeux vidéo dépensant sans compter pour s\'équiper de matériel informatique...<br />
La dépendance (addiction) à Internet ou aux jeux vidéo se manifeste d\'autant de façons qu\'il y a d\'utilisateurs.<br />
<br />
Ces exemples sont un peu caricaturaux, mais il est tout à fait possible de devenir accro sans s\'en rendre compte. C\'est d\'autant plus fréquent qu\'il est difficile d\'aborder le sujet avec un médecin ou un thérapeute. Nées de l\'univers ludique, ces dépendances ne sont pourtant pas exemptes de danger.<br />
Des couples se séparent à cause du temps ou de l\'argent engloutis. Pour certains jeunes, cette passion, poussée à l\'extrême, prime sur la scolarité, la vie familiale et sociale.<br />
<br />
Le phénomène est étudié très sérieusement. L\'hôpital Marmottan, à Paris, lieu réputé pour la prise en charge des toxicomanes, s\'y intéresse depuis plusieurs années. "L\'addiction à Internet ou aux jeux vidéo est dite silencieuse, car les personnes qu\'elle affecte n\'ont pas de comportement spécifique. Le public ne dispose pas des informations utiles pour prendre conscience du problème. Nous avons donc ouvert une consultation, anonyme et gratuite, dans notre hôpital", explique Michel Hautefeuille, praticien hospitalier, chargé du pôle Recherche et formation à l hôpital Marmotta<br />
<br />
Le traitement de cette dépendance en tant que pathologie vise un nombre important de personnes.<br />
"Selon des enquêtes américaines  effectuées pour le compte de compagnies d\'assurances  le jeu pathologique, reconnu officiellement comme une maladie par les Etats-Unis, et dont découlent les addictions à Internet ou aux jeux vidéo, concerne 1 à 2% de la population. C\'est-à-dire 600 000 à 1,2 million de personnes en France. Soit deux à quatre fois plus que de toxicomanes", précise Marc Valleur, chef de service à l\'hôpital Marmottan.<br />
<br />
<p class="titre">Il existe, schématiquement, deux manières de devenir accro.</p>
<br />
"D\'une part, une dépendance sans passion, équivalente à l\'accoutumance à la cigarette.<br />
Cette activité, généralement accessoire, peut devenir gênante, jusqu\'à nuire aux activités professionnelles et sociales, voire coûter de l\'argent. D\'autre part, une dépendance plus envahissante, celle, par exemple, aux jeux de rôle en ligne. Les expériences "initiatiques" s\'avèrent si exaltantes qu\'elles apparaissent comme une révélation mystique. On pénètre dans un autre monde en éprouvant un authentique choc émotionnel : le coup de foudre toxicomaniaque".<br />
<br />
<p class="titre">L\'exaltation étant recherchée de tous, comment faire pour qu\'elle ne devienne pas une dépendance ?</p>
"On parle d\'addiction quand les personnes concernées se rendent compte que leur pratique empêche ou perturbe des moments fondamentaux de leur vie affective et sociale, qu\'ils essaient de mettre fin au problème et quils n\'y parviennent pas".<br />
<br />
"La passion est un choix permanent, intensif, positif et constructif, permettant l\'élaboration de la personnalité. L\'addiction est une espèce de passion qui aurait perdu de son sens, renvoyant davantage à la routine. Elle est caractérisée par la centration, c\'est-à-dire par le fait qu\'un élément est devenu l\'organisateur de la vie entière. Un héroïnomane passe plus de temps à chercher le produit qu\'à le consommer".<br />
<br />
<p class="titre">Une passion qui envahit tout</p>
<br />
Pour les plus mordus du jeu ou du Net, il existe généralement deux phases de prise de conscience. La première, la contemplation, est similaire à ce que ressentent les fumeurs, qui pensent arrêter un jour, mais pas dans l\'immédiat... La deuxième, la décision, est celle où l\'on tente de mettre fin aux conséquences nocives de la dépendance.<br />
<br />
Cependant, il n\'y a pas de critère objectif et quantitatif devant une addiction sans produit, comme celle qui lie le consommateur au Web ou à la console de jeux. On peut ainsi très bien passer cinq heures d\'affilée devant son ordinateur ou à jouer à un jeu passionnant sans être fatalement accro.<br />
<br />
<p class="titre">Un indicateur : les résultats scolaires</p>
<br />
Il est faux de considérer que, pour un enfant, jouer sur l\'ordinateur avec des amis favorise l\'addiction. Car il existe une autorégulation de groupe. Les jeunes s\'échangent plus de conseils que ne limaginent les parents, faisant office entre eux de garde-fous.<br />
<br />
Mieux vaut surveiller un désintérêt général, des troubles du sommeil, des résultats scolaires en baisse... Un enfant ne doit pas, au nom du jeu, se priver des moments de plaisir partagés dans la famille, comme le repas du soir ou la sortie du dimanche. De tels manquements constituent un signal de l\'empiétement du jeu sur la vie privée.<br />
<br />
<p class="titre">Bien différencier jeu et réalité</p>
<br />
Pour ne pas confondre réel et virtuel, et lutter contre l\'enfermement dans une conduite addictive, le joueur a besoin de pas rester seulIl doit pouvoir parler à son entourage de ses connexions au Net ou de son jeu. Son mutisme est un signe que lordinateur ne représente plus simplement un sas de décompression entre le travail et la famille.<br />
<br />
Dans un couple, par exemple, la dépendance trahira une mauvaise communication du conjoint, qui ne vit plus qu\'à travers le virtuel. "La dépendance est, au départ, une réaction face à l\'anxiété et à la solitude ; elle a un effet apaisant. Chez une personne "malade", il y a un déplacement du désir vers l\'objet de dépendance. Les accros aux jeux vidéo éprouvent des sensations orgasmiques. Certains dépendants d\'Internet pratiquent une sexualité sans objet sexuel sur les sites pornos", explique ainsi le Dr Dan Véléa, responsable de la partie toxicomanie du site Internet de la Fédération française de psychiatrie.<br />
<br />
Si le jeu devient un remède à un sentiment d\'insécurité existentielle, la personne dépendante a un réel besoin d\'aide. Elle peut la trouver auprès de son entourage, bien sûr, qui doit rester vigilant et contrôler l\'utilisation du Net ou des jeux vidéo. Mais aussi, et surtout, auprès de thérapeutes. Ainsi, au centre Marmottan, on oriente les personnes vers une psychothérapie traditionnelle, parfois accompagnée d\'une prescription d\'antidépresseurs. La prise de conscience par le joueur des raisons pour lesquelles il est tombé dans la dépendance suffit, la plupart du temps, à l\'endiguer.<br />
Un centre d\'aide pour les joueurs
<p class="titre">Une action citoyenne</p>
Afin d\'éviter de provoquer une telle dérive chez les joueurs de Delain, j\'ai décidé de faire de cette journée une <b>journée d\'action</b>, afin de montrer à tous ce que peut être une journée sans Delain. Cette initiative restera, je l\'espère, dans les annales comme <b>la première journée delainienne contre l\'addicitivté au jeu de rôle sur Internet.</b><br />
J\'incite tous les webmasters de jeu à suivre mon exemple, et à couper <b>autant qu\'il le faudra</b> l\'accès à leur jeu.<br />
Toutefois, afin de ne pas provoquer un sevrage trop intense, les souterrains ouvriront leurs portes de temps en temps dans la journée, <b>pendant 1 minute maximum</b>. A vous de trouver les bons moments !




	


';
$t->set_var("contenu_page",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
