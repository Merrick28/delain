<?php
/**
 * includes/class.aquete_etape.php
 */

/**
 * Class aquete_etape
 *
 * Gère les objets BDD de la table aquete_etape
 */
class aquete_etape
{
    var $aqetape_cod;
    var $aqetape_nom;
    var $aqetape_aquete_cod;
    var $aqetape_aqetaptemp_cod;
    var $aqetape_parametres;
    var $aqetape_texte;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_etape
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_etape where aqetape_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqetape_cod = $result['aqetape_cod'];
        $this->aqetape_nom = $result['aqetape_nom'];
        $this->aqetape_aquete_cod = $result['aqetape_aquete_cod'];
        $this->aqetape_aqetaptemp_cod = $result['aqetape_aqetaptemp_cod'];
        $this->aqetape_parametres = $result['aqetape_parametres'];
        $this->aqetape_texte = $result['aqetape_texte'];
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
            $req = "insert into quetes.aquete_etape (
            aqetape_nom,
            aqetape_aquete_cod,
            aqetape_aqetaptemp_cod,
            aqetape_parametres,
            aqetape_texte                        )
                    values
                    (
                        :aqetape_nom,
                        :aqetape_aquete_cod,
                        :aqetape_aqetaptemp_cod,
                        :aqetape_parametres,
                        :aqetape_texte                        )
    returning aqetape_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetaptemp_cod" => $this->aqetape_aqetaptemp_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_etape
                    set
            aqetape_nom = :aqetape_nom,
            aqetape_aquete_cod = :aqetape_aquete_cod,
            aqetape_aqetaptemp_cod = :aqetape_aqetaptemp_cod,
            aqetape_parametres = :aqetape_parametres,
            aqetape_texte = :aqetape_texte                        where aqetape_cod = :aqetape_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_cod" => $this->aqetape_cod,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetaptemp_cod" => $this->aqetape_aqetaptemp_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
            ),$stmt);
        }
    }

    /**
     * supprime l'enregistrement
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function supprime($code="")
    {
        // Si un code est fourni, on doit charger l'élément
        if ($code=="") $code = $this->aqetape_cod;

        // On doit supprimer les éléments qui ont été préparé pour cette étape.
        $element = new aquete_element;
        $element->deleteBy_aqetape_cod($code) ;

        $pdo    = new bddpdo;
        $req    = "DELETE from quetes.aquete_etape where aqetape_cod = ?";
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
     * @return \aquete_etape
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqetape_cod  from quetes.aquete_etape order by aqetape_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_etape;
            $temp->charge($result["aqetape_cod"]);
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
                    $req = "select aqetape_cod  from quetes.aquete_etape where " . substr($name, 6) . " = ? order by aqetape_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_etape;
                        $temp->charge($result["aqetape_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_etape');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}