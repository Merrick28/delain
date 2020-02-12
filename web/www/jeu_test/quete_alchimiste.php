<?php

//
//Contenu de la div de droite
//
$contenu_page = '';
//
// on vérifie que le type d'appel soit bien passé
// s'il n'est pas passé, on considère qu'on est sur un lieu
//
if (!isset($type_appel))
    $type_appel = 0;

$perso = new perso;
$perso->charge($perso_cod);

//
// en fonction du type d'appel, on vérifie, soit le lieu, soit la compétence.
//
switch ($type_appel)
{
    case 0:
        $type_lieu = 99;
        $nom_lieu = 'une boutique d\'alchimiste';

        include "blocks/_test_lieu.php";


        include "blocks/_tests_appels_page_externe.php";


        break;
    case 2: //Cette fois, on vérifie qu'un perso sur la case est un enchanteur PNJ et qu'il ne s'agit pas d'un familier
        $erreur    = 1;
        $tab_quete = $perso->get_perso_quete();
        foreach ($tab_quete as $key => $val)
        {
            if ($val == 'quete_alchimiste.php')
            {
                $erreur = 0;
            }
        }
        if ($erreur != 0)
        {
            $contenu_page .= "Aucun alchimiste n'a pu être détecté dans le coin";
            $erreur = 1;
        }
        if ($perso->is_fam())
        {
            $contenu_page .= "Un familier ne peut pas contacter un alchimiste directement";
            $erreur = 1;
        }
        break;
    //
    // on rajoute un cas "default" pour les petits malins qui essaieraient de tricher
    //
    default:
        $contenu_page .= "<p>Erreur sur le type d'appel !";
        $erreur = 1;
        break;
}
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences 
	where pcomp_perso_cod = $perso_cod 
		and pcomp_pcomp_cod in (97,100,101);";
$stmt = $pdo->query($req_comp);
if ($stmt->rowCount() == 0)
{
    $comp_alchimie = 0;
} else
{
    $result = $stmt->fetch();
    $comp_alchimie = $result['pcomp_pcomp_cod'];
    $pourcent_alchimie = $result['pcomp_modificateur'];
}
//
// fin des controles principaux
//
$req_quete = "select pquete_nombre,pquete_date_debut from quete_perso
	where pquete_perso_cod = $perso_cod 
		and pquete_quete_cod = 14;";
$stmt = $pdo->query($req_quete);
if($result = $stmt->fetch())
{
    $quete_partie = $result['pquete_nombre'];
    $date_debut = $result['pquete_date_debut'];
} else
{
    $quete_partie = 0;
    $date_debut = '';
}

if ($quete_partie == 2)
{
    $controle = 'OK_quete_chasse';
} else if ($quete_partie > 0)
{
    $controle = '';
    /* on recherche si il y a réalisation d'une quête de chasse après avoir eu la quête de l'alchimiste*/
    $req_chasse = "select pquete_cod,pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut,pquete_date_fin,pquete_termine,pquete_param
		from quete_perso 
		where pquete_quete_cod in (11,12,13) 
			and pquete_perso_cod = $perso_cod 
			and pquete_termine = 'O'
			and pquete_date_debut > '$date_debut'";
    $stmt = $pdo->query($req_chasse);
    if ($stmt->rowCount() != 0 && $quete_partie == 1)
    {
        $controle = 'OK_quete_chasse';
        /*On met à jour la compétence Alchimiste du perso*/
        $req_comp = "insert into perso_competences (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) values ($perso_cod,97,20)";
        $stmt = $pdo->query($req_comp);
        $result = $stmt->fetch();
        /*On met à jour l'étape de la quête, pour éviter de retomber sur l'accord pour devenir alchimiste*/
        $req_quete = "update quete_perso set pquete_nombre = 2 
			where pquete_perso_cod = $perso_cod 
				and pquete_quete_cod = 14;";
        $stmt = $pdo->query($req_quete);
        $result = $stmt->fetch();
        /*Message spécial d'introduction dans la communauté des alchimistes*/
        $contenu_page .= '<strong>Un bruit étrange et inhabituel vous invite à vous retourner. C’est alors que, sous vos yeux ébahis, se dégage une silhouette intrigante. La femme qui se tient devant vous se tient légèrement vouté bien qu’elle ne paraisse pas particulièrement vieille.</strong><br>';
        $contenu_page .= '<br><em>Enfin, voilà quelqu’un qui est apte à devenir alchimiste. Je vais vous enseigner quelques rudiments de cet art.<br>
		Néanmoins, sachez qu’il s’agit d’une entreprise laborieuse qui vous demandera de la pratique, de découvrir des recettes ou de les échanger, et de tester tout ceci !</em>
		<br><br>Suite à cette introduction, la vieille part dans des délires dont elle seule semble avoir le secret. Elle vous explique certaines des bases de l\'alchimie, mais cela reste néanmoins très théorique.<br><br>';
        $quete_partie = 2;
    }
}

