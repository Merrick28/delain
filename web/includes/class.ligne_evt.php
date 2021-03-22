<?php
/**
 * includes/class.ligne_evt.php
 */

/**
 * Class ligne_evt
 *
 * Gère les objets BDD de la table ligne_evt
 */
class ligne_evt
{
    public $levt_cod;
    public $levt_tevt_cod;
    public $levt_date;
    public $levt_type_per1 = 1;
    public $levt_perso_cod1;
    public $levt_type_per2;
    public $levt_perso_cod2;
    public $levt_texte;
    public $levt_lu        = 'N';
    public $levt_visible;
    public $levt_attaquant = 0;
    public $levt_cible     = 0;
    public $levt_nombre;
    public $levt_parametres;

    public $perso_attaquant = array();
    public $perso_cible     = array();


    public function __construct()
    {
        $this->levt_date = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de ligne_evt
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @throws Exception
     * @global bdd_mysql $pdo
     */
    public function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "SELECT * FROM ligne_evt WHERE levt_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->levt_cod        = $result['levt_cod'];
        $this->levt_tevt_cod   = $result['levt_tevt_cod'];
        $this->levt_date       = $result['levt_date'];
        $this->levt_type_per1  = $result['levt_type_per1'];
        $this->levt_perso_cod1 = $result['levt_perso_cod1'];
        $this->levt_type_per2  = $result['levt_type_per2'];
        $this->levt_perso_cod2 = $result['levt_perso_cod2'];
        $this->levt_texte      = $result['levt_texte'];
        $this->levt_lu         = $result['levt_lu'];
        $this->levt_visible    = $result['levt_visible'];
        $this->levt_attaquant  = $result['levt_attaquant'];
        $this->levt_cible      = $result['levt_cible'];
        $this->levt_nombre     = $result['levt_nombre'];
        $this->levt_parametres = $result['levt_parametres'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    public function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req
                  = "INSERT INTO ligne_evt (
            levt_tevt_cod,
            levt_date,
            levt_type_per1,
            levt_perso_cod1,
            levt_type_per2,
            levt_perso_cod2,
            levt_texte,
            levt_lu,
            levt_visible,
            levt_attaquant,
            levt_cible,
            levt_nombre,
            levt_parametres                        )
                    VALUES
                    (
                        :levt_tevt_cod,
                        :levt_date,
                        :levt_type_per1,
                        :levt_perso_cod1,
                        :levt_type_per2,
                        :levt_perso_cod2,
                        :levt_texte,
                        :levt_lu,
                        :levt_visible,
                        :levt_attaquant,
                        :levt_cible,
                        :levt_nombre,
                        :levt_parametres                        )
    RETURNING levt_cod AS id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":levt_tevt_cod"   => $this->levt_tevt_cod,
                                      ":levt_date"       => $this->levt_date,
                                      ":levt_type_per1"  => $this->levt_type_per1,
                                      ":levt_perso_cod1" => $this->levt_perso_cod1,
                                      ":levt_type_per2"  => $this->levt_type_per2,
                                      ":levt_perso_cod2" => $this->levt_perso_cod2,
                                      ":levt_texte"      => $this->levt_texte,
                                      ":levt_lu"         => $this->levt_lu,
                                      ":levt_visible"    => $this->levt_visible,
                                      ":levt_attaquant"  => $this->levt_attaquant,
                                      ":levt_cible"      => $this->levt_cible,
                                      ":levt_nombre"     => $this->levt_nombre,
                                      ":levt_parametres" => $this->levt_parametres,
                                  ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req
                  = "UPDATE ligne_evt
                    SET
            levt_tevt_cod = :levt_tevt_cod,
            levt_date = :levt_date,
            levt_type_per1 = :levt_type_per1,
            levt_perso_cod1 = :levt_perso_cod1,
            levt_type_per2 = :levt_type_per2,
            levt_perso_cod2 = :levt_perso_cod2,
            levt_texte = :levt_texte,
            levt_lu = :levt_lu,
            levt_visible = :levt_visible,
            levt_attaquant = :levt_attaquant,
            levt_cible = :levt_cible,
            levt_nombre = :levt_nombre,
            levt_parametres = :levt_parametres                        WHERE levt_cod = :levt_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":levt_cod"        => $this->levt_cod,
                                      ":levt_tevt_cod"   => $this->levt_tevt_cod,
                                      ":levt_date"       => $this->levt_date,
                                      ":levt_type_per1"  => $this->levt_type_per1,
                                      ":levt_perso_cod1" => $this->levt_perso_cod1,
                                      ":levt_type_per2"  => $this->levt_type_per2,
                                      ":levt_perso_cod2" => $this->levt_perso_cod2,
                                      ":levt_texte"      => $this->levt_texte,
                                      ":levt_lu"         => $this->levt_lu,
                                      ":levt_visible"    => $this->levt_visible,
                                      ":levt_attaquant"  => $this->levt_attaquant,
                                      ":levt_cible"      => $this->levt_cible,
                                      ":levt_nombre"     => $this->levt_nombre,
                                      ":levt_parametres" => $this->levt_parametres,
                                  ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \ligne_evt
     * @global bdd_mysql $pdo
     */
    public function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "SELECT levt_cod  FROM ligne_evt ORDER BY levt_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new ligne_evt;
            $temp->charge($result["levt_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * @param $perso_cod
     * @return ligne_evt[]
     */
    public function getNbEvtByPersoNonLu($perso_cod)
    {
        $pdo    = new bddpdo;
        $req  = "SELECT count(*) as count  FROM ligne_evt WHERE levt_perso_cod1 = ?  AND levt_lu = 'N'";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso_cod), $stmt);
        if (!$result = $stmt->fetch()) return 0;
        return $result["count"] ;

    }

    /**
     * @param $perso_cod
     * @return ligne_evt[]
     */
    public function getByPersoNonLu($perso_cod)
    {
        $retour = array();
        $pdo    = new bddpdo;
        // Marlysa - 2021-03-22 - Il arrive qu'un monstre possède plusieurs milliers d'evenement non-lu (comme un boss de defis léno par exemple)
        // Lire ses milliers d'evenement n'apporte rien et ralenti l'nterface de l'admin, on limit au 100 derniers events! 
        $req
                = "SELECT levt_cod  FROM ligne_evt
          WHERE levt_perso_cod1 = ?
          AND levt_lu = 'N'
          ORDER BY levt_cod DESC LIMIT 100";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($perso_cod), $stmt);
        while ($result = $stmt->fetch())
        {
            $temp = new ligne_evt;
            $temp->charge($result["levt_cod"]);
            $tevt = new type_evt();
            $tevt->charge($temp->levt_tevt_cod);
            $temp->tevt = $tevt;
            // on prend les evts liés
            if (!is_null(trim($this->levt_attaquant)))
            {
                $perso_attaquant = new perso;
                if ($perso_attaquant->charge($this->levt_attaquant))
                {
                    $temp->perso_attaquant = $perso_attaquant;
                } else
                {
                    $temp->perso_attaquant = "erreur";
                }
                unset($perso_attaquant);
            }
            if (!is_null(trim($this->levt_cible)))
            {
                $perso_cible = new perso;
                if ($perso_cible->charge($this->levt_cible))
                {
                    $temp->perso_cible = $perso_cible;
                } else
                {
                    $temp->perso_cible = "erreur";
                }
                unset($perso_cible);
            }

            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function getByPerso($perso_cod, $offset = 0, $limit = 50, $withvisible = true)
    {
        $retour = array();
        $pdo    = new bddpdo;
        if ($withvisible)
        {
            $req
                = "SELECT levt_cod  FROM ligne_evt
          WHERE levt_perso_cod1 = :perso
          ORDER BY levt_cod DESC
          limit $limit offset $offset";
        } else
        {
            $req
                = "SELECT levt_cod  FROM ligne_evt
          WHERE levt_perso_cod1 = :perso
            and levt_visible = 'O'
          ORDER BY levt_cod DESC
          limit $limit offset $offset";
        }


        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso" => $perso_cod), $stmt);

        while ($result = $stmt->fetch())
        {
            $temp = new ligne_evt;
            $temp->charge($result["levt_cod"]);
            $tevt = new type_evt();
            $tevt->charge($temp->levt_tevt_cod);
            $temp->tevt = $tevt;
            // on prend les evts liés
            //die("**" . trim($this->levt_attaquant) . "**");
            $perso1 = new perso;
            $perso1->charge($temp->levt_perso_cod1);
            $temp->perso1 = $perso1;
            unset($perso1);

            $perso_attaquant = new perso;

            $perso_attaquant->charge($temp->levt_attaquant);

            $temp->perso_attaquant = $perso_attaquant;

            unset($perso_attaquant);


            $perso_cible = new perso;
            $perso_cible->charge($temp->levt_cible);

            $temp->perso_cible = $perso_cible;

            unset($perso_cible);


            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    /**
     * @param ligne_evt[] $tab_evt
     * @param bool $isauth
     * @param bool $strong
     * @param bool $lien
     */
    public function mise_en_page_evt($tab_evt, $isauth, $strong = false, $lien = false)
    {
        $strong_avant = '';
        $strong_apres = '';
        if ($strong)
        {
            $strong_avant = '<strong>';
            $strong_apres = '</strong>';
        }
        foreach ($tab_evt as $key => $val)
        {
            $lien_avant = '';
            $lien_apres = '';

            $perso = new perso;
            $perso->charge($val->levt_perso_cod1);
            if ($isauth)
            {
                if ($lien)
                {
                    $lien_avant = '<a href="' . G_URL . 'jeu/visu_desc_perso.php?visu="' . $perso->perso_cod . '>';
                    $lien_apres = '</a>';
                }
                // on prend la ligne de l'événement
                $texte =
                    str_replace(
                        '[perso_cod1]',
                        $strong_avant . $lien_avant . $perso->perso_nom . $lien_apres .
                        $strong_apres,
                        $val->levt_texte
                    );
            } else
            {
                if ($lien)
                {
                    $lien_avant = '<a href="' . G_URL . 'jeu/visu_desc_perso.php?visu="' . $perso->perso_cod . '>';
                    $lien_apres = '</a>';
                }
                // non auth, on prend la ligne du type d'événement
                $texte =
                    str_replace(
                        '[perso_cod1]',
                        $strong_avant . $lien_avant . $perso->perso_nom . $lien_apres .
                        $strong_apres,
                        $val->tevt->tevt_texte
                    );
            }
            $perso_attaquant = new perso;
            $perso_attaquant->charge($val->levt_attaquant);
            if ($lien)
            {
                $lien_avant = '<a href="' . G_URL . 'jeu/visu_desc_perso.php?visu="' .
                              $perso_attaquant->perso_cod . '>';

                $lien_apres = '</a>';
            }
            $texte =
                str_replace('[attaquant]', $strong_avant . $lien_avant . $perso_attaquant->perso_nom .
                                           $lien_apres . $strong_apres, $texte);
            unset($perso_attaquant);
            $perso_cible = new perso;
            $perso_cible->charge($val->levt_cible);
            if ($lien)
            {
                $lien_avant = '<a href="' . G_URL . 'jeu/visu_desc_perso.php?visu="' .
                              $perso_cible->perso_cod . '>';
                $lien_apres = '</a>';
            }
            $texte =
                str_replace(
                    '[cible]',
                    $strong_avant . $lien_avant . $perso_cible->perso_nom . $lien_apres .
                    $strong_apres,
                    $texte
                );
            unset($perso_cible);
            $val->levt_texte = $texte;
            // suppression des persos
            $val->perso_cod_attaquant = $val->perso_attaquant->perso_cod;
            $val->perso_cod_cible     = $val->perso_cible->perso_cod;
            unset($val->perso_cible);
            unset($val->perso_attaquant);
            unset($val->tevt);
            unset($val->levt_type_per1);
            unset($val->levt_type_per1);
            unset($val->levt_type_per2);
            unset($val->levt_perso_cod2);
            unset($val->levt_nombre);
            unset($val->levt_parametres);
            unset($val->perso_cod_attaquant);
            unset($val->perso_cod_cible);
            if (!$isauth)
            {
                if ($val->levt_visible == 'N')
                {
                    unset($val);
                }
            }
        }
        return $tab_evt;
    }

    public function marquePersoLu($perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = "UPDATE ligne_evt SET levt_lu = 'O' WHERE levt_perso_cod1 = ? AND levt_lu = 'N'";
        $stmt = $pdo->prepare($req);
        $pdo->execute(array($perso_cod), $stmt);
        return true;
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
                    $req    = "SELECT levt_cod  FROM ligne_evt WHERE " . substr($name, 6) . " = ? ORDER BY levt_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new ligne_evt;
                        $temp->charge($result["levt_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table ligne_evt');
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
