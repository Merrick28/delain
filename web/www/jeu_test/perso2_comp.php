<?php

$perso = new perso;
if(!$perso->charge($perso_cod))
{
    die('Erreur sur le chargement de perso');
}

$typecomp = new type_competences();
$perso_comp = new perso_competences();
$alltypecomp = $typecomp->getAll();
echo "<pre>";
foreach($alltypecomp as $key => $val )
{

    $comp_perso = $perso_comp->getByPersoTypeComp($perso->perso_cod,$val->typc_cod);
    $alltypecomp[$key]->liste_comp = $comp_perso;
}


print_r($alltypecomp);
die("</pre>");
while($db->next_record())
{
	$contenu_page .= '<tr>
	<td colspan="4" class="soustitre"><p class="soustitre">' . $db->f("typc_libelle"). '</p></td>
	</tr>';
	$typecomp = $db->f("typc_cod");
	$req_comp = 'select comp_libelle,pcomp_modificateur from perso_competences,competences 
		where pcomp_perso_cod = ' . $perso_cod . '
		and pcomp_modificateur != 0 
		and pcomp_pcomp_cod = comp_cod 
		and comp_typc_cod = ' . $typecomp;
	$db_comp->query($req_comp);
	if ($db_comp->nf() <= 0)
	{
		$contenu_page .= 'Une erreur est survenue pendant la recherche des compétences !';
	} 
	else 
	{
		$cpt = 1;
		$contenu_page .= '<tr>';
		while($db_comp->next_record())
		{	
			$cpt++;
			if(fmod($cpt,2) == 0)
			{
				$contenu_page .= '</tr><tr>';
			}
			$contenu_page .= '<td class="soustitre2">' . $db_comp->f("comp_libelle") . '</td>
			<td>' . $db_comp->f("pcomp_modificateur") . ' %</td>';
			
		}
		$contenu_page .= '</tr>';
	}
}
$contenu_page .= '</table>';


$template     = $twig->load('_perso2_comp.twig');
$options_twig = array(

    'PERSO'             => $perso,
    'PHP_SELF'          => $PHP_SELF,

);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));

