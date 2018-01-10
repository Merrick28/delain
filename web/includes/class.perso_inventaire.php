<?php

/**
 * includes/class.perso_inventaire.php
 */

/**
 * Class perso_inventaire
 *
 * Gère les objets BDD liés aux tables perso_objets, objets, objet_generique, type_objet
 */
class perso_inventaire
{
    var $perso_cod = null;
  
    function __construct($perso_cod)
    {     
		$this->perso_cod = $perso_cod ;		
    }
	
	
    /**
     * Retourne un tableau du matos que l'on peut envoyer par les relais de la poste
     * @global bdd_mysql $pdo
	   @return array
     */
	 
    function getObjetsDeposableRelaisPoste()
    { 
	    $retour = array();
	    if (!$this->perso_cod) return  retour; // pas de perso, pas d'inventaire!
		
        $pdo    = new bddpdo;
        $req    = "select obj_etat, tobj_cod, gobj_cod, tobj_libelle, CASE WHEN perobj_identifie = 'N' THEN obj_nom_generique ELSE obj_nom END obj_nom, obj_cod, obj_poids, gobj_tobj_cod, gobj_url
	               from perso_objets
	               INNER JOIN objets ON obj_cod = perobj_obj_cod
	               INNER JOIN objet_generique ON gobj_cod = obj_gobj_cod
	               INNER JOIN type_objet ON tobj_cod = gobj_tobj_cod
	               WHERE perobj_perso_cod = :perobj_perso_cod
	               AND perobj_equipe = 'N'
	               AND perobj_identifie = 'O'
	               AND gobj_tobj_cod not in (5,11,14,22,28,30,34)
	               ORDER BY tobj_libelle,gobj_nom ";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perobj_perso_cod" => $this->perso_cod), $stmt);
		while ( $result = $stmt->fetch() )
        { 
            $retour[] = $result ;
        }

        return $retour;		
	}
		
    /**
     * Retourne true si l'opération c'est bien déroulée
	 * Cette fonction réalise les actions nécéssaires dans l'inventaire du perso pour pour lui retirer un objet qui vient d'être posté:
	 *   - Suppression des transactions en cours sur l'objet s'il y en avaient
	 *   - suppression de l'objet de l'inventaire
	 *   - Ajout d'une ligne d'évenement
     * @global bdd_mysql $pdo
     * @param $objets_poste : objet qui a été posté
	   @return boolean
     */
	 
    function deposeObjetRelaisPoste(objets_poste $objets_poste)
    { 
	    if (!$this->perso_cod) return  false; // pas de perso, pas de dépose possible
		
        $pdo    = new bddpdo;
		
		/********************************************************************/
		// On vérifie que l’objet est bien dans l’inventaire du perso, on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT perobj_cod, obj_nom_generique 
				   	FROM perso_objets 
					INNER JOIN objets ON obj_cod = perobj_obj_cod 
					INNER JOIN objet_generique on gobj_cod = obj_gobj_cod 
					WHERE perobj_perso_cod = :perso_cod AND perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $this->perso_cod,":opost_obj_cod" => $objets_poste->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie !
        }	
		$nom_objet = $result["obj_nom_generique"] ; 

		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements 
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_perso_cod1 =  $this->perso_cod ;
				
		/********************************************************************/
		// On supprime les transactions sur l'objet s'il y en avait 
		/********************************************************************/
		$req    = "DELETE FROM transaction WHERE tran_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $objets_poste->opost_obj_cod), $stmt);
		if ($stmt->rowCount()>0) 
		{
			// Mettre la ligne d'événement correpondante
			$ligne_evt->levt_tevt_cod = 17;			
			$ligne_evt->levt_texte = 'La transaction en cours sur l’objet « ' . $nom_objet . ' » (' . (1*$objets_poste->opost_obj_cod) . ') a été annulée !';
			$ligne_evt->levt_lu = 'O';
			$ligne_evt->levt_visible = 'N';
			$ligne_evt->stocke(true);	// Nouvel évènement
		}
		
		/********************************************************************/
		// On supprime l'objet de l'inventaire (il est maintenant à la poste)
		$req    = "DELETE FROM perso_objets WHERE perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $objets_poste->opost_obj_cod), $stmt);		

		// Mettre la ligne d'événement correpondante
		$ligne_evt->levt_tevt_cod = 103;					
		$ligne_evt->levt_texte = '[perso_cod1] a déposé l’objet « ' . $nom_objet . ' » (' . (1*$objets_poste->opost_obj_cod) . ') au relais de la poste pour [cible].';
		$ligne_evt->levt_cible = $objets_poste->opost_dest_perso_cod ;			
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';
		$ligne_evt->stocke(true);		// Nouvel évènement	
		
		return true;
		
	}
	
}
