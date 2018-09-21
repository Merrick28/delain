<?php
/**
 * includes/class.aquete_perso.php
 */

/**
 * Class aquete_perso
 *
 * Gère les objets BDD de la table aquete_perso
 */
class aquete_perso
{
    var $aqperso_cod;
    var $aqperso_perso_cod;
    var $aqperso_aquete_cod;
    var $aqperso_etape_cod = 0;
    var $aqperso_actif = 'O';
    var $aqperso_nb_realisation = 0;
    var $aqperso_nb_termine = 0;
    var $aqperso_date_debut;
    var $aqperso_date_fin;

    function __construct()
    {

        $this->aqperso_date_debut = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de aquete_perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_perso where aqperso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqperso_cod = $result['aqperso_cod'];
        $this->aqperso_perso_cod = $result['aqperso_perso_cod'];
        $this->aqperso_aquete_cod = $result['aqperso_aquete_cod'];
        $this->aqperso_etape_cod = $result['aqperso_etape_cod'];
        $this->aqperso_actif = $result['aqperso_actif'];
        $this->aqperso_nb_realisation = $result['aqperso_nb_realisation'];
        $this->aqperso_nb_termine = $result['aqperso_nb_termine'];
        $this->aqperso_date_debut = $result['aqperso_date_debut'];
        $this->aqperso_date_fin = $result['aqperso_date_fin'];
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
        if($new)
        {
            $req = "insert into quetes.aquete_perso (
            aqperso_perso_cod,
            aqperso_aquete_cod,
            aqperso_etape_cod,
            aqperso_actif,
            aqperso_nb_realisation,
            aqperso_nb_termine,
            aqperso_date_debut,
            aqperso_date_fin                        )
                    values
                    (
                        :aqperso_perso_cod,
                        :aqperso_aquete_cod,
                        :aqperso_etape_cod,
                        :aqperso_actif,
                        :aqperso_nb_realisation,
                        :aqperso_nb_termine,
                        :aqperso_date_debut,
                        :aqperso_date_fin                        )
    returning aqperso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_aquete_cod" => $this->aqperso_aquete_cod,
                ":aqperso_etape_cod" => $this->aqperso_etape_cod,
                ":aqperso_actif" => $this->aqperso_actif,
                ":aqperso_nb_realisation" => $this->aqperso_nb_realisation,
                ":aqperso_nb_termine" => $this->aqperso_nb_termine,
                ":aqperso_date_debut" => $this->aqperso_date_debut,
                ":aqperso_date_fin" => $this->aqperso_date_fin,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_perso
                    set
            aqperso_perso_cod = :aqperso_perso_cod,
            aqperso_aquete_cod = :aqperso_aquete_cod,
            aqperso_etape_cod = :aqperso_etape_cod,
            aqperso_actif = :aqperso_actif,
            aqperso_nb_realisation = :aqperso_nb_realisation,
            aqperso_nb_termine = :aqperso_nb_termine,
            aqperso_date_debut = :aqperso_date_debut,
            aqperso_date_fin = :aqperso_date_fin                        where aqperso_cod = :aqperso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperso_cod" => $this->aqperso_cod,
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_aquete_cod" => $this->aqperso_aquete_cod,
                ":aqperso_etape_cod" => $this->aqperso_etape_cod,
                ":aqperso_actif" => $this->aqperso_actif,
                ":aqperso_nb_realisation" => $this->aqperso_nb_realisation,
                ":aqperso_nb_termine" => $this->aqperso_nb_termine,
                ":aqperso_date_debut" => $this->aqperso_date_debut,
                ":aqperso_date_fin" => $this->aqperso_date_fin,
            ),$stmt);
        }
    }

    // Créé une nouvelle instance de la quete #$aquete_cod pour le $perso_cod
    // $trigger est un tableau qui contient les elements déclencheurs de la quete
    // Rreourne rien si tout c'est bien passé, sinon un message d'erreur
    function nouvelle_instance($perso_cod, $aquete_cod, $trigger)
    {
        $pdo = new bddpdo;
            
        $quete = new aquete();
        $quete->charge($aquete_cod);

        //On regarde les conditions
        $req = "select *  from quetes.aquete_perso where aqperso_perso_cod = ? and aqperso_aquete_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $aquete_cod),$stmt);
        $result = $stmt->fetch();
        $new = true ;
        if ($result)
        {
            // La quete a déjà été faite, on vérifié si on peut la refaire
            $new = false ;
            $this->charge($result["aqperso_cod"]);
            if ($this->aqperso_actif=='O') return "Vous avez déjà démarré cette quête, vous ne pouvez la recommencer sans terminer la précédente";
            if ($this->aqperso_nb_realisation>=$quete->aquete_nb_max_rejouable) return "Vous avez déjà terminé cette quête {$this->aqperso_nb_realisation} fois, il ne vous est plus possible de la refaire une fois de plus";
            if ($quete->get_nb_en_cours()>=$quete->aquete_nb_max_instance) return "Il n'est pas possible de commencer cette quête actuellement.";
            if ($quete->get_nb_total()>=$quete->aquete_nb_max_quete) return "Cette quête est maintenant fermé.";
        }

        // Bon on a passé tous les tests, on ouvre ou ré-ouvre la quete
        $this->aqperso_perso_cod = $perso_cod;
        $this->aqperso_aquete_cod = $aquete_cod;

        $element = new aquete_element();
        $element->charge($trigger[aqelem_cod]);
        if (($element->aqelem_aquete_cod!=$aquete_cod) && ($element->aqelem_misc_cod!=0)) return "Vous essayez commencer par l'étape d'une autre quête ?";
        if ($element->aqelem_misc_cod<0) return "Le choix pour démarrer la quête n'est pas valide!";

        if ($element->aqelem_misc_cod==0)
            $this->aqperso_etape_cod = $quete->aquete_etape_cod ;   // on commence par le première étape de la quête
        else
            $this->aqperso_etape_cod = $element->aqelem_misc_cod ;   // on commence par le choix demandé !

        $this->aqperso_actif = 'O';
        $this->aqperso_nb_realisation ++ ;
        $this->aqperso_date_debut = date('Y-m-d H:i:s');
        $this->aqperso_date_fin = NULL;

        $this->stocke($new) ;


        // Il reste à générer les objets de cette quête dédié au perso!!!

        return "" ;     // Ok!
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_perso
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqperso_cod  from quetes.aquete_perso order by aqperso_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso;
            $temp->charge($result["aqperso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select aqperso_cod  from quetes.aquete_perso where " . substr($name, 6) . " = ? order by aqperso_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_perso;
                        $temp->charge($result["aqperso_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if(count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_perso');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}