<?php
/**
 * includes/class.bonus.php
 */

/**
 * Class bonus
 *
 * Gère les objets BDD de la table bonus
 */
class bonus
{
    var $bonus_cod;
    var $bonus_perso_cod;
    var $bonus_nb_tours   = 2;
    var $bonus_tbonus_libc;
    var $bonus_valeur;
    var $bonus_croissance = 0;
    var $bonus_mode       = 'S';
    var $bonus_degressivite;
    var $bonus_obj_cod;
    var $bonus_objbm_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de bonus
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from bonus where bonus_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->bonus_cod          = $result['bonus_cod'];
        $this->bonus_perso_cod    = $result['bonus_perso_cod'];
        $this->bonus_nb_tours     = $result['bonus_nb_tours'];
        $this->bonus_tbonus_libc  = $result['bonus_tbonus_libc'];
        $this->bonus_valeur       = $result['bonus_valeur'];
        $this->bonus_croissance   = $result['bonus_croissance'];
        $this->bonus_mode         = $result['bonus_mode'];
        $this->bonus_degressivite = $result['bonus_degressivite'];
        $this->bonus_obj_cod      = $result['bonus_obj_cod'];
        $this->bonus_objbm_cod    = $result['bonus_objbm_cod'];
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
            $req  = "insert into bonus (
            bonus_perso_cod,
            bonus_nb_tours,
            bonus_tbonus_libc,
            bonus_valeur,
            bonus_croissance,
            bonus_mode,
            bonus_degressivite,
            bonus_obj_cod,
            bonus_objbm_cod                        )
                    values
                    (
                        :bonus_perso_cod,
                        :bonus_nb_tours,
                        :bonus_tbonus_libc,
                        :bonus_valeur,
                        :bonus_croissance,
                        :bonus_mode,
                        :bonus_degressivite,
                        :bonus_obj_cod,
                        :bonus_objbm_cod                        )
    returning bonus_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":bonus_perso_cod"    => $this->bonus_perso_cod,
                                      ":bonus_nb_tours"     => $this->bonus_nb_tours,
                                      ":bonus_tbonus_libc"  => $this->bonus_tbonus_libc,
                                      ":bonus_valeur"       => $this->bonus_valeur,
                                      ":bonus_croissance"   => $this->bonus_croissance,
                                      ":bonus_mode"         => $this->bonus_mode,
                                      ":bonus_degressivite" => $this->bonus_degressivite,
                                      ":bonus_obj_cod"      => $this->bonus_obj_cod,
                                      ":bonus_objbm_cod"    => $this->bonus_objbm_cod,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update bonus
                    set
            bonus_perso_cod = :bonus_perso_cod,
            bonus_nb_tours = :bonus_nb_tours,
            bonus_tbonus_libc = :bonus_tbonus_libc,
            bonus_valeur = :bonus_valeur,
            bonus_croissance = :bonus_croissance,
            bonus_mode = :bonus_mode,
            bonus_degressivite = :bonus_degressivite,
            bonus_obj_cod = :bonus_obj_cod,
            bonus_objbm_cod = :bonus_objbm_cod                        where bonus_cod = :bonus_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":bonus_cod"          => $this->bonus_cod,
                                      ":bonus_perso_cod"    => $this->bonus_perso_cod,
                                      ":bonus_nb_tours"     => $this->bonus_nb_tours,
                                      ":bonus_tbonus_libc"  => $this->bonus_tbonus_libc,
                                      ":bonus_valeur"       => $this->bonus_valeur,
                                      ":bonus_croissance"   => $this->bonus_croissance,
                                      ":bonus_mode"         => $this->bonus_mode,
                                      ":bonus_degressivite" => $this->bonus_degressivite,
                                      ":bonus_obj_cod"      => $this->bonus_obj_cod,
                                      ":bonus_objbm_cod"    => $this->bonus_objbm_cod,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \bonus
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select bonus_cod  from bonus order by bonus_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new bonus;
            $temp->charge($result["bonus_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * recherche les bonus (non-equipement) du perso
     * @param $perso_cod
     */
    function get_perso_bonus_temporaire($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select bonus_cod from bonus where bonus_perso_cod = ? and bonus_mode!='E' order by bonus_cod ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new bonus;
            $temp->charge($result["bonus_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        if (count($retour) == 0)
        {
            return false;
        }
        return $retour;
    }

    /**
     * Calcule l'état visuel d'une aura (libellé ou couleur) en fonction
     * de sa valeur actuelle par rapport à sa valeur à la création.
     * Ne révèle jamais la valeur réelle : uniquement un état qualitatif.
     * @param numeric $valeur : bonus_valeur (valeur actuelle de l'aura)
     * @param numeric $valeur_initiale : bonus_valeur_initiale (valeur à la création)
     * @param string $type : "etat" pour le libellé texte, autre chose pour la couleur
     * @return string
     */
    static function get_etat_aura($valeur, $valeur_initiale, $type = "etat")
    {
        if (empty($valeur_initiale) || $valeur_initiale <= 0)
        {
            $pourcentage = 0;
        }
        else
        {
            $pourcentage = min(100, ($valeur / $valeur_initiale) * 100);
        }

        if ($type == "etat")
        {
            if ($pourcentage < 15)        $retour = 'presque éteinte';
            else if ($pourcentage < 25)   $retour = 'vacillante';
            else if ($pourcentage < 50)   $retour = 'fortement affaiblie';
            else if ($pourcentage < 75)   $retour = 'affaiblie';
            else if ($pourcentage < 100)  $retour = 'quasi intacte';
            else                          $retour = 'intacte';
        }
        else
        {
            if ($pourcentage < 15)        $retour = '#c62828'; // rouge
            else if ($pourcentage < 25)   $retour = '#e8672c'; // orangé-rouge
            else if ($pourcentage < 50)   $retour = '#f2a93c'; // orange
            else if ($pourcentage < 75)   $retour = '#c6cf3a'; // vert-jaune
            else if ($pourcentage < 100)  $retour = '#6cbf3e'; // vert clair
            else                          $retour = '#1b8a3c'; // vert intense
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
                    $req    = "select bonus_cod  from bonus where " . substr($name, 6) . " = ? order by bonus_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new bonus;
                        $temp->charge($result["bonus_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table bonus');
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