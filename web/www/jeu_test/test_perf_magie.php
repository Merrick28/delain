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

$db_detail    = new base_delain;
//
//Contenu de la div de droite
//
$contenu_page = '';

// -------------------------
// debut sorts rune
// -------------------------
$contenu_page .= '<h3>Début test ancienne méthode</h3><table>';
$debut = time();
for ($compteurBoucle = 0; $compteurBoucle < 10; $compteurBoucle++)
{
    for ($famille = 1; $famille < 7; $famille++)
    {
        $req_famille = "select frune_desc from rune_famille where frune_cod = $famille order by frune_cod";
        $db->query($req_famille);
        $db->next_record();
        $contenu_page .= '<tr>
			<td class="soustitre2"><div style="text-align:center;">Famille : ' . $db->f("frune_desc") . '</div></td>
			</tr>
			<tr>
			<td>';

        $req_rune = 'select gobj_cod,gobj_rune_position,gobj_nom from objet_generique where gobj_tobj_cod = 5
				and gobj_frune_cod = ' . $famille . '
				order by gobj_rune_position ';
        $db->query($req_rune);
        $contenu_page .= '<div style="text-align:center;"><input type="radio" class="vide" name="fam_' . $famille . '" value="0" id="fam_' . $famille . '_0" checked><label for="fam_' . $famille . '_0">Aucune rune de cette famille</label></div>
			<center><table><tr>';
        while ($db->next_record())
        {
            $perso_rune = 'select count(*) as nombre from perso_objets,objets
					where perobj_perso_cod = ' . $perso_cod . '
					and perobj_obj_cod = obj_cod
					and obj_gobj_cod = ' . $db->f("gobj_cod");
            $db_detail->query($perso_rune);
            $db_detail->next_record();

            // Construction du label qui permet de cliquer sur la rune plutôt que sur le petit bouton rond...
            $labelDebut = "";
            $labelFin   = "";
            $labelId    = "";
            if ($db_detail->f("nombre") != 0)
            {
                $labelId    = 'fam_' . $famille . '_' . $db->f("gobj_rune_position");
                $labelDebut = '<label for="' . $labelId . '">';
                $labelFin   = "</label>";
            }

            $contenu_page .= '
				<td><table><tr>
				<td><center>' . $labelDebut . '<img src="' . G_IMAGES . 'rune_' . $famille . '_' . $db->f("gobj_rune_position") . '.gif" alt="">' . $labelFin . '</center>
				</td></tr><tr><td>';
            if ($db_detail->f("nombre") != 0)
                $contenu_page .= '<input type="radio" class="vide" name="fam_' . $famille . '" value="' . $db->f("gobj_rune_position") . '" id="' . $labelId . '">';
            $contenu_page .= $db->f("gobj_nom") . ' <i>(' . $db_detail->f("nombre") . ')</i>
					</td>
					</tr>
					</table>
					</td>';
        }
        $contenu_page .= '</tr></table></center>';
    }
}
$contenu_page .= '</table>';
$secondes = time() - $debut;
$contenu_page .= "Calculs réalisés en $secondes secondes";
$contenu_page .= '<h3>Début test nouvelle méthode</h3><table>';
$debut    = time();

for ($compteurBoucle = 0; $compteurBoucle < 10; $compteurBoucle++)
{
    $famille   = 0;
    $req_runes = "SELECT f.frune_desc, f.frune_cod, r.gobj_rune_position, r.gobj_nom, COALESCE(i.nombre, 0) AS nombre
				FROM rune_famille f
				INNER JOIN objet_generique r ON r.gobj_tobj_cod = 5 AND r.gobj_frune_cod = f.frune_cod
				LEFT OUTER JOIN (
					SELECT count(*) as nombre, obj_gobj_cod
					FROM objets o
					INNER JOIN objet_generique r2 ON r2.gobj_tobj_cod = 5 AND r2.gobj_cod = o.obj_gobj_cod
					INNER JOIN perso_objets p ON p.perobj_obj_cod = o.obj_cod
					WHERE perobj_perso_cod = $perso_cod
					GROUP BY obj_gobj_cod
					) i ON i.obj_gobj_cod = r.gobj_cod
				ORDER BY f.frune_cod, r.gobj_rune_position";
    $db->query($req_runes);

    while ($db->next_record())
    {
        // Si on arrive sur une nouvelle famille, on met l'en-tête, et, le cas échéant, on clôt la table précédente
        if ($famille != $db->f("frune_cod"))
        {
            if ($famille != 0)
                $contenu_page .= '</tr></table></center>';

            $famille = $db->f("frune_cod");
            $contenu_page .= '<tr>
					<td class="soustitre2"><div style="text-align:center;">Famille : ' . $db->f("frune_desc") . '</div></td>
					</tr>
					<tr>
					<td>';
            $contenu_page .= '<div style="text-align:center;"><input type="radio" class="vide" name="fam_' . $famille . '" value="0" id="fam_' . $famille . '_0" checked><label for="fam_' . $famille . '_0">Aucune rune de cette famille</label></div>
					<center><table><tr>';
        }

        // Construction du label qui permet de cliquer sur la rune plutôt que sur le petit bouton rond...
        $labelDebut = "";
        $labelFin   = "";
        $labelId    = "";
        if ($db->f("nombre") != 0)
        {
            $labelId    = 'fam_' . $famille . '_' . $db->f("gobj_rune_position");
            $labelDebut = '<label for="' . $labelId . '">';
            $labelFin   = "</label>";
        }

        $contenu_page .= '
				<td><table><tr>
				<td><center>' . $labelDebut . '<img src="' . G_IMAGES . 'rune_' . $famille . '_' . $db->f("gobj_rune_position") . '.gif" alt="">' . $labelFin . '</center>
				</td></tr><tr><td>';
        if ($db->f("nombre") != 0)
            $contenu_page .= '<input type="radio" class="vide" name="fam_' . $famille . '" value="' . $db->f("gobj_rune_position") . '" id="' . $labelId . '">';
        $contenu_page .= $db->f("gobj_nom") . ' <i>(' . $db->f("nombre") . ')</i>
					</td>
					</tr>
					</table>
					</td>';
    }
    $contenu_page .= '</tr></table></center>';
}

$contenu_page .= '</table>';
$secondes = time() - $debut;
$contenu_page .= "Calculs réalisés en $secondes secondes";

$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse("Sortie", "FileRef");
$t->p("Sortie");
?>
?>
