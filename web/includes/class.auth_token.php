<?php
/**
 * includes/class.auth_token.php
 */

/**
 * Class auth_token
 *
 * Gère les objets BDD de la table auth_token
 */
class auth_token
{
    var $at_token;
    var $at_compt_cod;
    var $at_date;

    function __construct()
    {
        $this->at_date = date('Y-m-d H:i:s');
        if (rand(1, 100) <= 1)
        {
            $pdo = new bddpdo;
            $req = "delete from auth_token
				where at_date < now() - interval '12 hours'";
            $pdo->query($req);
        } //rand(1, 100) <= GC_GARBAGE
    }

    /**
     * Charge dans la classe un enregistrement de auth_token
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @throws Exception
     * @global bddpdo $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from auth_token where at_token = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->at_token     = $result['at_token'];
        $this->at_compt_cod = $result['at_compt_cod'];
        $this->at_date      = $result['at_date'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @throws Exception
     * @global bdd_mysql $pdo
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into auth_token (
            at_token,
                        at_compt_cod,
            at_date                        )
                    values
                    (
                     :at_token,   
                     :at_compt_cod,
                        :at_date                        )
    returning at_token as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":at_token"     => $this->at_token,
                                      ":at_compt_cod" => $this->at_compt_cod,
                                      ":at_date"      => $this->at_date,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update auth_token
                    set
            at_compt_cod = :at_compt_cod,
            at_date = :at_date                        where at_token = :at_token ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":at_token"     => $this->at_token,
                                      ":at_compt_cod" => $this->at_compt_cod,
                                      ":at_date"      => $this->at_date,
                                  ), $stmt);
        }
    }

    /**
     * Deletes a token
     * @return bool
     * @throws Exception
     */
    function delete()
    {
        $pdo = new bddpdo;
        $req = "delete from auth_token where at_token = :token";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":token"     => $this->at_token
                              ), $stmt);
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \auth_token
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select at_token  from auth_token order by at_token";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new auth_token;
            $temp->charge($result["at_token"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function create_token($compte)
    {
        $token = exec('uuidgen -r');
        $this->at_token = $token;
        $this->at_compt_cod = $compte->compt_cod;
        $this->stocke(true);
        return $token;
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
                    $req    = "select at_token  from auth_token where " . substr($name, 6) . " = ? order by at_token";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new auth_token;
                        $temp->charge($result["at_token"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table auth_token');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}