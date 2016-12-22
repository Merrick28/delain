<?php 
/*ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set("html_errors","1");
ini_set("pgsql.ignore_notice","0");
ini_set("error_reporting","E_ALL");
error_reporting(E_ALL);*/
$sign = array(
"Les gourous ne sont pas télépathes. Je suis moi même en phase de beta test de HomoSapiens 2.0Alpha12, mais il y a plein de bugs et on est obligé de me rebooter assez souvent.",
"La capote c'est le soulier de verre de notre génération, on l'enfile quand on rencontre une inconnue, on danse toute la nuit, et puis on la balance... la capote j'veux dire, pas l'inconnue.",
"Si la connerie était cotée en bourse,tu serais incarcéré pour délit d'initié...",
"Il est horrible de penser que le cerveau de certains pose un problème.
Il me paraît plus charitable de supposer qu'il n'en ont pas.",
"I gave up on finding an answer and started drinking.",
"Je vais de ce pas chercher les drosophiles, la vaseline (les pauvres elles vont chauffer sinon) et le couteau à quadricapillotomie, et on peut commencer la discussion sur la création de nouveaux sorts.",
"Internet, ça rend con et ça abime les yeux. Feriez mieux de regarder la télé.",
"les seuls qui lisent les FAQs sont ceux qui savent déjà ce qu'il y a dedans.",
"Il n'y a aucune corrélation entre l'état de putréfaction d'une discussion et la réussite du vote.",
"Pour moi, une seule chose est claire. Avant d'effectuer toute modification des règles, il faut définir des règles de modification des règles. ",
"On va bientôt avoir des algorithmes à pondération variable en fonction des saisons et des adresses d'émission. Les votes blancs seront comptés sans compter et les anonymes vont être acceptés si on sait qui c'est.",
"Utiliser des machines non françaises aussi m'est insupportable. Je compte bientôt m'équiper d'un boulier bien de chez nous. Pour les graphiques, j'utiliserai de vrais camemberts, au lait cru évidemment.",
"Il y a effectivement écrit \"FREE\" en grosses lettres clignotantes sur la page d'accueil |...| ce qui est suffisant pour rendre méfiant n'importe quel organisme vivant plus évolué que le zooplancton.",
"Yep. Moi j'ai un clavier à une touche. Par contre, ma souris a 102 boutons, c'est pas toujours pratique.",
"Non non, on peut être tout aussi bien de mauvaise foi en n'ayant rien compris. D'ailleurs, si chacun réfléchissait avant d'écrire, nous serions en grand danger que le débat avançât. La prudence s'impose.",
"L'intelligence et la connerie ne sont pas incompatibles.
La bêtise et la connerie non plus.",
"Passe que moi, au départ, j'avais fait informatique comme études, pas NT, et je voudrais revenir à mon métier premier.",
"Marre des versions stables, je vais tester les instables, au moins je saurai pourquoi ça plante ",
"Un detail, je suis sur Unix... Redemarrer un systeme Unix fait generalement apparaitre dans mon bureau entre 1 et 3 ingenieurs systeme a l'humeur agressive voire violente.",
"Le neuneu est un con qui débute. C'est une espèce rare mais qui fait beaucoup de bruit.",
"Je n'utilise pas Linux, c'est un système qui fait la part trop belle aux neuneus. Un peu de complexité et de prise de tête n'a jamais fait de mal à personne, et ça assure une sélection naturelle. ",
"Je crois que le meilleur moyen, c'est une signature qui contient toutes les signatures et de virer les mauvaises avant envoi.",
"Au royaume des aveugles les borgnes sont mal vus.",
"Les forums, c'est un espace public et en public, il est beaucoup plus facile de passer pour un con que de se faire des amis. Plus facile et plus rapide surtout. ",
"Sinon, la tartiflette c'est facile : tu fais cuire des patates et un reblochon dessus. Une fois que c'est chaud, tu jettes et tu te prépare un bon plat d'endives braisées au jambon.",
"Le neuneu est à Internet ce que le staphylocoque doré est au furoncle. Ils bénéficient tous les deux d'un système cognitif de niveau équivalent, malgré un léger avantage intellectuel au staphylocoque.",
"Don't give in without a fight.",
"-Va mourrir.
-Tu aurais dû glisser un disclaimer : \"ceci est un conseil et ne constitue en aucun cas une obligation contractuelle\". ",
"Oh que non, je ne suis pas juriste. Mais j'aimerais bien voir la tête  du magistrat si Jean Dupont se pointe pour porter plainte avec le print d'un post de \"Colargol\" intitulé \"Bozo Le Clown est une raclure\" ",
"J'ai appris qu'il était question de mettre la gestion du système solaire sous Linux, c'est Dieu qui va etre content.",
"Solution à un problème Windows : Vire || (com2 usb) || probleme || xpertplay || resserve les adresse memoire || reboot || bios || active || windoz || parametrage || reservation de ressource || reboot || bios || reset || disable || prie.",
"À l'unanimité de moi-même, je suis d'accord.",
"Désolé mais cette expression n'a aucun sens ... 
Il est informaticien, alors, tant que tu compiles pas, ses paroles n'ont aucun sens...",
"Si maintenant on introduit le concept de normalité des bugs, où va l'informatique ? Chez Microsoft ? Ah oui, pas faux ça.",
"Les roots ne sont plus ce qu'ils étaient...Maintenant il sont dioxinés, c'est de la m... ! Avant on les élevaient avec du bon unix mais ça été remplacé par des farines industrielles nouvelles technologies (NT). ",
"Après avoir passé la nuit à souffler sur la led du modem pour qu'elle se rallume, à essayer tous les rites chamaniques, je finis par m'endormir, bien décidé à achever mon modem à coup de hache le lendemain.",
"Si les royalties sur la connerie finançaient le plan cable, on aurait tous du GByte ethernet à domicile.",
"Ouvre les mains, place les à 30 cm devant toi et replie les pouces contre les paumes. Ça te fait un octet de doigts.",
"Je tenais a remercier Wanadoo de l'hommage rendu ce jour au roi Hussein récemment disparu. En effet ma connexion est elle aussi \"cliniquement morte\" avec un débit de 0,2 Ko par seconde en pointe. ",
"Tu as lu les docs. Tu es devenu un informaticien. Que tu le veuilles ou non. Lire la doc, c'est le Premier et Unique Commandement de l'informaticien.",
"Solution à un problème Windows : Vire || (com2 usb) || probleme || xpertplay || resserve les adresse memoire || reboot || bios || active || windoz || parametrage || reservation de ressource || reboot || bios || reset || disable || prie.",
"À l'unanimité de moi-même, je suis d'accord.",
"Pour rappel, Windaube il est optimisé pour ne pas être utilisé mais c'est un secret.",
"Si maintenant on introduit le concept de normalité des bugs, où va l'informatique ? Chez Microsoft ? Ah oui, pas faux ça.",
"Les roots ne sont plus ce qu'ils étaient...Maintenant il sont dioxinés, c'est de la m... ! Avant on les élevaient avec du bon unix mais ça été remplacé par des farines industrielles nouvelles technologies (NT). ",
"Après avoir passé la nuit à souffler sur la led du modem pour qu'elle se rallume, à essayer tous les rites chamaniques, je finis par m'endormir, bien décidé à achever mon modem à coup de hache le lendemain.",
"Si les royalties sur la connerie finançaient le plan cable, on aurait tous du GByte ethernet à domicile.",
"Ouvre les mains, place les à 30 cm devant toi et replie les pouces contre les paumes. Ça te fait un octet de doigts.",
"Je tenais a remercier Wanadoo de l'hommage rendu ce jour au roi Hussein récemment disparu. En effet ma connexion est elle aussi \"cliniquement morte\" avec un débit de 0,2 Ko par seconde en pointe. ",
"Pouvez vous me laisser un message car je me fais chier
As-tu envisagé de faire chier les autres ? Le secteur est porteur. En pleine expansion. Tu ne le regrêteras pas, parole.",
"Je n'utilise pas Linux, c'est un système qui fait la part trop belle aux neuneus. Un peu de complexité et de prise de tête n'a jamais fait de mal à personne, et ça assure une sélection naturelle. ",
"à partir de quand n'est-on plus un neuneu? est-ce que ça se soigne? 
C'est une variété de maladie infantile la réponse est donc oui. La réponse à la question est-ce que ça se guérit est ; pas toujours. ",
"Au royaume des aveugles les borgnes sont mal vus.",
"Les forums, c'est un espace public et en public, il est beaucoup plus facile de passer pour un con que de se faire des amis. Plus facile et plus rapide surtout. ",
"Sinon, la tartiflette c'est facile : tu fais cuire des patates et un reblochon dessus. Une fois que c'est chaud, tu jettes et tu te prépare un bon plat d'endives braisées au jambon.",
"Un bon président américain doit savoir évaluer le cout d'une vie, au litre de pétrole près.",
"Les antivirus, c'est un peu comme les plats surgelés - sur l'emballage ils sont très appetissants, mais quand ils sortent du micro-ondes ils ont une allure comme si un camion avait roulé dessus 3 fois.",
"Together we stand, divided we fall.",
"Oh que non, je ne suis pas juriste. Mais j'aimerais bien voir la tête  du magistrat si Jean Dupont se pointe pour porter plainte avec le print d'un post de \"Colargol\" intitulé \"Bozo Le Clown est une raclure\" ",
"Je cherche 150KF pour un investissement boursier. Remboursement selon entente même à fort taux de crédit. Si vous êtes intéressé ou avez des renseignements pouvant m'être utile merci de m'envoyer un mail.",
"Quand je passe par le bios et que je lui dit de booter sur C, il boot sur le PM en C:\ et il met le SM en D:\. Quand le lui dit de booter sur D, il boot sur le SM en C:\ et il met le PM en D:\. Ca c'est génial. ",
"Un inconnu du nom de Mailer-Daemon reçoit mes mails. Comment cela est  possible ? Comment l'eviter. Répondre très rapidement, car mon mois d'essai se termine ce soir, et je suis très inquiet de ce piratage ? ",
"J'ai windows 98 et comment faire pour changer l'os de windows 98 ? Dans ajout et suppression du programme et il ne parle pas d'os. ",
"quand j'utilise la roulette avec la souris ps/2 weel mouse crosoft = apr=E9s 2/3 recherches je subis un gel d'=E9cran soyez prudent il y a peut-=EAtre des risques d'esplosion du moniteur... ",
"Etant nouveau, certains termes m'échappent encore. Mail Bombing ! Kesako ? Comment on pose la bombe ? et comment on règle le minuteur ? Quelle est la portée du missile ?",
"-Tiens, et combien de programmeurs faut-il pour changer une ampoule?
-Ben, un pour appeler la maintenance et quatorze pour lui expliquer qu'avec Linux ce serait mieux. ",
"-Qui peut encore croire que le bio est reserve aux gens financierement aises? 
-Tous les gens qui ne sont pas financierement aises. ",
"-Au fait elle est mieux ma signature maintenant ? 
-Oui. T'enlève encore les conneries que t'as écrit dedans et c'est bon.",
"-\"mot de passe invalide\" ca veut dire quoi?
-Que tu peux s'assoir dans les places réservées des autobus.",
"Le seul moyen pour ne pas etre \"rattrapé\" par des aneries qu'on a dites c'est de ne pas dire d'anerie ce qui au bout du compte serait tout bénef pour Internet, mais ce média perdrait alors beaucoup de son attrait",
"- Il est injuste de faire l 'amalgame entre du bulk mail et du courrier non-solicité très ciblé. 
 - un suppositoire non reclame, meme tres cible, reste un suppositoire. ",
" Nan, les gens riches sont souvent intelligents (comment seraient-ils riches sinon ?). D'où l'expression pauvre con.",
"Tout développeur faisant des promesses sur des dates de disponibilité tend le bazooka à clous pour se faire battre. ",
"X..., c'est un millefeuille avec une couche de crème patissière, une de sauce tomate et une de crème d'anchois... Mais c'est vrai que c'est un système ouvert: tu peux y rajouter des pépites de chocolat...",
"- Comment faire pour rechercher par exemple tout les dentistes ayant un email ?
- Du côté de l'email, ça devrait aller, pour les dentistes ! ",
"-Ben, non, en français, pas de points de suspension après un etc., c'est une redondance pour dire deux fois la même répétition... 
 -Moi j'aime bien la redondance qui dit 2 fois la même répétition. ",
"- Au royaume des aveugles le borgne est roi 
- Au royaume des aveugles les borgnes sont mal vus. ",
"Je poste des messages de demeuré parce que j'ai une réputation de demeuré à défendre. On n'imagine pas combien une telle réputation est difficile à établir. Son entretien est un souci permanent... ",
"- qui pourrait me renseigner ou je pourrais trouver un site web avec des liste de noms pour envoi de mailing 
- Demande à info@cnil.fr, les listes de noms, ca les connaît.. ",
" Le dino, c'est celui qui comprend un message qu'il ne lit pas. 
 Le neuneu, c'est celui qui ne comprend pas un message qu'il lit.",
 "- Just do it.
- Traduction ?
- Bouge toi le cul.",
"Je cherche aussi des adresses de lieux contenant des fossiles dans la région parisienne 
 http://www.senat.fr/ ",
"- Ses mollets: impeccables. Je n'en dirais pas autant de son strabisme.
 - Vous avez quelque chose contre le strabisme ?
 - Je trouve ça louche.",
 "- J'ai eu cette idée sans rien lire sur le sujet
 Rassurez-vous, ça se voit.",
 "- Salut ! J'ai un sujet de philo à vous soumettre : 'Suffit-il d'observer pour connaître' Idées + plan Merçi
 Oui, ya qu'a t'observer pour connaître le fait que tu es une feignasse.",
  " La maintenance, c'est plus que du « bricolage », c'est un mode de vie, une attitude...",
  "je ne parle pas de secte moi, je parle d'extraterrestres et de religions.",
  "Je dois faire un exposé pour mon cours de comm. Le sujet : les crottes de nez ou la spéléologie nasale. recherche toute expression, image, plan, document scientifique, photo, page ouèbe sur ce sujet.",
  "'Les newbies d' aujourd'hui seront l' élite de demain'.",
  " Les conflits non résolus se règlent devant le juge, une personne qui n'est ni juge ni partie.",
 "- il me dit 'Unable to parse configuration file'. Que dois je faire ?
- Lire la doc
- CA MARCHE !!!!!!",
" L'argument de poids qui dit que mes potes et moi on est d'accord n'avance pas à grand chose. On va voter les théoremes, ça ira plus vite que de les demontrer et elire les annees bisextiles, ça sera rigolo.",
"  Etant nouveau, certains termes m'échappent encore. Mail Bombing ! Kesako ? Comment on pose la bombe ? et comment on règle le minuteur ? Quelle est la portée du missile ?",
" - Qui peut encore croire que le bio est reserve aux gens financierement aises?
 - Tous les gens qui ne sont pas financierement aises.",
 " D'accord, mais si on se met à utiliser des arguments intelligents dans ce genre de débat, il devient impossible de discuter.",
 "Le fait est que dans mon bureau à moi que j'ai, quand le téléphone sonne et que le numéro ne s'affiche pas je considère que la personne n'a pas envie que je décroche. Et comme je suis un gars conciliant...",
 " I just loaded a shortwave program into my PC, and the screen says to continue, hit ANY key...but I can't find a key that says ANY on it, what do I do? ",
 "Je pense donc je fuis. Je fuis donc je serai encore.",
"Q:Que donne le croisement entre un T-Rex et une truie ?
R:Un Jurassic Porc",
"Les quatre cavaliers dont la chevauchée présage la fin du monde sont, tout le monde le sait, la Mort, la Guerre, la Famine et la Pestilence.
Les quatre cavaliers dont lapparition annonce les jours fériés sont la Tempête, la Bourrasque, la Neige Fondue et la Voie à Contresens.",
"Les bons sont innocents et créent la justice. Les méchants sont coupables, et c'est pour cela qu'ils inventent la pitié.",
"La Mort faisait remarquer qu'elle utilisait encore la faux, a la maniere artisanale, alors que les Morts des autres mondes était déja passée a a moissoneuse batteuse",
"Les Dieux de Delain affrontaient l'éternité avec l'état d'esprit des inactifs qui se demandent comment tuer l'après-midi. Ils jouaient à des jeux avec le destin des hommes, disait-on. A quel jeu exactement ils croyaient jouer en ce moment, mystère. Mais évidemment, il existait des règles. Tout le monde savait ça. Il fallait seulement espérer très fort que les dieux les connaissent eux aussi.",
"- Quest-ce que cest, un philosophe ?
- Quelquun dassez malin pour trouver un boulot sans rien de lourd à soulever  ",
"Tu seras un home, mon fils (F.Bouygues)",
"Je rêve d'une femme à poil (le Yéti)",
"Et si nous dinions ce soir en tête à tête ? (Louis XVi à Marie-Antoinette)",
"Le poker c'est comme le sexe, si t'as pas de bons partenaires, t'as interêt à avoir une bonne main.",
"Si Dieu meurt, c'est Jésus qui hérite de tout.",
"Les autorités publiques pensent repousser les vacances de Juillet-Aout à Septembre-Octobre, d'abord parce qu'il fait moins chaud, qu'il y a moins de monde et qu'en plus, les voitures se jetant sur les arbres contribueraient à des récoltes faciles de fruite et à la chute des feuilles mortes.",
"Greve.
Pour protester contre le temps qui passent, deux vieillards jeunent.",
"un guerrier A frappe un guerrier B, le guerrier B encaisse
un guerrier A frappe un voleur B, le voleur B meurt
un guerrier A frappe un magicien B , le magicien B meurt massacré",
"un voleur A frappe un guerrier B, le guerrier B encaisse
un voleur A frappe un voleur B, le voleur B esquive
un voleur A frappe un magicien B, le magicien B meurt",
"un magicien A frappe un guerrier B, le magicien A meurt massacré
un magicien A frappe un voleur B, le magicien A meurt
un magicien A frappe un magicien B, les deux magiciens meurent",
"Pour soulever un objet par télékinésie, le magicien doit 
toucher l'objet et vouloir que celui-ci se soulève.
La preuve que ce fait est magique: avec la force de ses bras, 
jamais un magicien ne pourrait soulever quoi que se soit.",
"Lorsque un magicien A frappe en jetant 'Magie' sur un magicien B,  
le magicien B meurt explosé.
Ensuite la magicien A meurt de faim car il n'a plus d'énergie.",
"Le magicien est le meilleur connaisseur de la magie;
Il devrait donc être le premier à fuir les objets magiques.",
"La massue en balsa : exclusivement réservée au magicien: il s'en sert 
une fois (normal, elle se brise lors de l'unique coup) et agace fortement
le personnage ciblé, qui tue le mago.",
"Aimez vous non pas, les uns les autres , mais les uns sur les autres",
"Que cherchait exactement l'homme quand il a découvert que la vache donnait du lait ?",
"On nous montre comment, avec les détergents actuels, on peut enlever des taches de sang. Mais il est probable qu'avec un t-shirt couvert de sang, notre souci premier ne soit pas de faire partir la tache mais de trouver un endroit pour cacher le corps.",
"Les jeunes c'est tous des bons-à-rien. Et ça devient pire avec l'age.",
"On dit que les cygnes chantent avant de mourir... Je connais des types qui feraient bien de mourir avant de chanter.",
"Les types qui déboisent la forêt amazonienne acceptent enfin de faire un geste pour l'environnement. Désormais, ils mettront de l'essence sans plomb dans leurs tronçonneuses.",
"La mort, c'est un peu comme une connerie. Le mort, lui, il ne sait pas qu'il est mort. Ce sont les autres qui sont tristes. Le con, c'est pareil.",
"La différence entre l'amour et l'argent, c'est que si on partage son argent, il diminue, tandis que si on partage son amour, il augmente. L'idéal étant d'arriver à partager son amour avec quelqu'un qui a du pognon.",
"Beaucoup de gens sont malades quand ils sont vieux... C'est triste... D'un autre côté, ça les occupe.",
"S'endormir au volant, c'est très dangereux. S'endormir à vélo, c'est très rare. S'endormir à pied, c'est très con.",
"Il paraît que même à Monaco les rues ne sont plus sûres. Les milliardaires n'osent plus sortir le soir... Il y a des millionnaires qui rôdent.",
"La différence entre la chasse et la guerre, c'est qu'à la chasse on ne fait pas de prisonniers.",
"La déclaration universelle des droits de l'homme comporte 30 articles. Certains pays en ont ajouté un 31ème. L'article 31 annule les 30 premiers.",
"- rah chuis deg ma soeur a installé les fontes japonaises de windows et maintenant elle sont partout! comment s'en débarasser?
- Un coup sec derrière la nuque.",
"- Aujourd'hui je crois bien que j'ai évité un viol
- Wow balèze ta fait comment ?
- J'ai réussi à la convaincre finalement ^^",
"<+tchot6> au fait comment on fait pr changer son pseudo?
<@|3RTy|bAmbOo> /nick pseudo
* tchot6 is now known as pseudo",
"house of pain on traduit ca par boulangerie?",
"(@yavin-) n'empeche vous avez remarqué ? le truc pour faire une putain de carrière c'est de s'appeler armstrong
(@yavin-) louis, neil, lance, craig
(@FaskZiK) yavin- > bah pas forcément... igor armstrong il a crevé comme une merde et personne sait qui c'est
(@Caf_away) FaskZiK: ouais
(@yavin-) FaskZiK, c'est qui ?
(@FaskZiK) yavin- > ben tu vois",
"'cheri cheri ! j'arrive pas a coucher avec un autre femme !' .... comment ca surprend comme premiere phrase en rentrant du boulot
et il faut bien 5 minutes a l'ecouter pour comprendre de quoi elle parle ...
putain de sims ...",
"Là, il est l'heure d'aller me coucher : je viens d'essayer de cliquer sur une mouche qui se baladais sur mon écran, pour la faire partir...",
"Mon frère, il a grillé le forfait 50h d'AOL en 2 jours...",
"Bon ma fille a invité des copines pour son anniversaire, elles sont 7 petites filles deguisees en princesses
Ce serait charment si elles ne faisaient pas un concours de rots :/",
"vous pensez que quand les fans de tuning se retrouvent sur IRC, ça fait un jacky chan ?",
"Tiens, cet aprèm...
ma femme sort de la piaule en chemise à fleur / bermuda / tongues...
elle me dit 'alors, je te plais ?' ...
'ouais carrément ! tu ressembles à Tortue Géniale !'
Elle m'a collé une baffe.",
"Windows, c'est mieux qu'un tamagochi... meme quand tu t'en occupes, il meurt !",
"mince
j'avais pas d'idée de pass (et je voualis pas mettre mon habituel)
alors j'ai essayé penis
il m'a dit que c'était trop court ...",
"g failli ouvrir la grande bouteille d eau alor que la pitite etait pas finie
mes journees sont palpitantes",
"NewOrleans has quit IRC (excess flood)",
"- connaissez vous un virus qui fait se deconnecter internet ?
- AOL",
"- mais ca veut dire quoi ctb et cmb?
- Comme Ta/Ma Bite
- a vi c pas mal raccourci
- ctb",
"- ouai mé g pa windows nn plus
- tu as quoi ?
- Packard bell",
"c'est tellement le bordel dans cette piole que le jour où je vais la ranger, j'vais surement retrouver un pote sous les gravats :x",
"hum, efficace
j'installe un programme pour libérer de la RAM
et il ferme Windows",
"putain, le robot nettoyeur de piscine, ca coute super cher, g fait le calcul, je peux embaucher un smicard lui acheter des palmes, un tuba et une eponge spontex pendant des mois pour ce prix la",
"J'ai proposé à ma copine de regarder \"le Dernier samourai\"
Elle m'a répondu : \"ouais mais j'ai pas vu les premiers\"",
"quand g apelé le sevrice teknik de Acer pour leur dire que mon lecteur/graveur dvd ne lisait plus et ne gravait plus, ils m'ont demandés d'insérer le disk de recovery system dsans le lecteur dvd",
"Fait vachement bon, à Toulouse. Fait pas chaud, fait pas froid, et y'a du soleil. J'irai presque faire un tour si j'avais la télécommande de mon portail, tiens.",
"C'est /quit et /quat qui sont sur un bateau, /quat tombe a l'eau... qui reste t'il?
Disconnected from chat",
"- vous faites quoi demain pour commémorer le 11 septembre ?
- je vais aller faire un bowling",
"le plus chiant quand tu fais nuit blanche c'est que t'as l'impression de pas avoir dormi",
" -moi la seule meuf ke je connais c'est celle ki répond sur le service allopass
\"allopass bonjour ce service vous sera facturé 1.34 la minutes puis 0.34 senss la minutes aprés le bip\"
- elle est bonne ?
- c'est possible jpeux pas dire
- au moins t'as réussi à gratter son n° c'est djà ca...",
"dites,
en louisiane,
quand ils auront tout reconstruit, ils appelleront ça nouvelle-nouvelle-orléans ?",
"un cheat c'est comme se finir à la main alors que tu couches avec Monica Belluci
ça a pas la même saveur",
"j'ai découvert que Matrix c'est de la connerie
on peut tordre des cuilleres
et sans problèmes
avec de la glace a la vanille",
"Vous savez pourquoi on utilises A, B, C, D, E et F pour les bonnets de soutif?
A => Appréciable
B => Bien
C => Canon
D => Dément
E => Enorme, voire Ecoeurant
F => Faux",
" - c fou comme une machine peu changer un homme
- tu l'as dit bouffi, passe un homme dans une moissonneuse batteuse tu verra comme il sera changé",
"- Sinon, je sais pas bien combien ça prends dans un YMCA, mais vu l'état de décrépitude de celui de Washington, j'ai vite zappé vers un hotel de banlieue (au final, je suis sur bien pire, mais là, j'était seul avec ma femme) et c'était 86$ la nuit
- elle t'arnaque, ta femme, normalement c'est gratuit",
"- J'ai un document de 40 pages à imprimer en 10 minutes. C'est possible ?
- Bah envoie 40 pages de test et chronomètre. ",
"Il faut combien de Super Saiyen pour remplacer une ampoule ?
- je sais pas
- Un seul, mais ça lui prend 3 épisodes.",
"mince les moustiques continuent à m'attaquer,et ils ont de plus en plus gros
heureusement ils sont dirigés par une vieille IA de jeu de stratégie qui envoie ses unités 1 par 1",
" le bois de boulogne c'est une foret ou les buissons parlent
c'est un peu comme Brocéliande, mais avec un climat brésilien chaud et humide",
"j'accepte d'un codeur que son appart soit en bordel, qu'il y ait des restes de pizza sur le canapé, ou bien carrément qu'il n'ait pas de canapé... mais le code doit être propre. Y'a des priorités dans la vie ;)",
"ca fait quoi si tu mélange du orange (couleur panure) avec du rose (couleur jambon) et du jaune (couleur fromage clair) ?
ca fait pas du bleu par hazard ?
pk j'aimerai savoir pourquoi on appelle un cordon bleu cordon bleu",
"En nage synchronisée, si une nageuse se noie, est-ce que les autres se noient aussi ? ",
"de la prog irc c'est du c par infrarouge ?",
"bnoujor, je crhehce de l adie, mon caliver est tmboé, les lretets se snot mealneges",
"on voit notre vie avec presque trop de recul quand on parles, on dirait que c pas la notre
qu'on joue à un MMORPG, les filles, c les coffres, pour les ouvrir, faut la technique, le matos et les bonnes combinaisons, et en plus, tu sais pas ce que tu vas trouver dedans. C'est toute une quête.",
"> perso, je suis plutôt Gimli que Legolas
> Je suis plutot gollum moi, recroquevillé sur mon pc...mon précieux pc",
"C'est pas vraiment ma faute s'il y en a qui ont faim, mais ça le deviendrait si on n'y change rien.",
"Dans les ruines de l'école où brûle un tableau noir,
une craie s'est brisée en écrivant \"espoir\"",
"Faut leur parler dans leur langage Garlok.
L'école ça sert à faire de l'XP. Ca se fait toujours en raid et y'a le lead qui vous apprend les stratégies pour les instances futures!
Quand tu sors de là, tu postules dans des sortes de guildes qui t'apprendront à bien monté tes compétences dans certains métiers. Ceci te permettra d'avoir des PO mais tu les toucheras qu'à la maintenance qui est en début de mois...
Le GM de ta guilde sera tout puissant et si il trouve que tu sers à rien et bé il te /gkick et du coup tu touches plus de PO.
En gros c'est ça...",
"Il y a 3 grands mensonges en informatique : ça marche, c'est compatible et ça sort bientôt",
"'L'important c'est de participer' ça ferait un bon slogan pour L'ANPE.",
"que se passe t il quand marseille gagne la coupe d'europe ?
on eteind la play",
"vous utiliser quoi pour faire vos sreenshot?
un stagiaire",
"Tout ce qui ne nous tue pas nous rend plus fort... ou paraplégique si on n'a pas de chance.",
" Les plus jolies fleurs ternissent et se fanent
Les jeunes filles qu'ont de la chance deviennent de vieilles femmes",
"Les traînées aux coins des yeux sont le lit des fleuves
Où s'écoulent peu à peu les larmes des veuves",
"setup a l'envers ca fait putes",
"\"Les jeux vidéos n'affectent pas les enfants : je veux dire si Pac-Man nous avait influencé étant enfant, nous devrions tous courrir en rond dans des pièces sombres, en gobant des pillules magiques tout en écoutant de la musique répétitive.\"
Kristian Winlson, président de Nintendo, 1989",
"faire croire que le pere noel n'existe pas
pour faire croire que les parents sont genereux
c'est fort quand meme
tout le monde est si naif....",
"un bon brossage de dents, c'est comme un bon rapport sexuel...
ça sert à rien d'y aller comme un bourrin mais y a une durée minimum à respecter pour que ce soit efficace",
"- Je me suis encore endormi en classe aujourd'hui...
- ca craint mec, qu'est c'qu'a fait le prof ?
- C'est moi le prof",
"en fait, une synagogue, c'est comme une église mais avec un CSS différent",
"Portez des soutiens-gorge Walt Disney et vous aurez des seins animés",
"-c bete que personne puisse prevoir les evenement qui vont suivre apres dadvsi
- si : Orwell.",
"On dit qu'on apprend avec ses erreurs... A mon avis, c'est une erreur. Et au moins si je me trompe, et bien j'aurais appris quelque chose.",
"En théorie, il n'y a pas de différence entre la théorie et la pratique, mais en pratique il y en a une !",
"Si tu doutes, reboote ... si ça rate, formate! (proverbe windowsien)",
"La théorie c'est quand çà ne marche pas mais qu'on sait pourquoi
La pratique c'est quand çà marche mais qu'on sait pas pourquoi
La réalité c'est quand ça marche pas et qu'on sait pas pourquoi",
"Si l'on se trouve dans l'obligation de tuer un mime, faut-il utiliser un silencieux ?",
"il est toujours très difficile de perdre sa belle-mère ... C'est même pratiquement impossible",
"Si vous pensez m'avoir compris, c'est que je me suis mal expliqué.",
"    Je connais un type très important. Il a des centaines de personnes en dessous de lui. Il est gardien de cimetière",
"Si tu parles à ton eau de javel pendant que tu fais la lessive, elle est moins concentrée (JCVD)",
"Plus je l'écoute et plus je me dis que Diam's pourrait avoir l'obligeance d'aller se pendre.",
"Jeanne D'Arc était la premiere femme au foyer",
">cd /pub
>more beer",
"- qui c'est qui pourait me conseiller une carte mère pas trop chère et bien???
 - regarde dans un jeu de sept familles :)",
 "Maintenant je sais que les fantômes existent
 ils sont pas méchants, ils sont juste un peu tristes.
 Ils veillent sur ceux qui les aimaient
 Eh ben les fantômes, ils sont pas rancuniers.",
 "Tout le monde connait le Prozac, y'a aussi le viagra.Un labo a fait un mix des 2: le Viazac. Tu bandes pas mais tu t'en fous",
 "Si Microsoft inventait un truc qui plante pas, ce serait un clou!",
 "*8*
merde
*8*
Excusez moi, j'ai le octet.",
"J'aimerais terminer sur un message d'espoir. Je n'en ai pas.
En échange, est-ce que deux messages de désespoir vous iraient ?",
"je vais passer pour un pervers, alors que je suis quelqu'un de tres seins d'esprit",
"tu savais qu'il y avait une fac pour les + de 50 ans qui était ouverte depuis des années en France ? Ouais, ils sont 500, yen a jamais plus de 150, ya 2-3 intellos qui écoutent, le reste dort, joue aux cartes, fout le bordel... comme les jeunes quoi
C'est l'Assemblée Nationale",
"Il y a une lumière au bout de chaque tunnel...(reste à prier pour que ce soit pas un train).",
"- ca veut dire quoi gorgeous ?
- quel contexte ?
- You are really a gorgeous boy
- 'règle ta webcam'",
"ca pue le progrès, on pourra pas faire de feu de joie avec tous nos .ppt de cours",
"un seigneur sith c est un webmaster?",
"Les inscrits des amphis, c'est comme les pokemons : en tout il y en a 150, mais on n'en cotoie jamais plus d'une vingtaine...",
"Se tromper est humain, mais pour vraiment mettre le bordel, il faut y ajouter un ordinateur.",
"ptin si un jour vous en avez marre de jouer a la xbox, chatter en buvant des bieres, de sortir tout les soirs faire des teuf de malades, et de passer vos week end a faire l'amour a en faire palir de jalousie le plus endurant des marathoniens, faites un gosse, ca calme",
"le seul probleme avec windows c'est que des fois le clavier se blo",
"Je suis sorti de ma chambre, j'ai passé ma soirée dans le salon. Sympa comme pièce, faudras que j'y aille plus souvent.",
"- t'imagines si on était des sims et que Dieu comandait nos gestes et tout?
- non pasque si il joue aussi mal que ma soeur on est dans la merde, mais ca expliquerai pas mal de trucs chelou ^^",
"- quelqu'un aurait-il une recette de grand mere a dispo' ?
- fais cuire au four, mais oublie pas d'enlever les lunettes.",
"petit conseil de survie en milieu domestique: ne jamais faire pipi apres avoir manipulé du piment, ou alors, mettez des gants.",
"- t'as avancé pour la recherche sur Aristote à rendre pour demain?
- Ouai , jvien de découvrir qu'il était mort",
"hier je vois \"4:04\" au reveil je me dit serieusement \"merde l'heure est introuvable\"",
"Si le travail c'est la santé, Donnez le mien à quelqun de malade.",
"t'as vu le temps qu'il fait ? c'est à sortir son pc et coder dehors",
"De toute facon, les guerres de religions, c'est quand meme se battre pour savoir qui a le meilleur copain imaginaire...",
"En fait le blog de l'est, c'est l'ex-fluxrss ?",
"tain j'ai une gastro qui me lache pas depuis 3 jour, une fidèle gastro.",
"En fait, le mariage, c'est comme si on te collait un DRM qui t'interdit d'utiliser ton sexe ailleurs que dans un seul periph autorisé (ta femme)
Bien sur, tu peux contourner le DRM, mais dans ce cas, tu as DADVSI qui te tombe sur la gueule : 
Divorce Assuré Dans la Violence le Sang et les Insultes.",
"- Chef, chef ! Il y a eu un vol cette nuit au supermarché ! On a volé 2000 cartouches de cigarettes et 500 lcarottes.
- Bien, et vous avez des soupçons ?
- Ben ouais, on recherche un lapin qui tousse. ",
"Selon une étude indépendante conduite par des scientifiques, de nombreux utilisateurs de Linux utilisent des versions non Originales de leur système d'exploitation. Cela leur donne l'inconvénient d'avoir leur ordinateur fonctionnant normalement, sans devoir régulièrement téléphoner à la maison sans être annoncé pour vérifier si leur ordinateur peut continuer à fonctionner. Ces utilisateurs ratent aussi l'Avantage de payer continuellement des taxes de licences pour s'assurer que leur ordinateur continuera de fonctionner correctement.",
"Et dire que chaque fois que nous votions pour eux
nous faisions taire en nous ce cri 'Ni Dieu ni Maitre'
dont ils rient aujourd'hui puisqu'ils se sont fait dieux
et qu'une fois de plus, nous nous sommes fait mettre...",
"- Nous les femmes on recherche l'Amour avec un grand A !!
- Ben nous les hommes avons un clavier Qwerty",
"Avant d'installer linux, mon ordinateur plantait tout le temps, les filles me fuyaient, je n'avais pas d'amis ni de vie sociale, et j'avais des boutons.
Maintenant, mon ordinateur ne plante plus.",
"Jai un trou, là, c'est quoi déjà le contraire de false ?",
"Un proverbe chinois dit que lorsqu'on a rien d'intéressant à dire, on cite généralement un proverbe chinois.",
"Il est con l'ennemi, il croit que c'est nous alors que c'est lui.",
"Avant, j'étais schizophrène. Maintenant, nous allons mieux.",
"Et dire que chaque fois que nous votions pour eux
Nous faisions taire en nous ce cri \"Ni Dieu ni maitre\"
Dont ils rient aujourd'hui puiqu'ils se sont faits dieux
Et qu'une fois de plus, nous nous sommes fait mettre.",
"l'essentiel à nous apprendre c'est l'amour des livres qui fait
que tu peux voyager de ta chambre autour de l'humanité
C'est l'amour de ton prochain, même si c'est un beau salaud
la haine ça n'apporte rien, pis elle viendra bien assez tôt",
"Le pessimite est un optimiste qui a de l'expérience",
"VEDETTE : Personne qui travaille dur toute sa vie pour être connue, et qui porte ensuite de grosses lunettes noires pour ne pas être reconnue.",
"TRAVAIL D'EQUIPE : C'est la possibilité de faire endosser les fautes aux autres.",
"SYNONYME : Mot à écrire à la place de celui dont on n'est pas certain de l'orthographe.",
"PROGRAMMEUR : personne qui résout, de manière incompréhensible, un problème que tu ignorais avoir.",
"PARLEMENT : Nom étrange formé des verbes 'parler' et 'mentir'.",
"BABY-SITTER : Adolescent(e)s devant se conduire comme des adultes de façon à ce que les adultes qui sortent puissent se comporter comme des adolescents.",
"DANSE : Frustration verticale d'un désir horizontal.",
"ECONOMISTE : Expert qui saura demain pourquoi ce qu'il a prédit hier n'est pas arrivé aujourd'hui.",
"FACILE : Se dit d'une femme qui a la moralité sexuelle d'un homme.",
"HARDWARE : partie de l'ordinateur qui reçoit les coups quand le software se plante.",
"NYMPHOMANE : terme utilisé par certains hommes pour désigner une femme qui a envie de faire l'amour plus souvent qu'eux.",
"Il vaut mieux qu'il pleuve un jour comme aujourd'hui, plutôt qu'un jour où il fait beau",
"Ne jamais sous-estimer le caractere previsible de la connerie",
"Tous les champignons sont comestibles. Certains une fois seulement.",
"Soyez gentil avec vos enfants, car c'est eux qui choisiront votre hospice",
"Dans les ruines de l'école où brûle un tableau noir
Une craie s'est brisée en écrivant 'espoir'",
"Nouvelle technique : on passe pour des cons, les autres se marrent et on frappe. Cest nouveau.",
"Si la jeunesse se met à croire à ces conneries on se dirige tout droit vers une génération de dépressif ; le gras, c'est la vie.",
"Vous êtes marié tout comme moi, vous savez que la monstruosité peut prendre des formes très diverses.",
"Mourir pour voir si on était vivant juste avant c'est un peu con",
"Sur une durée suffisamment longue, l'espérance de vie tombe pour tout le monde à zéro",
"- Sans souffrance, il n'y a pas de compassion.
- Dis ça à ceux qui souffrent.",
"Le coup le plus rusé que le diable ait jamais réussi, ça a été de faire croire à tout le monde qu'il n'existait pas.",
"C'était donc ça être adulte, avoir un compteur qui affiche de 0 à 210 et ne jamais faire que du 60 ...",
"Le piano, c'est l'accordéon du riche.",
"La bave du crapaud n'empêche pas la caravane de passer.",
"à quoi bon aligner les douzes pieds d'un alexandrin si ces dames s'épuisent au premier pied qu'elles prennent...",
"Si le ministre de la santé etait un geek y a longtemps qu'il aurai compris que la solution à la canicule c'est le watercooling",
"Peu importe que le verre soit à moitié plein ou à moitié vide, l'important c'est qu'il en reste dans la bouteille !",
"Par contre,ce qui va me faire chier c'est de plus pouvoir chatter, car par courrier,la réponse est lente. On a un ping de 5 jours",
"Il faut boire de la vodka en 2 occasions seulement, qd on mange et qd on ne mange pas ( proverbe russe )",
"ce qui est chiant avec les filles, c'est que quand on sait pas comment ça marche, on peut pas aller sur le site du constructeur pour télécharger les bons drivers",
"avant, j'arrivais jamais à finir mes phrases... mais maintenant je",
"J'ai fais un truc très productif aujourd'hui, j'ai noté la liste de trucs que je remettais à demain",
"Je me demande quand est-ce que microsoft va mettre un brevet sur les bugs","
tu sais, la moindre viande, c'est 15 euros le kilo. A 60kg la nana, ça fait 900 euros la fille. La plupart ne valent pa ça.",
"IRC is just multiplayer notepad.",
"- Homme de peu de foi 
- c'est du RP pour \"connard\" non ?",
"Tu sais que le monde va mal quand tu vois que quand tu tape \"bill\" dans google, la première réponse est la page wikipédia de la chanteuse bill de tokio hotel... (oui, oui, avant bill gates )",
"Une station de train c'est la que les trains s'arretent
Une station de bus c'est la que les bus s'arretent,
Moi j'ai une station de travail",
"P'tain... A la SCNF ils sont tellement habitué à rien foutre que quand ils font greve ils apellent ca: \"une journée d'action\"",
"In a perfect world... spammers would get caught, go to jail, and share a cell with many men who have enlarged their penisses, taken Viagra and are looking for a new relationship.",
"partir c'est mourir un peu...
mais mourir c'est partir beaucoup...",
"Programming today is a race between software engineers striving to build bigger and better idiot-proof programs, and the universe trying to build bigger and better idiots. So far, the universe is winning.",
"Walking on water and developing software from a specification are easy if both are frozen.",
"I have always wished for my computer to be as easy to use as my telephone; my wish has come true because I can no longer figure out how to use my telephone.",
"Computer science education cannot make anybody an expert programmer any more than studying brushes and pigment can make somebody an expert painter.",
"I think Microsoft named .Net so it wouldn’t show up in a Unix directory listing.",
"Fine, Java MIGHT be a good example of what a programming language should be like. But Java applications are good examples of what applications SHOULDN’T be like.",
"For a long time it puzzled me how something so expensive, so leading edge, could be so useless. And then it occurred to me that a computer is a stupid machine with the ability to do incredibly smart things, while computer programmers are smart people with the ability to do incredibly stupid things. They are, in short, a perfect match.",
"Perfection [in design] is achieved, not when there is nothing more to add, but when there is nothing left to take away.",
"Perl – The only language that looks the same before and after RSA encryption.",
"If McDonalds were run like a software company, one out of every hundred Big Macs would give you food poisoning, and the response would be, ‘We’re sorry, here’s a coupon for two more.’",
 "Sometimes it pays to stay in bed on Monday, rather than spending the rest of the week debugging Monday's code.",
 "Most good programmers do programming not because they expect to get paid or get adulation by the public, but because it is fun to program.",
 "Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live."

);



$nb_sign = sizeof($sign);
$nb_sign = rand(1,$nb_sign);
$aff_sign = $sign[$nb_sign];
$aff_sign = pg_escape_string($aff_sign);
$dbconnect = pg_connect("host=127.0.0.1 dbname=forum user=forum password=twlgokwz28") or die("En cours de maintenance");; 
  if(!$dbconnect)
        {
         echo("Une erreur est survenue.\n");
		 exit;
       }
$aff_sign = str_replace("'","'",$aff_sign);
$req = "update phpbb_users set user_sig = E'$aff_sign' where user_id = 2";
echo "<p> $req";
pg_exec($dbconnect,$req);
echo "<p>OK";