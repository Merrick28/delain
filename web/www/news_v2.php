<?php
ob_start();
include "classes.php";
//
// DEBUT PUBLICITES
//
//require G_CHE . "choix_pub.php";
require G_CHE . "choix_pub.php";
$publicite = choix_pub_index();
/// FIN PUBLICITES

$db        = new base_delain;
$recherche = "SELECT gmon_avatar FROM monstre_generique where gmon_avatar is not null and gmon_avatar != 'defaut.png' order by random() limit 1";
$db->query($recherche);
$db->next_record();
$image     = $db->f("gmon_avatar");
?>
<div class="bordiv" style="margin:2px;text-align:center">
    <table>
        <td colspan="4">		
        </td>
        <td>
            <p style="text-align:center;"><?php echo $publicite; ?>
        <td>
    </table>
</div>

<div class="bordiv"  style="margin:2px;text-align:center">
    <table>
        <td><img src="<?php echo $type_flux . G_URL; ?>logo_delain.gif"></td>	
        <td style="width:800px;"><i><center><hr><b>Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain... Ou plutôt devrait-on dire... sous les terres.</b><br>
                    Là où s’éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l’assaut des extérieurs...
                    Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Pourras-tu protéger les contrées extérieures de ce mal grandissant ?
                    <br><b>Entre, et trouve par toi-même les réponses à ces questions.</b>
                    <br />Mais attention : aujourd’hui, dans les souterrains, <?php echo $db->getparm_n(64); ?> aventuriers, <?php echo $db->getparm_n(65); ?> monstres et <?php echo $db->getparm_n(66); ?> familiers sont morts au combat...</center></i><hr></td>
        <td>
            <?php
            $nb_pub    = rand(1, 6);
            if ($nb_pub == 1)
            {
                ?>
                <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FLes-souterrains-de-Delain%2F185893811435175&amp;width=292&amp;colorscheme=dark&amp;show_faces=false&amp;stream=false&amp;header=false&amp;height=62" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
                <?php
            }
            elseif ($nb_pub == 2)
            {
                ?>
            <g:plus href="https://plus.google.com/107447277968158531530" size="badge"></g:plus>
            <?php
        }
        else
        {
            ?>
            <img src="http://images.jdr-delain.net/avatars/<?php echo $image ?>" style="max-width: 200px; max-height: 200px;">
            <?php
        }
        ?>
        </td>

    </table>
    <br><b>Les souterrains de Delain</b> est un jeu de rôles en ligne, où vous pouvez incarner jusqu’à 3 aventuriers. Il se déroule en tour par tour, et les seuls outils nécessaires pour y jouer sont une connexion internet, un navigateur, et une adresse mail.
    Pour plus d’infos : <a href="aide.php">comment débuter</a>, <a href="regles.php">les règles</a>, et <a href="faq_v2.php">la faq</a>.<br><br>
</div>

