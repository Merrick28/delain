<?php if (!isset($perso_cod))
    die ("Erreur d’appel de la page");

$resultat_inc_competence_combat = '';

if (!isset($inc_verif_pa))
    $inc_verif_pa = 12;

if (!isset($inc_attaque_courante))
    $inc_attaque_courante = -1;

// Méthode de combat

$pa_n = $db->get_pa_attaque($perso_cod);
$pa_f_1 = $pa_n + 3;
$pa_f_2 = $pa_n + 1;
$pa_f = $db->get_pa_foudre($perso_cod);

$req_comp = "select comp_libelle,
    	case
			when comp_cod in (25, 61, 62) then $pa_f              -- Attaque foudroyante
			when comp_cod in (63, 66, 75) then $pa_f_1            -- Compétences niveau 1
			when comp_cod in (64, 67, 76) then $pa_f_2            -- Compétences niveau 2
			when comp_cod in (65, 68, 72, 73, 74, 77) then $pa_n  -- Compétences niveau 3 + Bout portant
			when comp_cod in (89, 94) then 6                      -- Balayage + Garde-manger
			when comp_cod in (95, 96) then 4                      -- Attaque d’hydre + Jeu de troll
		end as cout_pa,
		case comp_cod
			when 25 then 1
			when 61 then 2
			when 62 then 3
			when 63 then 4
			when 64 then 5
			when 65 then 6
			when 66 then 7
			when 67 then 8
			when 68 then 9
			when 72 then 10
			when 73 then 11
			when 74 then 12
			when 75 then 13
			when 76 then 14
			when 77 then 15
			when 89 then 16
			when 94 then 17
			when 95 then 18
			when 96 then 19
		end as type_attaque,
		case when comp_cod in (25, 61, 62) then 0 else 1 end as distance_ok
	from competences
	inner join perso_competences on pcomp_pcomp_cod = comp_cod
	where pcomp_perso_cod = $perso_cod
		and comp_cod in (25, 61, 62, 63, 64, 65, 66, 67, 68, 72, 73, 74, 75, 76, 77, 89, 94, 95, 96)
	order by comp_cod";
$db->query($req_comp);

if ($inc_verif_pa >= $pa_n && $inc_attaque_courante != 0)
    $resultat_inc_competence_combat .= "<option value=\"0\">Attaque normale (" . $pa_n . " PA)</option>";

while ($db->next_record())
{
    if (($db->f('distance_ok') == 1 || !$arme_dist) && $inc_verif_pa >= $db->f('cout_pa') && $inc_attaque_courante != $db->f('type_attaque'))
        $resultat_inc_competence_combat .= "<option value='" . $db->f('type_attaque') . "'>" . $db->f('comp_libelle') . " (" . $db->f('cout_pa') . " PA)</option>";
}
