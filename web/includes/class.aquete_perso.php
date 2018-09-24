<?php
/**
 * includes/class.aquete_perso.php
 */

/**
 * Class aquete_perso
 *
 * Gère les objets BDD de la table aquete_perso
 */
class aquete_perso
{
    var $aqperso_cod;
    var $aqperso_perso_cod;
    var $aqperso_aquete_cod;
    var $aqperso_quete_step;
    var $aqperso_etape_cod = 0;
    var $aqperso_actif = 'O';
    var $aqperso_nb_realisation = 0;
    var $aqperso_nb_termine = 0;
    var $aqperso_date_debut;
    var $aqperso_date_fin;

    function __construct()
    {

        $this->aqperso_date_debut = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de aquete_perso
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo = new bddpdo;
        $req = "select * from quetes.aquete_perso where aqperso_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code),$stmt);
        if(!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aqperso_cod = $result['aqperso_cod'];
        $this->aqperso_perso_cod = $result['aqperso_perso_cod'];
        $this->aqperso_aquete_cod = $result['aqperso_aquete_cod'];
        $this->aqperso_quete_step = $result['aqperso_quete_step'];
        $this->aqperso_etape_cod = $result['aqperso_etape_cod'];
        $this->aqperso_actif = $result['aqperso_actif'];
        $this->aqperso_nb_realisation = $result['aqperso_nb_realisation'];
        $this->aqperso_nb_termine = $result['aqperso_nb_termine'];
        $this->aqperso_date_debut = $result['aqperso_date_debut'];
        $this->aqperso_date_fin = $result['aqperso_date_fin'];
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
            $req = "insert into quetes.aquete_perso (
            aqperso_perso_cod,
            aqperso_aquete_cod,
            aqperso_quete_step,
            aqperso_etape_cod,
            aqperso_actif,
            aqperso_nb_realisation,
            aqperso_nb_termine,
            aqperso_date_debut,
            aqperso_date_fin                        )
                    values
                    (
                        :aqperso_perso_cod,
                        :aqperso_aquete_cod,
                        :aqperso_quete_step,
                        :aqperso_etape_cod,
                        :aqperso_actif,
                        :aqperso_nb_realisation,
                        :aqperso_nb_termine,
                        :aqperso_date_debut,
                        :aqperso_date_fin                        )
    returning aqperso_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_aquete_cod" => $this->aqperso_aquete_cod,
                ":aqperso_quete_step" => $this->aqperso_quete_step,
                ":aqperso_etape_cod" => $this->aqperso_etape_cod,
                ":aqperso_actif" => $this->aqperso_actif,
                ":aqperso_nb_realisation" => $this->aqperso_nb_realisation,
                ":aqperso_nb_termine" => $this->aqperso_nb_termine,
                ":aqperso_date_debut" => $this->aqperso_date_debut,
                ":aqperso_date_fin" => $this->aqperso_date_fin,
            ),$stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req = "update quetes.aquete_perso
                    set
            aqperso_perso_cod = :aqperso_perso_cod,
            aqperso_aquete_cod = :aqperso_aquete_cod,
            aqperso_quete_step = :aqperso_quete_step,
            aqperso_etape_cod = :aqperso_etape_cod,
            aqperso_actif = :aqperso_actif,
            aqperso_nb_realisation = :aqperso_nb_realisation,
            aqperso_nb_termine = :aqperso_nb_termine,
            aqperso_date_debut = :aqperso_date_debut,
            aqperso_date_fin = :aqperso_date_fin                        where aqperso_cod = :aqperso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqperso_cod" => $this->aqperso_cod,
                ":aqperso_perso_cod" => $this->aqperso_perso_cod,
                ":aqperso_aquete_cod" => $this->aqperso_aquete_cod,
                ":aqperso_quete_step" => $this->aqperso_quete_step,
                ":aqperso_etape_cod" => $this->aqperso_etape_cod,
                ":aqperso_actif" => $this->aqperso_actif,
                ":aqperso_nb_realisation" => $this->aqperso_nb_realisation,
                ":aqperso_nb_termine" => $this->aqperso_nb_termine,
                ":aqperso_date_debut" => $this->aqperso_date_debut,
                ":aqperso_date_fin" => $this->aqperso_date_fin,
            ),$stmt);
        }
    }

    //Comptage des quetes en cours d'un perso (toutes quetes confondues)
    function get_perso_nb_en_cours($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select count(*) as count from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_actif='O' ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        $result = $stmt->fetch();
        return 1*$result['count'];
    }

    //Retourne la liste des quetes en cours pour un perso
    function get_perso_quete_en_cours($perso_cod)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqperso_cod from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_actif='O' order by aqperso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso;
            $temp->charge($result["aqperso_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        if(count($retour) == 0)
        {
            return false;
        }
        return $retour;
    }


    //Charge la quete du perso
    function get_perso_quete($perso_cod, $aquete_cod)
    {

        $pdo = new bddpdo;
        $req = "select aqperso_cod from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_aquete_cod=? order by aqperso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $aquete_cod),$stmt);
        if (!$result = $stmt->fetch())   return false;


        $this->charge($result["aqperso_cod"]) ;
        return true;
    }

    // Créé une nouvelle instance de la quete #$aquete_cod pour le $perso_cod
    // $trigger est un tableau qui contient les elements déclencheurs de la quete
    // Rreourne rien si tout c'est bien passé, sinon un message d'erreur
    function demarre_quete($perso_cod, $aquete_cod, $trigger)
    {
        $pdo = new bddpdo;
            
        $quete = new aquete();
        $quete->charge($aquete_cod);

        //On regarde les conditions
        $req = "select *  from quetes.aquete_perso where aqperso_perso_cod = ? and aqperso_aquete_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $aquete_cod),$stmt);
        $result = $stmt->fetch();
        $new = true ;
        if ($result)
        {
            // La quete a déjà été faite, on vérifié si on peut la refaire
            $new = false ;
            $this->charge($result["aqperso_cod"]);
            if ($this->aqperso_actif=='O') return "Vous avez déjà démarré cette quête, vous ne pouvez la recommencer sans terminer la précédente";
            if ($this->aqperso_nb_realisation>=$quete->aquete_nb_max_rejouable) return "Vous avez déjà terminé cette quête {$this->aqperso_nb_realisation} fois, il ne vous est plus possible de la refaire une fois de plus";
            if ($quete->get_nb_en_cours()>=$quete->aquete_nb_max_instance) return "Il n'est pas possible de commencer cette quête actuellement.";
            if ($quete->get_nb_total()>=$quete->aquete_nb_max_quete) return "Cette quête est maintenant fermé.";
        }

        // Bon on a passé tous les tests, on ouvre ou ré-ouvre la quête
        $this->aqperso_perso_cod = $perso_cod;
        $this->aqperso_aquete_cod = $aquete_cod;
        $this->aqperso_quete_step = 2;       // Démarrage step 2 (la 1 est déjà presque terminé, il reste juste à instancier les éléments)!

        // On charge l'étape 1 !
        $etape = new aquete_etape();
        $etape->charge($quete->aquete_etape_cod);  // c'est la première étape, celle de démarrage

        // Vérification du choix fait à l'tape 1 pour déterminer l'étape suivante
        $element = new aquete_element();
        $element->charge($trigger["aqelem_cod"]);
        if (($element->aqelem_aquete_cod!=$aquete_cod) && ($element->aqelem_misc_cod!=0)) return "Vous essayez commencer par l'étape d'une autre quête ?";
        if ($element->aqelem_misc_cod<0) return "Le choix pour démarrer la quête n'est pas valide!";

        // L'étape 1 est considérée comme terminée, puisque qu'elle ne sert qu'a créer cette instance, on va directement à l'étape suivante
        if ($element->aqelem_misc_cod==0)
            $this->aqperso_etape_cod = $etape->aqetape_etape_cod ;       // on commence à l'étape suivante de la 1ere etape (seconde étape)
        else
            $this->aqperso_etape_cod = $element->aqelem_misc_cod ;      // on commence par le choix demandé !

        // cas où il n'y a pas de seconde étape, la quête est immédiatement terminée!!!
        if ($this->aqperso_etape_cod=="")
        {
            $this->aqperso_etape_cod = $quete->aquete_etape_cod ; // dernière etape c'est la première !
            $this->aqperso_actif = 'N';
            $this->aqperso_date_fin = date('Y-m-d H:i:s');
        }
        else
        {
            $this->aqperso_actif = 'O';
            $this->aqperso_date_fin = NULL;
        }

        // Les restes des infos et sauvegarde !
        $this->aqperso_nb_realisation ++ ;
        $this->aqperso_date_debut = date('Y-m-d H:i:s');
        $this->stocke($new) ;

        // Il reste à générer les éléments de cette quête dédiés au perso (juste le choix et le déclencheur) !!!
        $element->aqelem_aqperso_cod = $this->aqperso_cod ;      // le nouvelle élément est dédié au perso
        $element->aqelem_quete_step = 1 ;                        // une bouvelle quete démarre step 1
        $element->stocke(true);                             // stocke new va dupliquer l'élément

        $element = new aquete_element();
        $element->aqelem_aquete_cod = $aquete_cod;
        $element->aqelem_quete_step = 1 ;   // Elément du Step 1
        $element->aqelem_aqetape_cod = $quete->aquete_etape_cod ;
        $element->aqelem_param_id = 1;
        $element->aqelem_param_ordre = 0;
        $element->aqelem_type = $trigger["aqelem_type"] ;
        $element->aqelem_misc_cod = 1*$trigger['aqelem_misc_cod'];
        $element->aqelem_nom = $trigger['nom'] ;                 // lNom de l'élément
        $element->aqelem_aqperso_cod = $this->aqperso_cod ;       // élément dédié au perso
        $element->stocke(true);                             // stocke new va dupliquer l'élément

        // ajouter une ligne dans le journal
        $texte = $this->hydrate($this->aqperso_cod, 1);

        if ($texte != '')
        {
            // Journaliser le texte pour eviter de le repréparer à chaque consultation
            $perso_journal = new aquete_perso_journal();
            $perso_journal->aqpersoj_aqperso_cod = $this->aqperso_cod;
            $perso_journal->aqpersoj_quete_step = 1;
            $perso_journal->aqpersoj_texte = $texte ;
            $perso_journal->stocke(true);
        }

        return "" ;     // Ok!
    }


    // Une fonction privée qui met en forme une étape avec les éléments de cette étape
    function hydrate($aqperso_cod, $quete_step)
    {
        $element = new aquete_element;      // pour utilisation des fonctions de cette classe
        $pdo = new bddpdo;
        $req = "select aqelem_cod, aqelem_param_id, aqetape_texte from quetes.aquete_perso
                join quetes.aquete_element on aqelem_aqperso_cod=aqperso_cod
                join quetes.aquete_etape on aqetape_cod = aqelem_aqetape_cod
                where aqperso_cod = ? and aqelem_quete_step = ?
                order by aqelem_param_id,aqelem_cod ";
        $stmt = $pdo->prepare($req);

        $stmt = $pdo->execute(array($aqperso_cod, $quete_step),$stmt);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);        // Lire tout...
        if(count($result) == 0) return "";                  // si rien trouvé !

        // Préparation d'une table pour la recherche inversée
        $param_id = array() ;
        foreach ($result as $k=> $v) $param_id[$v["aqelem_param_id"]] = $k;

        $hydrate_texte = "" ;
        $textes = explode("[", $result[0]["aqetape_texte"]);
        $hydrate_texte.= $textes[0];        // Le début de la description
        foreach ($textes as $k => $v)
        {
            if (($v!="") && ($k>0))
            {
                $params = explode("]", $v);
                $param_num = 1*$params[0] ; // N° du paramètre dans le texte de l'étape
                $aqelem_cod = $result[$param_id[$param_num]]["aqelem_cod"] ;  // le code de l'élément rattaché au paramètre
                $hydrate_texte.= $element->get_element_texte($aqelem_cod);
                $hydrate_texte.= $params[1];
            }
        }

       return $hydrate_texte;
    }

    // On reprend à partir de l'étape en cours, on regarde si elle est terminée et ainsi de suite
    function run()
    {

        $perso_journal = new aquete_perso_journal();
        $perso_journal->aqpersoj_aqperso_cod = $this->aqperso_cod;

        do
        {
            $loop = false;   // par défaut on sort, sauf si une etape est validée, alors il faut continuer
            $etape = new aquete_etape();
            $etape->charge($this->aqperso_etape_cod);
            $etape_temp = new aquete_etape_template();
            $etape_temp->charge($etape->aqetape_aqetaptemp_cod);
            switch($etape_temp->aqetaptemp_tag)
            {
                case "#TEXTE":
                    // cette etape sert à mettre du contexte dans le journal, l'ètape est autovalidé et n'a pas de paramètre.
                    $perso_journal->aqpersoj_quete_step = $this->aqperso_quete_step ;
                    $perso_journal->aqpersoj_texte = $etape->aqetape_texte ;
                    $perso_journal->stocke(true);

                    // Etape suviante:
                    $this->aqperso_etape_cod = $etape->aqetape_etape_cod ;
                    $this->aqperso_quete_step ++ ;
                    $loop = true ; // Etape terminé, on boucle à l'étape suivante
                break;
            }
        } while ($loop);

        $this->stocke();    // Mettre à jours les infos
    }


    // retourne un texte pour l'étape courante (pour faire un choix par exemple)
    function get_texte_etape_courante()
    {
        $etape = new aquete_etape();
        $etape->charge($this->aqperso_etape_cod);
        $etape_temp = new aquete_etape_template();
        $etape_temp->charge($etape->aqetape_aqetaptemp_cod);

        switch($etape_temp->aqetaptemp_tag)
        {
            case "#CHOIX":
                return  $etape->get_texte_choix();
                break;
        }

        return "";
    }

    // retourne le journal de la quete du perso jusqu'a l'étape en cours.
    function journal($aqperso_cod=0)
    {
        $journal_quete = "" ;
        if ( $aqperso_cod != 0 ) $this->charge($aqperso_cod) ;   // charger la quete perso si ce n'est pas fait

        $pdo = new bddpdo;
        $perso_journal = new aquete_perso_journal() ;
        $perso_journaux =  $perso_journal->getBy_aqperso_cod($this->aqperso_cod);

        foreach ($perso_journaux as $k => $journal) $journal_quete.=$journal->aqpersoj_texte."<br><br>";

        return $journal_quete;
    }


    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \aquete_perso
     */
    function  getAll()
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqperso_cod  from quetes.aquete_perso order by aqperso_cod";
        $stmt = $pdo->query($req);
        while($result = $stmt->fetch())
        {
            $temp = new aquete_perso;
            $temp->charge($result["aqperso_cod"]);
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
                    $req = "select aqperso_cod  from quetes.aquete_perso where " . substr($name, 6) . " = ? order by aqperso_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array($arguments[0]),$stmt);
                    while($result = $stmt->fetch())
                    {
                        $temp = new aquete_perso;
                        $temp->charge($result["aqperso_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete_perso');
                }
                break;

            default:
                die('Unknown method.');
        }
    }
}