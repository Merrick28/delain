/**
* 
* Fichier de fonctions Javascript
* pour le site http://www.jdr-delain.net
*
*@author: Julien - www.genformbd.fr
*@notice: à placer apres l'insertion de la bibliothèque JQuery
*/

			
/** 
* Fonction isExist
*
* Teste l'existence de lObjet
* 
*@var lObjet: l'objet que l'on veut tester
*@return boolean: 1 si l'objet existe, 0 sinon
* 
**/
function isExist(lObjet)
{
	/* if typeof... => pour vérifier que l'objet existe, */
	if (typeof lObjet != "undefined") 
		return 1;
	else return 0;
}


/** 
* Fonction isNull
*
* Teste l'existence de lObjet
* 
*@var lObjet: l'objet que l'on veut tester
*@return boolean: 1 si l'objet est null, 0 sinon
* 
**/
function isNull(lObjet)
{
	if (lObjet == null) 
		return 1;
	else return 0;
}


/** 
* Fonction isNotNull
*
* Teste l'existence de lObjet
* 
*@var lObjet: l'objet que l'on veut tester
*@return boolean: 1 si l'objet n'est pas null, 0 sinon
* 
**/
function isNotNull(lObjet)
{
	if (lObjet != null) 
		return 1;
	else return 0;
}



