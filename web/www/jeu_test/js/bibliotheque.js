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
	
	var $Champ_idObjet=$('#idObjet');
	var $Champ_methode=$('#methode');

	var $link_equiper = $("#link_equiper");
	var $link_desequiper = $("#link_desequiper");
	var $link_identifier = $("#link_identifier");
	var $link_reparer = $("#link_reparer");
	var $link_abandonner = $("#link_abandonner");
	
	/*
	var $titre_enchantable="Objet enchantable !";
	var $titre_deposable="Non déposable !";
	*/
	
	/* id de l'objet */
	if ( isExist($($elem).attr('num')) ) 
	{
		//alert($($elem).attr('num'));
		$id = $($elem).attr('num');
		$Champ_idObjet.val($id);
	}
	else $Champ_idObjet.val(-1);
	
	
	/* if typeof... => pour vérifier que l'objet existe, */
	if ( isExist($($elem).attr('title')) ) 
	{
		$title = $($elem).attr('title');
		$('.titre_nom_item').text(unescape($titre_nom));
	}
	else 
	{
		$title = "";
		$('.titre_nom_item').text(unescape($title));
	}

	
	if ( isExist($($elem).attr('desc')) ) 
	{
		$desc = $($elem).attr('desc');
		$('.titre_desc_item').text(unescape($titre_desc));
	}
	else 
	{	
		$desc = "";
		$('.titre_desc_item').text(unescape($desc));
	}

	if ( isExist($($elem).attr('poids')) ) 
	{
		$poids = $($elem).attr('poids');
		$('.titre_poids_item').text(unescape($titre_poids));
	}
	else 
	{
		$poids = "";
		$('.titre_poids_item').text(unescape($poids));
	}

	if ( isExist($($elem).attr('etat')) ) 
	{
		$etat = $($elem).attr('etat');
		$('.titre_etat_item').text(unescape($titre_etat));
		$img_etat = "<img src='"+$($elem).attr('img_etat')+"' class='img_usure'/>";
	}
	else 
	{
		$etat = "";
		$img_etat = "";
		$('.titre_etat_item').text(unescape($etat));
	}

	if ( isExist($($elem).attr('pa')) ) 
	{
		$pa = $($elem).attr('pa');
		$('.titre_pa_item').text(unescape($titre_pa));
	}
	else 
	{
		$pa = "";
		$('.titre_pa_item').text(unescape($pa));
	}

	if ( isExist($($elem).attr('af')) ) 
	{
		$af = $($elem).attr('af');
		$('.titre_af_item').text(unescape($titre_af));
	}
	else 
	{
		$af = "";
		$('.titre_af_item').text(unescape($af));
	}

	if ( isExist($($elem).attr('degats')) ) 
	{
		$degats = $($elem).attr('degats');
		$('.titre_degats_item').text(unescape($titre_degats));
	}
	else 
	{
		$degats = "";
		$('.titre_degats_item').text(unescape($degats));
	}
	
	$class_dex = "";
	if ( isExist($($elem).attr('dexterite')) ) 
	{
		$dexterite = $($elem).attr('dexterite');
		$('.titre_dexterite_item').text(unescape($titre_dexterite));
		if ( isExist($($elem).attr('class_dex')) ) 
			$class_dex = $($elem).attr('class_dex');
		else $class_dex = "";
	}
	else 
	{
		$dexterite = "";
		$class_dex = "";
		$('.titre_dexterite_item').text(unescape($dexterite));
	}

	if ( isExist($($elem).attr('force')) ) 
	{
		$force = $($elem).attr('force');
		$('.titre_force_item').text(unescape($titre_force));
		if ( isExist($($elem).attr('class_for')) ) 
			$class_for = $($elem).attr('class_for');
		else $class_for = "";
	}
	else 
	{
		$force = "";
		$class_for = "";
		$('.titre_force_item').text(unescape($force));
	}

	if ( isExist($($elem).attr('chute')) ) 
	{
		$chute = $($elem).attr('chute');
		$('.titre_chute_item').text(unescape($titre_chute));
	}
	else 
	{
		$chute = "";
		$('.titre_chute_item').text(unescape($chute));
	}
	
	if ( isExist($($elem).attr('armure')) ) 
	{
		$armure = $($elem).attr('armure');
		$('.titre_armure_item').text(unescape($titre_armure));
	}
	else 
	{
		$armure = "";
		$('.titre_armure_item').text(unescape($armure));
	}

	if ( isExist($($elem).attr('competence')) ) 
	{
		$competence = $($elem).attr('competence');
		$('.titre_competence_item').text(unescape($titre_competence));
	}
	else 
	{
		$competence = "";
		$('.titre_competence_item').text(unescape($competence));
	}

	
	if ( isExist($($elem).attr('critique')) ) 
	{
		$critique = $($elem).attr('critique');
		$('.titre_critique_item').text(unescape($titre_critique));
	}
	else 
	{
		$critique = "";
		$('.titre_critique_item').text(unescape($critique));
	}
	
	
	if ( isExist($($elem).attr('bonus_vue')) ) 
	{
		$bonus_vue = $($elem).attr('bonus_vue');
		$('.titre_bonus_vue_item').text(unescape($titre_bonus_vue));
	}
	else 
	{
		$bonus_vue = "";
		$('.titre_bonus_vue_item').text(unescape($bonus_vue));
	}
	
	
	if ( isExist($($elem).attr('vampirisme')) ) 
	{
		$vampirisme = $($elem).attr('vampirisme');
		$('.titre_vampirisme_item').text(unescape($titre_vampirisme));
	}
	else 
	{
		$vampirisme = "";
		$('.titre_vampirisme_item').text(unescape($vampirisme));
	}
	
	
	if ( isExist($($elem).attr('aura_de_feu')) ) 
	{
		$aura_de_feu = $($elem).attr('aura_de_feu');
		$('.titre_aura_de_feu_item').text(unescape($titre_aura_de_feu));
	}
	else 
	{
		$aura_de_feu = "";
		$('.titre_aura_de_feu_item').text(unescape($aura_de_feu));
	}
	
	
	if ( isExist($($elem).attr('regen')) ) 
	{
		$regen = $($elem).attr('regen')+" à l'initialisation de DLT";
		$('.titre_regen_item').text(unescape($titre_regen));
	}
	else 
	{
		$regen = "";
		$('.titre_regen_item').text(unescape($regen));
	}
	
	
	if ( isExist($($elem).attr('poison')) ) 
	{
		$poison = $($elem).attr('poison');
		$('.titre_poison_item').text(unescape($titre_poison));
	}
	else 
	{
		$poison = "";
		$('.titre_poison_item').text(unescape($poison));
	}
	
	
	$method = "-1";
	
	$repare = "";
	$link_reparer.attr("href", "");
	$link_reparer.text("");
	if ( isExist($($elem).attr('repare')) ) 
	{
		if( $($elem).attr('repare')==1)
		{
			$link_reparer.attr("href", "javascript:document.trait_equipement.submit();");
			$link_reparer.text("Reparer(2PA)");
	
			$method = "reparer";
		}
	}
	
	$link_desequiper.attr("href", "");
	$link_desequiper.text("");
	$link_equiper.attr("href", "");
	$link_equiper.text("");
	if ( isExist($($elem).attr('equipe')) ) 
	{
		if ( $($elem).attr('equipe')==1 )
		{
			$link_desequiper.attr("href", "javascript:document.trait_equipement.submit();");
			$link_desequiper.text("Desequiper(2PA)");
			//$equipe = "<a href=\"javascript:document.trait_equipement.submit();\" >Desequiper(2PA)</a>";
			$method = "remettre";
		}
		else if ( $($elem).attr('equipe')==-1 )
		{
			$link_equiper.attr("href", "javascript:document.trait_equipement.submit();");
			$link_equiper.text("Equiper(2PA)");
			//$equipe = "<a href=\"javascript:document.trait_equipement.submit();\" >Equiper(2PA)</a>";
			$method = "equiper";
		}
		else $equipe = "";
	}
	else $equipe = "";

	
	$abandonne = "";
	$link_abandonner.attr("href", "");
	$link_abandonner.text("");
	if ( isExist($($elem).attr('abandonne')) ) 
	{
		if ( $($elem).attr('abandonne')==1 )
		{
			$link_abandonner.attr("href", "javascript:document.trait_equipement.submit();");
			$link_abandonner.text("Abandonner(1PA)");
			//$abandonne = "<a href=\"javascript:document.trait_equipement.submit();\" >Abandonner(1PA)</a>";
			$method = "abandonner";
		}
	}
	

	$identifier = "";
	$link_identifier.attr("href", "");
	$link_identifier.text("");
	if ( isExist($($elem).attr('identifier')) ) 
	{
		if ( $($elem).attr('identifier')==1 )
		{
			$link_identifier.attr("href", "javascript:document.trait_equipement.submit();");
			$link_identifier.text("Identifier(2PA)");
			//$identifier = "<a href=\"javascript:document.trait_equipement.submit();\" >Identifier (2PA)</a>";
			$method = "identifier";
		}
	}
	

	//$Champ_methode.val($method);

	
	
	if ( isExist($($elem).attr('deposable')) ) 
		$deposable = ($($elem).attr('deposable')==1)?"Non Déposable!":"";
	else $deposable = "";

			
	if ( isExist($($elem).attr('enchantable')) ) 
		$enchantable = ($($elem).attr('enchantable')==1)?"Objet Enchantable!":"";
	else $enchantable = "";
	
	
	var $espace = "&nbsp;&nbsp;";
	$('.nom_item').text(unescape($title));
	$('.desc_item').text(unescape($desc));
	$('.poids_item').text(unescape($poids));
	$('.etat_item').html(unescape($etat) + $img_etat);
	$('.pa_item').text(unescape($pa));
	$('.af_item').text(unescape($af));
	$('.degats_item').text(unescape($degats));
	//$('.dexterite_item').text(unescape($dexterite)+ $class_dex);
	$('.dexterite_item').html(unescape($dexterite) + $class_dex);
	//$('.force_item').text(unescape($force));
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

	var $Champ_idObjet=$('#idObjet');
	var $Champ_methode=$('#methode');
	
	var $link_equiper = $("#link_equiper");
	var $link_desequiper = $("#link_desequiper");
	var $link_identifier = $("#link_identifier");
	var $link_reparer = $("#link_reparer");
	var $link_abandonner = $("#link_abandonner");
	
	/* id de l'objet */
	if ( isExist($($elem).attr('num')) ) 
	{
		//alert($($elem).attr('num'));
		$id = $($elem).attr('num');
		$Champ_idObjet.val($id);
	}
	else $Champ_idObjet.val(-1);
	
	
	if ( isExist($($elem).attr('nom')) ) 
	{
		$nom = $($elem).attr('nom');
		$('.titre_nom_item').text(unescape($titre_nom));
	}
	else 
	{
		$nom = "";
		$('.titre_nom_item').text(unescape($nom));
	}
	
	
	if ( isExist($($elem).attr('desc')) ) 
	{
		$desc = $($elem).attr('desc');
		$('.titre_desc_item').text(unescape($titre_desc));
	}
	else 
	{	
		$desc = "";
		$('.titre_desc_item').text(unescape($desc));
	}
	
	
	if ( isExist($($elem).attr('poids')) ) 
	{
		$poids = $($elem).attr('poids');
		$('.titre_poids_item').text(unescape($titre_poids));
	}
	else 
	{
		$poids = "";
		$('.titre_poids_item').text(unescape($poids));
	}
		
		
	if ( isExist($($elem).attr('bonus_vue')) ) 
	{
		$bonus_vue = $($elem).attr('bonus_vue');
		$('.titre_bonus_vue_item').text(unescape($titre_bonus_vue));
	}
	else 
	{
		$bonus_vue = "";
		$('.titre_bonus_vue_item').text(unescape($bonus_vue));
	}
	
	
	if ( isExist($($elem).attr('vampirisme')) ) 
	{
		$vampirisme = $($elem).attr('vampirisme');
		$('.titre_vampirisme_item').text(unescape($titre_vampirisme));
	}
	else 
	{
		$vampirisme = "";
		$('.titre_vampirisme_item').text(unescape($vampirisme));
	}
	
	
	if ( isExist($($elem).attr('aura_de_feu')) ) 
	{
		$aura_de_feu = $($elem).attr('aura_de_feu');
		$('.titre_aura_de_feu_item').text(unescape($titre_aura_de_feu));
	}
	else 
	{
		$aura_de_feu = "";
		$('.titre_aura_de_feu_item').text(unescape($aura_de_feu));
	}
	
	
	if ( isExist($($elem).attr('regen')) ) 
	{
		$regen = $($elem).attr('regen')+" à l'initialisation de DLT";
		$('.titre_regen_item').text(unescape($titre_regen));
	}
	else 
	{
		$regen = "";
		$('.titre_regen_item').text(unescape($regen));
	}
	
	
	if ( isExist($($elem).attr('poison')) ) 
	{
		$poison = $($elem).attr('poison');
		$('.titre_poison_item').text(unescape($titre_poison));
	}
	else 
	{
		$poison = "";
		$('.titre_poison_item').text(unescape($poison));
	}
	
	$method = "-1";
	
	$repare = "";
	$link_reparer.attr("href", "");
	$link_reparer.text("");
	if ( isExist($($elem).attr('repare')) ) 
	{
		if( $($elem).attr('repare')==1)
		{
			$link_reparer.attr("href", "javascript:document.trait_equipement.submit();");
			$link_reparer.text("Reparer(2PA)");
	
			$method = "reparer";
		}
	}

	$link_desequiper.attr("href", "");
	$link_desequiper.text("");
	$link_equiper.attr("href", "");
	$link_equiper.text("");
	
	if ( isExist($($elem).attr('equipe')) ) 
	{
		if ( $($elem).attr('equipe')==1 )
		{
			$link_desequiper.attr("href", "javascript:document.trait_equipement.submit();");
			$link_desequiper.text("Desequiper(2PA)");
			//$equipe = "<a href=\"javascript:document.trait_equipement.submit();\" >Desequiper(2PA)</a>";
			$method = "remettre";
		}
		else if ( $($elem).attr('equipe')==-1 )
		{
			$link_equiper.attr("href", "javascript:document.trait_equipement.submit();");
			$link_equiper.text("Equiper(2PA)");
			//$equipe = "<a href=\"javascript:document.trait_equipement.submit();\" >Equiper(2PA)</a>";
			$method = "equiper";
		}
		else $equipe = "";
	}
	else $equipe = "";

	$abandonne = "";
	$link_abandonner.attr("href", "");
	$link_abandonner.text("");
	if ( isExist($($elem).attr('abandonne')) ) 
	{
		if ( $($elem).attr('abandonne')==1 )
		{
			$link_abandonner.attr("href", "javascript:document.trait_equipement.submit();");
			$link_abandonner.text("Abandonner(1PA)");
			$method = "abandonner";
		}
	}
	
	$identifier = "";
	if ( isExist($($elem).attr('identifier')) ) 
	{
		if ( $($elem).attr('identifier')==1 )
		{
			$identifier = "<a href=\"javascript:document.trait_equipement.submit();\" >Identifier (2PA)</a>";
			$method = "identifier";
		}
	}
	

	//$Champ_methode.val($method);
	
	if ( isExist($($elem).attr('deposable')) ) 
		$deposable = ($($elem).attr('deposable')==1)?"Non Déposable!":"";
	else $deposable = "";

	
	if ( isExist($($elem).attr('enchantable')) ) 
		$enchantable = ($($elem).attr('enchantable')==1)?"Objet Enchantable!":"";
	else $enchantable = "";
	
	
	
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
	
	/* Lorsqu'on clique sur un objet html de class img_equipement */
	$('.img_equipement').click(
		function()
		{
			affiche_fiche_equipement(this);
			self.location.hash="#fiche_item";
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
		

	/* on verra apres pour l'ajax :)
	$('.equip-liste').click(
		function()
		{
			$('.selected').children('img').attr('src',$(this).children('img').attr('src'));
			$('#ajaxdestination').load("equipement/addEquipement", { id : $(this).attr('id'), equipement : $(this).attr('type') } );
		}
		);
	*/
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
	$('#link_reparer').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'reparer');
		}
	);
	$('#link_identifier').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'identifier');
		}
	);
	$('#link_equiper').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'equiper');
		}
	);
	$('#link_desequiper').click(
		function()
		{ 
			gere_formulaire_equipement(this, 'remettre');
		}
	);
	$('#link_abandonner').click(
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
		gereFormulaire();
		gereToggle();
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
