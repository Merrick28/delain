<?php
/**
 * includes/class.sorts.php
 */

/**
 * Class sorts
 *
 * Gère les objets BDD de la table sorts
 */
class sorts
{
    var $sort_cod;
    var $sort_combinaison;
    var $sort_nom;
    var $sort_fonction;
    var $sort_cout;
    var $sort_comp_cod;
    var $sort_distance;
    var $sort_description;
    var $sort_aggressif;
    var $sort_niveau;
    var $sort_soi_meme;
    var $sort_monstre;
    var $sort_joueur;
    var $sort_soutien;
    var $sort_bloquable;
    var $sort_case           = 'N';
    var $sort_temps_recharge = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de sorts
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from sorts where sort_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->sort_cod            = $result['sort_cod'];
        $this->sort_combinaison    = $result['sort_combinaison'];
        $this->sort_nom            = $result['sort_nom'];
        $this->sort_fonction       = $result['sort_fonction'];
        $this->sort_cout           = $result['sort_cout'];
        $this->sort_comp_cod       = $result['sort_comp_cod'];
        $this->sort_distance       = $result['sort_distance'];
        $this->sort_description    = $result['sort_description'];
        $this->sort_aggressif      = $result['sort_aggressif'];
        $this->sort_niveau         = $result['sort_niveau'];
        $this->sort_soi_meme       = $result['sort_soi_meme'];
        $this->sort_monstre        = $result['sort_monstre'];
        $this->sort_joueur         = $result['sort_joueur'];
        $this->sort_soutien        = $result['sort_soutien'];
        $this->sort_bloquable      = $result['sort_bloquable'];
        $this->sort_case           = $result['sort_case'];
        $this->sort_temps_recharge = $result['sort_temps_recharge'];
        return true;
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
            $req  = "insert into sorts (
            sort_combinaison,
            sort_nom,
            sort_fonction,
            sort_cout,
            sort_comp_cod,
            sort_distance,
            sort_description,
            sort_aggressif,
            sort_niveau,
            sort_soi_meme,
            sort_monstre,
            sort_joueur,
            sort_soutien,
            sort_bloquable,
            sort_case,
            sort_temps_recharge                        )
                    values
                    (
                        :sort_combinaison,
                        :sort_nom,
                        :sort_fonction,
                        :sort_cout,
                        :sort_comp_cod,
                        :sort_distance,
                        :sort_description,
                        :sort_aggressif,
                        :sort_niveau,
                        :sort_soi_meme,
                        :sort_monstre,
                        :sort_joueur,
                        :sort_soutien,
                        :sort_bloquable,
                        :sort_case,
                        :sort_temps_recharge                        )
    returning sort_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":sort_combinaison"    => $this->sort_combinaison,
                                      ":sort_nom"            => $this->sort_nom,
                                      ":sort_fonction"       => $this->sort_fonction,
                                      ":sort_cout"           => $this->sort_cout,
                                      ":sort_comp_cod"       => $this->sort_comp_cod,
                                      ":sort_distance"       => $this->sort_distance,
                                      ":sort_description"    => $this->sort_description,
                                      ":sort_aggressif"      => $this->sort_aggressif,
                                      ":sort_niveau"         => $this->sort_niveau,
                                      ":sort_soi_meme"       => $this->sort_soi_meme,
                                      ":sort_monstre"        => $this->sort_monstre,
                                      ":sort_joueur"         => $this->sort_joueur,
                                      ":sort_soutien"        => $this->sort_soutien,
                                      ":sort_bloquable"      => $this->sort_bloquable,
                                      ":sort_case"           => $this->sort_case,
                                      ":sort_temps_recharge" => $this->sort_temps_recharge,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update sorts
                    set
            sort_combinaison = :sort_combinaison,
            sort_nom = :sort_nom,
            sort_fonction = :sort_fonction,
            sort_cout = :sort_cout,
            sort_comp_cod = :sort_comp_cod,
            sort_distance = :sort_distance,
            sort_description = :sort_description,
            sort_aggressif = :sort_aggressif,
            sort_niveau = :sort_niveau,
            sort_soi_meme = :sort_soi_meme,
            sort_monstre = :sort_monstre,
            sort_joueur = :sort_joueur,
            sort_soutien = :sort_soutien,
            sort_bloquable = :sort_bloquable,
            sort_case = :sort_case,
            sort_temps_recharge = :sort_temps_recharge                        where sort_cod = :sort_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":sort_cod"            => $this->sort_cod,
                                      ":sort_combinaison"    => $this->sort_combinaison,
                                      ":sort_nom"            => $this->sort_nom,
                                      ":sort_fonction"       => $this->sort_fonction,
                                      ":sort_cout"           => $this->sort_cout,
                                      ":sort_comp_cod"       => $this->sort_comp_cod,
                                      ":sort_distance"       => $this->sort_distance,
                                      ":sort_description"    => $this->sort_description,
                                      ":sort_aggressif"      => $this->sort_aggressif,
                                      ":sort_niveau"         => $this->sort_niveau,
                                      ":sort_soi_meme"       => $this->sort_soi_meme,
                                      ":sort_monstre"        => $this->sort_monstre,
                                      ":sort_joueur"         => $this->sort_joueur,
                                      ":sort_soutien"        => $this->sort_soutien,
                                      ":sort_bloquable"      => $this->sort_bloquable,
                                      ":sort_case"           => $this->sort_case,
                                      ":sort_temps_recharge" => $this->sort_temps_recharge,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \sorts
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select sort_cod  from sorts order by sort_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new sorts;
            $temp->charge($result["sort_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByCombinaison($combinaison)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select sort_cod  from sorts where sort_combinaison = :combinaison";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":combinaison" => $combinaison), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['sort_cod']);
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
                    $req    = "select sort_cod  from sorts where " . substr($name, 6) . " = ? order by sort_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new sorts;
                        $temp->charge($result["sort_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table sorts');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}