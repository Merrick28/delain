<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '
		<p class="titre">La religion dans Delain</p>
		<p>Vous êtes entrés dans un temple, et vous ne savez que faire ? Voici les grandes lignes pour vous permettre de comprendre comment se passe la religion dans les souterrains.
		</p>
		<h2>1 - Les dieux :</h2>
		<p>Il existe divers dieux dans Delain. Les dieux sont des entités non palpables dotés d’immenses pouvoirs qui peuvent se canaliser au travers de ceux qui croient en eux. Chacune de ces entités tire sa puissance de ses fidèles. Chaque fois que quelqu’un prie un dieu, ce dernier gagne en pouvoir. Plus un dieu a de fidèles, plus sa puissance est grande.</p>
		<p>Voici une liste de quelque dieux présents dans Delain :</p>
		<ul>
			<li><b>Io l’aveugle </b> : Roi de la nuit, de l’obscurité et des rêves </li>
			<li><b>Balgur </b> : Dieu de la guerre</li>
			<li><b>Galthée </b> : Déesse de la terre</li>
			<li><b>Elian </b> : Dieu des chasseurs</li>
			<li><b>Apiera </b> : Déesse de la Lune, des arts et de l’illusion </li>
		</ul>

		<p>Cette liste n’est bien sur pas exhaustive et peut varier au cours du temps. Certains dieux peuvent apparaître grâce aux prières de fidèles, ou bien disparaître si personne ne les prie plus.</p>
		<h2>2 - Les prières :</h2>
		<p>Afin de pouvoir donner de la puissance à son dieu, il est nécessaire de prier pour lui. Tout le monde peut prier son dieu dans un temple associé à celui-ci. Pour cela, il suffit de choisir « prière » ou bien « organiser une cérémonie ».</p>
		<p>Une cérémonie est coûteuse, mais amène considérablement plus de puissance à un dieu qu’une simple prière.</p>
		<h2>3 - Quitter une religion :</h2>
		<p>Lorsque vous priez un dieu, vous êtes automatiquement fidèle de ce dieu. Si vous priez dans un autre temple, votre premier dieu vous en voudra, et toutes les prières que vous aurez pu lui faire ne vous serviront plus à rien (voir le chapitre suivant : hiérarchie). Si en plus vous êtes monté dans la hiérarchie de ce dieu, vous êtes considéré comme un renégat. Il vous est impossible de rerentrer dans les rangs de ce dieu pendant plusieurs jours, voire plusieurs mois.</p>
		<h2>4 - Hiérarchie :</h2>
		<p>Lorsque vous priez un dieu, celui-ci vous connait. Au bout d’un moment, votre dieu peut vous proposer de passer du grade de fidèle à celui de « premier grade » (différent pour chaque dieu).
		Dans ce cas, la mention « novice de xxxx » (par exemple) sera automatiquement rajoutée à la suite de votre nom de perso dans la vue des autres personnages. <br>
		Le fait d’être en premier grade vous fait également gagner un sortilège divin. Vous pouvez à tout moment invoquer votre dieu pour lancer ce sortilège (à condition d’avoir assez de PA bien ûr). Si à ce moment votre dieu a assez de puissance, alors il lancera ce sort pour vous. Aucun jet de dés n’est effectué, et vous ne gagnez pas d’expérience sur les lancers de sorts divins. Si le dieu n’a pas assez de puissance à ce moment-là, rien ne se passe, et il faudra attendre que le dieu reçoive des prières pour pouvoir le relancer.<br>
		Un premier grade peut ensuite passer second grade (le terme peut varier selon les dieux). Ce statut comporte une condition importante : on renonce à la magie mémorisée. À partir de ce grade, le fidèle peut lancer des sorts en utilisant des runes, mais plus de sorts mémorisés. En contrepartie, son dieu lui offre de nouveaux sorts divins.<br>
		À partir de ce rang, on peut prier son dieu même en extérieur afin de lui redonner de la puissance.<br>
		Il existe d’autres grades plus élevés, à vous de les découvrir.</p>';

$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse("Sortie", "FileRef");
$t->p("Sortie");