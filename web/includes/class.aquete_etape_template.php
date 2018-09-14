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
        $req = "select * from aquete_etape_template where aqetaptemp_cod = ?";
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
            $req = "insert into aquete_etape_template (
            aqetaptemp_tag,
            aqetaptemp_nom,
            aqetaptemp_description,
            aqetaptemp_parametres,
            aqetaptemp_template                   )
                    values
                    (
                        :aqetaptemp_tag,
                        :aqetaptemp_nom,
                        :aqetaptemp_description,
                        :aqetaptemp_parametres,
                        :aqetaptemp_template                      )
    returning aqetaptemp_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetaptemp_tag" => $this->aqetaptemp_tag,
                ":aqetaptemp_nom" => $this->aqetaptemp_nom,
                ":aqetaptemp_description" => $this->aqetaptemp_description,
                ":aqetaptemp_parametres" => $this->aqetaptemp_parametres,
                ":aqetaptemp_template" => $this->aqetaptemp_template,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update aquete_etape_template
                    set
            aqetaptemp_tag = :aqetaptemp_tag,
            aqetaptemp_nom = :aqetaptemp_nom,
            aqetaptemp_description = :aqetaptemp_description,
            aqetaptemp_parametres = :aqetaptemp_parametres,
            aqetaptemp_template = :aqetaptemp_template                        where aqetaptemp_cod = :aqetaptemp_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetaptemp_cod" => $this->aqetaptemp_cod,
                ":aqetaptemp_tag" => $this->aqetaptemp_tag,
                ":aqetaptemp_nom" => $this->aqetaptemp_nom,
                ":aqetaptemp_description" => $this->aqetaptemp_description,
                ":aqetaptemp_parametres" => $this->aqetaptemp_parametres,
                ":aqetaptemp_template" => $this->aqetaptemp_template,
            ),$stmt);
        }
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
        $req = "select aqetaptemp_cod  from aquete_etape_template order by aqetaptemp_cod";
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
                    $req = "select aqetaptemp_cod  from aquete_etape_template where " . substr($name, 6) . " = ? order by aqetaptemp_cod";
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