/**
* Fonction affiche_fiche_equipement
*
* Modifie les informations de la fiche equipement
* 
*@var $elem: le dom img qui contient les informations de l'objet
* 
**/
function affiche_fiche_equipement($elem)
{
	/* Les titres des rubriques */
	var $titre_nom="Nom";
	var $titre_desc="Description";
	var $titre_poids="Poids";
	var $titre_etat="Etat";
	var $titre_pa="PA / Attaque";
	var $titre_af="Att. Foudroyante";
	var $titre_degats="Dégats";
	var $titre_dexterite="Dextérité";
	var $titre_force="Force";
	var $titre_chute="Chute";
	var $titre_armure="Armure";
	var $titre_competence="Compétence";
	
	var $titre_critique="Protection contre les critique/spéciaux";
	var $titre_bonus_vue="Modificateur de vue";
	var $titre_vampirisme="Vampirisme";
	var $titre_aura_de_feu="Aura de feu";
	var $titre_regen="Bonus à la régénération";
	var $titre_poison="Dégâts infligés par poison";
	var $titre_url="Détail";
	
	var $Champ_idObjet=$('#idObjet');
	var $Champ_nomObjet=$('#nomObjet');
	var $Champ_methode=$('#methode');
	
	var $link_url = $("#link_url");
	
	/*
	var $titre_enchantable="Objet enchantable !";
	var $titre_deposable="Non déposable !";
	*/
	
	/* on vide le div resultat */
	$('#resultat_action').css('display', 'none');
	
	
	/* id de l'objet */
	if ( isExist($($elem).attr('num')) ) 
	{
		//alert($($elem).attr('num'));
		$id = $($elem).attr('num');
		$Champ_idObjet.val($id);
	}
	else $Champ_idObjet.val(-1);
	
	/* nom de l'objet */
	if ( isExist($($elem).attr('title')) ) 
	{
		$id = $($elem).attr('title');
		$Champ_nomObjet.val($id);
	}
	else $Champ_nomObjet.val(-1);
	
	
	/* if typeof... => pour vérifier que l'objet existe, */
	if ( isExist($($elem).attr('title')) ) 
	{
		$('#id_nom_item').css('display', 'block');
		$title = $($elem).attr('title');
		$('.titre_nom_item').text(unescape($titre_nom));
	}
	else 
	{
		$('#id_nom_item').css('display', 'none');
		$title = "&nbsp;";
		$('.titre_nom_item').text(unescape($title));
	}

	
	if ( isExist($($elem).attr('desc')) && isNotNull($($elem).attr('desc')) && $($elem).attr('desc')!= "" ) 
	{
		$('#id_desc_item').css('display', 'block');
		$desc = $($elem).attr('desc');
		$('.titre_desc_item').text(unescape($titre_desc));
	}
	else 
	{	
		$('#id_desc_item').css('display', 'none');
		$desc = "a";
		$('.titre_desc_item').text(unescape($desc));
	}

	
	if ( isExist($($elem).attr('competence')) ) 
	{
		$('#id_competence_item').css('display', 'block');
		$competence = $($elem).attr('competence');
		$('.titre_competence_item').text(unescape($titre_competence));
	}
	else 
	{
		$('#id_competence_item').css('display', 'none');
		$competence = "&nbsp;";
		$('.titre_competence_item').text(unescape($competence));
	}
	
	
	if ( isExist($($elem).attr('poids')) ) 
	{
		$('#id_poids_item').css('display', 'block');
		$poids = $($elem).attr('poids');
		$('.titre_poids_item').text(unescape($titre_poids));
	}
	else 
	{
		$('#id_poids_item').css('display', 'none');
		$poids = "&nbsp;";
		$('.titre_poids_item').text(unescape($poids));
	}
	

	if ( isExist($($elem).attr('etat')) ) 
	{
		$('#id_etat_item').css('display', 'block');
		$etat = $($elem).attr('etat');
		$('.titre_etat_item').text(unescape($titre_etat));
		$img_etat = "<img src='"+$($elem).attr('img_etat')+"' class='img_usure'/>";
	}
	else 
	{
		$('#id_etat_item').css('display', 'none');
		$etat = "&nbsp;";
		$img_etat = "";
		$('.titre_etat_item').text(unescape($etat));
	}

	
	if ( isExist($($elem).attr('pa')) ) 
	{
		$('#id_pa_item').css('display', 'block');
		$pa = $($elem).attr('pa');
		$('.titre_pa_item').text(unescape($titre_pa));
	}
	else 
	{
		$('#id_pa_item').css('display', 'none');
		$pa = "&nbsp;";
		$('.titre_pa_item').text(unescape($pa));
	}

	
	if ( isExist($($elem).attr('af')) ) 
	{
		$('#id_af_item').css('display', 'block');
		$af = $($elem).attr('af');
		$('.titre_af_item').text(unescape($titre_af));
	}
	else 
	{
		$('#id_af_item').css('display', 'none');
		$af = "&nbsp;";
		$('.titre_af_item').text(unescape($af));
	}

	
	if ( isExist($($elem).attr('degats')) ) 
	{
		$('#id_degats_item').css('display', 'block');
		$degats = $($elem).attr('degats');
		$('.titre_degats_item').text(unescape($titre_degats));
	}
	else 
	{
		$('#id_degats_item').css('display', 'none');
		$degats = "&nbsp;";
		$('.titre_degats_item').text(unescape($degats));
	}
	
	
	$class_dex = "";
	if ( isExist($($elem).attr('dexterite')) ) 
	{
		$('#id_dexterite_item').css('display','block');
		$dexterite = $($elem).attr('dexterite');
		$('.titre_dexterite_item').text(unescape($titre_dexterite));
		if ( isExist($($elem).attr('class_dex')) ) 
			$class_dex = $($elem).attr('class_dex');
		else $class_dex = "";
	}
	else 
	{
		$('#id_dexterite_item').css('display','none');
		$dexterite = "&nbsp;";
		$class_dex = "";
		$('.titre_dexterite_item').text(unescape($dexterite));
	}

	
	if ( isExist($($elem).attr('force')) ) 
	{
		$('#id_force_item').css('display', 'block');
		$force = $($elem).attr('force');
		$('.titre_force_item').text(unescape($titre_force));
		if ( isExist($($elem).attr('class_for')) ) 
			$class_for = $($elem).attr('class_for');
		else $class_for = "";
	}
	else 
	{
		$('#id_force_item').css('display', 'none');
		$force = "&nbsp;";
		$class_for = "";
		$('.titre_force_item').text(unescape($force));
	}

	
	if ( isExist($($elem).attr('chute')) ) 
	{
		$('#id_chute_item').css('display', 'block');
		$chute = $($elem).attr('chute');
		$('.titre_chute_item').text(unescape($titre_chute));
	}
	else 
	{
		$('#id_chute_item').css('display', 'none');
		$chute = "&nbsp;";
		$('.titre_chute_item').text(unescape($chute));
	}
	
	
	if ( isExist($($elem).attr('armure')) ) 
	{
		$('#id_armure_item').css('display', 'block');
		$armure = $($elem).attr('armure');
		$('.titre_armure_item').text(unescape($titre_armure));
	}
	else 
	{
		$('#id_armure_item').css('display', 'none');
		$armure = "&nbsp;";
		$('.titre_armure_item').text(unescape($armure));
	}

	
	if ( isExist($($elem).attr('critique')) ) 
	{
		$('#id_critique_item').css('display', 'block');
		$critique = $($elem).attr('critique');
		$('.titre_critique_item').text(unescape($titre_critique));
	}
	else 
	{
		$('#id_critique_item').css('display', 'none');
		$critique = "&nbsp;";
		$('.titre_critique_item').text(unescape($critique));
	}
	
	
	if ( isExist($($elem).attr('bonus_vue')) ) 
	{
		$('#id_bonus_vue_item').css('display', 'block');
		$bonus_vue = $($elem).attr('bonus_vue');
		$('.titre_bonus_vue_item').text(unescape($titre_bonus_vue));
	}
	else 
	{
		$('#id_bonus_vue_item').css('display', 'none');
		$bonus_vue = "&nbsp;";
		$('.titre_bonus_vue_item').text(unescape($bonus_vue));
	}
	
	
	if ( isExist($($elem).attr('vampirisme')) ) 
	{
		$('#id_vampirisme_item').css('display', 'block');
		$vampirisme = $($elem).attr('vampirisme');
		$('.titre_vampirisme_item').text(unescape($titre_vampirisme));
	}
	else 
	{
		$('#id_vampirisme_item').css('display', 'none');
		$vampirisme = "&nbsp;";
		$('.titre_vampirisme_item').text(unescape($vampirisme));
	}
	
	
	if ( isExist($($elem).attr('aura_de_feu')) ) 
	{
		$('#id_aura_de_feu_item').css('display', 'block');
		$aura_de_feu = $($elem).attr('aura_de_feu');
		$('.titre_aura_de_feu_item').text(unescape($titre_aura_de_feu));
	}
	else 
	{
		$('#id_aura_de_feu_item').css('display', 'none');
		$aura_de_feu = "&nbsp;";
		$('.titre_aura_de_feu_item').text(unescape($aura_de_feu));
	}
	
	
	if ( isExist($($elem).attr('regen')) ) 
	{
		$('#id_regen_item').css('display', 'block');
		$regen = $($elem).attr('regen')+" à l'initialisation de DLT";
		$('.titre_regen_item').text(unescape($titre_regen));
	}
	else 
	{
		$('#id_regen_item').css('display', 'none');
		$regen = "&nbsp;";
		$('.titre_regen_item').text(unescape($regen));
	}
	
	
	if ( isExist($($elem).attr('poison')) ) 
	{
		$('#id_poison_item').css('display', 'block');
		$poison = $($elem).attr('poison');
		$('.titre_poison_item').text(unescape($titre_poison));
	}
	else 
	{
		$('#id_poison_item').css('display', 'none');
		$poison = "&nbsp;";
		$('.titre_poison_item').text(unescape($poison));
	}
	
	$link_url.attr("href", "");
	$link_url.text("");
	$lien = "";
	if ( isExist($($elem).attr('url')) ) 
	{
		$('#id_url_item').css('display', 'block');
		$lien = './objets/'+$($elem).attr('url');
		$link_url.attr("href", $lien);
		//$link_url.attr("rel", $lien);
		$link_url.text("Voir le Détail");
		$('.titre_url_item').text(unescape($titre_url));
		//$('#link_url').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});
	}
	else 
	{
		$('#id_url_item').css('display', 'none');
		$url = "";
		//$('.url_item').text(unescape($url));
		$('.titre_url_item').text(unescape($url));
	}
	
	
	
	
	if ( isExist($($elem).attr('deposable')) ) 
	{
		$('#id_deposable_item').css('display', 'block');
		$deposable = ($($elem).attr('deposable')==1)?"Non Déposable!":"";
	}
	else 
	{
		$('#id_deposable_item').css('display', 'none');
		$deposable = "&nbsp;";
	}

			
	if ( isExist($($elem).attr('enchantable')) )
	{
		$('#id_enchantable_item').css('display', 'block');
		$enchantable = ($($elem).attr('enchantable')==1)?"Objet Enchantable!":"";
	}
	else 
	{
		$('#id_enchantable_item').css('display', 'none');
		$enchantable = "&nbsp;";
	}
	
	/* réparer */
	$('.action_reparer').css({ display: "none" });
	if ( isExist($($elem).attr('repare')) ) 
	{
		if( $($elem).attr('repare')==1)
		{
			$('.action_reparer').css({ display: "block" });
		}
	}
	
	
	/* équiper */
	$('.action_equiper').css({ display: "none" });
	$('.action_desequiper').css({ display: "none" });
	if ( isExist($($elem).attr('equipe')) ) 
	{
		if ( $($elem).attr('equipe')==1 )
		{
			$('.action_desequiper').css({ display: "block" });
		}
		else if ( $($elem).attr('equipe')==-1 )
		{
			$('.action_equiper').css({ display: "block" });
		}
		else $equipe = "";
	}
	
	
	/* abandonner */
	$('.action_abandonner').css({ display: "none" });
	if ( isExist($($elem).attr('abandonne')) ) 
	{
		if ( $($elem).attr('abandonne')==1 )
		{
			$('.action_abandonner').css({ display: "block" });
		}
	}
	
	/* identifier */
	$('.action_identifier').css({ display: "none" });
	if ( isExist($($elem).attr('identifier')) ) 
	{
		if ( $($elem).attr('identifier')==1 )
		{
			$('.action_identifier').css({ display: "block" });
		}
	}
	
	
	
	
	var $espace = "&nbsp;&nbsp;";
	$('.nom_item').text(unescape($title));
	$('.desc_item').text(unescape($desc));
	$('.poids_item').text(unescape($poids));
	$('.etat_item').html($img_etat + unescape($etat));
	$('.pa_item').text(unescape($pa));
	$('.af_item').text(unescape($af));
	$('.degats_item').text(unescape($degats));
	$('.dexterite_item').html(unescape($dexterite) + $class_dex);
	$('.force_item').html(unescape($force) + $class_for);
	$('.chute_item').text(unescape($chute));
	$('.armure_item').text(unescape($armure));
	$('.competence_item').text(unescape($competence));
	$('.critique_item').text(unescape($critique));
	$('.bonus_vue_item').text(unescape($bonus_vue));
	$('.vampirisme_item').text(unescape($vampirisme));
	$('.aura_de_feu_item').text(unescape($aura_de_feu));
	$('.regen_item').text(unescape($regen));
	$('.poison_item').text(unescape($poison));
	

	$('.titre_enchantable_item').text(unescape($enchantable));
	$('.titre_deposable_item').text(unescape($deposable));
	
	/*
	$('.titre_repare_item').text(unescape($repare));
	$('.titre_equipe_item').text(unescape($equipe));
	$('.titre_abandonne_item').text(unescape($abandonne));
	$('.titre_identifier_item').text(unescape($identifier));
	*/
	
}



