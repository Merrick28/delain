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
     * supprime tous les éléments d'une étapes qui ne sont pas dans la liste des elements
     * @global bdd_mysql $pdo
     * @return boolean => false pas réussi a supprimer
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
        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod = ? and aqelem_aqperso_cod is null $where ";;
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqetape_cod), $stmt);

        return true;
    }

    /**
     * recherche les éléments d'une étapes par son n°
     * @global bdd_mysql $pdo
     * @return boolean => false pas trouvé
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
     * retourne un texte en français pour l'élément
     * @global bdd_mysql $pdo
     */
    function get_element_texte($aqelem_cod=0)
    {
        $element_texte = "" ;

        // Si le cod est fourni c'est que l'élément n' pas été chargé
        if ($aqelem_cod!=0)
        {
            $this->charge($aqelem_cod);
        }

        switch ($this->aqelem_type)
        {
            case 'perso':
            case 'lieu':
                $element_texte = "<b><i>".$this->aqelem_nom."</i></b>";
            break;

            case 'choix':
                $element_texte =  "<br>&nbsp;&nbsp;&nbsp;Vous : <i>".$this->aqelem_param_txt_1 ."</i>";
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
                die('Unknown method.');
        }
    }
}