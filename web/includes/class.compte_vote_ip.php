<?php
/**
 * includes/class.compte_vote_ip.php
 */

/**
 * Class compte_vote_ip
 *
 * Gère les objets BDD de la table compte_vote_ip
 */
class compte_vote_ip
{
    var $compte_vote_icompt_cod;
    var $compte_vote_date;
    var $compte_vote_cod;
    var $compte_vote_compte_cod;
    var $compte_vote_verifier = false;
    var $compte_vote_pour_delain = false;
    var $compte_vote_ip_compte;

    function __construct()
    {

        $this->compte_vote_date = date('Y-m-d H:i:s');
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
            $req  = "insert into compte_vote_ip (
            compte_vote_icompt_cod,
            compte_vote_date,
            compte_vote_compte_cod,
            compte_vote_verifier,
            compte_vote_pour_delain,
            compte_vote_ip_compte                        )
                    values
                    (
                        :compte_vote_icompt_cod,
                        :compte_vote_date,
                        :compte_vote_compte_cod,
                        :compte_vote_verifier,
                        :compte_vote_pour_delain,
                        :compte_vote_ip_compte                        )
    returning compte_vote_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compte_vote_icompt_cod" => $this->compte_vote_icompt_cod,
                ":compte_vote_date" => $this->compte_vote_date,
                ":compte_vote_compte_cod" => $this->compte_vote_compte_cod,
                ":compte_vote_verifier" => $this->compte_vote_verifier,
                ":compte_vote_pour_delain" => $this->compte_vote_pour_delain,
                ":compte_vote_ip_compte" => $this->compte_vote_ip_compte,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update compte_vote_ip
                    set
            compte_vote_icompt_cod = :compte_vote_icompt_cod,
            compte_vote_date = :compte_vote_date,
            compte_vote_compte_cod = :compte_vote_compte_cod,
            compte_vote_verifier = :compte_vote_verifier,
            compte_vote_pour_delain = :compte_vote_pour_delain,
            compte_vote_ip_compte = :compte_vote_ip_compte                        where compte_vote_cod = :compte_vote_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":compte_vote_icompt_cod" => $this->compte_vote_icompt_cod,
                ":compte_vote_date" => $this->compte_vote_date,
                ":compte_vote_cod" => $this->compte_vote_cod,
                ":compte_vote_compte_cod" => $this->compte_vote_compte_cod,
                ":compte_vote_verifier" => $this->compte_vote_verifier,
                ":compte_vote_pour_delain" => $this->compte_vote_pour_delain,
                ":compte_vote_ip_compte" => $this->compte_vote_ip_compte,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de compte_vote_ip
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from compte_vote_ip where compte_vote_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->compte_vote_icompt_cod  = $result['compte_vote_icompt_cod'];
        $this->compte_vote_date        = $result['compte_vote_date'];
        $this->compte_vote_cod         = $result['compte_vote_cod'];
        $this->compte_vote_compte_cod  = $result['compte_vote_compte_cod'];
        $this->compte_vote_verifier    = $result['compte_vote_verifier'];
        $this->compte_vote_pour_delain = $result['compte_vote_pour_delain'];
        $this->compte_vote_ip_compte   = $result['compte_vote_ip_compte'];
        return true;
    }

    function getByCompteTrue($code)
    {
        $pdo  = new bddpdo;
        $req  = "select compte_vote_cod from compte_vote_ip where compte_vote_compte_cod = ? and compte_vote_pour_delain = true";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['compte_vote_cod']);
    }

    function getByCompteTrueMois($code)
    {
        $pdo  = new bddpdo;
        $req  = "select compte_vote_cod from compte_vote_ip 
          where compte_vote_compte_cod = ? 
          and compte_vote_pour_delain = true
          and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['compte_vote_cod']);
    }

    function getVoteAValider($code)
    {
        $pdo  = new bddpdo;
        $req  = "select compte_vote_cod from compte_vote_ip 
          where compte_vote_compte_cod = ? 
          and compte_vote_verifier=false
          and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['compte_vote_cod']);
    }

    function getVoteRefus($code)
    {
        $pdo  = new bddpdo;
        $req  = "select compte_vote_cod from compte_vote_ip 
          where compte_vote_compte_cod = ? 
          and compte_vote_verifier  = true
          and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['compte_vote_cod']);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compte_vote_ip
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select compte_vote_cod  from compte_vote_ip order by compte_vote_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new compte_vote_ip;
            $temp->charge($result["compte_vote_cod"]);
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
                    $req    = "select compte_vote_cod  from compte_vote_ip where " . substr($name, 6) . " = ? order by compte_vote_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new compte_vote_ip;
                        $temp->charge($result["compte_vote_cod"]);
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
                die('Unknown method.');
        }
    }
}