<?php
/**
 * includes/class.groupe_perso.php
 */

/**
 * Class groupe_perso
 *
 * Gère les objets BDD de la table groupe_perso
 */
class groupe_perso
{
    var $pgroupe_perso_cod;
    var $pgroupe_groupe_cod;
    var $pgroupe_statut = 0;
    var $pgroupe_chef = 0;
    var $pgroupe_montre_pa = 1;
    var $pgroupe_montre_dlt = 1;
    var $pgroupe_montre_pv = 1;
    var $pgroupe_montre_pv_max = 1;
    var $pgroupe_montre_bonus = 1;
    var $pgroupe_messages = 1;
    var $pgroupe_texte;
    var $pgroupe_texte_maj;
    var $pgroupe_message_mort = 1;
    var $pgroupe_champions = 1;
    var $pgroupe_valeur_rappel = 0;

    function __construct()
    {

        $this->pgroupe_texte_maj = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de groupe_perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from groupe_perso where pgroupe_perso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->pgroupe_perso_cod = $result['pgroupe_perso_cod'];
        $this->pgroupe_groupe_cod = $result['pgroupe_groupe_cod'];
        $this->pgroupe_statut = $result['pgroupe_statut'];
        $this->pgroupe_chef = $result['pgroupe_chef'];
        $this->pgroupe_montre_pa = $result['pgroupe_montre_pa'];
        $this->pgroupe_montre_dlt = $result['pgroupe_montre_dlt'];
        $this->pgroupe_montre_pv = $result['pgroupe_montre_pv'];
        $this->pgroupe_montre_pv_max = $result['pgroupe_montre_pv_max'];
        $this->pgroupe_montre_bonus = $result['pgroupe_montre_bonus'];
        $this->pgroupe_messages = $result['pgroupe_messages'];
        $this->pgroupe_texte = $result['pgroupe_texte'];
        $this->pgroupe_texte_maj = $result['pgroupe_texte_maj'];
        $this->pgroupe_message_mort = $result['pgroupe_message_mort'];
        $this->pgroupe_champions = $result['pgroupe_champions'];
        $this->pgroupe_valeur_rappel = $result['pgroupe_valeur_rappel'];
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
            $req = "insert into groupe_perso (
            pgroupe_groupe_cod,
            pgroupe_statut,
            pgroupe_chef,
            pgroupe_montre_pa,
            pgroupe_montre_dlt,
            pgroupe_montre_pv,
            pgroupe_montre_pv_max,
            pgroupe_montre_bonus,
            pgroupe_messages,
            pgroupe_texte,
            pgroupe_texte_maj,
            pgroupe_message_mort,
            pgroupe_champions,
            pgroupe_valeur_rappel                        )
                    values
                    (
                        :pgroupe_groupe_cod,
                        :pgroupe_statut,
                        :pgroupe_chef,
                        :pgroupe_montre_pa,
                        :pgroupe_montre_dlt,
                        :pgroupe_montre_pv,
                        :pgroupe_montre_pv_max,
                        :pgroupe_montre_bonus,
                        :pgroupe_messages,
                        :pgroupe_texte,
                        :pgroupe_texte_maj,
                        :pgroupe_message_mort,
                        :pgroupe_champions,
                        :pgroupe_valeur_rappel                        )
    returning pgroupe_perso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pgroupe_groupe_cod" => $this->pgroupe_groupe_cod,
                ":pgroupe_statut" => $this->pgroupe_statut,
                ":pgroupe_chef" => $this->pgroupe_chef,
                ":pgroupe_montre_pa" => $this->pgroupe_montre_pa,
                ":pgroupe_montre_dlt" => $this->pgroupe_montre_dlt,
                ":pgroupe_montre_pv" => $this->pgroupe_montre_pv,
                ":pgroupe_montre_pv_max" => $this->pgroupe_montre_pv_max,
                ":pgroupe_montre_bonus" => $this->pgroupe_montre_bonus,
                ":pgroupe_messages" => $this->pgroupe_messages,
                ":pgroupe_texte" => $this->pgroupe_texte,
                ":pgroupe_texte_maj" => $this->pgroupe_texte_maj,
                ":pgroupe_message_mort" => $this->pgroupe_message_mort,
                ":pgroupe_champions" => $this->pgroupe_champions,
                ":pgroupe_valeur_rappel" => $this->pgroupe_valeur_rappel,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update groupe_perso
                    set
            pgroupe_groupe_cod = :pgroupe_groupe_cod,
            pgroupe_statut = :pgroupe_statut,
            pgroupe_chef = :pgroupe_chef,
            pgroupe_montre_pa = :pgroupe_montre_pa,
            pgroupe_montre_dlt = :pgroupe_montre_dlt,
            pgroupe_montre_pv = :pgroupe_montre_pv,
            pgroupe_montre_pv_max = :pgroupe_montre_pv_max,
            pgroupe_montre_bonus = :pgroupe_montre_bonus,
            pgroupe_messages = :pgroupe_messages,
            pgroupe_texte = :pgroupe_texte,
            pgroupe_texte_maj = :pgroupe_texte_maj,
            pgroupe_message_mort = :pgroupe_message_mort,
            pgroupe_champions = :pgroupe_champions,
            pgroupe_valeur_rappel = :pgroupe_valeur_rappel                        where pgroupe_perso_cod = :pgroupe_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":pgroupe_perso_cod" => $this->pgroupe_perso_cod,
                ":pgroupe_groupe_cod" => $this->pgroupe_groupe_cod,
                ":pgroupe_statut" => $this->pgroupe_statut,
                ":pgroupe_chef" => $this->pgroupe_chef,
                ":pgroupe_montre_pa" => $this->pgroupe_montre_pa,
                ":pgroupe_montre_dlt" => $this->pgroupe_montre_dlt,
                ":pgroupe_montre_pv" => $this->pgroupe_montre_pv,
                ":pgroupe_montre_pv_max" => $this->pgroupe_montre_pv_max,
                ":pgroupe_montre_bonus" => $this->pgroupe_montre_bonus,
                ":pgroupe_messages" => $this->pgroupe_messages,
                ":pgroupe_texte" => $this->pgroupe_texte,
                ":pgroupe_texte_maj" => $this->pgroupe_texte_maj,
                ":pgroupe_message_mort" => $this->pgroupe_message_mort,
                ":pgroupe_champions" => $this->pgroupe_champions,
                ":pgroupe_valeur_rappel" => $this->pgroupe_valeur_rappel,
            ),$stmt);
        }
    }
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \groupe_perso
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select pgroupe_perso_cod  from groupe_perso order by pgroupe_perso_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new groupe_perso;
            $temp->charge($result["pgroupe_perso_cod"]);
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
                    $req = "select pgroupe_perso_cod  from groupe_perso where " . substr($name, 6) . " = ? order by pgroupe_perso_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new groupe_perso;
                        $temp->charge($result["pgroupe_perso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table groupe_perso');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}