/**
* Fonction affiche_desc_item
*
* Modifie les informations de la fiche equipement
* 
*@var $elem: le dom desc_tab qui contient les informations de l'objet
* 
**/
function affiche_desc_item($elem)
{
	/* Lorsqu'on clique sur un objet html de class desc_tab_item */
	var $titre_nom="Nom";
	var $titre_desc="Description";
	var $titre_poids="Poids";
	
	var $titre_bonus_vue="Modificateur de vue";
	var $titre_vampirisme="Vampirisme";
	var $titre_aura_de_feu="Aura de feu";
	var $titre_regen="Bonus à la régénération";
	var $titre_poison="Dégâts infligés par poison";
	var $titre_url="Détail";
	
	var $Champ_idObjet=$('#idObjet');
	var $Champ_nomObjet=$('#nomObjet');
	var $Champ_methode=$('#methode');
	
	var $link_url = $("#link_url");
	
	//on vide les champs
	$('#resultat_action').css('display', 'none');
	$('#id_chute_item').css('display', 'none');
	$('#id_armure_item').css('display', 'none');
	$('#id_critique_item').css('display', 'none');
	$('#id_force_item').css('display', 'none');
	$('#id_dexterite_item').css('display','none');
	$('#id_degats_item').css('display', 'none');
	$('#id_af_item').css('display', 'none');
	$('#id_pa_item').css('display', 'none');
	$('#id_etat_item').css('display', 'none');
	$('#id_competence_item').css('display', 'none');
		
		
	/* id de l'objet */
	if ( isExist($($elem).attr('num')) ) 
	{
		//alert($($elem).attr('num'));
		$id = $($elem).attr('num');
		$Champ_idObjet.val($id);
	}
	else $Champ_idObjet.val(-1);
	
	/* nom de l'objet */
	if ( isExist($($elem).attr('title')) ) 
	{
		$('#id_nom_item').css('display', 'block');
		$nom = $($elem).attr('title');
		$('.titre_nom_item').text(unescape($titre_nom));
		$id = $($elem).attr('title');
		$Champ_nomObjet.val($id);
	}
	else 
	{
		$('#id_nom_item').css('display', 'none');
		$nom = "&nbsp;";
		$('.titre_nom_item').text(unescape($nom));
		$Champ_nomObjet.val(-1);
	}
	
	
	if ( isExist($($elem).attr('desc')) && isNotNull($($elem).attr('desc')) && $($elem).attr('desc')!= "") 
	{
		$('#id_desc_item').css('display', 'block');
		$desc = $($elem).attr('desc');
		$('.titre_desc_item').text(unescape($titre_desc));
	}
	else 
	{	
		$('#id_desc_item').css('display', 'none');
		$desc = "a";
		$('.titre_desc_item').text(unescape($desc));
	}
	
	
	if ( isExist($($elem).attr('poids')) ) 
	{
		$('#id_poids_item').css('display', 'block');
		$poids = $($elem).attr('poids');
		$('.titre_poids_item').text(unescape($titre_poids));
	}
	else 
	{
		$('#id_poids_item').css('display', 'none');
		$poids = "&nbsp;";
		$('.titre_poids_item').text(unescape($poids));
	}
		
		
	if ( isExist($($elem).attr('bonus_vue')) ) 
	{
		$('#id_bonus_vue_item').css('display', 'block');
		$bonus_vue = $($elem).attr('bonus_vue');
		$('.titre_bonus_vue_item').text(unescape($titre_bonus_vue));
	}
	else 
	{
		$('#id_bonus_vue_item').css('display', 'none');
		$bonus_vue = "&nbsp;";
		$('.titre_bonus_vue_item').text(unescape($bonus_vue));
	}
	
	
	if ( isExist($($elem).attr('vampirisme')) ) 
	{
		$('#id_vampirisme_item').css('display', 'block');
		$vampirisme = $($elem).attr('vampirisme');
		$('.titre_vampirisme_item').text(unescape($titre_vampirisme));
	}
	else 
	{
		$('#id_vampirisme_item').css('display', 'none');
		$vampirisme = "&nbsp;";
		$('.titre_vampirisme_item').text(unescape($vampirisme));
	}
	
	
	if ( isExist($($elem).attr('aura_de_feu')) ) 
	{
		$('#id_aura_de_feu_item').css('display', 'block');
		$aura_de_feu = $($elem).attr('aura_de_feu');
		$('.titre_aura_de_feu_item').text(unescape($titre_aura_de_feu));
	}
	else 
	{
		$('#id_aura_de_feu_item').css('display', 'none');
		$aura_de_feu = "&nbsp;";
		$('.titre_aura_de_feu_item').text(unescape($aura_de_feu));
	}
	
	
	if ( isExist($($elem).attr('regen')) ) 
	{
		$('#id_regen_item').css('display', 'block');
		$regen = $($elem).attr('regen')+" à l'initialisation de DLT";
		$('.titre_regen_item').text(unescape($titre_regen));
	}
	else 
	{
		$('#id_regen_item').css('display', 'none');
		$regen = "&nbsp;";
		$('.titre_regen_item').text(unescape($regen));
	}
	
	
	if ( isExist($($elem).attr('poison')) ) 
	{
		$('#id_poison_item').css('display', 'block');
		$poison = $($elem).attr('poison');
		$('.titre_poison_item').text(unescape($titre_poison));
	}
	else 
	{
		$('#id_poison_item').css('display', 'none');
		$poison = "&nbsp;";
		$('.titre_poison_item').text(unescape($poison));
	}
	
	$link_url.attr("href", "");
	$link_url.text("");
	$lien = "";
	if ( isExist($($elem).attr('url')) ) 
	{
		$('#id_url_item').css('display', 'block');
		$lien = './objets/'+$($elem).attr('url');
		$link_url.attr("href", $lien);
		$link_url.text("Voir le Détail");
		//$link_url.attr("rel", $lien);
		$('.titre_url_item').text(unescape($titre_url));
		//$('#link_url').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: true, arrows: true, activation: 'click'});
	}
	else 
	{
		$('#id_url_item').css('display', 'none');
		$url = "";
		//$('.url_item').text(unescape($url));
		$link_url.attr("rel", "");
		$('.titre_url_item').text(unescape($url));
	}
	
	$method = "-1";
	

	if ( isExist($($elem).attr('deposable')) ) 
	{
		$('#id_deposable_item').css('display', 'block');
		$deposable = ($($elem).attr('deposable')==1)?"Non Déposable!":"";
	}
	else 
	{
		$('#id_deposable_item').css('display', 'none');
		$deposable = "&nbsp;";
	}

	
	if ( isExist($($elem).attr('enchantable')) ) 
	{
		$('#id_enchantable_item').css('display', 'block');
		$enchantable = ($($elem).attr('enchantable')==1)?"Objet Enchantable!":"";
	}
	else 
	{
		$('#id_enchantable_item').css('display', 'none');
		$enchantable = "&nbsp;";
	}
	
	/* équiper */
	$('.action_equiper').css({ display: "none" });
	$('.action_desequiper').css({ display: "none" });
	if ( isExist($($elem).attr('equipe')) ) 
	{
		if ( $($elem).attr('equipe')==1 )
		{
			$('.action_desequiper').css({ display: "block" });
		}
		else if ( $($elem).attr('equipe')==-1 )
		{
			$('.action_equiper').css({ display: "block" });
		}
		else $equipe = "";
	}
	
	
	/* réparer */
	$('.action_reparer').css({ display: "none" });
	/*
	if ( isExist($($elem).attr('repare')) ) 
	{
		if( $($elem).attr('repare')==1)
		{
			$('.action_reparer').css({ display: "block" });
		}
	}
	*/
	
	/* équiper */
	$('.action_equiper').css({ display: "none" });
	$('.action_desequiper').css({ display: "none" });
	/*
	if ( isExist($($elem).attr('equipe')) ) 
	{
		if ( $($elem).attr('equipe')==1 )
		{
			$('.action_desequiper').css({ display: "block" });
		}
		else if ( $($elem).attr('equipe')==-1 )
		{
			$('.action_equiper').css({ display: "block" });
		}
		else $equipe = "";
	}
	*/
	
	
	/* abandonner */
	$('.action_abandonner').css({ display: "none" });
	if ( isExist($($elem).attr('abandonne')) ) 
	{
		if ( $($elem).attr('abandonne')==1 )
		{
			$('.action_abandonner').css({ display: "block" });
		}
	}
	
	/* identifier */
	$('.action_identifier').css({ display: "none" });
	if ( isExist($($elem).attr('identifier')) ) 
	{
		if ( $($elem).attr('identifier')==1 )
		{
			$('.action_identifier').css({ display: "block" });
		}
	}
	
	
	$('.nom_item').text(unescape($nom));
	$('.desc_item').text(unescape($desc));
	$('.poids_item').text(unescape($poids));
	$('.bonus_vue_item').text(unescape($bonus_vue));
	$('.vampirisme_item').text(unescape($vampirisme));
	$('.aura_de_feu_item').text(unescape($aura_de_feu));
	$('.regen_item').text(unescape($regen));
	$('.poison_item').text(unescape($poison));
	
	$('.titre_enchantable_item').text(unescape($enchantable));
	$('.titre_deposable_item').text(unescape($deposable));
	/*
	$('.titre_repare_item').text($repare);
	$('.titre_equipe_item').text(unescape($equipe));
	$('.titre_abandonne_item').text(unescape($abandonne));
	$('.titre_identifier_item').text(unescape($identifier));
	*/
}



