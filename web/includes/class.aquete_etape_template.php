<?php
/**
 * includes/class.aquete_etape_template.php
 */

/**
 * Class aquete_etape_template
 *
 * Gère les objets BDD de la table aquete_etape_template
 */
class aquete_etape_template
{
    var $aqetaptemp_cod;
    var $aqetaptemp_tag;
    var $aqetaptemp_nom;
    var $aqetaptemp_description;
    var $aqetaptemp_parametres;
    var $aqetaptemp_param_desc;
    var $aqetaptemp_template;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_etape_template
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_etape_template where aqetaptemp_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqetaptemp_cod = $result['aqetaptemp_cod'];
        $this->aqetaptemp_tag = $result['aqetaptemp_tag'];
        $this->aqetaptemp_nom = $result['aqetaptemp_nom'];
        $this->aqetaptemp_description = $result['aqetaptemp_description'];
        $this->aqetaptemp_parametres = $result['aqetaptemp_parametres'];
        $this->aqetaptemp_param_desc = $result['aqetaptemp_param_desc'];
        $this->aqetaptemp_template = $result['aqetaptemp_template'];
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
            $req = "insert into quetes.aquete_etape_template (
            aqetaptemp_tag,
            aqetaptemp_nom,
            aqetaptemp_description,
            aqetaptemp_parametres,
            aqetaptemp_param_desc,
            aqetaptemp_template                   )
                    values
                    (
                        :aqetaptemp_tag,
                        :aqetaptemp_nom,
                        :aqetaptemp_description,
                        :aqetaptemp_parametres,
                        :aqetaptemp_param_desc,
                        :aqetaptemp_template                      )
    returning aqetaptemp_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetaptemp_tag" => $this->aqetaptemp_tag,
                ":aqetaptemp_nom" => $this->aqetaptemp_nom,
                ":aqetaptemp_description" => $this->aqetaptemp_description,
                ":aqetaptemp_parametres" => $this->aqetaptemp_parametres,
                ":aqetaptemp_param_desc" => $this->aqetaptemp_param_desc,
                ":aqetaptemp_template" => $this->aqetaptemp_template,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_etape_template
                    set
            aqetaptemp_tag = :aqetaptemp_tag,
            aqetaptemp_nom = :aqetaptemp_nom,
            aqetaptemp_description = :aqetaptemp_description,
            aqetaptemp_parametres = :aqetaptemp_parametres,
            aqetaptemp_param_desc = :aqetaptemp_param_desc,
            aqetaptemp_template = :aqetaptemp_template                        where aqetaptemp_cod = :aqetaptemp_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetaptemp_cod" => $this->aqetaptemp_cod,
                ":aqetaptemp_tag" => $this->aqetaptemp_tag,
                ":aqetaptemp_nom" => $this->aqetaptemp_nom,
                ":aqetaptemp_description" => $this->aqetaptemp_description,
                ":aqetaptemp_parametres" => $this->aqetaptemp_parametres,
                ":aqetaptemp_param_desc" => $this->aqetaptemp_param_desc,
                ":aqetaptemp_template" => $this->aqetaptemp_template,
            ),$stmt);
        }
    }


    function get_liste_parametres()
    {

        $retour = array();

        $l = explode(',', $this->aqetaptemp_parametres);
        $desc = explode('|', $this->aqetaptemp_param_desc);
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

            $retour[$id] = array( "type" => $type, "n" => (1*$n), "M" => (1*$M), 'desc' => $desc[$k] ,'raw' => $param, 'texte' => "$n $type".( $M == 0 ? " parmi plusieurs" : ( $M == 1 ? "" : " parmi $M au max.")) );
        }
        return $retour;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_etape_template
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqetaptemp_cod  from quetes.aquete_etape_template order by aqetaptemp_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_etape_template;
            $temp->charge($result["aqetaptemp_cod"]);
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
                    $req = "select aqetaptemp_cod  from quetes.aquete_etape_template where " . substr($name, 6) . " = ? order by aqetaptemp_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_etape_template;
                        $temp->charge($result["aqetaptemp_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_etape_template');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}