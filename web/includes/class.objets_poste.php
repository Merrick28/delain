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
	private $frais_de_port_par_kilo = 100 ;
	private $delai_livraison = "5 DAYS" ;				
	private $delai_confiscation = "2 MONTHS" ;				
  
	//-------------- Données de l'objet récupérer/sauvegarder en DB
    var $opost_cod ;
    var $opost_colis_cod ;
    var $opost_obj_cod ;
    var $opost_emet_perso_cod ;
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
                         opost_dest_perso_cod,
                         opost_date_poste,
						 opost_prix_demande)
                    values
                    (
                        :opost_colis_cod,
                        :opost_obj_cod,
                        :opost_emet_perso_cod,
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
               ":opost_dest_perso_cod"  => $this->opost_dest_perso_cod,
               ":opost_date_poste"      => $this->opost_date_poste,
               ":opost_prix_demande"    => $this->opost_prix_demande
               ), $stmt);
        }
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
     * Retourne les frais de port en fonction du poids d'un objet
     * @param integer $poids => poids de l'objet	 
     * @global bdd_mysql $pdo
     * @return numeric
     */
    function getFraisDePort($poids)
    {
		return $poids * $this->frais_de_port_par_kilo ;             
    }	

	//----------------------------------------
    /**
     * Retourne la date de livraison d'un objet 
     * @global bdd_mysql $pdo
     * @return date
     */
    function getDateLivraison()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->delai_livraison));       
    }	

	//----------------------------------------
    /**
     * Retourne la date de confiscation d'un objet 
     * @global bdd_mysql $pdo
     * @return date
     */
    function getDateConfiscation()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->delai_confiscation));       
    }			


}