/** 
* Fonction afficheFicheItem
* 
* Affiche la fiche de l'item que l'on a selectionné
* 
**/
function afficheFicheItem()
{
	/* Lorsqu'on clique sur un objet html de class img_item */
	$('.img_item').click(
		function()
		{
			affiche_fiche_equipement(this);
			self.location.hash="#fiche_item";
		}
	);
	
	$('.img_item_usure').click(
		function()
		{
			affiche_fiche_equipement(this);
			self.location.hash="#fiche_item";
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class img_equipement */
	$('.img_equipement').click(
		function()
		{
			affiche_fiche_equipement(this);
			self.location.hash="#fiche_item";
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class action_abandonner */
	$('.action_abandonner').click(
		function()
		{
			gere_formulaire_equipement(this, 'abandonner');
			$("#trait_equipement").submit();
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class action_reparer */
	$('.action_reparer').click(
		function()
		{
			gere_formulaire_equipement(this, 'reparer');
			$("#trait_equipement").submit();
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class action_identifier */
	$('.action_identifier').click(
		function()
		{
			gere_formulaire_equipement(this, 'identifier');
			$("#trait_equipement").submit();
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class action_equiper */
	$('.action_equiper').click(
		function()
		{
			gere_formulaire_equipement(this, 'equiper');
			$("#trait_equipement").submit();
		}
	);
	
	
	/* Lorsqu'on clique sur un objet html de class action_desequiper */
	$('.action_desequiper').click(
		function()
		{
			gere_formulaire_equipement(this, 'remettre');
			$("#trait_equipement").submit();
		}
	);
	
	
	/* Lorsqu'on clique sur un objet html de class desc_tab_item */
	
	$('.desc_tab_item').click(
		function()
		{
			affiche_desc_item(this);
			self.location.hash="#fiche_item";
		}
	);
	
	/* Lorsqu'on clique sur un objet html de class action_deposer_brouzouf */
	$('.action_deposer_brouzouf').click(
		function()
		{ 
			$('#depot_or').css('display', 'block');
		}
	);
	
}



function gere_formulaire($elem, $case)
{
	var $Formulaire=$('#trait_objet');
	var $Champ_idObjet=$('#idObjet_divers');
	var $Champ_typeObjet=$('#typeObjet');
	var $Champ_nbObjet=$('#nbObjet');
	var $Champ_nomObjetGenerique=$('#nomObjetGenerique');
	var $Champ_nomObjet=$('#nomObjet');
	var $Champ_methode=$('#methode_divers');
	
	var $ok = 1;
	
	/* nom du formulaire */
	if ( isExist($($elem).attr('nom')) ) 
	{
		//alert($($elem).attr('nom'));
		
		$nom = $($elem).attr('nom');
		$Champ_methode.val($nom);
	}
	else $ok = 0;
	
	
	/* id de l'objet */
	if ( isExist($($elem).attr('num')) ) 
	{
		//alert($($elem).attr('num'));
		$id = $($elem).attr('num');
		$Champ_idObjet.val($id);
	}
	else $Champ_idObjet.val(-1);
	
	
	/* type de l'objet */
	if ( isExist($($elem).attr('type')) ) 
	{
		//alert($($elem).attr('type'));
		$type = $($elem).attr('type');
		$Champ_typeObjet.val($type);
	}
	else $Champ_typeObjet.val(-1);
	
	
	/* nom generique de l'objet */
	if ( isExist($($elem).attr('nom_objet_generique')) ) 
	{
		//alert($($elem).attr('nom_objet_generique'));
		$nom_objet_generique = $($elem).attr('nom_objet_generique');
		$Champ_nomObjetGenerique.val($nom_objet_generique);
	}
	else $Champ_nomObjetGenerique.val(-1);
	
	
	/* nom  de l'objet */
	if ( isExist($($elem).attr('nom_objet')) ) 
	{
		//alert($($elem).attr('nom_objet'));
		$nom_objet = $($elem).attr('nom_objet');
		$Champ_nomObjet.val($nom_objet);
	}
	else $Champ_nomObjet.val(-1);
	
	
	
	/* nom du champ indiquant le nombre d'objets (pour abandonner) */
	//if ( isExist($($elem).attr('nomchamp')) ) 
	if($case == 1)
	{
		//$nom_champ = $($elem).attr('nomchamp');
		$fre = $($elem).parent().prev().children();
		$nb = $fre.val();
		if ( $nb == "" )
			$nb = 1;
		
		$Champ_nbObjet.val($nb);
	}
	else $Champ_nbObjet.val(1);
	
	
	/* Validation du formulaire */
	if ( $ok == 1)
		$Formulaire.submit();
	
}




function gere_formulaire_equipement($elem, $method)
{
	var $Champ_methode=$('#methode');
	
	$val = -1;
	/* nom du formulaire */
	if ( isExist($method) ) 
	{
		$val = $method;	
	}
	$Champ_methode.val($val);
}




/** 
* Fonction gereFormulaire
* 
* Modifie les elements du formulaire de validation 
* (gestion de Abandon, Identifier, Réparer)
* 
**/
function gereFormulaire()
{
	/* Lorsqu'on clique sur un objet html de class elem_objet */
	$('.elem_objet').click(
		function()
		{ 
			gere_formulaire(this, 1);
		}
	);
	
	$('.elem_objet2').click(
		function()
		{ 
			gere_formulaire(this, 2);
		}
	);
	
	$('.action_reparer').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'reparer');
		}
	);
	
	$('.action_identifier').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'identifier');
		}
	);
	
	$('.action_equiper').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'equiper');
		}
	);
	
	$('.action_desequiper').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'remettre');
		}
	);
	
	$('.action_abandonner').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'abandonner');
		}
	);
}



