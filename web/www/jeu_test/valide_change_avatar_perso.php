<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
if ($suppr == 0)
{
    $tab_nom     = explode(".", $_FILES['avatar']['name']);
    $nb_nom      = count($tab_nom);
    $n_extension = $nb_nom - 1;
    $extension   = $tab_nom[$n_extension];
    if (($extension != 'png') && ($extension != 'bmp') && ($extension != 'jpg') && ($extension != 'gif'))
    {
        echo("<p>Le format n’est pas supporté !");
        $valide = 1;
    } else
    {
        $valide = 0;
    }
    if ($valide == 0)
    {
        $uploaddir = G_CHE . "avatars/";

        if (!is_uploaded_file($_FILES['avatar']['tmp_name']))
        {
            echo '<br>problème lors de l’upload initial.';
            echo '<br>Infos debug : ' . $_FILES['avatar']['tmp_name'];
            print_r($_FILES);
        }
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploaddir . $perso_cod . "." . $extension))
        {
            $erreur   = 0;
            $test_mod = chmod(G_CHE . 'avatars/' . $perso_cod . '.' . $extension, 0777);
            if (!$test_mod)
                echo '<!-- probleme de droits -->';
        } else
        {
            echo '<br>Problème lors du renommage de fichiers. Opération interrompue.';
            $erreur = 1;
        }
        if ($erreur == 0)
        {
            $perso->perso_avatar         = $perso_cod . "." . $extension;
            $perso->perso_avatar_version = $perso->perso_avatar_version + 1;
            $perso->stocke();

            echo "<p>Votre avatar est enregistré !";
        }
    } else
    {
        echo "<p>Une erreur est survenue pendant l’upload !";
        print_r($_FILES);
    }
} else
{

    $perso->perso_avatar = null;
    $perso->stocke();
    echo("<p>Votre avatar a bien été effacé !");

}
echo "<p><a href=\"perso2.php\">Retour !</a>";
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";