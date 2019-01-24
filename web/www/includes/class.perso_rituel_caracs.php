<?php
/**
 * includes/class.perso_rituel_caracs.php
 */

/*************************************************************/
/* Voir fonction sql                                         */
/* FUNCTION public.passage_niveau_test(integer, integer);    */
/*   carac_cod :                                             */
/*        1 = temps                                          */
/*        2 = dégats distance                                */
/*        3 = régénération                                   */
/*        4 = dégats corps à corps                           */
/*        5 = armure                                         */
/*        6 = vue                                            */
/*        7 = réparation                                     */
/*        8 = sorts mémorisables                             */
/*        9 = vampirisme                                     */
/*       10 = af lvl 2                                       */
/*       11 = af lvl 3                                       */
/*       12 = feinte                                         */
/*       13 = feinte lvl 2                                   */
/*       14 = feinte lvl 3                                   */
/*       15 = Coup de grace                                  */
/*       16 = Coup de grace lvl 2                            */
/*       17 = Coup de grace lvl 3                            */
/*       18 = af                                             */
/*       19 = réceptacle magique                             */
/*       20 = amel memo                                      */
/*       21 -> 23 : bour portant                             */
/*       24 -> 26 : tir précis                               */
/*       27 -> force                                         */
/*       28 -> dext.                                         */
/*       29 -> constit                                       */
/*       30 -> intelligence                                  */
/*************************************************************/

/**
 * Class perso_rituel_caracs
 *
 * Gère les objets BDD de la table perso_rituel_caracs
 */
class perso_rituel_caracs
{
    var $prcarac_cod;
    var $prcarac_perso_cod;
    var $prcarac_date_rituel;
    var $prcarac_amelioration_carac_cod;
    var $prcarac_diminution_carac_cod;

    function __construct()
    {
        $this->prcarac_date_rituel = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de perso_rituel_caracs
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from perso_rituel_caracs where prcarac_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->prcarac_cod = $result['prcarac_cod'];
        $this->prcarac_perso_cod = $result['prcarac_perso_cod'];
        $this->prcarac_date_rituel = $result['prcarac_date_rituel'];
        $this->prcarac_amelioration_carac_cod = $result['prcarac_amelioration_carac_cod'];
        $this->prcarac_diminution_carac_cod = $result['prcarac_diminution_carac_cod'];
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
            $req = "insert into perso_rituel_caracs (
            prcarac_perso_cod,
            prcarac_date_rituel,
            prcarac_amelioration_carac_cod,
            prcarac_diminution_carac_cod                        )
                    values
                    (
                        :prcarac_perso_cod,
                        :prcarac_date_rituel,
                        :prcarac_amelioration_carac_cod,
                        :prcarac_diminution_carac_cod                        )
    returning prcarac_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":prcarac_perso_cod" => $this->prcarac_perso_cod,
                ":prcarac_date_rituel" => $this->prcarac_date_rituel,
                ":prcarac_amelioration_carac_cod" => $this->prcarac_amelioration_carac_cod,
                ":prcarac_diminution_carac_cod" => $this->prcarac_diminution_carac_cod,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update perso_rituel_caracs
                    set
            prcarac_perso_cod = :prcarac_perso_cod,
            prcarac_date_rituel = :prcarac_date_rituel,
            prcarac_amelioration_carac_cod = :prcarac_amelioration_carac_cod,
            prcarac_diminution_carac_cod = :prcarac_diminution_carac_cod                        where prcarac_cod = :prcarac_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":prcarac_cod" => $this->prcarac_cod,
                ":prcarac_perso_cod" => $this->prcarac_perso_cod,
                ":prcarac_date_rituel" => $this->prcarac_date_rituel,
                ":prcarac_amelioration_carac_cod" => $this->prcarac_amelioration_carac_cod,
                ":prcarac_diminution_carac_cod" => $this->prcarac_diminution_carac_cod,
            ),$stmt);
        }
    }

    /***
     * @param $perso_cod
     * @return bool faux ou l'objet représentant le dernier rituel du perso_cod
     * @throws Exception
     */
    function  get_dernier_rituel($perso_cod)
    {
        $retour = new perso_rituel_caracs();
        $pdo = new bddpdo;
        $req = "select prcarac_cod from perso_rituel_caracs where prcarac_perso_cod=:perso_cod
                and prcarac_date_rituel=(select max(prcarac_date_rituel) prcarac_date_rituel from perso_rituel_caracs where prcarac_perso_cod=:perso_cod)
                order by prcarac_perso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso_cod"=>$perso_cod),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $retour->charge($result["prcarac_cod"]);
        return $retour;
    }

    /**
     * @param $perso_cod
     * @return bool vrai si le rituel est permis faux dans le cas contraire (si la date du dernier rituel est trop proche voir param 138)
     * @throws Exception
     */
    function  is_rituel_possible($perso_cod)
    {
        $retour = new perso_rituel_caracs();
        $pdo = new bddpdo;
        $req = "select prcarac_cod from perso_rituel_caracs where prcarac_perso_cod=:perso_cod and prcarac_date_rituel > now()-((getparm_n(138)::text)||' DAYS')::interval ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso_cod"=>$perso_cod),$stmt);
        if($result = $stmt->fetch())
        {
            return false;       // Il y a eu des rituels trop récents
        }

        // plus de rituel récent, c'est ok, pour en faire un !
        return true;
    }

    /***
     * @param $perso_cod
     * @return int
     * @throws Exception
     */
    function get_nb_rituel($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select count(*) nb_rituel from perso_rituel_caracs where prcarac_perso_cod=:perso_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":perso_cod"=>$perso_cod),$stmt);
        $result = $stmt->fetch();

        return (int) $result["nb_rituel"];
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \perso_rituel_caracs
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select prcarac_cod  from perso_rituel_caracs order by prcarac_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new perso_rituel_caracs;
            $temp->charge($result["prcarac_cod"]);
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
                    $req = "select prcarac_cod  from perso_rituel_caracs where " . substr($name, 6) . " = ? order by prcarac_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new perso_rituel_caracs;
                        $temp->charge($result["prcarac_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table perso_rituel_caracs');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}