<?php

/**
 * includes/class.objets_poste.php
 */

/**
 * Class objets_poste
 *
 * Gère les objets BDD de la table objets_poste
 */


class objets_poste
{
	//-------------- Constante de l'objet objets_poste
	private $frais_de_port_par_kilo = null ;		//si null, le parametre global 129 est utilisé (anciennement  100)
	private $delai_livraison = null ;				//si null, le parametre global 130 est utilisé (anciennement "5 DAYS")	
	private $delai_confiscation = null ;			//si null, le parametre global 131 est utilisé (anciennement "2 MONTHS")			
  
	//-------------- Données de l'objet récupérer/sauvegarder en DB
    var $opost_cod ;
    var $opost_colis_cod ;
    var $opost_obj_cod ;
    var $opost_emet_perso_cod ;
    var $opost_emet_pos_cod ;
    var $opost_dest_perso_cod ;
    var $opost_date_poste ;   
    var $opost_prix_demande ;   
	  

    function __construct()
    {  
		$this->opost_date_poste = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de objets_poste
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from objets_poste where opost_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->opost_cod          	   = $result['opost_cod'];
        $this->opost_colis_cod         = $result['opost_colis_cod'];
        $this->opost_obj_cod           = $result['opost_obj_cod'];
        $this->opost_emet_perso_cod    = $result['opost_emet_perso_cod'];
        $this->opost_emet_pos_cod      = $result['opost_emet_pos_cod'];
        $this->opost_dest_perso_cod    = $result['opost_dest_perso_cod'];
        $this->opost_date_poste        = $result['opost_date_poste'];
        $this->opost_prix_demande      = $result['opost_prix_demande'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into objets_poste (
                         opost_colis_cod,
                         opost_obj_cod,
                         opost_emet_perso_cod,
                         opost_emet_pos_cod,
                         opost_dest_perso_cod,
                         opost_date_poste,
						 opost_prix_demande)
                    values
                    (
                        :opost_colis_cod,
                        :opost_obj_cod,
                        :opost_emet_perso_cod,
                        :opost_emet_pos_cod,
                        :opost_dest_perso_cod,
                        :opost_date_poste,
                        :opost_prix_demande
                    )
    returning opost_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":opost_colis_cod"          => $this->opost_colis_cod,
               ":opost_obj_cod"            => $this->opost_obj_cod,
               ":opost_emet_perso_cod"     => $this->opost_emet_perso_cod,
               ":opost_emet_pos_cod"       => $this->opost_emet_pos_cod,
               ":opost_dest_perso_cod"     => $this->opost_dest_perso_cod,
               ":opost_date_poste"         => $this->opost_date_poste,
               ":opost_prix_demande"       => $this->opost_prix_demande
               ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update objets_poste
                    set
                opost_colis_cod =       :opost_colis_cod,
                opost_obj_cod =         :opost_obj_cod,
                opost_emet_perso_cod =  :opost_emet_perso_cod,
                opost_emet_pos_cod =    :opost_emet_pos_cod,
                opost_dest_perso_cod =  :opost_dest_perso_cod,
                opost_date_poste =      :opost_date_poste,
                opost_prix_demande =    :opost_prix_demande
            where opost_cod = :opost_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":opost_cod"             => $this->opost_cod,
               ":opost_colis_cod"       => $this->opost_colis_cod,
               ":opost_obj_cod"         => $this->opost_obj_cod,
               ":opost_emet_perso_cod"  => $this->opost_emet_perso_cod,
               ":opost_emet_pos_cod"    => $this->opost_emet_pos_cod,
               ":opost_dest_perso_cod"  => $this->opost_dest_perso_cod,
               ":opost_date_poste"      => $this->opost_date_poste,
               ":opost_prix_demande"    => $this->opost_prix_demande
               ), $stmt);
        }
    }

	 /**
     * supprime l'enregistrement de objets_poste
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function supprime($code="")
    {
		if ($code=="") $code = $this->opost_cod;
        $pdo    = new bddpdo;
        $req    = "DELETE from objets_poste where opost_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
  	    if ($stmt->rowCount()==0) 
        {
            return false;
        }

        return true;
    }
	
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objets_poste
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select opost_cod from objets_poste order by opost_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new objets_poste;
            $temp->charge($result["opost_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }
	
    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6))
        {
            case 'getBy_':
                if (property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "select opost_cod from objets_poste where " . substr($name, 6) . " = ? order by opost_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new objets_poste;
                        $temp->charge($result["opost_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }

    //----------------------------------------
    /**
     * Retourne un texte avec la zone d'étage désservies
     * @param integer $pos_cod => position du dépot
     * @param boolean $short => si vrai, texte court
     * @return text
     */
    function getTexteZoneCouverture($pos_cod, $short=false)
    {
        // position du relais de -- livraison --
        $pos = new positions();
        $pos->charge($pos_cod);

        if ($short)
        {
            if ($pos->pos_etage>=-5)
            {
                return "du 0 au -5";
            }
            else
            {
                return "du -6 et au dessous";
            }
        }
        else
        {
            if ($pos->pos_etage>=-5)
            {
                return "étages de la surface au -5";
            }
            else
            {
                return "étages du -6 et au dessous";
            }
        }
    }

	//----------------------------------------
	//fonction interne pour récupérer les frais de port dans les paramètres globaux
	function _frais_de_port_par_kilo()
	{
		if ($this->frais_de_port_par_kilo == null)
		{
			//recupération du parametre global du jeux
			$param = new parametres();
			$this->frais_de_port_par_kilo = $param->getparm(129);		//Parametre 129
			
		}
		return $this->frais_de_port_par_kilo;
			
	}

	//----------------------------------------
    /**
     * Retourne les frais de port en fonction du poids d'un objet
     * @param integer $poids => poids de l'objet
     * @return numeric
     */
    function getFraisDePort($poids)
    {
		return $poids * $this->_frais_de_port_par_kilo() ;             
    }	

	//----------------------------------------
	//fonction interne pour récupérer les delais de livraison dans les paramètres globaux
	function _delai_livraison()
	{
		if ($this->delai_livraison == null)
		{
			//recupération du parametre global du jeux
			$param = new parametres();
			$this->delai_livraison = $param->getparm(130);		//Parametre 130
			
		}
		return $this->delai_livraison;	
	}
	
	//----------------------------------------
    /**
     * Retourne la date de livraison d'un objet
     * @return date
     */
    function getDateLivraison()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->_delai_livraison()));       
    }	

	//----------------------------------------
    /**
     * Retourne vrai si l'objet peut être retiré de la poste
     * @param integer $etage_numero => étage de reception
     * @return boolean
     */
    function estLivrable($pos_cod)
    {
        // position du relais de -- livraison --
        $pos1 = new positions();
        $pos1->charge($this->opost_emet_pos_cod);

        // position du relais de -- reception --
        $pos2 = new positions();
        $pos2->charge($pos_cod);

        if ((($pos1->pos_etage<=-5)&&($pos2->pos_etage>-5)) || (($pos1->pos_etage>-5)&&($pos2->pos_etage<=-5)))
        {
            return false;   // le receptionneur n'est pas dans la zone de couverture du relais de livraison
        }

		return date('Y-m-d H:i:s') >= $this->getDateLivraison() ;       
    }	

	//----------------------------------------
	//fonction interne pour récupérer les delais de livraison dans les paramètres globaux
	function _delai_confiscation()
	{
		if ($this->delai_confiscation == null)
		{
			//recupération du parametre global du jeux
			$param = new parametres();
			$delai_confiscation = $param->getparm(131);					// Parametre 131
			if (1*$delai_confiscation<=0) $delai_confiscation=1;		// 1 mois minimum (pour éviter la confiscation immédiate après un depot)
			$this->delai_confiscation = $delai_confiscation." MONTHS";  // String pour strtotime
			
		}
		return $this->delai_confiscation;	
	}
		
	//----------------------------------------
    /**
     * Retourne la date de confiscation d'un objet
     * @return date
     */
    function getDateConfiscation()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->_delai_confiscation()));       
    }			

	//----------------------------------------
    /**
     * Retourne vrai si l'objet doit-être confisqué par la poste
     * @return boolean
     */
    function estConfiscable()
    {
		return date('Y-m-d H:i:s') >= $this->getDateConfiscation() ;       
    }	

	//----------------------------------------
    /**
     * Supprime l'objet de la poste ainsi que l'objet lui-meme si il est confiscable
     * @param integer $perso_cod => le perso qui est à l'origine de la confiscation (emmeteur ou destinataire)		 
     * @return boolean : true si l'objet a été bien confisqué
     */
    function Confisque($perso_cod)
    {
		if (!$this->estConfiscable())
		{
			return false;
		}

	    $pdo    = new bddpdo;	

		/********************************************************************/
		// On vérifie que l’objet existe et on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT obj_nom_generique FROM objets INNER JOIN objet_generique on gobj_cod = obj_gobj_cod WHERE obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie !
        }	
		$nom_objet = $result["obj_nom_generique"] ; 
		

		$this->supprime();		//supression de l'objet dans la base avant l'objet lui même (car il possèdes des clés étrangères)

		/********************************************************************/
		// On supprime l'objet de la base identification et objets
		$req    = "DELETE FROM perso_identifie_objet WHERE pio_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);	
	
		/********************************************************************/
		// On supprime l'objet et de la base des objets		
		$req    = "DELETE FROM objets WHERE obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);		

		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements (une par perso)
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_tevt_cod = 103;		
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';

		// evenement pour l'emetteur
		$ligne_evt->levt_perso_cod1 = $this->opost_emet_perso_cod   ;
		$ligne_evt->levt_cible = $this->opost_dest_perso_cod  ;			
		$ligne_evt->levt_texte = "L’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [perso_cod1] pour [cible] a été consfisqué par le relais de la poste.";
		$ligne_evt->stocke(true);		// Nouvel évènement	

		// evenement pour le destinataire (si le perso du destinataire n'a pas été détruit)
		if ($this->opost_dest_perso_cod)
		{
			$ligne_evt->levt_perso_cod1 = $this->opost_dest_perso_cod   ;
			$ligne_evt->levt_cible = $this->opost__perso_cod  ;			
			$ligne_evt->levt_texte = "L’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [cible] pour [perso_cod1] a été consfisqué par le relais de la poste.";
			$ligne_evt->stocke(true);		// Nouvel évènement	
		}

        /********************************************************************/
        // mettre dans le fichier de log pour un suivi
        /********************************************************************/
        $p1 =  new Perso;
        $p1->charge($this->opost_dest_perso_cod);
        $p2 =  new Perso;
        $p2->charge($this->opost_emet_perso_cod);
        $textline= "L’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par ".$p2->perso_nom."(" . (1*$this->opost_emet_perso_cod) . ") pour ".$p1->perso_nom."(" . (1*$this->opost_dest_perso_cod) . ") a été consfisqué.";
        $this->writelog($textline);

		return true; // objet a été confisqué!
    }	

	
    /**
     * Retourne un tableau du matos qu'un perso peut envoyer par les relais de la poste
     * @param $perso_cod : chercher dans l'inventaire de ce perso 
	 * @return array
     */
    function getObjetsDeposableRelaisPoste($perso_cod)
    { 
	    $retour = array();
		
        $pdo    = new bddpdo;
        $req    = "select obj_etat, tobj_cod, gobj_cod, tobj_libelle, CASE WHEN perobj_identifie = 'N' THEN obj_nom_generique ELSE obj_nom END obj_nom, obj_cod, obj_poids, gobj_tobj_cod, gobj_url
	               from perso_objets
	               INNER JOIN objets ON obj_cod = perobj_obj_cod
	               INNER JOIN objet_generique ON gobj_cod = obj_gobj_cod
	               INNER JOIN type_objet ON tobj_cod = gobj_tobj_cod
	               WHERE perobj_perso_cod = :perobj_perso_cod
	               AND perobj_equipe = 'N'
	               AND perobj_identifie = 'O'
	               AND gobj_deposable = 'O'
	               AND gobj_postable = 'O'
	               AND obj_enchantable != 2
	               ORDER BY tobj_libelle,gobj_nom ";

                    //$types_ventes_gros = "(5, 11, 17, 18, 19, 21, 22, 28, 30, 34)";
                    // Objets interdits dans les relais: (vente en gros+ qq elements specifiques)
                    // 5;"Rune"
                    // 7;"Relique"
                    // 11;"Objet de quête"
                    // 14;"Poisson"
                    // 17;"Minerai"
                    // 19;"Pierre précieuse"
                    // 21;"Potion"
                    // 22;"Plantes"
                    // 26;"Glyphe"
                    // 28;"Espèce Minérale"
                    // 30;"Ingrédient magique"
                    // 31;"Quiddités"
                    // 34;"Gemme"
                    // 35;"Clé"
                    // 24;"peau magique"
                    // 20;"parchemin"

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perobj_perso_cod" => $perso_cod), $stmt);
		while ( $result = $stmt->fetch() )
        { 
            $retour[] = $result ;
        }

        return $retour;		
	}
		
    /**
     * Retourne true si l'opération c'est bien déroulée
	 * Cette fonction réalise les actions nécéssaires dans l'inventaire du perso emmeteur pour pour lui retirer l'objet à la poste:
	 *   - Suppression des transactions en cours sur l'objet s'il y en avaient
	 *   - suppression de l'objet de l'inventaire
	 *   - Ajout d'une ligne d'évenement
	 *   - sauvegarde le present objet
	 * @return boolean
     */
	 
    function deposeObjetRelaisPoste()
    { 
        $pdo    = new bddpdo;
		
		/********************************************************************/
		// On vérifie que l’objet est bien dans l’inventaire du perso, on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT perobj_cod, obj_nom_generique 
				   	FROM perso_objets 
					INNER JOIN objets ON obj_cod = perobj_obj_cod 
					INNER JOIN objet_generique on gobj_cod = obj_gobj_cod 
					WHERE perobj_perso_cod = :opost_emet_perso_cod AND perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_emet_perso_cod" => $this->opost_emet_perso_cod,":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie (objet pas/plus dans dans l'inventaire)!
        }	
		$nom_objet = $result["obj_nom_generique"] ; 

		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements 
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_perso_cod1 =  $this->opost_emet_perso_cod ;
				
		/********************************************************************/
		// On supprime les transactions sur l'objet s'il y en avait 
		/********************************************************************/
		$req    = "DELETE FROM transaction WHERE tran_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);
		if ($stmt->rowCount()>0) 
		{
			// Mettre la ligne d'événement correpondante
			$ligne_evt->levt_tevt_cod = 17;			
			$ligne_evt->levt_texte = "La transaction en cours sur l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") a été annulée !";
			$ligne_evt->levt_lu = 'O';
			$ligne_evt->levt_visible = 'N';
			$ligne_evt->stocke(true);	// Nouvel évènement
		}
		
		/********************************************************************/
		// On supprime l'objet de l'inventaire (il est maintenant à la poste)
		$req    = "DELETE FROM perso_objets WHERE perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);		
	
		// Cet un nouveau dépot, il faut l'ajouter dans la base de donnée
		$this->stocke(true);
					
		// Mettre la ligne d'événement correpondante
		$ligne_evt->levt_tevt_cod = 101;					
		$ligne_evt->levt_texte = "[perso_cod1] a déposé l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") au relais de la poste pour [cible].";
		$ligne_evt->levt_cible = $this->opost_dest_perso_cod ;			
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';
		$ligne_evt->stocke(true);		// Nouvel évènement	

        /********************************************************************/
        // mettre dans le fichier de log pour un suivi
        /********************************************************************/
        $p1 =  new Perso;
        $p1->charge($this->opost_emet_perso_cod);
        $p2 =  new Perso;
        $p2->charge($this->opost_dest_perso_cod);
        $textline=$p1->perso_nom."(" . (1*$this->opost_emet_perso_cod) . ") a déposé l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") pour ".$p2->perso_nom."(" . (1*$this->opost_dest_perso_cod) . ").";
        $this->writelog($textline);

		return true;
	}
		
    /**
     * Retourne true si l'opération c'est bien déroulée
	 * Cette fonction réalise les actions nécéssaires dans l'inventaire du perso destinataire pour ajouter un objet qui vient d'être retiré:
	 *   - ajout de l'objet de l'inventaire	 
	 *   - Ajout d'une ligne d'évenement
	 *   - supression du présent objet de la base
	 * @return boolean
     */
	 
    function retraitObjetRelaisPoste()
    { 
        $pdo    = new bddpdo;
		
		/********************************************************************/
		// Vérifier si l'objet a déjà été identifié par le perso
		/********************************************************************/
		$req    = "SELECT pio_cod FROM perso_identifie_objet WHERE pio_perso_cod=:pio_perso_cod AND pio_obj_cod=:pio_obj_cod ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pio_perso_cod" => $this->opost_dest_perso_cod,":pio_obj_cod" => $this->opost_obj_cod), $stmt);
        $perobj_identifie = ($stmt->rowCount()==0) ? 'N' : 'O' ;

		
		/********************************************************************/
		// On insert l'objet dans l’inventaire du perso
		/********************************************************************/
		$req    = "INSERT INTO perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie) VALUES(:perobj_perso_cod, :perobj_obj_cod, :perobj_identifie);";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perobj_perso_cod" => $this->opost_dest_perso_cod,":perobj_obj_cod" => $this->opost_obj_cod, ":perobj_identifie" => $perobj_identifie), $stmt);
        if ($stmt->rowCount()==0) 
        {
            return false;	// Anomalie !
        }	

		// L'objet a été insérer dans l'inventaire on le supprime immediatement du relais poste (action en base)
		$this->supprime();	
		
		/********************************************************************/
		// On vérifie que l’objet est bien dans l’inventaire du perso, on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT perobj_cod, obj_nom_generique 
				   	FROM perso_objets 
					INNER JOIN objets ON obj_cod = perobj_obj_cod 
					INNER JOIN objet_generique on gobj_cod = obj_gobj_cod 
					WHERE perobj_perso_cod = :opost_dest_perso_cod AND perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_dest_perso_cod" => $this->opost_dest_perso_cod,":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie !
        }	
		$nom_objet = $result["obj_nom_generique"] ; 
		
		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements 
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_perso_cod1 =  $this->opost_dest_perso_cod ;							

		// Mettre la ligne d'événement correpondante
		$ligne_evt->levt_tevt_cod = 102;				 //"[perso_cod1] a retiré un objet au relais de la poste."	
		$ligne_evt->levt_texte = "Au relais de la poste [perso_cod1] a retiré l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [cible].";
		$ligne_evt->levt_cible = $this->opost_emet_perso_cod ;			
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';
		$ligne_evt->stocke(true);		// Nouvel évènement

        /********************************************************************/
        // mettre dans le fichier de log pour un suivi
        /********************************************************************/
        $p1 =  new Perso;
        $p1->charge($this->opost_dest_perso_cod);
        $p2 =  new Perso;
        $p2->charge($this->opost_emet_perso_cod);
        $textline=$p1->perso_nom."(" . (1*$this->opost_dest_perso_cod) . ") a retiré l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par ".$p2->perso_nom."(" . (1*$this->opost_emet_perso_cod) . ").";
        $this->writelog($textline);

		return true;	
	}
    /**
     * Cette fonction log dans dans le fichier dédié
     * @param $textline : ligne à logguer
     */
    function writelog($textline)
    {
        $file = __DIR__ . "/../www/logs/relais_poste.log";
        @file_put_contents($file, date("Y-m-d H:i:s")." : ".$textline."\n",  FILE_APPEND);
    }
}
