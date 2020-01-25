<?php
/**
 * includes/class.perso_objets.php
 */

/**
 * Class perso_objets
 *
 * Gère les objets BDD de la table perso_objets
 */
class perso_objets
{
    var $perobj_cod;
    var $perobj_perso_cod;
    var $perobj_obj_cod;
    var $perobj_identifie = 'N';
    var $perobj_equipe    = 'N';
    var $perobj_dfin;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de perso_objets
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from perso_objets where perobj_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->perobj_cod       = $result['perobj_cod'];
        $this->perobj_perso_cod = $result['perobj_perso_cod'];
        $this->perobj_obj_cod   = $result['perobj_obj_cod'];
        $this->perobj_identifie = $result['perobj_identifie'];
        $this->perobj_equipe    = $result['perobj_equipe'];
        $this->perobj_dfin      = $result['perobj_dfin'];
        return true;
    }

    /***
     * @param $perso
     * @param $objet
     * @return bool
     * @throws Exception
     */
    function getByPersoObjet($perso, $objet)
    {
        $pdo  = new bddpdo;
        $req  = "select perobj_cod
						from perso_objets
						where perobj_perso_cod = :perso
						and perobj_obj_cod = :objet";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":perso" => $perso,
                                  ":objet" => $objet
                              ), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        return $this->charge($result['perobj_cod']);
    }

    /***
     * @param $perso
     * @param $objet_generique
     * @return bool
     * @throws Exception
     */
    function getByPersoObjetGenerique($perso, $objet_generique)
    {
        $retour = array();

        $pdo  = new bddpdo;
        $req  = "select perobj_cod
						from perso_objets join objets on obj_cod = perobj_obj_cod
						where perobj_perso_cod = :perso
						and obj_gobj_cod = :objet_generique";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":perso"           => $perso,
                                  ":objet_generique" => $objet_generique
                              ), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_objets;
            $temp->charge($result["perobj_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    function getByPersoEquipe($perso)
    {
        $retour = array();

        $pdo = new bddpdo;
        /*
         * select obj_nom_porte,tobj_libelle
	from perso_objets,objets,objet_generique,type_objet
	where perobj_perso_cod = $visu
	and perobj_equipe = 'O'
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = tobj_cod
         */

        $req = "select perobj_cod
						from perso_objets 
						where perobj_perso_cod = :perso
						and perobj_equipe = 'O'";

        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":perso" => $perso
                              ), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_objets;
            $temp->charge($result["perobj_cod"]);

            $objets = new objets();
            $objets->charge($temp->perobj_obj_cod);
            $temp->objet = $objets;
            $temp->nom_type_objet = $objets->get_type_libelle();
            $gobj        = new objet_generique();
            $gobj->charge($objets->obj_gobj_cod);
            $temp->objet_generique = $gobj;


            unset($gobj);
            unset($objets);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /***
     * @param $objet_generique
     * @return bool
     * @throws Exception
     */
    function getByObjetGenerique($objet_generique)
    {
        $retour = array();

        $pdo  = new bddpdo;
        $req  = "select perobj_cod
						from perso_objets join objets on obj_cod = perobj_obj_cod
						where obj_gobj_cod = :objet_generique";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                  ":objet_generique" => $objet_generique
                              ), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_objets;
            $temp->charge($result["perobj_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
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
            $req  = "insert into perso_objets (
            perobj_perso_cod,
            perobj_obj_cod,
            perobj_identifie,
            perobj_equipe,
            perobj_dfin                        )
                    values
                    (
                        :perobj_perso_cod,
                        :perobj_obj_cod,
                        :perobj_identifie,
                        :perobj_equipe,
                        :perobj_dfin                        )
    returning perobj_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":perobj_perso_cod" => $this->perobj_perso_cod,
                                      ":perobj_obj_cod"   => $this->perobj_obj_cod,
                                      ":perobj_identifie" => $this->perobj_identifie,
                                      ":perobj_equipe"    => $this->perobj_equipe,
                                      ":perobj_dfin"      => $this->perobj_dfin,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req  = "update perso_objets
                    set
            perobj_perso_cod = :perobj_perso_cod,
            perobj_obj_cod = :perobj_obj_cod,
            perobj_identifie = :perobj_identifie,
            perobj_equipe = :perobj_equipe,
            perobj_dfin = :perobj_dfin                        where perobj_cod = :perobj_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":perobj_cod"       => $this->perobj_cod,
                                      ":perobj_perso_cod" => $this->perobj_perso_cod,
                                      ":perobj_obj_cod"   => $this->perobj_obj_cod,
                                      ":perobj_identifie" => $this->perobj_identifie,
                                      ":perobj_equipe"    => $this->perobj_equipe,
                                      ":perobj_dfin"      => $this->perobj_dfin,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_objets
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select perobj_cod  from perso_objets order by perobj_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new perso_objets;
            $temp->charge($result["perobj_cod"]);
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
                    $req    =
                        "select perobj_cod  from perso_objets where " . substr($name, 6) . " = ? order by perobj_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new perso_objets;
                        $temp->charge($result["perobj_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_objets');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}