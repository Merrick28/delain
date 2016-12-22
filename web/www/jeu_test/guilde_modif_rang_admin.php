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
$contenu_page = '';
ob_start();
if (!isset($methode))
{
    $methode = 'debut';
}
if ($db->is_admin_guilde($perso_cod))
{
    $req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod from guilde,guilde_perso,guilde_rang
												where pguilde_perso_cod = $perso_cod
												and pguilde_guilde_cod = guilde_cod
												and rguilde_guilde_cod = guilde_cod
												and rguilde_rang_cod = pguilde_rang_cod
												and pguilde_valide = 'O' ";
    $db->query($req_guilde);
    $db->next_record();
    $num_guilde = $db->f("guilde_cod");
    switch ($methode)
    {
        case "debut":
            $admin['O'] = 'Administrateur';
            $admin['N'] = 'Membre';
            $req        = "select rguilde_libelle_rang,rguilde_rang_cod,rguilde_cod,rguilde_admin
									 		from guilde_rang
									 		where rguilde_guilde_cod = $num_guilde
									 		order by rguilde_cod ";
            echo "<form name=\"modif\" method=\"post\" action=\"guilde_modif_rang.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"suite\">";
            echo "<input type=\"hidden\" name=\"vperso\" value=\"$vperso\">";
            echo "<p>Choisissez le rang à affecter :";
            echo "<select name=\"rang\">";
            $db->query($req);
            while ($db->next_record())
            {
                echo "<option value=\"", $db->f("rguilde_rang_cod"), "\">", $db->f("rguilde_libelle_rang"), "</option>";
            }
            echo "</select><br><center><input type=\"submit\" class=\"test\" value=\"Valider !\"><center>";
            echo "</form>";
            break;
        case "suite":
            // on compte le nombre d'admin, si >2, alors un admin peut changer son rang pour devenir simple membre
            $req          = "select count(pguilde_perso_cod) as nombre from guilde_perso,guilde_rang
										where pguilde_guilde_cod = 
												(select pguilde_guilde_cod from guilde_perso,guilde_rang
														where pguilde_perso_cod = $vperso
														and pguilde_guilde_cod = rguilde_guilde_cod
														and pguilde_rang_cod = rguilde_rang_cod) 
										and pguilde_guilde_cod = rguilde_guilde_cod
										and pguilde_rang_cod = rguilde_rang_cod
										and rguilde_admin = 'O'";
            $db->query($req);
            $db->next_record();
            $nombre_admin = $db->f("nombre");

            // on recherche le rang d'admin précédent			
            $req = "select rguilde_admin from guilde_perso,guilde_rang
										where pguilde_perso_cod = $vperso
										and pguilde_guilde_cod = rguilde_guilde_cod
										and pguilde_rang_cod = rguilde_rang_cod ";
            $db->query($req);
            $db->next_record();
            if ($db->f("rguilde_admin") == 'O')
            {
                // on recherche le nouveau rang admin
                $req = "select rguilde_admin from guilde_rang 
											where rguilde_guilde_cod = $num_guilde
											and rguilde_rang_cod =$rang";
                $db->query($req);
                $db->next_record();
                if ($vperso == $perso_cod)
                {
                    if ($db->f("rguilde_admin") != 'O' and $nombre_admin <= 1)
                    {
                        echo "<p>Erreur ! Le nouveau rang n'est pas un rang <b>administrateur</b>, alors que vous êtes actuellement <b>le seul administrateur</b> de cette guilde !";
                    }
                    else if ($db->f("rguilde_admin") != 'O' and $nombre_admin > 1)
                    {
                        $req = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $db->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié. Vous étiez <b>administrateur</b> de cette guilde, et vous êtes redevenu simple <b>membre</b>.";
                    }
                    else /* Le nouveau rang est un rang admin */
                    {
                        $req = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $db->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié.";
                    }
                }
                else
                {
                    if ($db->f("rguilde_admin") != 'O')
                    {
                        echo "<p>Erreur ! Le nouveau rang n'est pas un rang <b>administrateur</b>. Vous ne pouvez pas dégrader un administrateur, seul lui peut se changer de rang !";
                    }
                    else
                    {
                        $req = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $db->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié.";
                    }
                }
            }
            else
            {
                $req = "update guilde_perso set pguilde_rang_cod = $rang
												where pguilde_perso_cod = $vperso ";
                $db->query($req);
                echo "<p>Le rang de l'utilisateur à été modifié.";
            }
            break;
    }
}
else
{
    echo "<p>Vous n'êtes pas un administrateur de guilde !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');