<?php
/**
 * includes/class.trackvg.php
 */

/**
 * Class trackvg
 *
 * Gère les objets BDD de la table trackvg
 */
class trackvg
{
    var $tgv_cod;
    var $tgv_varname;
    var $tgv_page;
    var $tvg_type;
    var $tgv_traite = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de trackvg
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from trackvg where tgv_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->tgv_cod     = $result['tgv_cod'];
        $this->tgv_varname = $result['tgv_varname'];
        $this->tgv_page    = $result['tgv_page'];
        $this->tvg_type    = $result['tvg_type'];
        $this->tgv_traite  = $result['tgv_traite'];
        return true;
    }

    function getByVarPage($var,$page)
    {
        $pdo  = new bddpdo;
        $req  = "select tgv_cod from trackvg where tgv_page = :page and tgv_varname = :var";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":page" => $page,":var" => $var),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['tgv_cod']);
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into trackvg (
            tgv_varname,
            tgv_page,
            tvg_type,
            tgv_traite                        )
                    values
                    (
                        :tgv_varname,
                        :tgv_page,
                        :tvg_type,
                        :tgv_traite                        )
    returning tgv_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tgv_varname" => $this->tgv_varname,
                                      ":tgv_page"    => $this->tgv_page,
                                      ":tvg_type"    => $this->tvg_type,
                                      ":tgv_traite"  => $this->tgv_traite,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update trackvg
                    set
            tgv_varname = :tgv_varname,
            tgv_page = :tgv_page,
            tvg_type = :tvg_type,
            tgv_traite = :tgv_traite                        where tgv_cod = :tgv_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":tgv_cod"     => $this->tgv_cod,
                                      ":tgv_varname" => $this->tgv_varname,
                                      ":tgv_page"    => $this->tgv_page,
                                      ":tvg_type"    => $this->tvg_type,
                                      ":tgv_traite"  => $this->tgv_traite,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \trackvg
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select tgv_cod  from trackvg order by tgv_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new trackvg;
            $temp->charge($result["tgv_cod"]);
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
                    $req    = "select tgv_cod  from trackvg where " . substr($name, 6) . " = ? order by tgv_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new trackvg;
                        $temp->charge($result["tgv_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                } else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table trackvg');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}