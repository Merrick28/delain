<?php
/**
 * includes/class.aquete_etape_modele.php
 */

/**
 * Class aquete_etape_modele
 *
 * Gère les objets BDD de la table aquete_etape_modele
 */
class aquete_etape_modele
{
    var $aqetapmodel_cod;
    var $aqetapmodel_tag;
    var $aqetapmodel_nom;
    var $aqetapmodel_description;
    var $aqetapmodel_parametres;
    var $aqetapmodel_param_desc;
    var $aqetapmodel_modele;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_etape_modele
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_etape_modele where aqetapmodel_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqetapmodel_cod = $result['aqetapmodel_cod'];
        $this->aqetapmodel_tag = $result['aqetapmodel_tag'];
        $this->aqetapmodel_nom = $result['aqetapmodel_nom'];
        $this->aqetapmodel_description = $result['aqetapmodel_description'];
        $this->aqetapmodel_parametres = $result['aqetapmodel_parametres'];
        $this->aqetapmodel_param_desc = $result['aqetapmodel_param_desc'];
        $this->aqetapmodel_modele = $result['aqetapmodel_modele'];
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
            $req = "insert into quetes.aquete_etape_modele (
            aqetapmodel_tag,
            aqetapmodel_nom,
            aqetapmodel_description,
            aqetapmodel_parametres,
            aqetapmodel_param_desc,
            aqetapmodel_modele                   )
                    values
                    (
                        :aqetapmodel_tag,
                        :aqetapmodel_nom,
                        :aqetapmodel_description,
                        :aqetapmodel_parametres,
                        :aqetapmodel_param_desc,
                        :aqetapmodel_modele                      )
    returning aqetapmodel_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetapmodel_tag" => $this->aqetapmodel_tag,
                ":aqetapmodel_nom" => $this->aqetapmodel_nom,
                ":aqetapmodel_description" => $this->aqetapmodel_description,
                ":aqetapmodel_parametres" => $this->aqetapmodel_parametres,
                ":aqetapmodel_param_desc" => $this->aqetapmodel_param_desc,
                ":aqetapmodel_modele" => $this->aqetapmodel_modele,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_etape_modele
                    set
            aqetapmodel_tag = :aqetapmodel_tag,
            aqetapmodel_nom = :aqetapmodel_nom,
            aqetapmodel_description = :aqetapmodel_description,
            aqetapmodel_parametres = :aqetapmodel_parametres,
            aqetapmodel_param_desc = :aqetapmodel_param_desc,
            aqetapmodel_modele = :aqetapmodel_modele                        where aqetapmodel_cod = :aqetapmodel_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetapmodel_cod" => $this->aqetapmodel_cod,
                ":aqetapmodel_tag" => $this->aqetapmodel_tag,
                ":aqetapmodel_nom" => $this->aqetapmodel_nom,
                ":aqetapmodel_description" => $this->aqetapmodel_description,
                ":aqetapmodel_parametres" => $this->aqetapmodel_parametres,
                ":aqetapmodel_param_desc" => $this->aqetapmodel_param_desc,
                ":aqetapmodel_modele" => $this->aqetapmodel_modele,
            ),$stmt);
        }
    }


    function get_liste_parametres()
    {

        $retour = array();

        if ($this->aqetapmodel_parametres=="") return $retour;

        $l = explode(',', $this->aqetapmodel_parametres);
        $desc = explode('|', $this->aqetapmodel_param_desc);
        foreach ($l as $k => $param)
        {
            // Format de param=> [id:type|n%M] avec %M et |n%M  factultatif
            $t = explode(':', str_replace("]","", str_replace("[","", $param)));
            $id = 1*$t[0];
            $n = 1 ;        // Par défaut
            $M = 1;         // Par défaut

            if (strpos($t[1],'|') !== false)
            {
                // il y a des paramètres facultatifs
                $t = explode('|', $t[1]);
                $type = $t[0];
                if (strpos($t[1], '%') !== false)
                {
                    $t = explode('%', $t[1]);
                    $n = $t[0];
                    $M = $t[1];
                }
                else
                {
                    $n = $t[1] ;
                }
            }
            else
            {
                // il n'y a pas de paramètres facultatifs retoruner les valeurs par defaut
                $type = $t[1];
            }

            $retour[$id] = array( "type" => $type, "n" => (1*$n), "M" => (1*$M), 'desc' => $desc[$k] ,'raw' => $param, 'texte' => ($n>0 ? "$n " : "")."$type".( $M == 0 ? " parmi plusieurs" : ( $M == 1 ? "" : " parmi $M au max.")) );
        }
        return $retour;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_etape_modele
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqetapmodel_cod  from quetes.aquete_etape_modele order by aqetapmodel_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_etape_modele;
            $temp->charge($result["aqetapmodel_cod"]);
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
                    $req = "select aqetapmodel_cod  from quetes.aquete_etape_modele where " . substr($name, 6) . " = ? order by aqetapmodel_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_etape_modele;
                        $temp->charge($result["aqetapmodel_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_etape_modele');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}