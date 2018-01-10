<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
include_once "../includes/constantes.php";
include_once "verif_connexion.php";

//$perso = new perso;
//$perso->charge($perso_cod);
//echo "<pre>"; print_r($perso); echo "</pre>";
//$perso->perso_tuteur = false;
//$perso->stocke();
//die();

//-----------------------------------------------------------------------
// on regarde si le joueur est bien sur le lieu souhaité ----------------
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 39)		// 39 = cod du relais poste !
	{
		$erreur = 1;
	}
	$lieu_cod = $tab_lieu['lieu_cod'];
}

// ----- Traitement du lieu ---------------------------------------------
if ($erreur!=0) 
{ 
	
	//cas d'un erreur de lieu (dans la cas par exemple d'un perso qui aurait changer de lieu avec une autre page web)
	$template = $twig->load('lieu_anomalie.twig');
	echo $template->render(array('LIEU' => "un relais poste"));
	
} 
else 
{
	//===========================================================================================
	// details du perso
	$perso = new perso;
	$perso->charge($perso_cod);
	$objets_poste = new objets_poste;		
	
	//===========================================================================================
	// On commence par regarder s'il y a déjà des objets envoyés --------------------------------
	$options_twig_colis = array();	// vide par défaut !
	$objets_poste_emet = $objets_poste->getBy_opost_emet_perso_cod($perso_cod);
	if (count($objets_poste_emet)>0)
	{
		$objets_emet = array();
		$perso_desc = new perso;
		$objet_desc = new objets;
		foreach ($objets_poste_emet as $k => $objet)
		{
			$objet_desc->charge($objet->opost_obj_cod);
			$perso_desc->charge($objet->opost_dest_perso_cod);
			$objets_emet[] = array(
					'date_poste' => $objet->opost_date_poste, 
					'date_livraison' => $objet->getDateLivraison(), 
					'date_confiscation' => $objet->getDateConfiscation(), 
					'perso_nom_dest' => $perso_desc->perso_nom, 
					'obj_nom' => $objet_desc->obj_nom, 
					'obj_poids' => $objet_desc->obj_poids, 
					'prix_demande' => $objet->opost_prix_demande
				);
			
		}
		$options_twig_colis['objets_poste_emet'] = $objets_emet;	
		unset($perso_desc);
		unset($objet_desc);		
	}
	//echo "<pre>"; print_r($objets_emet); echo "</pre>";	
	
	//===========================================================================================
	// On regarde ensuite s'il y a des objets pour nous -----------------------------------------
	$objets_poste_dest = $objets_poste->getBy_opost_dest_perso_cod($perso_cod);
	if (count($objets_poste_dest)>0)
	{
		$objets_dest = array();
		$perso_desc = new perso;
		$objet_desc = new objets;
		foreach ($objets_poste_dest as $k => $objet)
		{
			$objet_desc->charge($objet->opost_obj_cod);
			$perso_desc->charge($objet->opost_emet_perso_cod);
			$objets_dest[] = array(
					'date_poste' => $objet->opost_date_poste, 
					'date_livraison' => $objet->getDateLivraison(), 
					'date_confiscation' => $objet->getDateConfiscation(), 
					'perso_nom_dest' => $perso_desc->perso_nom, 
					'obj_nom' => $objet_desc->obj_nom, 
					'obj_poids' => $objet_desc->obj_poids, 
					'prix_demande' => $objet->opost_prix_demande
				);
			
		}
		$options_twig_colis['objets_poste_dest'] = $objets_dest;	
		unset($perso_desc);
		unset($objet_desc);		
	}
	//echo "<pre>"; print_r($options_twig_colis); echo "</pre>";
	
	//===========================================================================================
	// Objets "postable" dans l'inventaire du perso ---------------------------------------------
	$perso_inventaire = new perso_inventaire($perso_cod);
	$objets = $perso_inventaire->getObjetsDeposableRelaisPoste();	
	 
	// Initialisation des variables
	$colis = array();			// Pour l'instant il n'y a qu'un seul objet dans le colis, mais on prevoit pour plusieurs
	$poids_colis = 0;
	$prix_demande = 0;	
	
	// Préparation du colis si le/les objets ont été selectionnés
	if (isset($_POST["obj"])) 
	{ 
		$obj_cod = $_POST["obj"] ;	// objet envoyé!
		foreach ($objets as $k => $objet)
		{
			if ($objet["obj_cod"]==$obj_cod) 
			{
				$poids_colis += 1 * $objet["obj_poids"];
				$prix_demande += 1 * $_POST["prix"][$obj_cod];
				$objet["prix_demande"] = 1 * $_POST["prix"][$obj_cod]; 					
				$colis[] = $objet ;		// objet a mettre dans le colis					
			}
		}
		$frais_port	= $objets_poste->getFraisDePort($poids_colis) ;		
	}
	
	// recherche de l'adresse de livraison
	$destinataire = isset($_POST["destinataire"]) ? $_POST["destinataire"] : "" ;
	$destinataire_list = array() ;
	if (isset($_POST["destinataire"]))
	{
		$perso_dest = new perso;
		$destinataire_list = $perso_dest->getPersosByNameLike($_POST["destinataire"]);
		
		// S'il n'y a qu'un seul destinataire 		
		if (count($destinataire_list)==1) 
		{
			//on cherche son proprietaire
			$perso_compte = new perso_compte ;
			$perso_compte = $perso_compte->getBy_pcompt_perso_cod($destinataire_list[0]->perso_cod)[0];
			
			//on cherche aussi s'il n'y a pas déjà des colis pour lui
			$perso_attend_colis = $objets_poste->getBy_opost_dest_perso_cod($destinataire_list[0]->perso_cod);
		}		
	}
	
	// Debugage
	//print_r($_POST);
	//echo "<pre>"; print_r($objets); echo "</pre>";
	//echo "<pre>"; print_r($colis); echo "</pre>";
	//echo "<pre>"; print_r($destinataire_list); echo "</pre>";
	
	//------------------------------------------------------------------------------------------------------------------------------------
	// Mecanique de saisie des formulaires
    //------------------------------------------------------------------------------------------------------------------------------------	
	
	//--------------------------------------- payer, oter l'objet de l'inventaire et envoyer le colis ------------------------------------
    if (($_POST["action"]=="poste4") && ($_POST["next"]!=""))	
	{	
		// revérifier la demande complète, faire le paiement et envoyer 
		if (	(count($destinataire_list)==1) 							// il doit y avoir un seul destinataire
			 && ($destinataire_list[0]->perso_cod!=$perso_cod)			// mais pas à soit même
			 && ($perso_compte->pcompt_compt_cod!=$compt_cod)			// ni à un autre perso de la triplette/quadruplette
			 && (count($colis)>0)										// il y a moins un objet dans le colis
			 && (!$perso_attend_colis)									// le destinataire n'attend pas déjà un autre colis
			 && ($perso->perso_po>=$frais_port)							// on a assez de bz pour payer les frais de port 
			)
		{
			// Dans cette version on ne traite qu'un seul colis: $colis[0]	
		    $objets_poste->opost_obj_cod           = $colis[0]['obj_cod'];
		    $objets_poste->opost_prix_demande      = 1*$colis[0]['prix_demande'];			
		    $objets_poste->opost_emet_perso_cod    = $perso_cod;
		    $objets_poste->opost_dest_perso_cod    = $destinataire_list[0]->perso_cod;
		    $objets_poste->stocke(true);
			
		    $objets_poste->opost_colis_cod		   = $objets_poste->opost_cod ;	// le cod du premier objet du colis c'est le n° de colis
		    $objets_poste->stocke();			     // Update
			
		    //faire le paiement
		    $perso->perso_po = $perso->perso_po - $frais_port ;
		    $perso->stocke();
			
			// Retirer l'objet de l'inventaire du perso (traite l'événement)
		    $perso_inventaire->deposeObjetRelaisPoste($objets_poste);
		
		    $options_twig = array(
				'titre' => "Le colis a bien été envoyé:",
				'action' => "poste4",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire_list[0]->perso_nom,			
				'date_livraison' => $objets_poste->getDateLivraison(),	
				'date_confiscation' => $objets_poste->getDateConfiscation()
		    );							
		}
		else
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste3",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire_list[0]->perso_nom,			
				'date_livraison' => $objets_poste->getDateLivraison(),	
				'date_confiscation' => $objets_poste->getDateConfiscation(),
                'message' => "Une erreur c'est produite, nous ne pouvons pas envoyer votre colis. Veuillez nous excuser pour et impondérable!"
			);				
		}
	}
	//--------------------------------------- valider l'adresse de livraison (nom du destinataire) ---------------------------------------
    else if (($_POST["action"]=="poste3") && ($_POST["next"]!=""))	
	{	
		if (count($destinataire_list)<=0) 
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire,
				'message' => "Aucun aventuriers ne correspond à ce nom, veuillez corriger:"
			);				
		} 
		else if (count($destinataire_list)>1) 
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire,
				'message' => "Trop d'aventuriers correspondent à ce nom, veuillez corriger:"
			);				
		} 
		else if ((count($destinataire_list)==1) && ($destinataire_list[0]->perso_cod==$perso_cod))
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire,
				'message' => "Il est impossible de s'envoyer un colis à soit même, veuillez corriger:"
			);				
		} 
		else if ((count($destinataire_list)==1) && ($perso_compte->pcompt_compt_cod==$compt_cod))
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire,
				'message' => "Il est impossible d'envoyer un colis à un autre membre de sa triplette, veuillez corriger:"
			);				
		} 			
		else if ((count($destinataire_list)==1) && ($perso_attend_colis))
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire,
				'message' => "Il est impossible d'envoyer le colis à un aventurier qui en attend déjà un autre, veuillez corriger:"
			);				
		} 		
		else
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste3",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port,
				'destinataire' => $destinataire_list[0]->perso_nom,			
				'date_livraison' => $objets_poste->getDateLivraison(),	
				'date_confiscation' => $objets_poste->getDateConfiscation()			
			);				
		} 
	}
	//--------------------------------------- peser le colis, afficher les frais de port et demander le destinataire --------------------------------------- 
    else if (($_POST["action"]=="poste2") && ($_POST["next"]!=""))	
	{
		if ($_POST["obj"]=="") 
		{
			$options_twig = array(
				'titre' => "Il faut sélectionner au moins un équipement à envoyer:",
				'action' => "poste1",
				'objets' => $objets
			);
		}
		else if ($perso->perso_po<$frais_port) 
		{
			$options_twig = array(
				'titre' => "Essayez de sélectionner un objet moins lourd:",
				'action' => "poste1",
				'objets' => $objets,
				'message' => "Vous ne disposez pas d'assez de brouzoufs pour payer les frais de port ($frais_port brouzoufs)"				
			);
		}
		else 
		{
			$options_twig = array(
				'titre' => "Le colis à envoyer contient:",
				'action' => "poste2",
				'objets' => $colis,
				'poids' => $poids_colis,
				'prix_demande' => $prix_demande,
				'frais_port' => $frais_port	
			);			
	
		}
	}
	//--------------------------------------- Saisie de/des éléments à mettre dans le colis ---------------------------------------
	else 
	{
		$options_twig = array(
			'titre' => "Sélectionner l'équipement à envoyer:",
			'action' => "poste1",
			'objets' => $objets
		);
	} 
	
	//---------------------------------------rendering ! ---------------------------------------
	$template = $twig->load('lieu_relais_poste.twig');	
	echo $template->render(array_merge($options_twig_colis, $options_twig));

}


?>