<?php
/**
 * includes/class.aquete_perso_journal.php
 */

/**
 * Class aquete_perso_journal
 *
 * Gère les objets BDD de la table aquete_perso_journal
 */
class aquete_perso_journal
{
    var $aqpersoj_cod;
    var $aqpersoj_date;
    var $aqpersoj_aqperso_cod;
    var $aqpersoj_realisation;
    var $aqpersoj_quete_step = 0;
    var $aqpersoj_texte;
    var $aqpersoj_lu = 'N' ;

    function __construct()
    {
        $this->aqpersoj_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de aquete_perso_journal
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_perso_journal where aqpersoj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqpersoj_cod = $result['aqpersoj_cod'];
        $this->aqpersoj_date = $result['aqpersoj_date'];
        $this->aqpersoj_aqperso_cod = $result['aqpersoj_aqperso_cod'];
        $this->aqpersoj_realisation = $result['aqpersoj_realisation'];
        $this->aqpersoj_quete_step = $result['aqpersoj_quete_step'];
        $this->aqpersoj_texte = $result['aqpersoj_texte'];
        $this->aqpersoj_lu = $result['aqpersoj_lu'];
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
            $req = "insert into quetes.aquete_perso_journal (
            aqpersoj_date,
            aqpersoj_aqperso_cod,
            aqpersoj_realisation,
            aqpersoj_quete_step,
            aqpersoj_texte,
            aqpersoj_lu                        )
                    values
                    (
                        :aqpersoj_date,
                        :aqpersoj_aqperso_cod,
                        :aqpersoj_realisation,
                        :aqpersoj_quete_step,
                        :aqpersoj_texte,
                        :aqpersoj_lu                        )
    returning aqpersoj_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqpersoj_date" => $this->aqpersoj_date,
                ":aqpersoj_aqperso_cod" => $this->aqpersoj_aqperso_cod,
                ":aqpersoj_realisation" => $this->aqpersoj_realisation,
                ":aqpersoj_quete_step" => $this->aqpersoj_quete_step,
                ":aqpersoj_texte" => $this->aqpersoj_texte,
                ":aqpersoj_lu" => $this->aqpersoj_lu,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_perso_journal
                    set
            aqpersoj_date = :aqpersoj_date,
            aqpersoj_aqperso_cod = :aqpersoj_aqperso_cod,
            aqpersoj_realisation = :aqpersoj_realisation,
            aqpersoj_quete_step = :aqpersoj_quete_step,
            aqpersoj_texte = :aqpersoj_texte,
            aqpersoj_lu = :aqpersoj_lu                        where aqpersoj_cod = :aqpersoj_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqpersoj_cod" => $this->aqpersoj_cod,
                ":aqpersoj_date" => $this->aqpersoj_date,
                ":aqpersoj_aqperso_cod" => $this->aqpersoj_aqperso_cod,
                ":aqpersoj_realisation" => $this->aqpersoj_realisation,
                ":aqpersoj_quete_step" => $this->aqpersoj_quete_step,
                ":aqpersoj_texte" => $this->aqpersoj_texte,
                ":aqpersoj_lu" => $this->aqpersoj_lu,
            ),$stmt);
        }
    }

    // retourne les journaux d'une quete trié par step
    function  getBy_perso_realisation($aqperso_cod, $realisation)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqpersoj_cod from quetes.aquete_perso_journal where aqpersoj_aqperso_cod = ? and aqpersoj_realisation = ? order by aqpersoj_quete_step ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqperso_cod, $realisation),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso_journal;
            $temp->charge($result["aqpersoj_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    // Charge le dernier step du journal (ou prepare la premièere page)
    function  chargeDernierePage($aqperso_cod, $realisation)
    {
        $pdo = new bddpdo;
        $req = "select aqpersoj_cod
                from quetes.aquete_perso_journal
                join (
                    select aqpersoj_aqperso_cod perso_cod, aqpersoj_realisation realisation, max(aqpersoj_quete_step) quete_step from quetes.aquete_perso_journal group by aqpersoj_aqperso_cod, aqpersoj_realisation
                ) page  on aqpersoj_aqperso_cod=perso_cod and aqpersoj_realisation=realisation and aqpersoj_quete_step=quete_step
                where aqpersoj_aqperso_cod=? and aqpersoj_realisation=? order by aqpersoj_cod desc ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqperso_cod, $realisation),$stmt);
        if ( $result = $stmt->fetch() )
        {
            $this->charge($result["aqpersoj_cod"]);
        }
        else
        {
            // Prépare avec le peu d'information que l'on a
            $this->aqpersoj_aqperso_cod = $aqperso_cod;
            $this->aqpersoj_realisation = $realisation;
            $this->quete_step = 0 ;
        }
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_perso_journal
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqpersoj_cod  from quetes.aquete_perso_journal order by aqpersoj_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso_journal;
            $temp->charge($result["aqpersoj_cod"]);
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
                    $req = "select aqpersoj_cod  from quetes.aquete_perso_journal where " . substr($name, 6) . " = ? order by aqpersoj_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_perso_journal;
                        $temp->charge($result["aqpersoj_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_perso_journal');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}