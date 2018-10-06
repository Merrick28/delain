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
    var $aqperso_actif = 'O';               // ETAT: O Actif, S=Actif attente cloture sur Succès, E=Actif attente cloture sur Echec,  N=Non-Active.
    var $aqperso_nb_realisation = 0;
    var $aqperso_nb_termine = 0;
    var $aqperso_date_debut;
    var $aqperso_date_fin;
    var $aqperso_date_debut_etape;
    var $quete;         // définition de la quete
    var $etape;         // définition de l'étape en cours
    var $etape_modele;  // définition du modele d'étape en cours
    var $action;        // pour la réalisation des actions de la quête

    function __construct()
    {
        $this->quete = new aquete();
        $this->etape = new aquete_etape();
        $this->etape_modele = new aquete_etape_modele();
        $this->action = new aquete_action();
        $this->aqperso_date_debut = date('Y-m-d H:i:s');
    }

    //getter de l'élément quete, le charger si ce n'est pas déjà fait.
    function get_quete()
    {
        if (!$this->quete->aquete_cod)  $this->quete->charge($this->aqperso_aquete_cod);
        return  $this->quete ;
    }

    //getter de l'élément etape, le charger si ce n'est pas déjà fait.
    function get_etape()
    {
        if (!$this->etape->aqetape_cod) $this->etape->charge($this->aqperso_etape_cod);
        return  $this->etape ;
    }
    //getter de l'élément etape, le charger si ce n'est pas déjà fait.
    function get_etape_modele()
    {
        if (!$this->etape_modele->aqetapmodel_cod)
        {
            $etape = $this->get_etape();
            $this->etape_modele->charge($etape->aqetape_aqetapmodel_cod);
        }
        return  $this->etape_modele ;
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
        $this->aqperso_date_debut_etape = $result['aqperso_date_debut_etape'];
        // Raz des objets liés
        $this->quete = new aquete();
        $this->etape = new aquete_etape();
        $this->etape_modele = new aquete_etape_modele();
        $this->action = new aquete_action();

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
            aqperso_date_fin ,
            aqperso_date_debut_etape                        )
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
                        :aqperso_date_fin,
                        :aqperso_date_debut_etape                        )
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
                ":aqperso_date_debut_etape" => $this->aqperso_date_debut_etape,
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
            aqperso_date_fin = :aqperso_date_fin,
            aqperso_date_debut_etape = :aqperso_date_debut_etape                        where aqperso_cod = :aqperso_cod ";
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
                ":aqperso_date_debut_etape" => $this->aqperso_date_debut_etape,
            ),$stmt);
        }
    }

    //Comptage des quetes en cours d'un perso (toutes quetes confondues)
    function get_perso_nb_quete($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select sum(case when aqperso_actif<>'N' then 1 else 0 end) nb_encours, count(*) as nb_total from quetes.aquete_perso where aqperso_perso_cod=?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod),$stmt);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    //Comptage des quetes en cours d'un perso (toutes quetes confondues)
    function get_perso_nb_en_cours($perso_cod)
    {
        $pdo = new bddpdo;
        $req = "select count(*) as count from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_actif<>'N' ";
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
        $req = "select aqperso_cod from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_actif<>'N' order by aqperso_cod";
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


    //Retourne la liste des quetes terminée pour un perso
    function get_perso_quete_terminee($perso_cod)
    {
        $retour = array();
        $pdo = new bddpdo;
        $req = "select aqperso_cod from quetes.aquete_perso  where aqperso_perso_cod=? and (aqperso_actif='N' OR (aqperso_actif<>'N' and aqperso_nb_realisation>1)) order by aqperso_cod";
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
    function chargeBy_perso_quete($perso_cod, $aquete_cod)
    {

        $pdo = new bddpdo;
        $req = "select aqperso_cod from quetes.aquete_perso where aqperso_perso_cod=? and aqperso_aquete_cod=? order by aqperso_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($perso_cod, $aquete_cod),$stmt);
        if (!$result = $stmt->fetch())   return false;

        return $this->charge($result["aqperso_cod"]) ;
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
            if ($this->aqperso_actif<>'N') return "Vous avez déjà démarré cette quête, vous ne pouvez la recommencer sans terminer la précédente";
            if ($this->aqperso_nb_realisation>=$quete->aquete_nb_max_rejouable && 1*$quete->aquete_nb_max_rejouable>0) return "Vous avez déjà terminé cette quête {$this->aqperso_nb_realisation} fois, il ne vous est plus possible de la refaire une fois de plus";
            if ($quete->get_nb_en_cours()>=$quete->aquete_nb_max_instance && 1*$quete->aquete_nb_max_instance>0) return "Il n'est pas possible de commencer cette quête actuellement.";
            if ($quete->get_nb_total()>=$quete->aquete_nb_max_quete && 1*$quete->aquete_nb_max_quete>0) return "Cette quête est maintenant fermé.";
        }
        // On charge l'étape 1 !
        $etape = new aquete_etape();
        $etape->charge($quete->aquete_etape_cod);  // c'est la première étape, celle de démarrage

        // Vérification du choix fait à l'etape 1 pour déterminer l'étape suivante (on vérifie en amont que le joueur n'a pas essayez de tricher en injectant un mauvais code de choix)
        $element = new aquete_element();
        $element->charge( $trigger["aqelem_cod"] );
        if (($element->aqelem_aquete_cod!=$aquete_cod) && ($element->aqelem_misc_cod!=0)) return "Vous essayez commencer par l'étape d'une autre quête ?";
        if ($element->aqelem_misc_cod<0) return "Le choix pour démarrer la quête n'est pas valide!";
        // A FAIRE: --> vérifier aussi que c'est bien un element de type choix

        // On commence par purger tous les éléments qui pourraient avoir été créé par une précédente réalisation de cette quete.
        $element->deleteBy_aqperso_cod( $this->aqperso_cod );

        // on commence par le choix demandé ou a l'étape suivante!
        $seconde_etape_cod = ($element->aqelem_misc_cod==0) ? 1*$etape->aqetape_etape_cod : 1*$element->aqelem_misc_cod ;

        // La quête a peut-être déjà été réalisée dans le passé, on redémarre à zero -------------------------------------
        $this->aqperso_perso_cod = $perso_cod;      // dans le cas d'une première instanciation
        $this->aqperso_aquete_cod = $aquete_cod;    // dans le cas d'une première instanciation
        $this->aqperso_quete_step = 1;              // (Re-)Démarrage step 1 (important pour l'hydratation)
        $this->aqperso_etape_cod = $quete->aquete_etape_cod ;
        $this->aqperso_nb_realisation ++ ;
        $this->aqperso_date_debut = date('Y-m-d H:i:s');
        $this->aqperso_actif = 'O';
        $this->aqperso_date_fin = NULL;
        $this->aqperso_date_debut_etape = $this->aqperso_date_debut ;
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
        $texte = $this->hydrate();
        if ($texte != '')
        {
            // Journaliser le texte pour éviter de l'hydratiation à chaque consultation
            $perso_journal = new aquete_perso_journal();
            $perso_journal->aqpersoj_aqperso_cod = $this->aqperso_cod;
            $perso_journal->aqpersoj_realisation = $this->aqperso_nb_realisation;
            $perso_journal->aqpersoj_quete_step = 1;
            $perso_journal->aqpersoj_texte = $texte ;
            $perso_journal->stocke(true);
        }

        // L'étape 1 est considérée comme terminée!!!! Puisque qu'elle ne sert qu'a créer cette instance, on va directement à l'étape suivante
        $this->aqperso_quete_step ++ ;
        $this->aqperso_etape_cod = $seconde_etape_cod ;
        $this->stocke() ; // sauvegarder les nouvelle mise à jour

        return "" ;     // Ok!
    }


    // Une fonction privée qui met en forme une étape avec les éléments de cette étape
    function hydrate()
    {
        $element = new aquete_element;          // pour utilisation des fonctions de cette classe
        $etape = $this->get_etape();            // Pour obtenir le texte brut de l'étape

        // S'il n'y a pas de variazble on retourne directement le texte brut!
        if (!preg_match_all ('#\[(.+)\]#isU', $etape->aqetape_texte, $matches)) return $etape->aqetape_texte;

        $search = array();
        $replace = array();
        // Boucle sur les éléments à remplacer
        foreach ($matches[1] as $k => $v)
        {
            // Ne faire que les nouveaux
            if (!in_array("[$v]", $search))
            {
                $search[] = "[$v]";
                $replace[] = $this->get_elements_texte($v); // retourne un texte correpondant au paramètre de l'élément pour le perso ou celui du modele le cas echeant
            }
        }
        //echo "<pre>"; print_r($search); echo "</pre>";
        //echo "<pre>"; print_r($replace); echo "</pre>";
        //print_r($etape->aqetape_texte);
        return str_replace($search, $replace, $etape->aqetape_texte);
    }

    // Une fonction privée qui recherche des elements en fonction du paramètre
    // et en retourne un chaine de caractère.
    function get_elements_texte($param_id)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        $elements = $element ->getBy_aqperso_param_id($this, $param_id);

        if(count($elements) == 0)
        {
            return "";                  // si rien trouvé !
        }

        // Préparation d'une table pour la recherche inversée
        $element_texte = "";
        foreach ($elements as $k=> $elem)
        {
            if ( $k == 0 )
                $element_texte .= $elem->get_element_texte();
            else if ( $k <count($result)-1 )
                $element_texte .= ", ".$elem->get_element_texte();
            else
                $element_texte .= " et ".$elem->get_element_texte();
        }

        return $element_texte;
    }

    // On reprend à partir de l'étape en cours, on regarde si elle est terminée et ainsi de suite
    function run()
    {
        if ($this->aqperso_etape_cod==0) return;    // on a déjà fini la quete, il reste au joueur à la valider.

        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($this->aqperso_cod, $this->aqperso_nb_realisation);

        // Vérification du délai global
        $quete = $this->get_quete() ;
        if ((1*$quete->aquete_max_delai>0) && (date("Y-m-d H:i:s")> date("Y-m-d H:i:s", strtotime($this->aqperso_date_debut." +".$quete->aquete_max_delai." DAYS"))))
        {
            // On indique dans le journal que la quete c'est terminé sur un échec de délai!
            $perso_journal->aqpersoj_quete_step = $this->aqperso_quete_step ;
            $perso_journal->aqpersoj_texte = "Cela fait plus de <b>{$quete->aquete_max_delai} jours</b> que vous avez commencé cette quête.<br> Vous ne l'avez pas terminée dans les temps, c'est trop tard!<br> ";
            $perso_journal->stocke(true);

            $this->aqperso_actif = 'E'  ;       // Etape terminée sur un Echec.
            $this->aqperso_etape_cod = 0 ;      // Fin de quête!
            $this->aqperso_quete_step ++ ;
            $this->stocke();                    // Mettre à jours les infos
            return;
        }

        do
        {
            $loop = false;   // par défaut on sort, sauf si une etape est validée, alors il faudra continuer
            $this->etape->charge($this->aqperso_etape_cod);
            $this->etape_modele->charge($this->etape->aqetape_aqetapmodel_cod);

            // On charge le 1er élements de l'étape (pour vérifier si c'est un élément delai, et vérifier si le délai n'est pas ecoulé)
            $element = new aquete_element;
            $elements = $element->getBy_etape_param_id($this->etape->aqetape_cod, 1) ;

            // Si c'est un nouveau step, il faut en générer ses éléments, ils seront utilisés par les actions
            // Sauf les éléments de type choix qui ne sont pas géré par les actions
            if ( !in_array($this->etape_modele->aqetapmodel_tag, array("#CHOIX")) )
            {
                $element->setInstance_perso_step($this) ;
            }

            // La première fois que l'on arrive à l'étape, on le note dans le journal
            if (    ( $this->etape->aqetape_texte != "" )
                 && ( $perso_journal->aqpersoj_quete_step != $this->aqperso_quete_step )
                 && (  !in_array($this->etape_modele->aqetapmodel_tag, array("#CHOIX")) )
               )
            {
                $perso_journal->aqpersoj_quete_step = $this->aqperso_quete_step ;
                $perso_journal->aqpersoj_texte =  $this->hydrate() ;
                $perso_journal->stocke(true);       // Nouvelle page !
            }

            //---------------------------------------------------------------------------------
            // ---------------- Le gros moteur de traitment des actions -----------------------
            $status_etape = 0 ;     // 0=> on est bloqué en attente, 1 => ok etape suivante (vers $next_etape_cod), -1=> Etape Fin OK, -2 => Etape KO
            $next_etape_cod = 1*$this->etape->aqetape_etape_cod ;
            $model_tag = $this->etape_modele->aqetapmodel_tag ;

            // Vérification du délai d'étape, seuelement pour certaine etape, ce délai s'il existe est TOUJOURS le 1er paramètre--------------
            if (count($elements)==1 && $elements[0]->aqelem_type == 'delai' && $elements[0]->aqelem_param_num_1 > 0)
            {
                if ((date("Y-m-d H:i:s")> date("Y-m-d H:i:s", strtotime($this->aqperso_date_debut_etape." +".$elements[0]->aqelem_param_num_1." DAYS"))))
                {
                    // On indique dans le journal que la quete c'est terminé sur un échec de délai! (si c'est pas géré par une autre étape)
                    if (1*$elements[0]->aqelem_misc_cod <0)
                    {
                        $this->aqperso_quete_step ++ ; // L'étape était déjà commencée, il y a déjà une entrée dans le journal pour celle-ci on passe un step !
                        $perso_journal->aqpersoj_quete_step = $this->aqperso_quete_step ;
                        $perso_journal->aqpersoj_texte = "Cela fait plus de <b>{$elements[0]->aqelem_param_num_1} jours</b> que vous avez commencé cette étape.<br> Vous ne l'avez pas terminée dans les temps, c'est trop tard!<br> ";
                        $perso_journal->stocke(true);
                    }
                    // le "timeout d'étape" se comporte comme une étape du type SAUT, le aqelem_misc_cod contient la prochaine étape !
                    $model_tag = "#SAUT" ;
                }
            }

            switch($model_tag)
            {
                case "#SAUT":
                    // cette etape sert à faire un saut vers une autre, elle est toujours réussie.
                    // Récupérer le n° d'Etape suivante:
                    $etape_cod = 1*$elements[0]->aqelem_misc_cod ;

                    if ($etape_cod<0)
                    {
                        $this->aqperso_actif = ($etape_cod== -2) ? 'S' : 'E' ;   // Etape terminée avec Succes ou sur une Echec.
                        $next_etape_cod = 0 ;                          // Fin de quête!
                    }
                    else if ($etape_cod==0)
                    {
                        // Stupide, on a mit un saut d'étape pour sauter à l'étape suivante ! (peut-être une étape automatiquement réussi en cas de délai trop long :-) )
                        $next_etape_cod = 1*$this->etape->aqetape_etape_cod ;     // saut à l'étape suivante comme etape du type TEXTE
                    }
                    else
                    {
                        $next_etape_cod = $etape_cod ;
                    }

                    $status_etape = 1;      // 1 => ok etape suivante,
                break;

                case "#END #OK":
                case "#END #KO":
                    // cette etape sert à mettre fin à la quête avec ou sans succès.
                    $status_etape = ($model_tag == '#END #KO') ? -2 : -1 ;   // Etape terminée avec Succes ou sur une Echec.
                break;

                case "#TEXTE":
                    // cette etape sert à mettre du contexte dans le journal, l'ètape est autovalidé et n'a pas de paramètre.
                    $status_etape = 1;      // 1 => ok etape suivante,
                    break;

                case "#GAIN #PX":
                    // On distribution PO et PX
                    $this->action->gain_po_px($this);
                    $status_etape = 1;      // 1 => ok etape suivante,
                break;

                case "#MOVE #PERSO":
                    // Le joueur doit rejoindre un perso
                    if ($this->action->move_perso($this))
                    {
                        // Le perso est à l'endroit attendu
                        $status_etape = 1;      // 1 => ok etape suivante,
                    }
                break;
            }

            //------- traitement du status d'étape------------------------------
            if ($status_etape == 1)
            {
                $this->aqperso_etape_cod = $next_etape_cod ;
                $this->aqperso_quete_step ++ ;
                $this->aqperso_date_debut_etape = date('Y-m-d H:i:s');  // Début de la nouvelle étape !
                $loop = true ; // Etape terminé, on boucle à l'étape suivante
            }
            else if ($status_etape == -1)
            {
                $this->aqperso_actif = 'S' ;        // Etape terminée avec Succes ou sur une Echec.
                $this->aqperso_etape_cod = 0 ;      // Fin de quête!
                $this->aqperso_quete_step ++ ;
                $loop = false ;                     // Etape terminée, on boucle ne boucle plus.
            }
            else if ($status_etape == -2)
            {
                $this->aqperso_actif = 'E' ;        // Etape terminée sur un Echec.
                $this->aqperso_etape_cod = 0 ;      // Fin de quête!
                $this->aqperso_quete_step ++ ;
                $loop = false ;                     // Etape terminée, on boucle ne boucle plus.
            }


        } while ($loop && $this->aqperso_etape_cod>0);      // Tant que les step en cours est fini et qu'il y a encore d'autres étapes..

        $this->stocke();    // Mettre à jours les infos
    }


    // retourne un texte pour l'étape courante (pour faire un choix par exemple)
    function get_texte_etape_courante()
    {
        $etape_modele = $this->get_etape_modele();
        $etape = $this->get_etape();
        switch($etape_modele->aqetapmodel_tag)
        {
            case "#CHOIX":
                return  $etape->get_texte_choix();
                break;
        }

        return "";
    }

    // Injection du choix utilisateur dans l'étape courante
    function set_choix_aventurier($aqelem_cod)
    {

        // vérifier que l'on est bien en attente à cette étape !
        $etape_modele = $this->get_etape_modele();
        $etape = $this->get_etape();
        if ($etape_modele->aqetapmodel_tag != "#CHOIX") return;    // pas de choix à cette etape!

        $element = new aquete_element();
        $element->charge($aqelem_cod);

        // le choix vient de l'utilisateur, il faut s'en méfier et le verifier
        if (($element->aqelem_aqetape_cod != $this->aqperso_etape_cod) || ($element->aqelem_aqetape_cod != $this->aqperso_etape_cod)) return;

        // Ici, tout est Ok: bon perso, bonne quete, etape qui demande un choix, alors traiter le choix

        // L'étape en cours est considérée comme terminée
        if ($element->aqelem_misc_cod<0)
        {
            // si le code est négatif, c'est une fin parce que l'admin n'a pas mis de vrai étape de fin dans sa quete
            // -1 => Quitter sur demande utilisateur
            // -2 => Quête terminée avec succès
            // -3 => Quête terminée avec échec
            $this->aqperso_actif = ($element->aqelem_misc_cod == -2) ? 'S' : 'E' ;   // Succès ou Echec
            $this->aqperso_etape_cod = 0 ;
        }
        else if ($element->aqelem_misc_cod==0)
        {
            $this->aqperso_etape_cod = 1*$etape->aqetape_etape_cod ;       // on passe à l'étape suivante
        }
        else
        {
            $this->aqperso_etape_cod = 1*$element->aqelem_misc_cod ;       // on passe à l'étape demandée !
        }
        // Il reste à générer les éléments de cette quête dédiés au perso (l'élément du type choix) !!!
        $element->aqelem_aqperso_cod = $this->aqperso_cod ;              // le nouvelle élément est dédié au perso
        $element->aqelem_quete_step = $this->aqperso_quete_step ;        // step en cours
        $element->stocke(true);                                     // stocke new va dupliquer l'élément

        // ajouter une ligne dans le journal
        $texte = $this->hydrate();
        if ($texte != '')
        {
            // Journaliser le texte pour eviter de le repréparer à chaque consultation
            $perso_journal = new aquete_perso_journal();
            $perso_journal->aqpersoj_aqperso_cod = $this->aqperso_cod;
            $perso_journal->aqpersoj_realisation = $this->aqperso_nb_realisation;
            $perso_journal->aqpersoj_quete_step = $this->aqperso_quete_step;
            $perso_journal->aqpersoj_texte = $texte ;
            $perso_journal->stocke(true);
        }

        $this->aqperso_quete_step++ ;   // Step suivant
        $this->stocke();                // Mettre à jour l'avcenement
    }

    // retourne le journal de la quete du perso jusqu'a l'étape en cours.
    function journal($realisation=-1)
    {
        $journal_quete = "" ;

        $pdo = new bddpdo;
        $perso_journal = new aquete_perso_journal() ;
        $perso_journaux =  $perso_journal->getBy_perso_realisation($this->aqperso_cod, $realisation>=0 ? $realisation : $this->aqperso_nb_realisation);

        foreach ($perso_journaux as $k => $journal) $journal_quete.=$journal->aqpersoj_texte."<br><br>";

        return $journal_quete;
    }

    //Vrai si la quete est finie
    function est_finie()
    {
        return ( $this->aqperso_etape_cod == 0 );
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