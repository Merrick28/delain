<?php
/**
 * includes/class.aquete.php
 */

/**
 * Class aquete
 *
 * Gère les objets BDD de la table aquete
 */
class aquete
{
    var $aquete_cod;
    var $aquete_nom = '';
    var $aquete_description = '';
    var $aquete_etape_cod = 0;
    var $aquete_actif = 'O';
    var $aquete_date_debut;
    var $aquete_date_fin;
    var $aquete_nb_max_instance = 1;
    var $aquete_nb_max_participant = 1;
    var $aquete_nb_max_rejouable = 1;
    var $aquete_nb_max_quete;

    function __construct()
    {
        $this->aquete_date_debut = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de aquete
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete where aquete_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aquete_cod = $result['aquete_cod'];
        $this->aquete_nom = $result['aquete_nom'];
        $this->aquete_description = $result['aquete_description'];
        $this->aquete_etape_cod = $result['aquete_etape_cod'];
        $this->aquete_actif = $result['aquete_actif'];
        $this->aquete_date_debut = $result['aquete_date_debut'];
        $this->aquete_date_fin = $result['aquete_date_fin'];
        $this->aquete_nb_max_instance = $result['aquete_nb_max_instance'];
        $this->aquete_nb_max_participant = $result['aquete_nb_max_participant'];
        $this->aquete_nb_max_rejouable = $result['aquete_nb_max_rejouable'];
        $this->aquete_nb_max_quete = $result['aquete_nb_max_quete'];
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
            $req = "insert into quetes.aquete 
                    (
                        aquete_nom,
                        aquete_description,
                        aquete_etape_cod,
                        aquete_actif,
                        aquete_date_debut,
                        aquete_date_fin,
                        aquete_nb_max_instance,
                        aquete_nb_max_participant,
                        aquete_nb_max_rejouable,
                        aquete_nb_max_quete
                    )
                    values
                    (
                        :aquete_nom,
                        :aquete_description,
                        :aquete_etape_cod,
                        :aquete_actif,
                        :aquete_date_debut,
                        :aquete_date_fin,
                        :aquete_nb_max_instance,
                        :aquete_nb_max_participant,
                        :aquete_nb_max_rejouable,
                        :aquete_nb_max_quete
                    )
                    returning aquete_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                    ":aquete_nom" => $this->aquete_nom,
                    ":aquete_description" => $this->aquete_description,
                    ":aquete_etape_cod" => $this->aquete_etape_cod,
                    ":aquete_actif" => $this->aquete_actif,
                    ":aquete_date_debut" => $this->aquete_date_debut,
                    ":aquete_date_fin" => $this->aquete_date_fin,
                    ":aquete_nb_max_instance" => $this->aquete_nb_max_instance,
                    ":aquete_nb_max_participant" => $this->aquete_nb_max_participant,
                    ":aquete_nb_max_rejouable" => $this->aquete_nb_max_rejouable,
                    ":aquete_nb_max_quete" => $this->aquete_nb_max_quete
            ),$stmt);

            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete
                    set
            aquete_nom = :aquete_nom,
            aquete_description = :aquete_description,
            aquete_etape_cod = :aquete_etape_cod,
            aquete_actif = :aquete_actif,
            aquete_date_debut = :aquete_date_debut,
            aquete_date_fin = :aquete_date_fin,
            aquete_nb_max_instance = :aquete_nb_max_instance,
            aquete_nb_max_participant = :aquete_nb_max_participant,
            aquete_nb_max_rejouable = :aquete_nb_max_rejouable,
            aquete_nb_max_quete = :aquete_nb_max_quete                     
            where aquete_cod = :aquete_cod ";

            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aquete_cod" => $this->aquete_cod,
                ":aquete_nom" => $this->aquete_nom,
                ":aquete_description" => $this->aquete_description,
                ":aquete_etape_cod" => $this->aquete_etape_cod,
                ":aquete_actif" => $this->aquete_actif,
                ":aquete_date_debut" => $this->aquete_date_debut,
                ":aquete_date_fin" => $this->aquete_date_fin,
                ":aquete_nb_max_instance" => $this->aquete_nb_max_instance,
                ":aquete_nb_max_participant" => $this->aquete_nb_max_participant,
                ":aquete_nb_max_rejouable" => $this->aquete_nb_max_rejouable,
                ":aquete_nb_max_quete" => $this->aquete_nb_max_quete
            ),$stmt);
        }
    }

    function get_nb_etape()
    {
        $etape = new aquete_etape;
        $etapes = $etape->getBy_aqetape_aquete_cod($this->aquete_cod);
        return !$etapes ? 0: sizof($etapes);
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aquete_cod from quetes.aquete order by aquete_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete;
            $temp->charge($result["aquete_cod"]);
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
                    $req = "select aquete_cod from quetes.aquete where " . substr($name, 6) . " = ? order by aquete_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete;
                        $temp->charge($result["aquete_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}