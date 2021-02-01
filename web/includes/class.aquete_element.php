<?php
/**
 * includes/class.aquete_element.php
 */

/**
 * Class aquete_element
 *
 * Gère les objets BDD de la table aquete_element
 */
class aquete_element
{
    var $aqelem_cod;
    var $aqelem_aquete_cod;
    var $aqelem_quete_step;
    var $aqelem_aqetape_cod;
    var $aqelem_nom;
    var $aqelem_param_id;
    var $aqelem_param_ordre;
    var $aqelem_type;
    var $aqelem_misc_cod;
    var $aqelem_param_num_1;
    var $aqelem_param_num_2;
    var $aqelem_param_num_3;
    var $aqelem_param_txt_1;
    var $aqelem_param_txt_2;
    var $aqelem_param_txt_3;
    var $aqelem_aqperso_cod;

    function __construct()
    {
    }

    public function __clone()
    {
        $this->aqelem_cod = null;      // En cas de clonage, le clone de doit pas avoir le même code
    }

    /**
     * Charge dans la classe un enregistrement de aquete_element
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_element where aqelem_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqelem_cod = $result['aqelem_cod'];
        $this->aqelem_aquete_cod = $result['aqelem_aquete_cod'];
        $this->aqelem_quete_step = $result['aqelem_quete_step'];
        $this->aqelem_aqetape_cod = $result['aqelem_aqetape_cod'];
        $this->aqelem_nom = $result['aqelem_nom'];
        $this->aqelem_param_id = $result['aqelem_param_id'];
        $this->aqelem_param_ordre = $result['aqelem_param_ordre'];
        $this->aqelem_type = $result['aqelem_type'];
        $this->aqelem_misc_cod = $result['aqelem_misc_cod'];
        $this->aqelem_param_num_1 = $result['aqelem_param_num_1'];
        $this->aqelem_param_num_2 = $result['aqelem_param_num_2'];
        $this->aqelem_param_num_3 = $result['aqelem_param_num_3'];
        $this->aqelem_param_txt_1 = $result['aqelem_param_txt_1'];
        $this->aqelem_param_txt_2 = $result['aqelem_param_txt_2'];
        $this->aqelem_param_txt_3 = $result['aqelem_param_txt_3'];
        $this->aqelem_aqperso_cod = $result['aqelem_aqperso_cod'];
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
        if($new)
        {
            $req = "insert into quetes.aquete_element (
            aqelem_aquete_cod,
            aqelem_quete_step,
            aqelem_aqetape_cod,
            aqelem_nom,
            aqelem_param_id,
            aqelem_param_ordre,
            aqelem_type,
            aqelem_misc_cod,
            aqelem_param_num_1,
            aqelem_param_num_2,
            aqelem_param_num_3,
            aqelem_param_txt_1,
            aqelem_param_txt_2,
            aqelem_param_txt_3 ,
            aqelem_aqperso_cod                        )
                    values
                    (
                        :aqelem_aquete_cod,
                        :aqelem_quete_step,
                        :aqelem_aqetape_cod,
                        :aqelem_nom,
                        :aqelem_param_id,
                        :aqelem_param_ordre,
                        :aqelem_type,
                        :aqelem_misc_cod,
                        :aqelem_param_num_1,
                        :aqelem_param_num_2,
                        :aqelem_param_num_3,
                        :aqelem_param_txt_1,
                        :aqelem_param_txt_2,
                        :aqelem_param_txt_3,
                        :aqelem_aqperso_cod                        )
    returning aqelem_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_aquete_cod" => $this->aqelem_aquete_cod,
                ":aqelem_quete_step" => $this->aqelem_quete_step,
                ":aqelem_aqetape_cod" => $this->aqelem_aqetape_cod,
                ":aqelem_nom" => $this->aqelem_nom,
                ":aqelem_param_id" => $this->aqelem_param_id,
                ":aqelem_param_ordre" => $this->aqelem_param_ordre,
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_misc_cod" => $this->aqelem_misc_cod,
                ":aqelem_param_num_1" => $this->aqelem_param_num_1,
                ":aqelem_param_num_2" => $this->aqelem_param_num_2,
                ":aqelem_param_num_3" => $this->aqelem_param_num_3,
                ":aqelem_param_txt_1" => $this->aqelem_param_txt_1,
                ":aqelem_param_txt_2" => $this->aqelem_param_txt_2,
                ":aqelem_param_txt_3" => $this->aqelem_param_txt_3,
                ":aqelem_aqperso_cod" => $this->aqelem_aqperso_cod,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_element
                    set
            aqelem_aquete_cod = :aqelem_aquete_cod,
            aqelem_quete_step = :aqelem_quete_step,
            aqelem_aqetape_cod = :aqelem_aqetape_cod,
            aqelem_nom = :aqelem_nom,
            aqelem_param_id = :aqelem_param_id,
            aqelem_param_ordre = :aqelem_param_ordre,
            aqelem_type = :aqelem_type,
            aqelem_misc_cod = :aqelem_misc_cod,
            aqelem_param_num_1 = :aqelem_param_num_1,
            aqelem_param_num_2 = :aqelem_param_num_2,
            aqelem_param_num_3 = :aqelem_param_num_3,
            aqelem_param_txt_1 = :aqelem_param_txt_1,
            aqelem_param_txt_2 = :aqelem_param_txt_2,
            aqelem_param_txt_3 = :aqelem_param_txt_3,
            aqelem_aqperso_cod = :aqelem_aqperso_cod                        where aqelem_cod = :aqelem_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqelem_cod" => $this->aqelem_cod,
                ":aqelem_aqetape_cod" => $this->aqelem_aqetape_cod,
                ":aqelem_nom" => $this->aqelem_nom,
                ":aqelem_aquete_cod" => $this->aqelem_aquete_cod,
                ":aqelem_quete_step" => $this->aqelem_quete_step,
                ":aqelem_param_id" => $this->aqelem_param_id,
                ":aqelem_param_ordre" => $this->aqelem_param_ordre,
                ":aqelem_type" => $this->aqelem_type,
                ":aqelem_misc_cod" => $this->aqelem_misc_cod,
                ":aqelem_param_num_1" => $this->aqelem_param_num_1,
                ":aqelem_param_num_2" => $this->aqelem_param_num_2,
                ":aqelem_param_num_3" => $this->aqelem_param_num_3,
                ":aqelem_param_txt_1" => $this->aqelem_param_txt_1,
                ":aqelem_param_txt_2" => $this->aqelem_param_txt_2,
                ":aqelem_param_txt_3" => $this->aqelem_param_txt_3,
                ":aqelem_aqperso_cod" => $this->aqelem_aqperso_cod,
            ),$stmt);
        }
    }

    /**
     * supprime tous les éléments d'une étapes (attention supprime aussi les élément généré pour les perso ayant fait la quete).
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
     */
    function deleteBy_aqetape_cod($aqetape_cod)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }

    /**
     * supprime tous les éléments d'une quete pour un perso.
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
     */
    function deleteBy_aqperso_cod($aqperso_cod)
    {
        $pdo    = new bddpdo;
        $req    = "DELETE from quetes.aquete_element where aqelem_aqperso_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso_cod), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }

    /**
     * supprime tous les éléments d'une étapes qui ne sont pas dans la liste des elements
     * @return bool|array => false pas réussi a supprimer
     * @global bdd_mysql $pdo
     */
    function clean($aqetape_cod, $element_list)
    {
        $where = "";
        if (sizeof($element_list)>0)
        {
            foreach ($element_list as $k => $e) $where .= (1*$e)."," ;
            $where = " and aqelem_cod not in (". substr($where, 0, -1) .") ";
        }
        
        $pdo    = new bddpdo;
        $retour = array();
        $req    = "SELECT * from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_aqperso_cod is null $where ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod), $stmt);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_element;
            $temp->charge($result["aqelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }

        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_aqperso_cod is null $where ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod), $stmt);

        return (count($retour) == 0) ?  false : $retour ;
    }

    /**
     * supprime tous les éléments d'une étapes d'un paramètre donné pour un perso qui ne sont pas dans la liste des elements
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
     */
    function clean_perso_step($aqetape_cod, $aqelem_aqperso_cod, $aqelem_quete_step, $aqelem_param_id, $element_list)
    {
        $where = "";
        if (sizeof($element_list)>0)
        {
            foreach ($element_list as $k => $e) $where .= (1*$e)."," ;
            $where = " and aqelem_cod not in (". substr($where, 0, -1) .") ";
        }

        $pdo    = new bddpdo;
        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_aqperso_cod = ? and aqelem_quete_step = ?  and aqelem_param_id = ? $where ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod, $aqelem_aqperso_cod, $aqelem_quete_step, $aqelem_param_id), $stmt);
        return true;
    }

    /**
     * recherche les éléments (pour un emodele) d'une étapes par son n° et le n° de paramètre
     * @global bdd_mysql $pdo
     * @return bool|array => false pas trouvé
     */
    function getBy_etape_param_id($aqetape_cod, $param_id)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqelem_cod  from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_param_id = ? and aqelem_aqperso_cod is null order by aqelem_param_ordre,aqelem_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqetape_cod, $param_id),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_element;
            $temp->charge($result["aqelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        if(count($retour) == 0)
        {
            return false;
        }
        return $retour;
    }

    /**
     * recherche les éléments d'un perso pour une étapes pour un id de paramètre
     * @global bdd_mysql $pdo
     * @return boolean => false pas trouvé
     */
    function getBy_aqperso_param_id(aquete_perso $aqperso, $param_id)
    {
        $retour = array();
        $pdo = new bddpdo;

        // D'abord on cherche pour le perso!
        $req = "select aqelem_cod from quetes.aquete_perso
                join quetes.aquete_element on aqelem_aqperso_cod=aqperso_cod
                join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod
                where aqperso_cod = ? and aqelem_quete_step = ? and aqelem_param_id = ?
                order by aqelem_param_id,aqelem_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqperso->aqperso_cod, $aqperso->aqperso_quete_step, $param_id),$stmt);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);        // Lire tout...

        //* La recherche n'est plus étendue aux éléments du modele le perso DOIT posseder les éléments
        //if(count($result) == 0)                 // si trouve rien on prend le modèle !
        //{
        //    // Si on a rien pour le perso on prend le modele
        //    $req = "select aqelem_cod from quetes.aquete_etape
        //        join quetes.aquete_element on aqelem_aqetape_cod=aqetape_cod and aqelem_aqperso_cod is NULL
        //        where aqetape_cod = ? and aqelem_param_id = ?
        //        order by aqelem_param_id,aqelem_cod ";
        //    $stmt = $pdo->prepare($req);
        //    $stmt = $pdo->execute(array($aqperso->etape->aqetape_cod, $param_id),$stmt);
