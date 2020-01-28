<?php
/**
 * includes/class.carac_orig.php
 */

/**
 * Class carac_orig
 *
 * Gère les objets BDD de la table carac_orig
 */
class carac_orig
{
    var $corig_perso_cod;
    var $corig_type_carac;
    var $corig_carac_valeur_orig;
    var $corig_dfin;
    var $corig_nb_tours;
    var $corig_cod;
    var $corig_mode   = 'S';
    var $corig_valeur = 0;
    var $corig_obj_cod;
    var $corig_objbm_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de carac_orig
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from carac_orig where corig_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->corig_perso_cod         = $result['corig_perso_cod'];
        $this->corig_type_carac        = $result['corig_type_carac'];
        $this->corig_carac_valeur_orig = $result['corig_carac_valeur_orig'];
        $this->corig_dfin              = $result['corig_dfin'];
        $this->corig_nb_tours          = $result['corig_nb_tours'];
        $this->corig_cod               = $result['corig_cod'];
        $this->corig_mode              = $result['corig_mode'];
        $this->corig_valeur            = $result['corig_valeur'];
        $this->corig_obj_cod           = $result['corig_obj_cod'];
        $this->corig_objbm_cod         = $result['corig_objbm_cod'];
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
            $req  = "insert into carac_orig (
            corig_perso_cod,
            corig_type_carac,
            corig_carac_valeur_orig,
            corig_dfin,
            corig_nb_tours,
            corig_mode,
            corig_valeur,
            corig_obj_cod,
            corig_objbm_cod                        )
                    values
                    (
                        :corig_perso_cod,
                        :corig_type_carac,
                        :corig_carac_valeur_orig,
                        :corig_dfin,
                        :corig_nb_tours,
                        :corig_mode,
                        :corig_valeur,
                        :corig_obj_cod,
                        :corig_objbm_cod                        )
    returning corig_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":corig_perso_cod"         => $this->corig_perso_cod,
                                      ":corig_type_carac"        => $this->corig_type_carac,
                                      ":corig_carac_valeur_orig" => $this->corig_carac_valeur_orig,
                                      ":corig_dfin"              => $this->corig_dfin,
                                      ":corig_nb_tours"          => $this->corig_nb_tours,
                                      ":corig_mode"              => $this->corig_mode,
                                      ":corig_valeur"            => $this->corig_valeur,
                                      ":corig_obj_cod"           => $this->corig_obj_cod,
                                      ":corig_objbm_cod"         => $this->corig_objbm_cod,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update carac_orig
                    set
            corig_perso_cod = :corig_perso_cod,
            corig_type_carac = :corig_type_carac,
            corig_carac_valeur_orig = :corig_carac_valeur_orig,
            corig_dfin = :corig_dfin,
            corig_nb_tours = :corig_nb_tours,
            corig_mode = :corig_mode,
            corig_valeur = :corig_valeur,
            corig_obj_cod = :corig_obj_cod,
            corig_objbm_cod = :corig_objbm_cod                        where corig_cod = :corig_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":corig_perso_cod"         => $this->corig_perso_cod,
                                      ":corig_type_carac"        => $this->corig_type_carac,
                                      ":corig_carac_valeur_orig" => $this->corig_carac_valeur_orig,
                                      ":corig_dfin"              => $this->corig_dfin,
                                      ":corig_nb_tours"          => $this->corig_nb_tours,
                                      ":corig_cod"               => $this->corig_cod,
                                      ":corig_mode"              => $this->corig_mode,
                                      ":corig_valeur"            => $this->corig_valeur,
                                      ":corig_obj_cod"           => $this->corig_obj_cod,
                                      ":corig_objbm_cod"         => $this->corig_objbm_cod,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \carac_orig
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select corig_cod  from carac_orig order by corig_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new carac_orig;
            $temp->charge($result["corig_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPerso($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select corig_cod  from carac_orig  where corig_perso_cod = :perso order by corig_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(':perso' => $perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new carac_orig;
            $temp->charge($result["corig_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPersoCumul($perso_cod,$equipement = false)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select corig_type_carac,
            sum(corig_valeur) as bonus_carac,
            to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss') as corig_dfin, 
            coalesce(corig_nb_tours, 0) as corig_nb_tours,
            case when corig_mode='E' then 'Equipement' else corig_nb_tours::text end as corig_mode
        from carac_orig
        where corig_perso_cod = :perso_cod
        and corig_mode " . ($equipement ? "=" : "!=") . " 'E'
        group by corig_type_carac, 
            to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss'), 
            coalesce(corig_nb_tours, 0), 
            case when corig_mode='E' then 'Equipement' else corig_nb_tours::text end
        order by corig_type_carac";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(':perso' => $perso_cod), $stmt);

        return $stmt->fetchAll();
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
                    $req    = "select corig_cod  from carac_orig where " . substr($name, 6) . " = ? order by corig_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new carac_orig;
                        $temp->charge($result["corig_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table carac_orig');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}