/** 
* Fonction gereToggle
* 
* Modifie les elements du formulaire de validation 
* (gestion de Abandon, Identifier, Réparer)
* 
**/
function gereToggle()
{
	$("#expand tr.even").hide();
		
	/* Lorsqu'on clique sur un objet html de id  expand */
	$("#expand tr.odd td.td_arrow").click(
		function() 
		{
			$(this).parent().next("tr").find(".even").parent().parent().toggle();
			$(this).find(".arrow").toggleClass("up"); 
		}
	);
		
}



function chargeAnalyse()
{
	/* Lorsqu'on clique sur un objet html de classe active_analyse */
	$('.active_analyse').click(	
		function()
		{	
			$('#humanMsg').css({ display: "block" }); 
			var $msg = $(this).attr('title')
			
			humanMsg.displayMsg($msg);
		}
	)
}



/* Pour activer les events Jquery */
$(document).ready(
	function()
	{
		/* Liste des fonctions que l'on veut executer avec des events*/
		
		afficheFicheItem();
		//gereFormulaire();
		//gereToggle();
		//chargeAnalyse();
		//afficheDescItem();
		
		/*
		var list_artefacts = document.getElementById('liste_artefacts');
		if ( isNotNull(list_artefacts))
		{
			humanMsg.setup();
		}
		*/
	
	}
);



/* Execution de code JS sans evenement */


//else alert(list_artefacts);
