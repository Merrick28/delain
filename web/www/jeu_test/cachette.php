<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
// comment
//Premier test : vérification si le joueur a bien vu la cachette
$req    = "select persocache_cache_cod
					from cachettes_perso,cachettes,perso_position
					where ppos_perso_cod = :perso_cod
					and ppos_perso_cod = persocache_perso_cod
					and persocache_cache_cod = cache_cod	
					and cache_pos_cod = ppos_pos_cod";
$stmt   = $pdo->prepare($req);
$stmt   = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
$result = $stmt->fetch();
if ($stmt->rowCount() == 0)
{
    echo 'Vous continuez à chercher sans grand espoir ! Vos efforts restent vains ...';

} else
{
    //Deuxième test : vérification de la position
    // On recherche la position du perso pour voir si correspondance avec une cachette
    $pos      = $perso->get_position();
    $position = $pos['pos']->pos_cod;
    // On récupère les infos générique pour les afficher
    $req_cache = "select cache_nom,cache_desc,cache_image,cache_cod,cache_fonction from cachettes
																	where cache_pos_cod = $position";
    $stmt      = $pdo->query($req_cache);
    $result    = $stmt->fetch();
    $nom       = $result['cache_nom'];
    $desc      = $result['cache_desc'];
    $image     = $result['cache_image'];
    $cache     = $result['cache_cod'];
    $fonction  = $result['cache_fonction'];
    if ($stmt->rowCount() == 0)
    {
        echo 'Vous cherchez à accéder à une page qui n\'existe pas !';
    } else
    {
        $methode = get_request_var('methode', 'debut');
        //début 2nd tableau

        switch ($methode)
        {
            case "debut":

                ?>
                <table width="100%" cellspacing="2" cellapdding="2">
                    <form name="ramasser" method="post" action="cachette.php">
                        <input type="hidden" name="methode" value="suite">


                        <?php

                        if ($nom != '')
                        {
                            echo("<tr><td colspan=\"3\" class=\"titre\"><p class='centrer'>" . $nom . "</p></td></tr>");
                        }
                        ?>
                        <td>
                            <hr>
                            <em> Vous venez de tomber sur une cachette encore inviolée. Réjouissez vous, ou méfiez vous.
                                Certaines trouvailles ne sont pas toujours bonnes à exploiter ...</em><br>

                            <?php if ($fonction != '')
                            {
                                $fonction = str_replace('[perso]', $perso_cod, $fonction);
                                $req      = "select $fonction as resultat";
                                $stmt     = $pdo->query($req);
                                $result   = $stmt->fetch();
                                echo '<br>' . $result['resultat'];
                            }
                            if ($desc != '')
                            {
                                echo("<br><p\">" . $desc . "</p></td><br>");
                            }
                            if ($image != '')
                            {
                                echo("<td><br><p\"><img src=\"../avatars/" . $image . "\"></p></td>");
                            }
                            ?>
                            <hr>
                            <tr>
                                <td colspan="3" class="soustitre"><p class="soustitre">Objets cachés</p></td>
                            </tr>


                            <?php
                            //******************************************
                            //            O B J E T S                 **
                            //******************************************
                            // On recherche les objets dans la cachette
                            $req = "select obj_nom_generique,tobj_libelle,obj_cod,obj_nom,objcache_cod_cache_cod,objcache_obj_cod 
															from objet_generique,type_objet,objets,cachettes_objets,cachettes
															where cache_pos_cod = $position
															and cache_cod = objcache_cod_cache_cod
															and obj_cod = objcache_obj_cod
															and obj_gobj_cod = gobj_cod
															and gobj_tobj_cod = tobj_cod
															order by tobj_libelle";
                            $stmt = $pdo->query($req);


                            // on affiche la ligne d'en tête objets
                            ?>
                            <tr>
                                <td class="soustitre2"><p><strong>Nom</strong></p></td>
                                <td class="soustitre2"><p><strong>Type objet</strong></p></td>
                                <td class="soustitre2"></td>
                            </tr>

                            <?php
                            if ($stmt->rowCount() != 0)
                            {

                                $nb_objets = 1;
                                // on boucle sur les objets dans la cachette
                                while ($result = $stmt->fetch())
                                {
                                    echo("<tr>");
                                    $objet = $result['obj_cod'];
                                    echo "<td class=\"soustitre2\"><p><strong>" . $result['obj_nom_generique'] . "</strong></p></td>";
                                    echo "<td><p>" . $result['tobj_libelle'] . "</p></td>";
                                    echo "<td><p><input type=\"checkbox\" class=\"vide\" name=\"objet[" . $result['obj_cod'] . "]\" value=\"0\"></p></td>";
                                    echo "</tr>";
                                }
                            } else
                            {
                                echo "Aucun objet dans la cachette !";
                            }
                            ?>
                </table>
                <input type="submit" class="test centrer" value="Récupérer les objets cochés !">
                </form>
                <?php
                break;

            case "suite":
                $pa     = $perso->perso_pa;
                $erreur = 0;
                $total  = 0;
                if ($_REQUEST['objet'])
                {
                    foreach ($_REQUEST['objet'] as $key => $val)
                    {
                        $total = $total + 1;
                    }
                }
                if ($pa < $total)
                {
                    echo "<p>Vous n'avez pas assez de PA pour récupérer tous ces objets ! Certains devront rester dans la cachette.";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    if ($_REQUEST['objet'])
                    {
                        foreach ($_REQUEST['objet'] as $key => $val)
                        {
                            $req_ramasser = "select ramasse_objet_cachette(:perso_cod,:key) as resultat";
                            $stmt         = $pdo->prepare($req_ramasser);
                            $stmt         = $pdo->execute(array(":perso_cod" => $perso_cod, ":key" => $key), $stmt);
                            $result       = $stmt->fetch();
                            echo $result['resultat'];
                        }
                    }
                }
                break;
        }
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
