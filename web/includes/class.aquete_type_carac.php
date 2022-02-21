<?php
/**
 * includes/class.aquete_type_carac.php
 */

/**
 * Class aquete_type_carac
 *
 * Gère les objets BDD de la table aquete_type_carac
 */
class aquete_type_carac
{
    var $aqtypecarac_cod;
    var $aqtypecarac_nom;
    var $aqtypecarac_type;
    var $aqtypecarac_description;
    var $aqtypecarac_aff;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_type_carac
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_type_carac where aqtypecarac_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqtypecarac_cod = $result['aqtypecarac_cod'];
        $this->aqtypecarac_nom = $result['aqtypecarac_nom'];
        $this->aqtypecarac_type = $result['aqtypecarac_type'];
        $this->aqtypecarac_description = $result['aqtypecarac_description'];
        $this->aqtypecarac_aff = $result['aqtypecarac_aff'];
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
            $req = "insert into quetes.aquete_type_carac (
            aqtypecarac_nom,
            aqtypecarac_type,
            aqtypecarac_description,
            aqtypecarac_nom,
            aqtypecarac_type,
            aqtypecarac_description,
            aqtypecarac_aff                        )
                    values
                    (
                        :aqtypecarac_nom,
                        :aqtypecarac_type,
                        :aqtypecarac_description,
                        :aqtypecarac_nom,
                        :aqtypecarac_type,
                        :aqtypecarac_description,
                        :aqtypecarac_aff                        )
    returning aqtypecarac_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_aff" => $this->aqtypecarac_aff,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_type_carac
                    set
            aqtypecarac_nom = :aqtypecarac_nom,
            aqtypecarac_type = :aqtypecarac_type,
            aqtypecarac_description = :aqtypecarac_description,
            aqtypecarac_nom = :aqtypecarac_nom,
            aqtypecarac_type = :aqtypecarac_type,
            aqtypecarac_description = :aqtypecarac_description ,
            aqtypecarac_aff = :aqtypecarac_aff                        where aqtypecarac_cod = :aqtypecarac_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqtypecarac_cod" => $this->aqtypecarac_cod,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_cod" => $this->aqtypecarac_cod,
                ":aqtypecarac_nom" => $this->aqtypecarac_nom,
                ":aqtypecarac_type" => $this->aqtypecarac_type,
                ":aqtypecarac_description" => $this->aqtypecarac_description,
                ":aqtypecarac_aff" => $this->aqtypecarac_aff,
            ),$stmt);
        }
    }

    /**
     * @param objet_element $element
     * @return string
     */
    function element_language_humain(objet_element $objelem) {

        $signe = array("=" => "égale à", "!=" => "différent de", "<" => "inférieur à", "<=" => "inférieur ou égale à", "entre" => "entre", ">" => "supérieur à", ">=" => "supérieur ou égale à");
        $param_txt_2 = $objelem->objelem_param_txt_2 ;
        $param_txt_3 = $objelem->objelem_param_txt_3 ;

        // gestion des cas particuliers
        if ($objelem->objelem_misc_cod==16) {

            $voie = new voie_magique();
            $voie->charge($objelem->objelem_param_txt_2);
            $param_txt_2 = $voie->mvoie_libelle;

            if ($objelem->objelem_param_txt_3!="") {
                $voie->charge($objelem->objelem_param_txt_3);
                $param_txt_3 = $voie->mvoie_libelle;
            }
        }
        else if ($objelem->objelem_misc_cod==17) {

            // cas du type de perso
            if ($objelem->objelem_param_txt_2==1) $param_txt_2 = "aventurier" ;
            else if ($objelem->objelem_param_txt_2==2) $param_txt_2 = "monstre" ;
            else if ($objelem->objelem_param_txt_2==3) $param_txt_2 = "familier" ;
            else $param_txt_2 = "inconnu" ;
            if ($objelem->objelem_param_txt_3!="") {
                if ($objelem->objelem_param_txt_3==1) $param_txt_3 = "aventurier" ;
                else if ($objelem->objelem_param_txt_3==2) $param_txt_3 = "monstre" ;
                else if ($objelem->objelem_param_txt_3==3) $param_txt_3 = "familier" ;
                else $param_txt_3 = "inconnu" ;
            }
        }
        else if ($objelem->objelem_misc_cod==18) {

            // cas du type de PNJ
            if ($objelem->objelem_param_txt_2==0) $param_txt_2 = "PJ" ;
            else if ($objelem->objelem_param_txt_2==1) $param_txt_2 = "PNJ" ;
            else if ($objelem->objelem_param_txt_2==3) $param_txt_2 = "4ème" ;
            else $param_txt_2 = "inconnu" ;
            if ($objelem->objelem_param_txt_3!="") {
                if ($objelem->objelem_param_txt_3==1) $param_txt_3 = "PJ" ;
                else if ($objelem->objelem_param_txt_3==2) $param_txt_3 = "PNJ" ;
                else if ($objelem->objelem_param_txt_3==3) $param_txt_3 = "4ème" ;
                else $param_txt_3 = "inconnu" ;
            }
        }
        else if ($objelem->objelem_misc_cod==27) {

            // cas du Code perso
            $p = new perso();
            $p->charge($objelem->objelem_param_txt_2);
            $param_txt_2 = $p->perso_nom ;

        }

        return $this->aqtypecarac_aff." ".$signe[$objelem->objelem_param_txt_1]." ".$param_txt_2.($param_txt_3=="" ? "" : " et ".$param_txt_3);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_type_carac
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqtypecarac_cod  from quetes.aquete_type_carac order by aqtypecarac_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_type_carac;
            $temp->charge($result["aqtypecarac_cod"]);
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
                    $req = "select aqtypecarac_cod  from quetes.aquete_type_carac where " . substr($name, 6) . " = ? order by aqtypecarac_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_type_carac;
                        $temp->charge($result["aqtypecarac_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_type_carac');
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