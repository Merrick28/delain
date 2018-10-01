<?php
/**
 * includes/class.aquete_etape.php
 */

/**
 * Class aquete_etape
 *
 * Gère les objets BDD de la table aquete_etape
 */
class aquete_etape
{
    var $aqetape_cod;
    var $aqetape_nom;
    var $aqetape_aquete_cod;
    var $aqetape_aqetapmodel_cod;
    var $aqetape_parametres;
    var $aqetape_texte;
    var $aqetape_etape_cod;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de aquete_etape
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_etape where aqetape_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqetape_cod = $result['aqetape_cod'];
        $this->aqetape_nom = $result['aqetape_nom'];
        $this->aqetape_aquete_cod = $result['aqetape_aquete_cod'];
        $this->aqetape_aqetapmodel_cod = $result['aqetape_aqetapmodel_cod'];
        $this->aqetape_parametres = $result['aqetape_parametres'];
        $this->aqetape_texte = $result['aqetape_texte'];
        $this->aqetape_etape_cod = $result['aqetape_etape_cod'];
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
            $req = "insert into quetes.aquete_etape (
            aqetape_nom,
            aqetape_aquete_cod,
            aqetape_aqetapmodel_cod,
            aqetape_parametres,
            aqetape_texte,
            aqetape_etape_cod                        )
                    values
                    (
                        :aqetape_nom,
                        :aqetape_aquete_cod,
                        :aqetape_aqetapmodel_cod,
                        :aqetape_parametres,
                        :aqetape_texte ,
                        :aqetape_etape_cod                        )
    returning aqetape_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetapmodel_cod" => $this->aqetape_aqetapmodel_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
                ":aqetape_etape_cod" => $this->aqetape_etape_cod,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_etape
                    set
            aqetape_nom = :aqetape_nom,
            aqetape_aquete_cod = :aqetape_aquete_cod,
            aqetape_aqetapmodel_cod = :aqetape_aqetapmodel_cod,
            aqetape_parametres = :aqetape_parametres,
            aqetape_texte = :aqetape_texte,
            aqetape_etape_cod = :aqetape_etape_cod                        where aqetape_cod = :aqetape_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_cod" => $this->aqetape_cod,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetapmodel_cod" => $this->aqetape_aqetapmodel_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
                ":aqetape_etape_cod" => $this->aqetape_etape_cod,
            ),$stmt);
        }
    }

    /**
     * supprime l'enregistrement
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function supprime($code="")
    {
        $pdo    = new bddpdo;

        // Si un code est fourni, on doit charger l'élément
        if ($code=="")
        {
            $code = $this->aqetape_cod;
        }
        else
        {
           $this->charge($code);    // oui, on le charge avant de le supprimer car on a besoin d'info pour le chemin
        }

        // On commence par supprimer les éléments qui ont été préparé pour cette étape.
        $element = new aquete_element;
        $element->deleteBy_aqetape_cod($code) ;

        //on fait pointer toutes les étapes qui pointaient sur celle-ci vers sa prochaine étape à la place.
        $req    = "UPDATE quetes.aquete_etape set aqetape_etape_cod = ? where aqetape_etape_cod = ? ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(1*$this->aqetape_etape_cod, 1*$this->aqetape_cod), $stmt);

        //idem pour la quete s'il s'agissait de la première étape
        $req    = "UPDATE quetes.aquete set aquete_etape_cod = ? where aquete_etape_cod = ? ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(1*$this->aqetape_etape_cod, 1*$this->aqetape_cod), $stmt);


        $req    = "DELETE from quetes.aquete_etape where aqetape_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }

    /**
     * retourne toutes les etapes de la quete dans l'ordre chronologique !
     * @global bdd_mysql $pdo
     * @param integer $quete => quete dont on veut les etapes
     * @param integer $etape => Commencer à cette étape, si 0 commencer au debut
     * @return boolean => false pas trouvé d'étape
     */
    function get_quete_etapes($quete_cod=0, $etape_cod=0)
    {
        if ($etape_cod==0)
        {
            // La première etape est celle fournie par la quete
            $quete = new aquete;
            $quete->charge( $quete_cod==0 ? $this->aqetape_aquete_cod : $quete_cod ) ;     // Si un code est fourni, on doit charger l'élément sinon prendre celui déjà chargé

            if ( 1*$quete->aquete_etape_cod == 0 ) return array();            // La quete ne dispose encore d'aucune étape

            $etape = new aquete_etape;
            $etape->charge( $quete->aquete_etape_cod ) ;                // Charger la première etape

            if ( 1*$etape->aqetape_etape_cod == 0 ) return array($etape) ;    // La quete ne dispose que de cette étape

            // retourner la première etape et toutes les autres
            return array_merge( array($etape), $this->get_quete_etapes($quete_cod, $etape->aqetape_etape_cod)) ;

        }
        else
        {
            // Charger l'étape demandé, et passer à la suivante
            $etape = new aquete_etape;
            $etape->charge( $etape_cod ) ;

            if ( 1*$etape->aqetape_etape_cod == 0 ) return  array($etape) ;    // il s'agissait de la dernière etape

            // Retourner cette étape et les vuivantes
            return array_merge( array($etape), $this->get_quete_etapes($quete_cod, $etape->aqetape_etape_cod)) ;
        }

    }

    // Fonction pour mettre en forme le texte d'une étape alors que l'étape n'a pas encore démarrée.
    // Les éléments de l'étapes n'ont pas encore été instanciés, on se base sur le template
    // Param1 => Element déclencheur, Param2 => Choix
    function get_initial_texte( $trigger_nom )
    {
        $hydrate_texte = "" ;
        $textes = explode("[", $this->aqetape_texte);

        $hydrate_texte.= $textes[0];        // Le début de la description
        foreach ($textes as $k => $v)
        {
            if (($v!="") && ($k>0))
            {
                $params = explode("]", $v);

                // On traite le cas particulier de la première etape non instanciée, alors on on a: Param1 => Element déclencheur, Param2 => Choix
                $param_num = 1*$params[0] ;
                if ($param_num == 1)
                {
                    $hydrate_texte.= $trigger_nom;
                }
                else if ($param_num == 2)
                {
                    $hydrate_texte .= "<br>";
                    $element = new aquete_element();
                    $elements = $element->getBy_etape_param_id($this->aqetape_cod, $param_num);
                    foreach ($elements as $i => $e)
                    {
                        if ($e->aqelem_misc_cod<0)
                            $link = "/jeu_test/frame_vue.php" ;
                        else
                            $link = "/jeu_test/quete_auto.php?methode=start&quete=".$this->aqetape_aquete_cod."&choix=".$e->aqelem_cod ;
                        $hydrate_texte .= '<br><a href="'.$link.'" style="margin:50px;">'.$e->aqelem_param_txt_1.'</a>';
                    }
                }
                $hydrate_texte.= $params[1];
            }
        }

        return $hydrate_texte ;
    }

    // Fonction pour mettre en forme le texte d'une étape du type choix
    function get_texte_choix()
    {
        $hydrate_texte = "" ;

        $element = new aquete_element();
        $elements = $element->getBy_etape_param_id($this->aqetape_cod, 1);
        foreach ($elements as $i => $e)
        {
            if ($e->aqelem_misc_cod==-1)
                $link = "/jeu_test/quete_auto.php?methode=stop&quete=".$this->aqetape_aquete_cod."&choix=".$e->aqelem_cod ;
            else
                $link = "/jeu_test/quete_auto.php?methode=choix&quete=".$this->aqetape_aquete_cod."&choix=".$e->aqelem_cod ;
            $hydrate_texte .= '<br><a href="'.$link.'" style="margin:50px;">'.$e->aqelem_param_txt_1.'</a>';
        }

        if (strpos($this->aqetape_texte, "[1]") !== false)
            $hydrate_texte = str_replace("[1]", $hydrate_texte, $this->aqetape_texte);
        else
            $hydrate_texte = $this->aqetape_texte.$hydrate_texte;  // Le début de la description

        return $hydrate_texte ;
    }

        /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_etape
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqetape_cod  from quetes.aquete_etape order by aqetape_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_etape;
            $temp->charge($result["aqetape_cod"]);
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
                    $req = "select aqetape_cod  from quetes.aquete_etape where " . substr($name, 6) . " = ? order by aqetape_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_etape;
                        $temp->charge($result["aqetape_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_etape');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}