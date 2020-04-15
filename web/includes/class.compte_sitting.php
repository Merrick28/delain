<?php
/**
 * includes/class.compte_sitting.php
 */

/**
 * Class compte_sitting
 *
 * Gère les objets BDD de la table compte_sitting
 */
class compte_sitting
{
    var $csit_cod;
    var $csit_compte_sitte;
    var $csit_compte_sitteur;
    var $csit_ddeb;
    var $csit_dfin;
    var $csit_ddemande;

    function __construct()
    {

        $this->csit_ddemande = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de compte_sitting
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM compte_sitting WHERE csit_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->csit_cod            = $result['csit_cod'];
        $this->csit_compte_sitte   = $result['csit_compte_sitte'];
        $this->csit_compte_sitteur = $result['csit_compte_sitteur'];
        $this->csit_ddeb           = $result['csit_ddeb'];
        $this->csit_dfin           = $result['csit_dfin'];
        $this->csit_ddemande       = $result['csit_ddemande'];
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
        if ($new)
        {
            $req
                  = "INSERT INTO compte_sitting (
            csit_compte_sitte,
            csit_compte_sitteur,
            csit_ddeb,
            csit_dfin,
            csit_ddemande                        )
                    VALUES
                    (
                        :csit_compte_sitte,
                        :csit_compte_sitteur,
                        :csit_ddeb,
                        :csit_dfin,
                        :csit_ddemande                        )
    RETURNING csit_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":csit_compte_sitte"   => $this->csit_compte_sitte,
                ":csit_compte_sitteur" => $this->csit_compte_sitteur,
                ":csit_ddeb"           => $this->csit_ddeb,
                ":csit_dfin"           => $this->csit_dfin,
                ":csit_ddemande"       => $this->csit_ddemande,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req
                  = "UPDATE compte_sitting
                    SET
            csit_compte_sitte = :csit_compte_sitte,
            csit_compte_sitteur = :csit_compte_sitteur,
            csit_ddeb = :csit_ddeb,
            csit_dfin = :csit_dfin,
            csit_ddemande = :csit_ddemande                        WHERE csit_cod = :csit_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":csit_cod"            => $this->csit_cod,
                ":csit_compte_sitte"   => $this->csit_compte_sitte,
                ":csit_compte_sitteur" => $this->csit_compte_sitteur,
                ":csit_ddeb"           => $this->csit_ddeb,
                ":csit_dfin"           => $this->csit_dfin,
                ":csit_ddemande"       => $this->csit_ddemande,
            ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compte_sitting
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT csit_cod  FROM compte_sitting ORDER BY csit_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new compte_sitting;
            $temp->charge($result["csit_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function isSittingValide($compt_cod, $perso_cod)
    {
        $pdo  = new bddpdo;
        $req
              = "SELECT csit_compte_sitteur FROM perso_compte,compte_sitting
				WHERE pcompt_perso_cod = ? 
				AND pcompt_compt_cod = csit_compte_sitte
				AND csit_compte_sitteur = ?
				AND csit_ddeb <= now()
				AND csit_dfin >= now() ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $compt_cod), $stmt);
        if ($result = $stmt->fetch())
        {
            return true;
        }
        return false;
    }

    function isSittingFamilierValide($compt_cod, $perso_cod)
    {
        $pdo  = new bddpdo;
        $req
              = "select csit_compte_sitteur from perso_compte,perso_familier,compte_sitting
				where pcompt_perso_cod = pfam_perso_cod
				and pfam_familier_cod = ?
				and pcompt_compt_cod = csit_compte_sitte
				AND csit_compte_sitteur = ?
				and csit_ddeb <= now()
				and csit_dfin >= now() ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $compt_cod), $stmt);
        if ($stmt->fetch())
        {
            return true;
        }
        return false;
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
                    $req    = "SELECT csit_cod  FROM compte_sitting WHERE " . substr($name, 6) . " = ? ORDER BY csit_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new compte_sitting;
                        $temp->charge($result["csit_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table compte_sitting');
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