<?php
if (!isset($start_news))
{
    $req_concours = "select ccol_cod, ccol_titre, ccol_gobj_cod, to_char(ccol_date_fermeture,'DD/MM/YYYY') as ccol_date_fermeture, ccol_description
    				from concours_collections
    				where CURRENT_DATE between ccol_date_ouverture and ccol_date_fermeture + '14 days'::interval
    				order by ccol_cod";
    $db->query($req_concours);
    if ($db->nf() > 0)
    {
        echo '<div class="bordiv" style="margin:2px;text-align:left; "><div class="titre">Classement des concours en cours !</div>';
        echo '<script type="text/javascript">
    		function change_division(tableau, nom)
    		{
    			for (var i = 0; i < tableau.length; i++)
    			{
    				if (tableau[i] == nom)
    					document.getElementById(nom).style.display = "";
    				else
    					document.getElementById(tableau[i]).style.display = "none";
    			}
    		}
    		</script>';
        echo '<table><tr>';
        $db2 = new base_delain();
        while ($db->next_record())
        {
            $ccol_cod = $db->f('ccol_cod');

            echo '<td padding="3">';

            $req              = "SELECT DISTINCT case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division FROM concours_collections_resultats WHERE ccolres_ccol_cod = $ccol_cod
    			ORDER BY case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division";
            $db2->query($req);
            $nombre_divisions = $db2->nf();
            $tr_divisions     = '';
            $script_divisions = '<script type="text/javascript">var divisions' . $ccol_cod . ' = new Array();';
            $script_division1 = '';
            $i                = 0;
            if ($nombre_divisions > 1)
            {
                $tr_divisions = '<tr><td class="soustitre2" colspan="2">Classements : ';
                while ($db2->next_record())
                {
                    $code             = $ccol_cod . '|' . $db2->f('ccolres_division');
                    $tr_divisions .= "<span onclick=\"javascript:change_division(divisions$ccol_cod, '$code')\">#g1#" . $db2->f('ccolres_division') . '#g2#</span>&nbsp; &nbsp;';
                    $script_divisions .= "divisions$ccol_cod " . "[$i] = '$ccol_cod|" . $db2->f('ccolres_division') . "';";
                    if ($i == 0)
                        $script_division1 = "change_division(divisions$ccol_cod, '$code');";
                    $i++;
                }
                $tr_divisions .= '</td></tr>';
            }
            $script_divisions .= $script_division1 . '</script>';
            $style_div   = ($nombre_divisions > 1) ? ' style="display:none;"' : '';
            $debut_table = '<table id="' . $ccol_cod . '|#id#"' . $style_div . '><tr><th class="titre" colspan="2">' . $db->f('ccol_titre') . '</th></tr>';
            $debut_table .= '<tr><td class="soustitre2" colspan="2">' . $db->f('ccol_description') . '<p>Ce classement sera ouvert jusqu’au ' . $db->f('ccol_date_fermeture') . '</p></td></tr>';
            $debut_table .= $tr_divisions;
            $debut_table .= '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
            $fin_table   = '</table>';


            $req               = "select coalesce(perso_nom, 'Aventurier disparu') as perso_nom, ccolres_nombre, ccolres_division from concours_collections_resultats
    			left outer join perso on perso_cod = ccolres_perso_cod
    			where ccolres_ccol_cod = $ccol_cod
    			order by case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division, ccolres_nombre desc";
            $db2->query($req);
            $division_en_cours = -1;

            while ($db2->next_record())
            {
                if ($db2->f('ccolres_division') != $division_en_cours)
                {
                    if ($division_en_cours != -1)
                        echo $fin_table;
                    $txt_table         = str_replace('#id#', $db2->f('ccolres_division'), $debut_table);
                    $txt_table         = str_replace('#g1#' . $db2->f('ccolres_division') . '#g2#', '<b>' . $db2->f('ccolres_division') . '</b>', $txt_table);
                    $txt_table         = str_replace('#g1#', '', $txt_table);
                    $txt_table         = str_replace('#g2#', '', $txt_table);
                    echo $txt_table;
                    $division_en_cours = $db2->f('ccolres_division');
                }
                $nom    = $db2->f('perso_nom');
                $nombre = $db2->f('ccolres_nombre');
                echo "<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
            }
            echo $fin_table;
            echo $script_divisions;
            echo '</td>';
        }
        echo '</tr></table></div>';
    }

    $start_news = 0;
}
if ($start_news < 0)
{
    $start_news = 0;
}

if (!preg_match('/^[0-9]*$/i', $start_news))
{
    echo "<p>Anomalie sur Offset !";
    exit();
}
$max_news   = "SELECT count(*) as c FROM news";
$db->query($max_news);
$db->next_record();
$max_offset = $db->f('c');

$recherche = "SELECT news_cod,news_titre,news_texte,to_char(news_date,'DD/MM/YYYY') as date_news,news_auteur,news_mail_auteur FROM news order by news_cod desc limit 5 offset $start_news";
$db->query($recherche);
$cpt       = 0;
while ($db->next_record())
{
    $cpt = $cpt + 1;

    $titre_news       = $db->f("news_titre");
    $texte_news       = $db->f("news_texte");
    $date_news        = $db->f("date_news");
    $auteur_news      = $db->f("news_auteur");
    $mail_auteur_news = $db->f("news_mail_auteur");
    /* if ($cpt == 2)
      {
      $nb_pub = rand(1,5);
      if ($nb_pub == 1)
      {
      Bordure_Tab();
      ?>
      <center><script language='JavaScript' src='http://www.clicjeux.net/banniere.php?id=390'></script></center>
      <?
      }

      } */
    echo '<div class="bordiv" style="margin:2px;text-align:center"><div class="titre">' . $titre_news . '</div>';
    ?>

    <div class="texteNorm" style="text-align:right;">
        <?php echo $date_news ?>
    </div>
    <div class="texteNorm" style="text-align:left;">
        <?php echo $texte_news ?>
    </div>
    <div class="texteNorm" style="text-align:right;">
        <?php
        if ($mail_auteur_news != "")
        {
            echo "<a href=\"mailto:$mail_auteur_news\">$auteur_news</a>";
        }
        else
        {
            echo $auteur_news;
        }
        ?>
    </div></div>
    <?php
}

$suite = $start_news + 5;
$prec  = $start_news - 5;
if ($start_news != 0)
{
    echo "<a href=\"?start_news=$prec\"><== nouvelles plus récentes</a>
        <img src=\"" . G_IMAGES . "del.gif\" width=\"50\" height=\"1\" />
        --
        <img src=\"" . G_IMAGES . "del.gif\" width=\"50\" height=\"1\" />";
    if ($suite <= $max_offset)
    {
        echo "<a href=\"?start_news=$suite\">nouvelles plus anciennes ==></a>";
    }
}
else
{
    echo "<a href=\"?start_news=$suite\">nouvelles plus anciennes ==></a>";
}
echo ' 
        <hr /><p class="texteNorm" style="text-align:right;">
Conception et hébergement par <a href="http://www.sdewitte.net/hebergement" target="_blank">sdewitte.net</a>
</p>';

$contenu_page = ob_get_contents();
ob_end_clean();
?>
