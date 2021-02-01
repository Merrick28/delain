<?php
// gestion des quêtes sur les dispensaires.

$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

$methode2 = get_request_var('methode2', 'debut');


$perso        = new perso;
$perso        = $verif_connexion->perso;

switch ($methode2)
{
    case "debut":
        // à modifier dans le cas où une quête nécessite un test sur un type d’objet particulier
        // 373 == Plaques miroirs.
        // 374, 376 == objets de test, non utilisés pour le moment.
        $req = "select obj_gobj_cod,perobj_obj_cod,obj_nom
			from objets,perso_objets
			where perobj_obj_cod = obj_cod
				and perobj_perso_cod = $perso_cod
				and perobj_identifie = 'O'
				and obj_gobj_cod in (373, 374, 376) order by obj_gobj_cod";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $obj_gen_quete = $result['obj_gobj_cod'];
            $obj_quete     = $result['perobj_obj_cod'];
            $obj_nom       = $result['obj_nom'];
            if ($obj_gen_quete == 373)
            {
                //Quête du collectionneur : cette quête a pour vocation de faire ramener un objet rare,
                // droppé par les capitaines morbelins ou trouvé dans des cachettes
                ?>
                <form name="cede" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <p>En arrivant dans le dispensaire, un guérisseur vous interpelle :
                        <br><em>Je crois que vous possédez un objet qui pourrait intéresser l’un des membres de notre
                            communauté.
                            Il s’agit d’une <?php echo $obj_nom; ?> .
                            <br>Seriez vous prêt à me la céder, pour que je la lui remette ? Dans un tel cas, je vous
                            rétribuerais pour cela.</em>
                        <br><br>Le guérisseur attend alors votre réponse...<br>
                        <input type="hidden" name="methode2" value="cede_objet1">
                        <table>
                            <tr>
                                <td class="soustitre2">
                    <p>Volontiers ! Cela m’encombrait plus qu’autre chose.</p></td>
                    <td><input type="radio" class="vide" name="controle1" value="cede_objet1"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2"><p>Non, je préfère le garder, sait-on jamais, il pourrait me servir plus
                                tard ...</td>
                        <td><input type="radio" class="vide" name="controle1" value="non"></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" class="vide" name="obj_quete" value="<?php echo $obj_quete; ?>"></td>
                    </tr>
                    </table>
                    <input type="submit" class="test" value="Valider !">
                </form>
            <?php }
        }

        // Cas des écailles de basilic
        $nb_ecailles = $perso->compte_objet(182);
        if ($nb_ecailles >= 10)
        {
            // Quête des écailles de basilic contre des parchemins.
            // Cette quête permet d’échanger 10 écailles de basilic contre deux parchemins
            // Particularité : posséder 10 écailles minimum, et arriver avant le 16 du mois
            $jour = date('d');
            if ($jour < 16)
            {
                // Pour la réutilisabilité : formulaire à modifier en fonction de l’objet, notamment phrase à mettre en contexte
                ?>
                <form name="cede" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <p>Un soignant en chef du dispensaire s’approche de vous :
                        <br><em>Je vois que vous êtes en possession d’écailles de basilic !
                            Celles-ci nous sont fort utile pour réaliser certaines de nos préparations.
                            Pourriez-vous nous les céder ? Nous vous en prendrons dix.</em></p>
                    <input type="hidden" name="methode2" value="cede_objet2">
                    <table>
                        <tr>
                            <td><input type="radio" class="vide" name="controle2" value="cede_objet2"></td>
                            <td class="soustitre2"><p>Volontiers ! Cela m’encombrait plus qu’autre chose.</td>
                        </tr>
                        <tr>
                            <td><input type="radio" class="vide" name="controle2" value="non"></td>
                            <td class="soustitre2"><p>Non, je préfère le garder, sait-on jamais, elles pourraient me
                                    servir plus tard...</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="hidden" class="vide" name="obj_gen_quete" value="182"></td>
                        </tr>
                    </table>
                    <input type="submit" class="test" value="Valider !">
                </form>
            <?php } else
            {
                echo '<p>Un guérisseur du dispensaire vous interpelle : 
					<br><em>Je vois que vous possédez des écailles de basilic !
					Elles nous servent parfois pour faire nos préparations pour soigner nos patients.
					<br>Mais actuellement, nous n’en avons pas besoin.
					Revenez un autre jour, nous pourrons sûrement faire affaire !</em></p>';
            }
        } else
        {
            echo "Vous ne possédez pas suffisamment d’écailles de basilic pour nous intéresser.
				<br>Nos préparations en demandent un certain nombre, et nous ne les reprenons que par dizaines.";
        }
        break;

    // Résultat des quêtes des dispensaires
    // Quête du collectionneur
    case "cede_objet1":
        $cede_objet = $_POST['controle1'];
        if ($cede_objet == 'cede_objet1')
        {
            ?>
            <br>Quel choix judicieux. Je vous remercie de m’avoir cédé cet objet. Je vais le donner à notre collectionneur !
            <br>Pour votre peine, je vais puiser dans mes richesses et vous donner 3000 brouzoufs.
            <?php $req = "select f_del_objet($obj_quete)";
            $stmt      = $pdo->query($req);

            $perso->perso_po = $perso->perso_po + 3000;
            $perso->stocke();


        } else if ($cede_objet == 'non')
        {
            echo '<br>Ceci est particulièrement dommage. Vous auriez pu en retirer tant d’avantages...';
        } // on gère le cas où il y a risque de triche
        else
        {
            echo '<br>Pensez vous que ce soit bien malin de tenter de tricher ?';
        }
        break;

    // Quête des écailles
    case "cede_objet2":
        $cede_objet = $_POST['controle2'];
        if ($cede_objet == 'cede_objet2')
        {
            ?>
            <br>« Nous vous remercions pour votre geste. Cela nous permettra sûrement de sauver de nombreux patients
            <br>Pour marquer votre offrande, nous allons vous offrir un petit quelque chose au regard de nos maigres moyens. »
            <br><em>En regardant à nouveau votre inventaire, vous pouvez voir que deux parchemins s’y sont glissés,
            ainsi que des brouzoufs. Un certain sentiment de fierté pour cette bonne action vous envahit</em>
            (vous gagnez 20 pxs pour cette action)
            <?php
            // On crée deux parchemins différents dans l’inventaire du perso
            $req  = "select cree_objet_perso_nombre(362 + lancer_des(1, 6), $perso_cod, 1) as parcho1,
				cree_objet_perso_nombre(362 + lancer_des(1, 6), $perso_cod, 1) as parcho2 ";
            $stmt = $pdo->query($req);

            $req    = "select vente_ecailles($perso_cod, $obj_gen_quete)";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
        } else if ($cede_objet == 'non')
        {
            ?>
            <br>Nous sommes navrés de ce choix. De nombreux patients risquent de mourir du fait de votre geste...
            <?php
        } else // on gère le cas où il y a risque de triche
        {
            ?>
            <br>Pensez vous que ce soit bien malin de tenter de tricher ?
            <?php
        }
        break;
}
?>