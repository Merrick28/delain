<?php
/**
 * includes/class.lieu.php
 */

/**
 * Class lieu
 *
 * Gère les objets BDD de la table lieu
 */
class lieu
{
    var $lieu_cod;
    var $lieu_tlieu_cod;
    var $lieu_nom;
    var $lieu_description;
    var $lieu_refuge      = 'N';
    var $lieu_url;
    var $lieu_dest;
    var $lieu_alignement;
    var $lieu_dfin;
    var $lieu_compte;
    var $lieu_marge;
    var $lieu_prelev;
    var $lieu_mobile      = 'N';
    var $lieu_date_bouge;
    var $lieu_date_refill;
    var $lieu_port_dfin;
    var $lieu_dieu_cod;
    var $lieu_neutre      = 0;
    var $lieu_levo_niveau = 0;

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
            $req  = "insert into lieu (
            lieu_tlieu_cod,
            lieu_nom,
            lieu_description,
            lieu_refuge,
            lieu_url,
            lieu_dest,
            lieu_alignement,
            lieu_dfin,
            lieu_compte,
            lieu_marge,
            lieu_prelev,
            lieu_mobile,
            lieu_date_bouge,
            lieu_date_refill,
            lieu_port_dfin,
            lieu_dieu_cod,
            lieu_neutre,
            lieu_levo_niveau                        )
                    values
                    (
                        :lieu_tlieu_cod,
                        :lieu_nom,
                        :lieu_description,
                        :lieu_refuge,
                        :lieu_url,
                        :lieu_dest,
                        :lieu_alignement,
                        :lieu_dfin,
                        :lieu_compte,
                        :lieu_marge,
                        :lieu_prelev,
                        :lieu_mobile,
                        :lieu_date_bouge,
                        :lieu_date_refill,
                        :lieu_port_dfin,
                        :lieu_dieu_cod,
                        :lieu_neutre,
                        :lieu_levo_niveau                        )
    returning lieu_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":lieu_tlieu_cod"   => $this->lieu_tlieu_cod,
                                      ":lieu_nom"         => $this->lieu_nom,
                                      ":lieu_description" => $this->lieu_description,
                                      ":lieu_refuge"      => $this->lieu_refuge,
                                      ":lieu_url"         => $this->lieu_url,
                                      ":lieu_dest"        => $this->lieu_dest,
                                      ":lieu_alignement"  => $this->lieu_alignement,
                                      ":lieu_dfin"        => $this->lieu_dfin,
                                      ":lieu_compte"      => $this->lieu_compte,
                                      ":lieu_marge"       => $this->lieu_marge,
                                      ":lieu_prelev"      => $this->lieu_prelev,
                                      ":lieu_mobile"      => $this->lieu_mobile,
                                      ":lieu_date_bouge"  => $this->lieu_date_bouge,
                                      ":lieu_date_refill" => $this->lieu_date_refill,
                                      ":lieu_port_dfin"   => $this->lieu_port_dfin,
                                      ":lieu_dieu_cod"    => $this->lieu_dieu_cod,
                                      ":lieu_neutre"      => $this->lieu_neutre,
                                      ":lieu_levo_niveau" => $this->lieu_levo_niveau,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update lieu
                    set
            lieu_tlieu_cod = :lieu_tlieu_cod,
            lieu_nom = :lieu_nom,
            lieu_description = :lieu_description,
            lieu_refuge = :lieu_refuge,
            lieu_url = :lieu_url,
            lieu_dest = :lieu_dest,
            lieu_alignement = :lieu_alignement,
            lieu_dfin = :lieu_dfin,
            lieu_compte = :lieu_compte,
            lieu_marge = :lieu_marge,
            lieu_prelev = :lieu_prelev,
            lieu_mobile = :lieu_mobile,
            lieu_date_bouge = :lieu_date_bouge,
            lieu_date_refill = :lieu_date_refill,
            lieu_port_dfin = :lieu_port_dfin,
            lieu_dieu_cod = :lieu_dieu_cod,
            lieu_neutre = :lieu_neutre,
            lieu_levo_niveau = :lieu_levo_niveau                        where lieu_cod = :lieu_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":lieu_cod"         => $this->lieu_cod,
                                      ":lieu_tlieu_cod"   => $this->lieu_tlieu_cod,
                                      ":lieu_nom"         => $this->lieu_nom,
                                      ":lieu_description" => $this->lieu_description,
                                      ":lieu_refuge"      => $this->lieu_refuge,
                                      ":lieu_url"         => $this->lieu_url,
                                      ":lieu_dest"        => $this->lieu_dest,
                                      ":lieu_alignement"  => $this->lieu_alignement,
                                      ":lieu_dfin"        => $this->lieu_dfin,
                                      ":lieu_compte"      => $this->lieu_compte,
                                      ":lieu_marge"       => $this->lieu_marge,
                                      ":lieu_prelev"      => $this->lieu_prelev,
                                      ":lieu_mobile"      => $this->lieu_mobile,
                                      ":lieu_date_bouge"  => $this->lieu_date_bouge,
                                      ":lieu_date_refill" => $this->lieu_date_refill,
                                      ":lieu_port_dfin"   => $this->lieu_port_dfin,
                                      ":lieu_dieu_cod"    => $this->lieu_dieu_cod,
                                      ":lieu_neutre"      => $this->lieu_neutre,
                                      ":lieu_levo_niveau" => $this->lieu_levo_niveau,
                                  ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de lieu
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from lieu where lieu_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->lieu_cod         = $result['lieu_cod'];
        $this->lieu_tlieu_cod   = $result['lieu_tlieu_cod'];
        $this->lieu_nom         = $result['lieu_nom'];
        $this->lieu_description = $result['lieu_description'];
        $this->lieu_refuge      = $result['lieu_refuge'];
        $this->lieu_url         = $result['lieu_url'];
        $this->lieu_dest        = $result['lieu_dest'];
        $this->lieu_alignement  = $result['lieu_alignement'];
        $this->lieu_dfin        = $result['lieu_dfin'];
        $this->lieu_compte      = $result['lieu_compte'];
        $this->lieu_marge       = $result['lieu_marge'];
        $this->lieu_prelev      = $result['lieu_prelev'];
        $this->lieu_mobile      = $result['lieu_mobile'];
        $this->lieu_date_bouge  = $result['lieu_date_bouge'];
        $this->lieu_date_refill = $result['lieu_date_refill'];
        $this->lieu_port_dfin   = $result['lieu_port_dfin'];
        $this->lieu_dieu_cod    = $result['lieu_dieu_cod'];
        $this->lieu_neutre      = $result['lieu_neutre'];
        $this->lieu_levo_niveau = $result['lieu_levo_niveau'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return lieu
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select lieu_cod  from lieu order by lieu_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new lieu;
            $temp->charge($result["lieu_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getPos()
    {
        $lpos = new lieu_position();
        $lpos->getByLieu($this->lieu_cod);
        $pos = new positions();
        $pos->charge($lpos->lpos_pos_cod);
        $etage = new etage();
        $etage->getByNumero($pos->pos_etage);
        return array("pos"   => $pos,
                     "etage" => $etage);
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
                    $req    = "select lieu_cod  from lieu where " . substr($name, 6) . " = ? order by lieu_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new lieu;
                        $temp->charge($result["lieu_cod"]);
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
                    die('Unknown variable ' . substr($name, 6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}