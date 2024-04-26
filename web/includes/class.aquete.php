<?php
/**
 * includes/class.aquete.php
 */

/**
 * Class aquete
 *
 * Gère les objets BDD de la table aquete
 */
class aquete
{
    var $aquete_cod;
    var $aquete_nom                = '';
    var $aquete_description        = '';
    var $aquete_etape_cod          = 0;
    var $aquete_actif              = 'O';
    var $aquete_date_debut;
    var $aquete_date_fin;
    var $aquete_nb_max_instance    = 1;
    var $aquete_nb_max_participant = 1;
    var $aquete_nb_max_rejouable   = 1;
    var $aquete_nb_max_quete;
    var $aquete_max_delai;
    var $aquete_nom_alias          = '';
    var $aquete_journal_archive    = 'O';
    var $aquete_interaction        = 'N';
    var $aquete_pos_etage;
    var $aquete_limite_triplette   = 'N';

    function __construct()
    {
        $this->aquete_date_debut = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de aquete
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     * @global bdd_mysql $pdo
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from quetes.aquete where aquete_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->aquete_cod                = $result['aquete_cod'];
        $this->aquete_nom_alias          = $result['aquete_nom_alias'];
        $this->aquete_nom                = $result['aquete_nom'];
        $this->aquete_description        = $result['aquete_description'];
        $this->aquete_etape_cod          = $result['aquete_etape_cod'];
        $this->aquete_actif              = $result['aquete_actif'];
        $this->aquete_journal_archive    = $result['aquete_journal_archive'];
        $this->aquete_interaction        = $result['aquete_interaction'];
        $this->aquete_pos_etage          = $result['aquete_pos_etage'];
        $this->aquete_date_debut         = $result['aquete_date_debut'];
        $this->aquete_date_fin           = $result['aquete_date_fin'];
        $this->aquete_nb_max_instance    = $result['aquete_nb_max_instance'];
        $this->aquete_limite_triplette   = $result['aquete_limite_triplette'];
        $this->aquete_nb_max_participant = $result['aquete_nb_max_participant'];
        $this->aquete_nb_max_rejouable   = $result['aquete_nb_max_rejouable'];
        $this->aquete_nb_max_quete       = $result['aquete_nb_max_quete'];
        $this->aquete_max_delai          = $result['aquete_max_delai'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     * @global bdd_mysql $pdo
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into quetes.aquete 
                    (
                        aquete_nom_alias,
                        aquete_nom,
                        aquete_description,
                        aquete_etape_cod,
                        aquete_actif,
                        aquete_journal_archive,
                        aquete_interaction,
                        aquete_pos_etage,
                        aquete_date_debut,
                        aquete_date_fin,
                        aquete_nb_max_instance,
                        aquete_limite_triplette,
                        aquete_nb_max_participant,
                        aquete_nb_max_rejouable,
                        aquete_nb_max_quete,
                        aquete_max_delai
                    )
                    values
                    (
                        :aquete_nom_alias,
                        :aquete_nom,
                        :aquete_description,
                        :aquete_etape_cod,
                        :aquete_actif,
                        :aquete_journal_archive,
                        :aquete_interaction,
                        :aquete_pos_etage,
                        :aquete_date_debut,
                        :aquete_date_fin,
                        :aquete_nb_max_instance,
                        :aquete_limite_triplette,
                        :aquete_nb_max_participant,
                        :aquete_nb_max_rejouable,
                        :aquete_nb_max_quete,
                        :aquete_max_delai
                    )
                    returning aquete_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":aquete_nom_alias"          => $this->aquete_nom_alias,
                                      ":aquete_nom"                => $this->aquete_nom,
                                      ":aquete_description"        => $this->aquete_description,
                                      ":aquete_etape_cod"          => $this->aquete_etape_cod,
                                      ":aquete_actif"              => $this->aquete_actif,
                                      ":aquete_journal_archive"    => $this->aquete_journal_archive,
                                      ":aquete_interaction"        => $this->aquete_interaction,
                                      ":aquete_pos_etage"          => $this->aquete_pos_etage,
                                      ":aquete_date_debut"         => $this->aquete_date_debut,
                                      ":aquete_date_fin"           => $this->aquete_date_fin,
                                      ":aquete_nb_max_instance"    => $this->aquete_nb_max_instance,
                                      ":aquete_limite_triplette"    => $this->aquete_limite_triplette,
                                      ":aquete_nb_max_participant" => $this->aquete_nb_max_participant,
                                      ":aquete_nb_max_rejouable"   => $this->aquete_nb_max_rejouable,
                                      ":aquete_nb_max_quete"       => $this->aquete_nb_max_quete,
                                      ":aquete_max_delai"          => $this->aquete_max_delai
                                  ), $stmt);

            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        } else
        {
            $req = "update quetes.aquete
                    set
            aquete_nom_alias = :aquete_nom_alias,
            aquete_nom = :aquete_nom,
            aquete_description = :aquete_description,
            aquete_etape_cod = :aquete_etape_cod,
            aquete_actif = :aquete_actif,
            aquete_journal_archive = :aquete_journal_archive,
            aquete_interaction = :aquete_interaction,
            aquete_pos_etage = :aquete_pos_etage,
            aquete_date_debut = :aquete_date_debut,
            aquete_date_fin = :aquete_date_fin,
            aquete_nb_max_instance = :aquete_nb_max_instance,
            aquete_limite_triplette = :aquete_limite_triplette,
            aquete_nb_max_participant = :aquete_nb_max_participant,
            aquete_nb_max_rejouable = :aquete_nb_max_rejouable,
            aquete_nb_max_quete = :aquete_nb_max_quete,
            aquete_max_delai = :aquete_max_delai                     
            where aquete_cod = :aquete_cod ";

            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                                      ":aquete_cod"                => $this->aquete_cod,
                                      ":aquete_nom_alias"          => $this->aquete_nom_alias,
                                      ":aquete_nom"                => $this->aquete_nom,
                                      ":aquete_description"        => $this->aquete_description,
                                      ":aquete_etape_cod"          => $this->aquete_etape_cod,
                                      ":aquete_actif"              => $this->aquete_actif,
                                      ":aquete_journal_archive"    => $this->aquete_journal_archive,
                                      ":aquete_interaction"        => $this->aquete_interaction,
                                      ":aquete_pos_etage"          => $this->aquete_pos_etage,
                                      ":aquete_date_debut"         => $this->aquete_date_debut,
                                      ":aquete_date_fin"           => $this->aquete_date_fin,
                                      ":aquete_nb_max_instance"    => $this->aquete_nb_max_instance,
                                      ":aquete_limite_triplette"   => $this->aquete_limite_triplette,
                                      ":aquete_nb_max_participant" => $this->aquete_nb_max_participant,
                                      ":aquete_nb_max_rejouable"   => $this->aquete_nb_max_rejouable,
                                      ":aquete_nb_max_quete"       => $this->aquete_nb_max_quete,
                                      ":aquete_max_delai"          => $this->aquete_max_delai
                                  ), $stmt);
        }
    }

    //supprime une quete
    function supprime($aquete_cod=null)
    {
        $pdo    = new bddpdo;
        if ($aquete_cod==null) $aquete_cod = $this->aquete_cod;

        // supprimer les étapes de cette quete
        $req    = "select aqetape_cod from quetes.aquete_etape where aqetape_aquete_cod=:aqetape_aquete_cod  ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array("aqetape_aquete_cod" => $aquete_cod), $stmt);
        if ($result = $stmt->fetchAll())
        {
            $etape = new aquete_etape;
            foreach ($result as $aqetape)
            {
                $etape->supprime( $aqetape["aqetape_cod"] );
            }

        }

        // supprimer la quete elle même
        $req    = "delete from quetes.aquete where aquete_cod=:aquete_cod  ";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array("aquete_cod" => $aquete_cod), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }

        return true;
    }

    //Termine la quete pour tous les utilisateur l'ayant commencé
    function termine()
    {
        $pdo    = new bddpdo;

        # requperer la liste de queste perso à terminer
        $req  = "select aqperso_cod from quetes.aquete_perso where aqperso_aquete_cod=? and aqperso_actif='O' ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->aquete_cod), $stmt);

        // boucler sur la liste
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {

            $quete_perso = new aquete_perso();
            if (  $quete_perso->charge($result["aqperso_cod"]) )
            {
                $quete_perso->aqperso_quete_step++; // Step suivant pour le journal

                $perso_journal = new aquete_perso_journal();
                if ($this->aquete_journal_archive != 'O') {
                    // La quête est terminée, et l'on n'en conserve pas de journal, supprimer toutes les entrées déjà faites.
                    $perso_journal->deleteBy_aqperso_cod($quete_perso->aqperso_cod, $quete_perso->aqperso_nb_realisation);
                } else {
                    // On conserve l'archive du journal de quete
                    $perso_journal->aqpersoj_aqperso_cod = $quete_perso->aqperso_cod;
                    $perso_journal->aqpersoj_realisation = $quete_perso->aqperso_nb_realisation;
                    $perso_journal->aqpersoj_quete_step = $quete_perso->aqperso_quete_step;
                    $perso_journal->aqpersoj_texte = "L'administrateur a mis fin à cette quête!<br> ";
                    $perso_journal->aqpersoj_lu = "O";
                    $perso_journal->stocke(true);
                }


                $quete_perso->aqperso_actif = 'N';
                $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
                $quete_perso->stocke();
            }
        }

        return true;
    }

    //Comptage tous persos confondus
    function get_nb_total()
    {
        $pdo    = new bddpdo;
        $req    = "select sum(aqperso_nb_realisation) as count from quetes.aquete_perso where aqperso_aquete_cod=?  ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($this->aquete_cod), $stmt);
        $result = $stmt->fetch();
        return 1 * $result['count'];
    }


    //Comptage tous persos confondus, si une étape est passé en paramètre on compte le nombre de persos à cette étape
    function get_nb_en_cours($aqetape_cod = 0)
    {
        $pdo = new bddpdo;
        if ($aqetape_cod == 0)
        {
            $req  =
                "select count(*) as count from quetes.aquete_perso where aqperso_aquete_cod=? and aqperso_actif='O' ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($this->aquete_cod), $stmt);
        } else
        {
            $req  =
                "select count(*) as count from quetes.aquete_perso where aqperso_aquete_cod=? and aqperso_etape_cod = ? and aqperso_actif='O' ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($this->aquete_cod, $aqetape_cod), $stmt);
        }
        $result = $stmt->fetch();
        return 1 * $result['count'];
    }

    //Recherche de la liste des etapes de la quete

    /**
     * @return bool|array
     */
    function get_etapes()
    {
        $etape = new aquete_etape;
        return $etape->get_quete_etapes($this->aquete_cod);        // toutes les etapes de la quete dans l'ordre chronologique !
    }

    //Recherche de la dernière étape de la quete
    function get_derniere_etape()
    {
        $etapes = $this->get_etapes();
        if (sizeof($etapes) <= 0)
            return false;
        else
            return $etapes[sizeof($etapes) - 1]; // Retourner la dernière
    }

    //Liste des quetes qu'un perso à la possibilité de démarrer en fonction de sa position (sur une lieu, un pnj etc...)
    function get_debut_quete($perso_cod)
    {
        $quetes   = array();
        $triggers = array();

        // trouver les membre de la triplette pour les QA limitée à la réalisation par un seul membre
        $perso = new perso();
        $perso->charge($perso_cod);
        $triplette = $perso->get_triplette(false);
        if (!$triplette || $triplette == "") $triplette = "{$perso_cod}";   //cas des compte admin

        $pdo = new bddpdo;
        $req = "select aquete_cod, aqelem_misc_cod, aqelem_type, nom, aqelem_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=? -- and perso_type_perso=1
                join
                (   -- liste des démarrages de quete sur un lieu ou une position specifique
                    select aquete_cod, aquete_etape_cod, aquete_nb_max_rejouable, aquete_nb_max_instance, aquete_nb_max_quete, aqelem_misc_cod, aqelem_type, aqelem_misc_cod as pos_cod, 
                    COALESCE (lieu_nom, pos_x::text||','||pos_Y::text||' '||etage_libelle::text) as nom, aqelem_cod, aquete_interaction, aquete_limite_triplette
                    from quetes.aquete
                    join quetes.aquete_etape on aqetape_cod=aquete_etape_cod and aquete_actif='O' and (now()>=aquete_date_debut or aquete_date_debut is NULL )and (now()<=aquete_date_fin or aquete_date_fin is NULL)
                    join quetes.aquete_element on aqelem_aquete_cod=aquete_cod and aqelem_aqetape_cod=aquete_etape_cod and aqelem_type='position' and aqelem_aqperso_cod is null
                    join positions on pos_cod=aqelem_misc_cod 
                    join etage on etage_numero=pos_etage
                    left join lieu_position on lpos_pos_cod=aqelem_misc_cod   
                    left join lieu on lieu_cod=lpos_lieu_cod
                                   
                    UNION 
                    
                     -- liste des démarrages de quete sur un perso specifique
                    select aquete_cod, aquete_etape_cod, aquete_nb_max_rejouable, aquete_nb_max_instance, aquete_nb_max_quete, aqelem_misc_cod, aqelem_type, ppos_pos_cod pos_cod, perso_nom as nom, aqelem_cod, aquete_interaction, aquete_limite_triplette
                    from quetes.aquete
                    join quetes.aquete_etape on aqetape_cod=aquete_etape_cod and aquete_actif='O' and (now()>=aquete_date_debut or aquete_date_debut is NULL )and (now()<=aquete_date_fin or aquete_date_fin is NULL)
                    join quetes.aquete_element on aqelem_aquete_cod=aquete_cod and aqelem_aqetape_cod=aquete_etape_cod and aqelem_type='perso' and aqelem_aqperso_cod is null
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                
                    UNION
                
                    -- liste des démarrages de quete sur un type de lieu => transforation du type de lieu en lieu réel!
                    select aquete_cod, aquete_etape_cod, aquete_nb_max_rejouable, aquete_nb_max_instance, aquete_nb_max_quete, aqelem_misc_cod, aqelem_type, lpos_pos_cod, lieu_nom as nom, aqelem_cod, aquete_interaction, aquete_limite_triplette 
                    from quetes.aquete
                    join quetes.aquete_etape on aqetape_cod=aquete_etape_cod and aquete_actif='O' and (now()>=aquete_date_debut or aquete_date_debut is NULL )and (now()<=aquete_date_fin or aquete_date_fin is NULL)
                    join quetes.aquete_element on aqelem_aquete_cod=aquete_cod and aqelem_aqetape_cod=aquete_etape_cod and aqelem_type='lieu_type' and aqelem_aqperso_cod is null
                    join lieu on lieu_tlieu_cod=aqelem_misc_cod
                    join lieu_position on lpos_lieu_cod=lieu_cod                  
                    join positions on pos_cod=lpos_pos_cod 
                    join etage on etage_numero=pos_etage and etage_reference<=aqelem_param_num_1 and etage_reference>=aqelem_param_num_2
                ) as quete on quete.pos_cod=ppos_pos_cod
                where quetes.aq_verif_perso_condition_etape(perso_cod,aquete_etape_cod,0,0) >0
                and not exists(select 1 from quetes.aquete_perso where aquete_interaction!='O' and aqperso_perso_cod=perso_cod and aqperso_aquete_cod=aquete_cod and aqperso_actif='O')
                and not exists(select 1 from quetes.aquete_perso where aquete_interaction!='O' and aqperso_perso_cod=perso_cod and aqperso_aquete_cod=aquete_cod and aqperso_actif='N' and aquete_nb_max_rejouable>0 and aqperso_nb_realisation>=aquete_nb_max_rejouable)
                and not exists(select count(*) from quetes.aquete_perso where aquete_interaction!='O' and aqperso_aquete_cod=aquete_cod and aqperso_actif<>'N' and aquete_nb_max_instance>0 having count(*)>=aquete_nb_max_instance)
                and not exists(select count(*) from quetes.aquete_perso where aquete_interaction!='O' and aqperso_aquete_cod=aquete_cod and aquete_nb_max_quete>0 having count(*)>=aquete_nb_max_quete)
                and not exists(select 1 from quetes.aquete_perso where aquete_interaction!='O' and aquete_limite_triplette='O' and aqperso_perso_cod in ($triplette) and aqperso_aquete_cod=aquete_cod and aqperso_actif='O')
                and not exists(select 1 from quetes.aquete_perso where aquete_interaction!='O' and aquete_limite_triplette='O' and aqperso_perso_cod in ($triplette) and aqperso_aquete_cod=aquete_cod and aqperso_actif='N' and aquete_nb_max_rejouable>0 having sum(aqperso_nb_realisation)>=aquete_nb_max_rejouable)
                ";

        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod), $stmt);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $temp = new aquete;
            $temp->charge($result["aquete_cod"]);

            // Pour chaque quête trouvée, on vérifie si le perso ne l'a pas déjà prise ou en cours par un autre
            // A FAIRE !!!
            $quetes[]   = $temp;
            $triggers[] = $result;

            unset($temp);
        }
        return array("quetes" => $quetes, "triggers" => $triggers);
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @return \aquete
     * @global bdd_mysql $pdo
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select aquete_cod from quetes.aquete order by aquete_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new aquete;
            $temp->charge($result["aquete_cod"]);
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
                        "select aquete_cod from quetes.aquete where " . substr($name, 6) . " = ? order by aquete_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new aquete;
                        $temp->charge($result["aquete_cod"]);
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
                    die('Unknown variable ' . substr($name, 6) . ' in table aquete');
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

