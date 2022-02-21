<?php
/**
 * includes/class.objet_position.php
 */

/**
 * Class objet_position
 *
 * Gère les objets BDD de la table objet_position
 */
class objet_position
{
    var $pobj_cod;
    var $pobj_obj_cod;
    var $pobj_pos_cod;
    var $pobj_dlache;

    function __construct()
    {

        $this->pobj_dlache = date('Y-m-d H:i:s');
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
            $req  = "insert into objet_position (
            pobj_obj_cod,
            pobj_pos_cod,
            pobj_dlache                        )
                    values
                    (
                        :pobj_obj_cod,
                        :pobj_pos_cod,
                        :pobj_dlache                        )
    returning pobj_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pobj_obj_cod" => $this->pobj_obj_cod,
                ":pobj_pos_cod" => $this->pobj_pos_cod,
                ":pobj_dlache" => $this->pobj_dlache,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update objet_position
                    set
            pobj_obj_cod = :pobj_obj_cod,
            pobj_pos_cod = :pobj_pos_cod,
            pobj_dlache = :pobj_dlache                        where pobj_cod = :pobj_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pobj_cod" => $this->pobj_cod,
                ":pobj_obj_cod" => $this->pobj_obj_cod,
                ":pobj_pos_cod" => $this->pobj_pos_cod,
                ":pobj_dlache" => $this->pobj_dlache,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de objet_position
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from objet_position where pobj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pobj_cod     = $result['pobj_cod'];
        $this->pobj_obj_cod = $result['pobj_obj_cod'];
        $this->pobj_pos_cod = $result['pobj_pos_cod'];
        $this->pobj_dlache  = $result['pobj_dlache'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objet_position
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select pobj_cod  from objet_position order by pobj_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new objet_position;
            $temp->charge($result["pobj_cod"]);
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
                    $req    = "select pobj_cod  from objet_position where " . substr($name, 6) . " = ? order by pobj_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new objet_position;
                        $temp->charge($result["pobj_cod"]);
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
                ob_start();
                debug_print_backtrace();
                $out = ob_get_contents();
                error_log($out);
                die('Unknown method.');
        }
    }
}