if (!isset($methode))
    $methode = 'debut';
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            //
            // requête pour voir si le perso est déjà un alchimiste
            //
            if ($quete_partie == 1)
            {
                $contenu_page .= '<strong>Un bruit étrange et inhabituel vous invite à vous retourner. C’est alors que, sous vos yeux ébahis, se dégage une silhouette intrigante. La femme qui se tient devant vous se tient légèrement vouté bien qu’elle ne paraisse pas particulièrement vieille.</strong><br>';
                $contenu_page .= '« <em>Votre mission n’est pas encore exécutée ! Prenez donc la peine de réaliser d’abord un contrat de chasse avant de venir me voir à nouveau.</em>»
					<br /><br />';

            } // ajout Morgenese : on s'assure pour le bloc suivant de ne pas être dans partie 2 de la quête (cas où l'on vient de valider sa mission)
            else if (($comp_alchimie == 0 || $controle != 'OK_quete_chasse') and $quete_partie != 2)
            {
                $contenu_page .= '<strong>Un bruit étrange et inhabituel vous invite à vous retourner. C’est alors que, sous vos yeux ébahis, se dégage une silhouette intrigante. La femme qui se tient devant vous se tient légèrement vouté bien qu’elle ne paraisse pas particulièrement vieille.</strong><br>';
                $contenu_page .= 'C’est en jetant un œil au sac qu’elle transporte que vous comprenez qu’elle croule presque sous le poids de son fardeau. En plus de l’énorme besace, sa ceinture supporte de nombreuses fioles et alambics qui, en s’entrechoquant, sont à l’origine du bruit qui attira votre attention.
					<br /><br />
					Levant la tête avec peine, elle vous adresse un regard jovial. D’un geste mesuré, elle dépose tout son fourbi, réajuste sa tunique et passe une main désinvolte dans ses cheveux :
					<br /><br />
					- « <em>Qu’avez-vous donc à me regarder comme ça ? </em>» souffle-t-elle d’une voix rauque «<em> Vous croyez que c’est facile de transporter tout cet équipement ? </em>»
					<br /><br />
					La femme marque une courte pause, pose sur vous un regard malicieux et continue :
					<br /><br />
					- « <em>Je vais devancer votre question aventurier ! Ce sac contient mon nécessaire d’alchimie, mes produits, mes composants, mes recettes, ma table pliable, mes pipettes, mes flacons, mes tubes à essais, mes alambics, quelques plantes, des fragments de choses et d’autres, un mortier et un pilon, des livres ancestraux, quelques rations de voyage, j’en passe et des meilleures ! Je suis chargée comme un mulet ! </em>»
					<br /><br />
					Avant que vous ayez eu l’occasion d’en placer une, l’étrange bonne femme se met à nouveau à parler :
					<br /><br />
					- « <em>Au cas où vous ne l’auriez pas compris, je suis alchimiste. Une grande alchimiste. Je parcours les souterrains à la recherche de produits spéciaux afin d’améliorer mes recettes. C’est un travail fastidieux mais le résultat est à la hauteur, croyez moi ! Ah, ça vous impressionne hein de vous trouvez confronté à une savante telle que moi ? </em>» 
					<br /><br /><br /><a href="' . $PHP_SELF . '?methode=non"><strong> Si ça m’impressionne ? Non pas du tout, vous m’avez tout l’air d’une vieille cinglée ! </a></strong>
					<br /><br /><br /><a href="' . $PHP_SELF . '?methode=oui"><strong> Je suis totalement estomaqué ! Vous êtes assurément quelqu’un de très impressionnant, m’enseigneriez vous les rudiments de l’alchimie ? </a></strong>
					<br /><br />';
            } else /*On est dans le cas où le perso est un alchimiste. Il faudra traiter le cas des passages de niveau d'alchimie*/
            {
                $contenu_page .= '<br />- « <em>Vous voilà Confrère ! Il y a bien longtemps que je n’ai rencontré quelqu’un qui partage la même passion que moi pour l’alchimie !
					<br>Prenez donc un peu de temps pour échanger sur vos dernières découvertes ! »</em>
					<br><br>Et l’alchimiste part dans un discours de plusieurs minutes sans écouter ni voir vos baillements.
					<br /><br />
					Après quelques minutes, le voilà qui s’interrompt et qui vous regarde de nouveau :
					<br>« <em>Puis-je vous être d’une aide quelconque ? Souhaitez-vous acheter des flacons vides ? <a href="' . $PHP_SELF . '?methode=acheter"><strong>OUI !</strong></a>
					<br>Si vous acceptez, il vous en coutera <strong>1000 brouzoufs</strong> le flacon »</em><br><br>';
                if ($comp_alchimie == '97' && $pourcent_alchimie >= '90')
                {
                    $contenu_page .= '<br>« <em>Dites-moi donc, je vois que vous êtes maintenant plus qu’un simple alchimiste. Vous avez la capacité de suivre mon enseignement pour atteindre une nouvelle étape dans votre connaissance !
						<br>Souhaitez vous acquérir cette nouvelle connaissance ? <a href="' . $PHP_SELF . '?methode=niv1"><strong>OUI !</strong></a>
						<br>Pour conclure notre transaction, vous devez posséder un ambre, une émeraude, un rubis, une améthyste, une apophyllite diatropique, deux Brazilianites épimystiques et 10000 brouzoufs. »</em>
						<br>Vous devrez aussi posséder 6 PA correspondants au temps de votre enseignement.<br><br>';
                } else if ($comp_alchimie == '100' && $pourcent_alchimie >= '100')
                {
                    $contenu_page .= '<br>« <em>Dites-moi donc, je vois que vous êtes un alchmiste confirmé. Vous avez la capacité de suivre mon enseignement pour atteindre une nouvelle étape dans votre connaissance ! Vous serez alors un alchimiste de talent, expert dans son domaine.
						<br>Souhaitez vous acquérir cette nouvelle connaissance ? <a href="' . $PHP_SELF . '?methode=niv2"><strong>OUI !</strong></a>
						<br>Pour conclure notre transaction, vous devez me donner deux diamants, une cryptonite hémicaustique, un jade, deux topazes, une dolomite hyporhombique, une obsidienne, un ambre, une émeraude, un saphir, ainsi que 20000 brouzoufs. »</em>
						<br>Vous devrez aussi posséder 10 PA correspondants au temps de votre enseignement.<br><br>';
                }


            }
            break;

        case "oui":
            $contenu_page .= 'La femme cligne des yeux, se gratte le sommet du crâne et plonge son regard perçant dans le vôtre. Après quelques secondes qu’elle passe à vous scruter intensément, l’alchimiste répond à votre question :
				- «<em> Dans l’absolu, je ne suis pas opposée à l’idée d’enseigner ma science. Cependant, cette dernière dote la personne qui la pratique d’un grand pouvoir. Déontologiquement, je me refuse à donner ce pouvoir au premier venu. J’ai besoin d’avoir la certitude que j’ai affaire à une personne qui d’une certaine envergure. L’alchimie n’est pas une science pour les faibles.</em>»
				<br /><br /><br /><a href="' . $PHP_SELF . '?methode=non2"><strong>Faible ? Ce n’est pas moi qui souffle comme un vieux chameau arthritique parce que j’ai un sac à transporter. Vous n’êtes qu’une vieille taupe ! </a></strong>
				<br /><br /><br /><a href="' . $PHP_SELF . '?methode=oui2"><strong>J’ai toujours rêvé de connaître les secrets de votre Haute Science, je suis prêt à tout pour que vous acceptiez de m’en en enseigner les bases ! </a></strong>
				<br /><br />';

            break;

        case "non":
            $contenu_page .= 'L’alchimiste ouvre des yeux énormes, sa mâchoire inférieure béante lui donnant un air encore plus cocasse :
				<br /><br />
				- «<em> Vous... vous ne manquez pas d’air ! Vous ne savez pas à qui vous vous adressez, j’espère ne plus jamais avoir à vous revoir ! </em>» fait elle en s’éloignant de vous. 
				<br /><br />';
            break;

        case "non2":
            $contenu_page .= 'Furibarde et rouge comme un piment, l’alchimiste fait mine de vouloir vous parler puis, d’un geste théâtral, elle fait volte face et s’éloigne de vous.
				<br /><br />';
            break;

        case "non3":
            $contenu_page .= 'L’alchimiste, visiblement très choquée, affiche un rictus assez effrayant et finit par hausser les épaules en reprenant sa route sans plus vous accorder la moindre attention. 
				<br /><br />';
            break;

        case "oui2":
            $contenu_page .= '- «<em>  Bien, bien, bien ! Je vois que vous êtes enclin à me prouver que vous êtes digne d’un tel apprentissage. Passons donc aux choses sérieuses !  »
				<br />- «  Tout d’abord, je dois m’assurer que vous n’êtes pas un incurable fainéant.
				En même temps, il est important pour moi que j’ai la certitude que vous vous investissez vraiment
				dans le combat commun contre l’Innommable. Pour ce faire, je vous conseille de partir à la recherche
				d’un Traqueur ou de regagner un Bâtiment administratif. L’un comme l’autre vous permettront
				probablement de décrocher un contrat de chasse. Remplissez en les conditions, allez quérir votre
				récompense et, après seulement, revenez me voir, nous discuterons de la suite des opérations ! </em>»
				<br /><br /><a href="' . $PHP_SELF . '?methode=non3"><strong>Je ne vois pas en quoi la chasse aux monstres
				a un rapport avec l’alchimie, vous n’êtes qu’un charlatan, j’ai tout intérêt à retourner à mes
				occupations habituelles. Au revoir, ou pas !</a></strong>
				<br /><br /><br /><a href="' . $PHP_SELF . '?methode=oui3"><strong>Oui ! Je me plierai à votre volonté. Je pars de ce pas ! </a></strong>
				<br /><br />';
            break;

        case "oui3":
            if ($comp_alchimie != 0)
            {
                $contenu_page .= 'Il vous est impossible de franchir cette étape une nouvelle fois compte tenu du fait que vous êtes déjà alchimiste
					<br />D’autre part, le moyen que vous avez utilisé pour y parvenir est totalement contraire à la charte du jeu !';
                break;
            }
            $contenu_page .= '- «<em> Bonne chance, j’attends impatiemment votre retour ! Néanmoins, et car notre Guilde sait que cet exercice est difficile et périlleux, vous pourrez retourner voir n’importe lequel des membres de notre guilde pour qu’il valide votre entrée dans notre confrérie. Bon courage</em> »
				<br /><br />';
            /*On met à jour la première étape de la quête*/
            $req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod,pquete_nombre,pquete_date_debut) 
				values ('14','$perso_cod',1,now())";
            $stmt = $pdo->query($req);
            break;

        case "acheter":
            $req_quete = "select perso_po from perso where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            $result = $stmt->fetch();
            $po = $result['perso_po'];
            if ($po < 1000)
            {
                $contenu_page .= '- «<em> N’essayez donc pas de m’arnarquer ! Assumez votre pauvreté et remplissez votre bourse avant de venir me voir !</em>»
					<br /><br />';
                break;
            }

            $contenu_page .= 'Voici un flacon pour vous ! Prenez en soin, et tachez d’en faire bon usage
				<br>Souhaitez vous en acheter un autre pour 1000 autres brouzoufs ? <a href="' . $PHP_SELF . '?methode=acheter"><strong>OUI !</strong></a><br><br>';
            $req_quete = "select cree_objet_perso(412,$perso_cod)";
            $stmt = $pdo->query($req_quete);
            $result = $stmt->fetch();
            $req_quete = "update perso set perso_po = perso_po - 1000 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            $result = $stmt->fetch();
            break;

        case "niv1":
            if ($comp_alchimie != '97' or $pourcent_alchimie < '90') // Controle des conditions de base pour accéder à cette nouvelle compétence
            {
                $contenu_page .= 'Vous n’avez rien à faire ici !<br><br>';
                break;
            }
            $req_quete = "select perso_po,perso_pa from perso where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            $result = $stmt->fetch();
            $po = $result['perso_po']; // Controle des brouzoufs
            if ($po < 10000)
            {
                $contenu_page .= '- «<em> N’essayez donc pas de m’arnarquer ! Assumez votre pauvreté et remplissez votre bourse avant de venir me voir !</em>»
					<br /><br />';
                break;
            }
            $pa = $result['perso_pa'];
            if ($pa < 6)
            {
                $contenu_page .= 'Vous n’avez pas suffisamment de PA pour réaliser cette action
					<br /><br />';
                break;
            }
            $pierres = array();
            $pluriel = array();
            $contenu_page .= '- «<em> ';
            //On Contrôle la présence de pierres précieuses et du nombre de chaque, avec celle rentrant dans le deal
            $req_quete = "select gobj_cod, coalesce(nombre, 0) as nombre
				from objet_generique
				left outer join (
					select obj_gobj_cod, count(obj_gobj_cod) as nombre
					from perso_objets
					inner join objets on obj_cod = perobj_obj_cod 
					where perobj_perso_cod = $perso_cod 
					group by obj_gobj_cod
					) t on obj_gobj_cod = gobj_cod
				where gobj_cod in (337, 354, 360, 359, 355, 357, 361, 338, 339, 358, 352, 353, 356, 340)";

            $stmt = $pdo->query($req_quete);
            while ($result = $stmt->fetch())
            {
                $objet = $result['gobj_cod'];
                $pierres[$objet] = $result['nombre'];
                $pluriel[$objet] = ($pierres[$objet] > 1) ? 's' : '';
            }
            $p1 = $pierres['361'] . ' ambre' . $pluriel['361'] . ', ';
            $p2 = $pierres['338'] . ' émeraude' . $pluriel['338'] . ', ';
            $p3 = $pierres['339'] . ' rubis, ';
            $p4 = $pierres['358'] . ' améthyste' . $pluriel['358'] . ', ';
            $p5 = $pierres['352'] . ' apophyllite' . $pluriel['352'] . ' diatropique' . $pluriel['352'] . ' et ';
            $p6 = $pierres['353'] . ' brazilianite' . $pluriel['353'] . ' épimystique' . $pluriel['353'];

            $contenu_page .= 'Vous avez ' . $p1 . $p2 . $p3 . $p4 . $p5 . $p6 . ' dans votre besace.';

            $contenu_page .= '<br>Pour conclure notre transaction, vous devez posséder un ambre, une émeraude, un rubis, une améthyste, une apophyllite diatropique, 
				et deux brazilianites épimystiques.<br>';
            if ($pierres['361'] < '1' or $pierres['338'] < '1' or $pierres['339'] < '1' or $pierres['358'] < '1' or $pierres['352'] < '1' or $pierres['353'] < '2')
            {
                $contenu_page .= '<br>Il vous manque par conséquent quelques pierres avant de pouvoir prétendre à mon apprentissage. Revenez donc me voir lorsque vous aurez toute ma commande.</em> »
					<br /><br />';
                break;
            } else
            {
                $contenu_page .= '<br>Je suis capable de faire votre apprentissage d’alchimiste confirmé<br><br></em>»
					<br /><br />';
            }
            //On supprime les pierres précieuses rentrant dans le deal
            $req_quete = "select f_del_objet_generique(361,$perso_cod),
				f_del_objet_generique(338,$perso_cod),
				f_del_objet_generique(339,$perso_cod),
				f_del_objet_generique(358,$perso_cod),
				f_del_objet_generique(352,$perso_cod),
				f_del_objet_generique(353,$perso_cod),
				f_del_objet_generique(353,$perso_cod)";
            $stmt = $pdo->query($req_quete);
            //On supprime les brouzoufs et les PA
            $req_quete = "update perso set perso_po = perso_po - 10000, perso_pa = perso_pa - 6 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            //On insère la nouvelle compétence, on supprime l'ancienne
            $req_comp = "insert into perso_competences (pcomp_modificateur,pcomp_perso_cod,pcomp_pcomp_cod) values('$pourcent_alchimie','$perso_cod','100')";
            $stmt = $pdo->query($req_comp);
            $req_comp = "delete from perso_competences where pcomp_pcomp_cod = 97 and pcomp_perso_cod = $perso_cod";
            $stmt = $pdo->query($req_comp);
            break;

        case "niv2":
            if ($comp_alchimie != '100' or $pourcent_alchimie < '100') // Controle des conditions de base pour accéder à cette nouvelle compétence
            {
                $contenu_page .= 'Vous n’avez rien à faire ici !<br><br>';
                break;
            }
            $req_quete = "select perso_po,perso_pa from perso where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            $result = $stmt->fetch();
            $po = $result['perso_po']; // Controle des brouzoufs
            if ($po < 20000)
            {
                $contenu_page .= '- «<em> N’essayez donc pas de m’arnarquer ! Assumez votre pauvreté et remplissez votre bourse avant de venir me voir !</em>»
					<br /><br />';
                break;
            }
            $pa = $result['perso_pa']; // Controle des PA
            if ($pa < 10)
            {
                $contenu_page .= 'Vous n’avez pas suffisamment de PA pour réaliser cette action
					<br /><br />';
                break;
            }
            $pierres = array();
            $pluriel = array();
            $contenu_page .= '- «<em> ';
            //On Contrôle la présence de pierres précieuses et du nombre de chaque, avec celle rentrant dans le deal
            $req_quete = "select gobj_cod, coalesce(nombre, 0) as nombre
				from objet_generique
				left outer join (
					select obj_gobj_cod, count(obj_gobj_cod) as nombre
					from perso_objets
					inner join objets on obj_cod = perobj_obj_cod 
					where perobj_perso_cod = $perso_cod 
					group by obj_gobj_cod
					) t on obj_gobj_cod = gobj_cod
				where gobj_cod in (337, 354, 360, 359, 355, 357, 361, 338, 339, 358, 352, 353, 356, 340)";

            $stmt = $pdo->query($req_quete);
            while ($result = $stmt->fetch())
            {
                $objet = $result['gobj_cod'];
                $pierres[$objet] = $result['nombre'];
                $pluriel[$objet] = ($pierres[$objet] > 1) ? 's' : '';
            }
            $p1 = $pierres['361'] . ' ambre' . $pluriel['361'] . ', ';
            $p2 = $pierres['338'] . ' émeraude' . $pluriel['338'] . ', ';
            $p3 = $pierres['340'] . ' saphir' . $pluriel['340'] . ', ';
            $p4 = $pierres['359'] . ' topaze' . $pluriel['359'] . ', ';
            $p5 = $pierres['355'] . ' dolomite' . $pluriel['355'] . ' hyporhombique' . $pluriel['355'] . ', ';
            $p6 = $pierres['357'] . ' obsidienne' . $pluriel['357'] . ', ';
            $p7 = $pierres['337'] . ' diamant' . $pluriel['337'] . ', ';
            $p8 = $pierres['354'] . ' cryptonite' . $pluriel['354'] . ' hémicaustique' . $pluriel['354'] . ' et ';
            $p9 = $pierres['360'] . ' jade' . $pluriel['360'] . ', ';

            $contenu_page .= 'Vous avez ' . $p1 . $p2 . $p3 . $p4 . $p5 . $p6 . $p7 . $p8 . $p9 . ' dans votre besace.';

            $contenu_page .= '<br>Pour conclure notre transaction, vous devez posséder deux diamants, une cryptonite hémicaustique, un jade, deux topazes, une dolomite hyporhombique, une obsidienne, un ambre, une émeraude, un saphir<br>';
            if ($pierres['361'] < '1' or $pierres['338'] < '1' or $pierres['340'] < '1' or $pierres['359'] < '2' or $pierres['355'] < '1' or $pierres['357'] < '1' or $pierres['337'] < '2' or $pierres['354'] < '1' or $pierres['360'] < '1')
            {
                $contenu_page .= '<br>Il vous manque par conséquent quelques pierres avant de pouvoir prétendre à mon apprentissage. Revenez donc me voir lorsque vous aurez toute ma commande.</em> »
					<br /><br />';
                break;
            } else
            {
                $contenu_page .= '<br>Je suis capable de faire votre apprentissage d’alchimiste confirmé<br><br></em> »
					<br /><br />';
            }
            //On supprime les pierres précieuses rentrant dans le deal
            $req_quete = "select f_del_objet_generique(361,$perso_cod), 
				f_del_objet_generique(338,$perso_cod),
				f_del_objet_generique(340,$perso_cod),
				f_del_objet_generique(359,$perso_cod),
				f_del_objet_generique(359,$perso_cod),
				f_del_objet_generique(355,$perso_cod),
				f_del_objet_generique(357,$perso_cod),
				f_del_objet_generique(337,$perso_cod),
				f_del_objet_generique(354,$perso_cod),
				f_del_objet_generique(360,$perso_cod)";
            $stmt = $pdo->query($req_quete);
            //On supprime les brouzoufs et les PA
            $req_quete = "update perso set perso_po = perso_po - 20000,perso_pa = perso_pa - 10 where perso_cod = $perso_cod";
            $stmt = $pdo->query($req_quete);
            //On insère la nouvelle compétence, on supprime l'ancienne
            $req_comp = "insert into perso_competences (pcomp_modificateur,pcomp_perso_cod,pcomp_pcomp_cod) values('$pourcent_alchimie','$perso_cod','101')";
            $stmt = $pdo->query($req_comp);
            $req_comp = "delete from perso_competences where pcomp_pcomp_cod = 100 and pcomp_perso_cod = $perso_cod";
            $stmt = $pdo->query($req_comp);
            break;
    }
}
echo $contenu_page;
