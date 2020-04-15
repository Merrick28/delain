<?php
/**
 * includes/class.guilde_banque_transactions.php
 */

/**
 * Class guilde_banque_transactions
 *
 * Gère les objets BDD de la table guilde_banque_transactions
 */
class guilde_banque_transactions
{
    var $gbank_tran_cod;
    var $gbank_tran_gbank_cod;
    var $gbank_tran_perso_cod;
    var $gbank_tran_montant;
    var $gbank_tran_debit_credit;
    var $gbank_tran_date;
    /**
     * @var perso Perso qui a fait la transaction
     */
    var $perso;

    function __construct()
    {
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
            $req  = "insert into guilde_banque_transactions (
            gbank_tran_gbank_cod,
            gbank_tran_perso_cod,
            gbank_tran_montant,
            gbank_tran_debit_credit,
            gbank_tran_date                        )
                    values
                    (
                        :gbank_tran_gbank_cod,
                        :gbank_tran_perso_cod,
                        :gbank_tran_montant,
                        :gbank_tran_debit_credit,
                        :gbank_tran_date                        )
    returning gbank_tran_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gbank_tran_gbank_cod"    => $this->gbank_tran_gbank_cod,
                                      ":gbank_tran_perso_cod"    => $this->gbank_tran_perso_cod,
                                      ":gbank_tran_montant"      => $this->gbank_tran_montant,
                                      ":gbank_tran_debit_credit" => $this->gbank_tran_debit_credit,
                                      ":gbank_tran_date"         => $this->gbank_tran_date,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update guilde_banque_transactions
                    set
            gbank_tran_gbank_cod = :gbank_tran_gbank_cod,
            gbank_tran_perso_cod = :gbank_tran_perso_cod,
            gbank_tran_montant = :gbank_tran_montant,
            gbank_tran_debit_credit = :gbank_tran_debit_credit,
            gbank_tran_date = :gbank_tran_date                        where gbank_tran_cod = :gbank_tran_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":gbank_tran_cod"          => $this->gbank_tran_cod,
                                      ":gbank_tran_gbank_cod"    => $this->gbank_tran_gbank_cod,
                                      ":gbank_tran_perso_cod"    => $this->gbank_tran_perso_cod,
                                      ":gbank_tran_montant"      => $this->gbank_tran_montant,
                                      ":gbank_tran_debit_credit" => $this->gbank_tran_debit_credit,
                                      ":gbank_tran_date"         => $this->gbank_tran_date,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de guilde_banque_transactions
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from guilde_banque_transactions where gbank_tran_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->gbank_tran_cod          = $result['gbank_tran_cod'];
        $this->gbank_tran_gbank_cod    = $result['gbank_tran_gbank_cod'];
        $this->gbank_tran_perso_cod    = $result['gbank_tran_perso_cod'];
        $this->gbank_tran_montant      = $result['gbank_tran_montant'];
        $this->gbank_tran_debit_credit = $result['gbank_tran_debit_credit'];
        $this->gbank_tran_date         = $result['gbank_tran_date'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \guilde_banque_transactions
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gbank_tran_cod  from guilde_banque_transactions order by gbank_tran_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_banque_transactions;
            $temp->charge($result["gbank_tran_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByCompte($compte_banque)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    =
            "select gbank_tran_cod  from guilde_banque_transactions where gbank_tran_gbank_cod = :compte order by gbank_tran_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":compte" => $compte_banque), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new guilde_banque_transactions;
            $temp->charge($result["gbank_tran_cod"]);
            $retour[] = $temp;
            $ptemp    = new perso;
            $temp->charge($temp->gbank_tran_perso_cod);
            $temp->perso = $ptemp;
            unset($ptemp);
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
                    $req    =
                        "select gbank_tran_cod  from guilde_banque_transactions where " . substr($name, 6) . " = ? order by gbank_tran_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new guilde_banque_transactions;
                        $temp->charge($result["gbank_tran_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table guilde_banque_transactions');
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