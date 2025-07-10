<?php
/**
 * includes/class.compteur_valeur.php
 */

/**
 * Class compteur_valeur
 *
 * Gère les objets BDD de la table compteur_valeur
 */
class compteur_valeur
{
    var $comptval_cod;
    var $comptval_compteur_cod;
    var $comptval_perso_cod;
    var $comptval_valeur = 0;

    function __construct()
    {
    }

    /**
     * Charge dans la classe un enregistrement de compteur_valeur
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from compteur_valeur where comptval_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->comptval_cod = $result['comptval_cod'];
        $this->comptval_compteur_cod = $result['comptval_compteur_cod'];
        $this->comptval_perso_cod = $result['comptval_perso_cod'];
        $this->comptval_valeur = $result['comptval_valeur'];
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
            $req = "insert into compteur_valeur (
            comptval_compteur_cod,
            comptval_perso_cod,
            comptval_valeur                        )
                    values
                    (
                        :comptval_compteur_cod,
                        :comptval_perso_cod,
                        :comptval_valeur                        )
    returning comptval_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":comptval_compteur_cod" => $this->comptval_compteur_cod,
                ":comptval_perso_cod" => $this->comptval_perso_cod,
                ":comptval_valeur" => $this->comptval_valeur,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update compteur_valeur
                    set
            comptval_compteur_cod = :comptval_compteur_cod,
            comptval_perso_cod = :comptval_perso_cod,
            comptval_valeur = :comptval_valeur                        where comptval_cod = :comptval_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":comptval_cod" => $this->comptval_cod,
                ":comptval_compteur_cod" => $this->comptval_compteur_cod,
                ":comptval_perso_cod" => $this->comptval_perso_cod,
                ":comptval_valeur" => $this->comptval_valeur,
            ),$stmt);
        }
    }

    /**
     * recehrche le compteur spécifié pour le perso, le crée s'il n'existe pas
     *  int $perso_cod le perso
     *  int $compteur_cod le compteur
     *
     * Nota si le compteur n'est pas de type individuel, on retourne (en le creant le cas échéant) la valeur globale
     */
    function chargeBy_perso_compteur($perso_cod, $compteur_cod)
    {
        $pdo = new bddpdo;

        $cpt = new compteur();
        if (!$cpt->charge($compteur_cod))
        {
            return false; // compteur inexistant
        }

        // préparation de la requête
        if ($cpt->compteur_type == 0 or $perso_cod == 0 or $perso_cod == null) {
            $req = "select comptval_cod from compteur_valeur where comptval_perso_cod is null and comptval_compteur_cod = ?";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($compteur_cod),$stmt);
        } else {
            $req = "select comptval_cod from compteur_valeur where comptval_perso_cod = ? and comptval_compteur_cod = ?";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($perso_cod, $compteur_cod),$stmt);
        }

        // on cherche le compteur
        if($result = $stmt->fetch())
        {
            return $this->charge($result['comptval_cod']);
        }

        // compteur non trouvé, on le crée
        $this->comptval_compteur_cod = $compteur_cod;
        $this->comptval_perso_cod = $cpt->compteur_type == 0 ? null : $perso_cod; // si c'est un compteur global, on met perso_cod à null
        $this->comptval_valeur = $cpt->compteur_init; // valeur initiale
        $this->stocke(true); // on le stocke

        return true; // on retourne le compteur créé
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \compteur_valeur
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select comptval_cod  from compteur_valeur order by comptval_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new compteur_valeur;
            $temp->charge($result["comptval_cod"]);
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
                    $req = "select comptval_cod  from compteur_valeur where " . substr($name, 6) . " = ? order by comptval_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new compteur_valeur;
                        $temp->charge($result["comptval_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table compteur_valeur');
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