//
        //    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);        // Lire tout...
        //}

        if(count($result) == 0)
        {
            return false;
        }

        foreach ($result as $k=> $v)
        {
            $temp = new aquete_element;
            $temp->charge($v["aqelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;

    }

    /**
     * recherche les éléments d'un perso pour une étapes pour un id de paramètre
     * La recherche s'appui sur getBy_aqperso_param_id mais réalise des controles sur les valeurs attendues
     * @global bdd_mysql $pdo
     * @return boolean => false pas trouvé, l'élément ou un tableau d'élément
     */
    function get_aqperso_element(aquete_perso $aqperso, $param_id, $type="", $nb_element=1)
    {
        if (!$result = $this-> getBy_aqperso_param_id( $aqperso, $param_id))
        {
            return false;       // Paramètre non trouvé
        }

        if ((count($result) != $nb_element) && ($nb_element>0))
        {
            return false;       // Nombre d'élement incompatible avec ce qui est attendu
        }

        // il est possible de passer un tableau contenant les types
        if (is_array($type))
        {
            foreach ($result as $k => $element)
            {
                if (!in_array($element->aqelem_type , $type))
                {
                    return false;       // Type d'élement incompatible avec ce qui est attendu
                }
            }
        }
        else if ($type!="")
        {
            foreach ($result as $k => $element)
            {
                if ($element->aqelem_type != $type)
                {
                    return false;       // Type d'élement incompatible avec ce qui est attendu
                }
            }
        }

        if ($nb_element==1)
        {
            return $result[0];      // Si un seul élément, on revoi directement l'objet!
        }

        return $result ;
    }


    /**
     * insitancie les éléments d'un perso pour une étape
     * @global bdd_mysql $pdo
     */
    function setInstance_perso_step(aquete_perso $aqperso)
    {
        $retour = array();
        $pdo = new bddpdo;

        // D'abord on cherche si cela n'a pas déjà été fait
        $req = "select count(*) as count from quetes.aquete_element where aqelem_aqperso_cod = ? and aqelem_quete_step = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqperso->aqperso_cod, $aqperso->aqperso_quete_step),$stmt);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result["count"]>0)
        {
            return ;        // ça a déjà été fait
        }

        //----------------------------------------------------------------------------
        // Maintenant on prend élément par élément depuis le modele
        $req = "select aqelem_cod from quetes.aquete_etape 
            join quetes.aquete_element on aqelem_aqetape_cod=aqetape_cod and aqelem_aqperso_cod is NULL
            where aqetape_cod = ?
            order by aqelem_param_id, aqelem_param_ordre, aqelem_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqperso->aqperso_etape_cod ),$stmt);

        $element = new aquete_element;
        while($result = $stmt->fetch())
        {
            $element->charge($result["aqelem_cod"]);        // là on a l'élément du modele,

            // Cas particulier du type "element", car il fait référence à un élément d'une étape précédente
            if ( $element->aqelem_type != 'element')
            {
                // on va le customiszer pour en sauvegarder un exemplaire dédié au perso
                $element->aqelem_quete_step = $aqperso->aqperso_quete_step ;    // Elément du Step en cours
                $element->aqelem_aqperso_cod = $aqperso->aqperso_cod ;          // élément dédié au perso
                $element->stocke(true);                                   // stocke new va dupliquer l'élément
            }
            else
            {
                // On va instancier les valeurs pointées plutôt que le pointeur
                //2 cas possible: on instancie à partir de l'étape  modéle (cas aqelem_param_num_2=1) ou à partir de de l'épae déjà instancié par le perso (cas aqelem_param_num_2=0)
                if ($element->aqelem_param_num_2 == 1){

                    $req = "select aqelem_cod from quetes.aquete_element 
                        where aqelem_aqetape_cod=? and aqelem_param_id=? and aqelem_aqperso_cod is null
                        order by aqelem_cod ";
                    $stmte = $pdo->prepare($req);
                    $stmte = $pdo->execute(array(
                                        $element->aqelem_misc_cod,
                                        $element->aqelem_param_num_1 ),$stmte);

                } else {

                    $req = "select aqelem_cod from quetes.aquete_element 
                        where aqelem_aqetape_cod=? and aqelem_aqperso_cod = ? and aqelem_param_id=? 
                        and aqelem_quete_step = (
                                  select max(aqelem_quete_step) 
                                  from quetes.aquete_element 
                                  where aqelem_aqetape_cod=? and aqelem_aqperso_cod = ? and aqelem_param_id=? 
                                )
                        order by aqelem_cod ";
                    $stmte = $pdo->prepare($req);
                    $stmte = $pdo->execute(array(
                                        $element->aqelem_misc_cod,
                                        $aqperso->aqperso_cod,
                                        $element->aqelem_param_num_1,
                                        $element->aqelem_misc_cod,
                                        $aqperso->aqperso_cod,
                                        $element->aqelem_param_num_1 ),$stmte);
                }

                while($result = $stmte->fetch())
                {
                    $elem = new aquete_element;
                    $elem->charge($result["aqelem_cod"]);                        // là on a l'élément du modele,
                    $elem->aqelem_aqetape_cod = $aqperso->aqperso_etape_cod ;    // Copie de l'Elément pour l'étape en cours
                    $elem->aqelem_param_id  = $element->aqelem_param_id ;        // Copie de l'Elément prent la place du paramètre
                    $elem->aqelem_quete_step = $aqperso->aqperso_quete_step ;    // Copie de l'Elément pour le step en cours
                    $elem->aqelem_aqperso_cod = $aqperso->aqperso_cod ;          // élément dédié au perso
                    $elem->stocke(true);                                   // stocke new va dupliquer l'élément
                }
            }
        }

    }

    /**
     * retourne un texte en français pour l'élément
     * @global bdd_mysql $pdo
     */
    function get_element_texte($aqelem_cod=0)
    {
        $pdo = new bddpdo;
        $element_texte = "" ;

        // Si le cod est fourni c'est que l'élément n' pas été chargé
        if ($aqelem_cod!=0)
        {
            $this->charge($aqelem_cod);
        }

        switch ($this->aqelem_type)
        {
            case 'perso':
                $perso = new perso();
                $perso->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$perso->perso_nom."</em></strong>";
                break;

            case 'lieu':
                $lieu = new lieu();
                $lieu->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$lieu->lieu_nom."</em></strong>";
            break;

            case 'position':

                $pos = new positions();
                $pos->charge($this->aqelem_misc_cod);
                $lieu = new lieu();
                $lieu->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$lieu->lieu_nom."</em></strong>";

                $position = new positions() ;
                $position->charge( $this->aqelem_misc_cod );
                $etage = new etage();
                $etage->getByNumero($position->pos_etage);
                $element_texte = 'Etage:'.$etage->etage_reference.': X='.$position->pos_x.',Y='.$position->pos_y.' - '.$etage->etage_libelle ;
                $lpos = new lieu_position();
                if ($lpos->getByPos( $this->aqelem_misc_cod ))
                {
                    $lieu = new lieu();
                    $lieu->charge($lpos->lpos_lieu_cod);
                    $element_texte = $lieu->lieu_nom." (".$element_texte.")";
                }
            break;

            case 'lieu_type':
                $tlieu = new lieu_type();
                $tlieu->charge($this->aqelem_misc_cod);
                $req = "SELECT etage_numero, etage_libelle from etage where etage_reference = etage_numero and etage_numero in (?,?) order by etage_numero desc";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array( $this->aqelem_param_num_1, $this->aqelem_param_num_2),$stmt);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($this->aqelem_param_num_1 == $this->aqelem_param_num_2)
                    $element_texte = "<strong><em>{$tlieu->tlieu_libelle}</em></strong> de l'étage <strong>{$result[0]["etage_numero"]}</strong> (<em>{$result[0]["etage_libelle"]}</em>)";
                else
                    $element_texte = "<strong><em>{$tlieu->tlieu_libelle}</em></strong>  des étages <strong>{$result[0]["etage_numero"]}</strong> à <strong>{$result[1]["etage_numero"]}</strong> (<em>{$result[0]["etage_libelle"]} à {$result[1]["etage_libelle"]}</em>)";
            break;

            case 'objet_generique':
                $objet_generique = new objet_generique();
                $objet_generique->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$objet_generique->gobj_nom."</em></strong>";
            break;

            case 'bonus':
                $bonus = new bonus_type();
                $bonus->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$bonus->tonbus_libelle."</em></strong>";
            break;

            case 'race':
                $race = new race();
                $race->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$race->race_nom."</em></strong>";
            break;

            case 'type_monstre_generique':
                $type = new monstre_generique();
                $type->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$type->gmon_nom."</em></strong>";
            break;

            case 'objet':
                $objet = new objets();
                $objet->charge($this->aqelem_misc_cod);
                $element_texte = "<strong><em>".$objet->obj_nom."</em></strong>";
            break;

            case 'valeur':
                $element_texte = "<strong><em>".$this->aqelem_param_num_1."</em></strong>";
            break;

            case 'texte':
                $element_texte = "<strong><em>".$this->aqelem_param_txt_1."</em></strong>";
            break;

            case 'choix':
                $element_texte =  "<br>&nbsp;&nbsp;&nbsp;Vous : <em>".$this->aqelem_param_txt_1 ."</em>";
                break;
        }

        return $element_texte ;
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_element
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqelem_cod  from quetes.aquete_element order by aqelem_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_element;
            $temp->charge($result["aqelem_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments){
        switch(substr($name, 0, 6)){
            case 'getBy_':
                if(property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo = new bddpdo;
                    $req = "select aqelem_cod  from quetes.aquete_element where " . substr($name, 6) . " = ? order by aqelem_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_element;
                        $temp->charge($result["aqelem_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if(count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_element');
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