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
    var $aqetape_saut_etape_cod;

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
        $this->aqetape_saut_etape_cod = $result['aqetape_saut_etape_cod'];
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
            aqetape_etape_cod,
            aqetape_saut_etape_cod                        )
                    values
                    (
                        :aqetape_nom,
                        :aqetape_aquete_cod,
                        :aqetape_aqetapmodel_cod,
                        :aqetape_parametres,
                        :aqetape_texte ,
                        :aqetape_etape_cod ,
                        :aqetape_saut_etape_cod                        )
    returning aqetape_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetapmodel_cod" => $this->aqetape_aqetapmodel_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
                ":aqetape_etape_cod" => $this->aqetape_etape_cod,
                ":aqetape_saut_etape_cod" => $this->aqetape_saut_etape_cod,
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
            aqetape_etape_cod = :aqetape_etape_cod,
            aqetape_saut_etape_cod = :aqetape_saut_etape_cod                        where aqetape_cod = :aqetape_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":aqetape_nom" => $this->aqetape_nom,
                ":aqetape_cod" => $this->aqetape_cod,
                ":aqetape_aquete_cod" => $this->aqetape_aquete_cod,
                ":aqetape_aqetapmodel_cod" => $this->aqetape_aqetapmodel_cod,
                ":aqetape_parametres" => $this->aqetape_parametres,
                ":aqetape_texte" => $this->aqetape_texte,
                ":aqetape_etape_cod" => $this->aqetape_etape_cod,
                ":aqetape_saut_etape_cod" => $this->aqetape_saut_etape_cod,
            ),$stmt);
        }
    }

    /**
     * supprime l'enregistrement
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function duplique($aqetape_cod=null)
    {
        $pdo    = new bddpdo;
        if ($aqetape_cod==null)
        {
            $aqetape_cod = $this->aqetape_cod;
        }

        $etape = new aquete_etape();
        $etape->charge($aqetape_cod);   // l'étape à dupliquer
        $this->charge($aqetape_cod);    // Nouvelle étape
        //$this->aqetape_nom = $this->aqetape_nom . " (copie)";    // Nouvelle étape
        $this->stocke(true);      // on commence par dupliquer l'étape (pour avoir la nouvelle)!

        if ($this->aqetape_cod == $aqetape_cod) return false;   // la dupication de l'étape a échoué

        $etape->aqetape_etape_cod = $this->aqetape_cod ;    // la nouvelle étape est insérée après celle qui a été dupliquée (
        $etape->stocke();

        // Maintenant on s'attaque à la duplication des éléments.
        $element = new aquete_element;

        // On récupère tous les élément de l'étape dà dupliquer
        if ($elements = $element->getBy_aqelem_aqetape_cod($aqetape_cod) )
        {
            //echo "<pre>"; print_r($elements); echo "</pre>"; die();

            foreach ($elements as $elem)
            {
                if ((int)$elem->aqelem_aqperso_cod == 0)  // seulement les éléments de la quete de base, pas ceux instanciés pour les persos.
                {
                    // On réaffecte à l'étape nouvellement dupliquée
                    $elem->aqelem_aqetape_cod = $this->aqetape_cod ;
                    $elem->stocke( true );      // on suvegarde ce nouvel element
                }
            }
        }

        return true;
    }


    /**
     * supprime l'enregistrement
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return liste des persos qui ont skipés l'étape!
     */
    function skip_perso_en_cours($aqetape_cod=null)
    {
        $pdo    = new bddpdo;
        if ($aqetape_cod==null)
        {
            $aqetape_cod = $this->aqetape_cod;
        }
        else
        {
            $this->charge($aqetape_cod);
        }

        // liste des perso en cours sur l'étape!
        $req  = "select aqperso_cod from quetes.aquete_perso where aqperso_etape_cod = :aqperso_etape_cod and aqperso_actif='O' ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":aqperso_etape_cod" => $aqetape_cod), $stmt);

        if (!$result = $stmt->fetchAll()) return "** aucun persos trouvés **";

        $perso_list = "" ;
        foreach ($result as $aqp)
        {
            $p = new perso();
            $aqperso = new aquete_perso();
            $aqperso->charge( $aqp["aqperso_cod"] );    // Charge la quete pour le perso
            $p->charge( $aqperso->aqperso_perso_cod );    // Charge la quete pour le perso
            $aqperso->skip_en_cours( );
            $perso_list.= ", ".$p->perso_nom ."(#".$p->perso_cod.")";
        }

        if ($perso_list == "") return "*** aucun persos trouvés ***";

        return substr($perso_list,2);
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
     * definit des element de position pour une étape du model interaction
     * @global bdd_mysql $pdo
     * @param array $pos_liste => list des positions
     */
    function set_interaction_positions($pos_liste)
    {
        $pdo    = new bddpdo;

        $where = "";        // liste des elements
        $ordre = 1;
        foreach ($pos_liste as $item)
        {
            if ((int)$item != 0)
            {
                $element = new aquete_element;

                $req    = "SELECT aqelem_cod FROM quetes.aquete_element where aqelem_misc_cod=:aqelem_misc_cod and aqelem_aqetape_cod=:aqelem_aqetape_cod and aqelem_param_id=1 and aqelem_aqperso_cod is null ";
                $stmt   = $pdo->prepare($req);

                $stmt   = $pdo->execute(array(":aqelem_aqetape_cod" => $this->aqetape_cod, ":aqelem_misc_cod" => (int)$item ), $stmt);
                if ($result = $stmt->fetch())
                {
                   // element déjà existant, mettre à jour
                    $element->charge($result["aqelem_cod"]);
                    $element->aqelem_param_ordre = $ordre ;
                }
                else
                {
                    // inserer nouvel element
                    $element->aqelem_aquete_cod = $this->aqetape_aquete_cod;
                    $element->aqelem_aqetape_cod = $this->aqetape_cod;
                    $element->aqelem_param_id = 1;
                    $element->aqelem_type = "position" ;
                    $element->aqelem_misc_cod = (int)$item ;
                    $element->aqelem_param_ordre = $ordre ;
                    $element->stocke(true);
                }
                $ordre ++;
                $where .= $element->aqelem_cod."," ;        // ajouter cet element à la list
            }
        }
        $where = " and aqelem_cod not in (". substr($where, 0, -1) .") ";

        // supprimer tous les elements (ayant le même parametres) qui ne sont pas dans la liste.
        $req    = "DELETE from quetes.aquete_element where aqelem_aqetape_cod=:aqelem_aqetape_cod and aqelem_param_id=1 and aqelem_aqperso_cod is null $where ";

        $stmt   = $pdo->prepare($req);
        $pdo->execute(array(":aqelem_aqetape_cod" => $this->aqetape_cod), $stmt);

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


    /**
     * Fonction pour mettre en forme le texte d'une étape alors que l'étape n'a pas encore démarrée.
     * Les éléments de l'étapes n'ont pas encore été instanciés, on se base sur le template
     * Param1 => Element déclencheur, Param2 => Choix
     * @param $trigger_nom
     * @return string
     */
    function get_initial_texte( perso $perso, $trigger )
    {
        $trigger_nom = $trigger["nom"];

        $hydrate_texte = "" ;
        $textes = explode("[", $this->aqetape_texte);

        $hydrate_texte.= $textes[0];        // Le début de la description
        foreach ($textes as $k => $v)
        {
            if (($v!="") && ($k>0))
            {
                $params = explode("]", $v);

                // On traite le cas particulier de la première etape non instanciée, alors on on a: Param1 => Element déclencheur, Param2 => Choix
                $param_num = (int)$params[0] ;

                if (substr($params[0],0,1)=="#")
                {
                    $hydrate_texte .=  $perso->get_champ(substr($params[0],1));
                }
                else if (! is_numeric($params[0]) )
                {
                    $hydrate_texte.= "[".$params[0]."]";
                }
                else if ($param_num == 1)
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
                            $link = "/jeu_test/quete_auto.php?methode=start&quete=".$this->aqetape_aquete_cod."&choix=".$e->aqelem_cod."&trigger=".$trigger["aqelem_cod"] ;
                        $hydrate_texte .= '<br><a href="'.$link.'" style="margin:50px;">'.$e->aqelem_param_txt_1.'</a>';
                    }
                }
                $hydrate_texte.= $params[1];
            }
        }

        return $hydrate_texte ;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type choix
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_texte_choix(aquete_perso $aqperso)
    {
        $hydrate_texte = "" ;

        $element = new aquete_element();
        $elements = $element->getBy_aqperso_param_id ( $aqperso,1) ;
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
     * Fonction pour mettre en forme le texte d'une étape du type choix_etape (saisi d'un texte)
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_texte_form(aquete_perso $aqperso, $type)
    {
        $etape_modele = $aqperso->get_etape_modele();

        if ($type=="perso")
        {
            // Vérifier que le perso est bien sur la case du PNJ (utilisation de la mini étape: action->move_perso
            if ( ! $aqperso->action->move_perso($aqperso, 1) )
            {
                return "Vous êtes trop loin de votre interlocuteur pour dialoguer." ;
            }
        } else {
            // Vérifier que le perso est bien sur la case d'un lieu avec interraction
            if ( ! $aqperso->action->move_position($aqperso, 1) )
            {
                return "Vous êtes trop loin du lieu pour interagir." ;
            }
        }

        return '<form method="post" action="quete_auto.php">
        <input type="hidden" name="methode" value="dialogue">
        <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">
        <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">
        &nbsp;&nbsp;&nbsp;Vous : <input name="dialogue" type="text" size="80"><br>
        <br>&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="choix_etape" value="Valider" >
        </form>' ;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type choix_etape (saisi d'un texte)
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_equipe_form(aquete_perso $aqperso)
    {
        $etape_modele = $aqperso->get_etape_modele();

        $element = new aquete_element();
        if (!$p2 = $element->get_etape_element( $aqperso->etape, 2, "craft" )) return false ;                      // Problème lecture (blocage)
        if (!$p3 = $element->get_etape_element( $aqperso->etape, 3, "selecteur" )) return false ;                       // Problème lecture (blocage)
        if (!$p4 = $element->get_etape_element( $aqperso->etape, 4, "valeur", 0 )) return false ;                       // Problème lecture (blocage)
        if (!$p5 = $element->get_etape_element( $aqperso->etape, 5, "etape", 0)) return false ;    // Problème lecture (blocage)
        if (!$p6 = $element->get_etape_element( $aqperso->etape, 6, "etape")) return false ;    // Problème lecture (blocage)
        if (!$p7 = $element->get_etape_element( $aqperso->etape, 7, "perso", 0)) return false ;    // Problème lecture (blocage)


        $nb_equipe = $p2->aqelem_param_num_1;                 // nombre d'équipe
        $equip_mini = $p2->aqelem_param_num_2 ;     // nombre mini par equipe
        $equip_maxi = $p2->aqelem_param_num_3 ;     // nombre maxi par equipe
        $countdown = $p2->aqelem_misc_cod ;      // en heure
        $triplette = $p3->aqelem_misc_cod ;      // 0 = tout autorisé, 1 = 1 joueur par triplette
        //echo "<pre>"; print_r([$_REQUEST, $aqperso, $nb_equipe,$equip_mini,$equip_maxi,$triplette, $p7]); die();

        // 1 he minimum pour le decompte
        $countdown = $countdown <=0 ? 1 : $countdown ;

        // vérifier si le perso est dejà dans la liste => trouve son index et son couple equipe/etat
        $perso_cod = $aqperso->aqperso_perso_cod ;
        $perso = new perso();
        $perso->charge($perso_cod);

        $p_idx = -1 ;
        $p_equipe = 0 ;
        $p_etat = 0 ;
        $equipe_perso = [] ;        // vide au debut $equipe_perso[row][equipe] => [perso,etat]
        $teams_lock = false ;
        $perso_triplette = false ;
        foreach ($p7 as $k => $p)
        {
            if ($p->aqelem_misc_cod == $perso_cod)
            {
                $p_idx = $k ;
                $p_equipe = $p->aqelem_param_num_1 ;
                $p_etat = $p->aqelem_param_num_2 ;
                if ($p->aqelem_param_num_3 >= 1) $teams_lock = true;   // les équipes sont verrouillées, elles ne peuvent plus être modifiées!
            }
            if ( ($p->aqelem_misc_cod != $perso_cod) && $perso->membreTriplette($p->aqelem_misc_cod ) ) {
                $perso_triplette = true ;
            }
        }

        // En cas de validation d'un couple equipe/etat
        if ($_REQUEST["dialogue-echanger"] == "dialogue" && $_REQUEST["valider"] == "Valider les modifications")
        {
            $element = new aquete_element();
            if ($p_idx >= 0) { // ajouter le perso dans la liste
                $element->charge($p7[$p_idx]->aqelem_cod);
            } else {
                $element->aqelem_aquete_cod = $aqperso->etape->aqetape_aquete_cod;
                $element->aqelem_aqetape_cod = $aqperso->etape->aqetape_cod;
                $element->aqelem_param_id = 7;
                $element->aqelem_param_ordre = count($p7) + 1;
                $element->aqelem_type = "perso";
                $element->aqelem_misc_cod = $perso_cod;
            }
            $element->aqelem_param_num_1 = (int)$_REQUEST["aqf_equipe"];
            $element->aqelem_param_num_2 = (int)$_REQUEST["aqf_etat"];
            $p_equipe = $element->aqelem_param_num_1 ;
            $p_etat = $element->aqelem_param_num_2 ;

            $element->stocke(($p_idx == -1) ? true : false );

            // recharger p7 avec les valeurs modifiées
            if (!$p7 = $element->get_etape_element( $aqperso->etape, 7, "perso", 0)) return false ;    // Problème lecture (blocage)
            if ($p_idx == -1) $p_idx = count($p7) + 1 ; // on est maintenant dans une équipe
        }

        //echo "<pre>"; print_r([$_REQUEST, $aqperso, $nb_equipe,$equip_mini,$equip_maxi,$triplette, $p7]); die();

        // Préparer le tableau des équipe pour un afichage ligne par ligne et au passage vérifier si les equipe sont prête
        $team_member = [] ; //comptage des membre de chaque equipe
        $teams_ready = true ; // il reste encore des joueurs en attente ?
        foreach ($p7 as $k => $p)
        {
            if ($p->aqelem_param_num_1 == null) $p->aqelem_param_num_1 = 0 ;
            if ($p->aqelem_param_num_2 == null) $p->aqelem_param_num_2 = 0 ;
            if ($p->aqelem_param_num_3 == null) $p->aqelem_param_num_3 = 0 ;
            if ($p->aqelem_misc_cod > 0 ){
                // en cas d'une nouvelle ligne, remplir de vide
                $equipe_row = 0 ;
                while ( isset( $equipe_perso[$equipe_row][$p->aqelem_param_num_1]) ) $equipe_row ++ ;
                //if (count($equipe_perso) <= $equipe_row)  {

                $team = $p->aqelem_param_num_1 ;
                $equipe_perso[$equipe_row][$team] = ["perso_cod"=> $p->aqelem_misc_cod, "etat"=> $p->aqelem_param_num_2] ;
                $team_member[$team] = isset( $team_member[$team] ) ?  $team_member[$team] + 1 : 1 ;
                if ($p->aqelem_param_num_2 == 0 ) $teams_ready = false ; // encore au moins un joueur en attente
            }
        }

        //echo "<pre>"; print_r([$team_member, $equipe_perso, $nb_equipe,$equip_mini,$equip_maxi,$triplette, $p7]); die();

        $teams_count = true ; // les equipes verifie les pré-requis ?
        for ($k=0; $k<$nb_equipe; $k++) {
            if (!isset($team_member[$k])){
                $teams_count = false ; // au moins une équipe n'a pas assez ou possède trop de joueur
                break;
            }
            $m=$team_member[$k];
            if (($m<$equip_mini) || ($m>$equip_maxi && $equip_maxi!=0)) {
                $teams_count = false ; // au moins une équipe n'a pas assez ou possède trop de joueur
                break;
            }
        }

        // verouiller les joueurs et lancé le chrono dès qu'on a les tous joueurs
        if (!$teams_lock && $teams_count && $teams_ready) {
            // on vient d'atteindre le quorum des équipes, locker les joueurs!
            $element = new aquete_element();
            foreach ($p7 as $k => $p)
            {
                $element->charge($p->aqelem_cod);
                $element->aqelem_param_num_3 = 1 ;
                $element->stocke();
            }
            $teams_lock = true;
            $element->charge($p2->aqelem_cod);
            $element->aqelem_param_txt_1 = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +{$countdown} hours"));
            $p2->aqelem_param_txt_1 = $element->aqelem_param_txt_1 ;
            $element->stocke();
        }

        if (!$teams_count || !$teams_ready) {
            // on vient d'atteindre le quorum des équipes, locker les joueurs!
            $element = new aquete_element();
            $element->charge($p2->aqelem_cod);
            $element->aqelem_param_txt_1 = "";
            $p2->aqelem_param_txt_1 = $element->aqelem_param_txt_1 ;
            $element->stocke();
        }

        if ($teams_count && !$teams_ready) {
            $condition_equipe = "Remplie, en attente! <img src=\"/images/smilies/reflexion.gif\">"; // par defaut
        } else if ($teams_count && $teams_ready) {
            $condition_equipe = "Remplie, verrouillée! <img src=\"/images/smilies/bien.gif\">";
        } else {
            $condition_equipe = "Non-remplie <img src=\"/images/smilies/nanana.gif\">";
        }

        // Affichage de la forme
        $form = "";
        $form .= '<form method="post" action="quete_auto.php">
            <input type="hidden" name="methode" value="dialogue">
            <input type="hidden" name="dialogue-echanger" value="dialogue">
            <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'"> 
            <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">        ';


        $form .= '<table style="border: solid 1px #800000;"><tr>';
        $form .= '<br> Conditions pour chaque équipe:<br>';
        $form .= '&nbsp;&nbsp;&nbsp; + Minimum <b> '.$equip_mini.' joueur(s)</b><br>';
        $form .= '&nbsp;&nbsp;&nbsp; + ' .( $equip_maxi == 0  ? 'Pas de limite maximum de joueur.<br>' : 'Maximum <b>'.$equip_maxi.' joueur(s)</b><br' );
        $form .= '<br>';

        // barre des titres du tableau, et préparation du selecteur d'équipe
        $equipes = [] ;
        for ($e=0; $e<$nb_equipe; $e++){
            $equipes[$e] = "Equipe #".($e+1);
            $form .=  '<td style="width:20px; font-weight: bold">&nbsp;</td><td style="min-width:400px; font-weight: bold">Equipe #'.($e+1).'</td>';
        }
        $form .= '</tr>';

        // Afficher le contenu des equipes
        $p = new perso();
        for ($r=0; $r < count($equipe_perso); $r++ ){
            $form .= "<tr>" ;
            for ($e=0; $e<$nb_equipe; $e++){
                if (isset($equipe_perso[$r][$e])) {
                    $p->charge($equipe_perso[$r][$e]["perso_cod"]);
                    $form .=  '<td><img src="/images/smilies/'.( $equipe_perso[$r][$e]["etat"] == 1 ? "bien.gif" : "reflexion.gif").'"></td><td>'.$p->perso_nom.' (<a href="visu_desc_perso.php?visu='.$p->perso_cod.'"#>'.$p->perso_cod.'</a>)</td>';
                } else {
                    $form .=  '<td></td><td></td>';
                }
            }
            $form .= "</tr>" ;
        }


        $form .= '</table><br>';

        // selecteur d'équipe et bouton de validation !
        if ($perso_triplette && $triplette) {
            $form .= " <b style='color:#FFFFFF; background-color: #800000'>&nbsp;Des membres d'une même triplette ne sont pas autorisés dans les équipes!</b><br>";
        } else if (($p_idx == -1) && $teams_count) {
            $form .= " <b style='color:#FFFFFF; background-color: #800000'>&nbsp;Malheureusement les équipes sont faites, Il n'y a plus de place pour vous!</b><br>";
        } else if (!$teams_lock || !$teams_count || !$teams_ready) {
            $form .= "Choisissez votre équipe : ".create_selectbox("aqf_equipe", $equipes, $p_equipe)." ";
            $form .= "Etat : ".create_selectbox("aqf_etat", [ "0"=>"Attendre", "1"=>"Prêt" ], $p_etat)."<br><br>";

            // footer (boutton annulaton, validation)
            $form.= '<input class="test" type="submit" name="valider" value="Valider les modifications">&nbsp;&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="cancel" value="Quitter"></form>' ;
        } else {
            $form .= "Lancement des joueurs le: <b style='color:#FFFFFF; background-color: #800000'>&nbsp;&nbsp;".date( "d/m/Y à H:i:s",  strtotime($p2->aqelem_param_txt_1))."&nbsp;&nbsp;</b><br>";
        }

        $form .= '<br><u><strong>CONDITION EQUIPE</strong></u>: '.$condition_equipe.'<br>';

        $form .= '<br><u><strong>ATTENTION</strong></u>:<br>';
        $form .= ' * Dès que les conditions d’équipes seront valides avec tous les joueurs prêts, il ne sera plus possible de modifier les équipes.<br>';
        $form .= ' * Aussi il est conseillé de <b>laisser 1 joueur « en attente »</b>, le temps que tous les autres joueurs choissisent leur équipe.<br>';
        $form .= ' * S’il y a plus de joueur dans une équipe que le maximum autorisé, certain devront se désister et  <b>« quitter»</b>.<br>';

        return $form;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type choix_etape (validation d'une dépense de PA)
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_texte_choix_pa(aquete_perso $aqperso)
    {
        $hydrate_texte = "" ;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                              // Problème lecture des paramètres

        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        if ($perso->perso_pa <  $p1->aqelem_param_num_1)
        {
            $hydrate_texte .= "Vous n'avez pas assez de PA pour continuer!";
        }
        else
        {
            $link = "/jeu_test/quete_auto.php?methode=dialogue&quete=".$this->aqetape_aquete_cod."&dialogue=O" ;
            $hydrate_texte .= '<br><a href="'.$link.'" style="margin:25px;">OUI</a>';
            $link = "/jeu_test/quete_auto.php?methode=dialogue&quete=".$this->aqetape_aquete_cod."&dialogue=N" ;
            $hydrate_texte .= '<a href="'.$link.'" style="margin:25px;">NON</a>';
        }


        return $hydrate_texte ;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type choix_etape (saisi d'un code)
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_texte_choix_code(aquete_perso $aqperso)
    {
        $hydrate_texte = "" ;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'selecteur')) return false ;                              // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'texte')) return false ;                              // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                              // Problème lecture des paramètres

        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        if ($perso->perso_pa <  $p3->aqelem_param_num_1)
        {
            $hydrate_texte .= "Vous n'avez pas assez de PA pour essayer!";
        }
        else
        {

            $hydrate_texte .='<form method="post" action="quete_auto.php">
                                <input type="hidden" name="methode" value="dialogue">
                                <input type="hidden" id="cryptex_value" name="dialogue" value="">
                                <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">';

            $cryptex = new cryptex($p1->aqelem_misc_cod, $p2->aqelem_param_txt_1);
            $hydrate_texte .= $cryptex->display();
            $hydrate_texte .="<div>";

            $hydrate_texte .="</div>";
            $hydrate_texte .='<br><input style="margin-left:20px; margin-top:20px;" onclick="$(\'#cryptex_value\').val(getCryptexValue());" class="test" type="submit" name="choix_etape" value="Valider'.( $p3->aqelem_param_num_1 > 0 ? " ({$p3->aqelem_param_num_1} PA)" : "").'"">';
            if ($p3->aqelem_param_num_1 > 0)
            {
                $hydrate_texte .= '<input style="margin-left:20px;" class="test" type="submit" name="choix_etape" value="Ignorer">';
            }
            $hydrate_texte .='</form>';
        }


        return $hydrate_texte ;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type echange_objet: '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:echange|0%0]'
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_echange_objet_form(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;
        $form = "" ;

        $etape_modele = $aqperso->get_etape_modele();

        // Vérifier que le perso est bien sur la case du PNJ (utilisation de la mini étape: action->move_perso
        if ( ! $aqperso->action->move_perso($aqperso) )
        {
            return "Pour faire du troc, rendez-vous en un lieu proposant ce service." ;
        }

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                              // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;                              // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                              // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'echange', 0)) return false ;              // Problème lecture des paramètres

        if ( count($p6) == 0 )
        {
            return "Il n'y a plus rien à troquer ici." ;
        }


        //Préparer la liste des éléments dispo à l'échange.-------------------
        $p6_matos = array();
        $p6_couts = array();
        $i = -1 ;
        foreach ($p6 as $k => $elem)
        {
            if ($elem->aqelem_misc_cod!=0)
            {
                $i = $k ;                       // On cale l'id sur l'ordre de lélément à vendre
                $p6_matos[$i] = $elem ;         // partie gauche
                $p6_couts[$i] = array();        // et droite
                $p6_couts[$i][] = $elem ;
            }
            else if ($i != -1)
            {
                $p6_couts[$i][] = $elem ;       // juste un cout supplémentaire
            }

        }


        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        // inventaire du perso
        $inventaire = [] ;
        $trocs_en_stock = [] ;
        $trocs = [] ;
        $trocs_matos = [] ;
        $trocs_bzf = 0 ;
        $req = "select obj_gobj_cod, count(*) as count  from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? group by obj_gobj_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod), $stmt);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $inventaire[$result["obj_gobj_cod"]] = (int)$result["count"] ;
            $trocs_en_stock[$result["obj_gobj_cod"]] = (int)$result["count"] ;           // pour controle
        }

        $nbtrocs = 0 ;
        $nbitem = 0 ;
        $maxitem = 0 ;
        $enstock = true ;
        $bourse = $perso->perso_po ;
        $selected_item = "" ;

        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue") return "Vous avez décidé de ne rien acheter.";

        if (isset($_REQUEST["dialogue-echanger"]))
        {
            // le joueur a valider, on vérifie qu'il a les objets nécéssaires
            foreach ($p6_matos as $k => $elem)
            {
                if (isset($_REQUEST["echange-{$k}"]) && (int)$_REQUEST["echange-{$k}"]>0)
                {
                    $nbtrocs ++ ;
                    $nb = (int)$_REQUEST["echange-{$k}"] ;
                    $maxitem = MAX($maxitem, $nb) ;
                    $nbitem += $nb  ;
                    $selected_item.='<input type="hidden" name="echange-'.$k.'" value="'.$_REQUEST["echange-{$k}"].'">';
                    $trocs_matos[$elem->aqelem_misc_cod] = (!isset($trocs_matos[$elem->aqelem_misc_cod])) ? $nb * (int)$elem->aqelem_param_num_1 : $trocs_matos[$elem->aqelem_misc_cod] + $nb * (int)$elem->aqelem_param_num_1 ;

                    // Ici on bloucle sur les lignes de cout (car il peut y en avoir plusieurs)
                    foreach ($p6_couts[$k] as $kk => $e)
                    {
                        // check brouzouf
                        $bourse = $bourse -  $nb * (int)$e->aqelem_param_txt_1;
                        $trocs_bzf = $trocs_bzf + $nb * (int)$e->aqelem_param_txt_1;
                        if ($bourse < 0)
                        {
                            $enstock = false;
                        }

                        if ((!isset($trocs_en_stock[$e->aqelem_param_num_2]) || ($trocs_en_stock[$e->aqelem_param_num_2] <  $nb * (int)$e->aqelem_param_num_3)) && ((int)$e->aqelem_param_num_2 > 0 && (int)$e->aqelem_param_num_3 > 0))
                        {
                            $enstock = false;
                        }
                        else if ((int)$e->aqelem_param_num_2 > 0 &&  (int)$e->aqelem_param_num_3 > 0)
                        {
                            $trocs[$e->aqelem_param_num_2] = (!isset($trocs[$e->aqelem_param_num_2])) ?  $nb * (int)$e->aqelem_param_num_3 : $trocs[$e->aqelem_param_num_2] +  $nb * (int)$e->aqelem_param_num_3;
                            $trocs_en_stock[$e->aqelem_param_num_2] = $trocs_en_stock[$e->aqelem_param_num_2] -  $nb * (int)$e->aqelem_param_num_3;
                        }
                    }
                }
            }

            if (!$enstock)
            {
                $form .= "Vous n'avez <strong>pas les objets ou les brouzoufs</strong> nécéssaires pour cette transaction, veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs>$p3->aqelem_param_num_1 && $p3->aqelem_param_num_1>0)
            {
                $enstock = false; // forcer la resaisie
                $form .= "Vous n'avez le droit qu'à <strong>{$p3->aqelem_param_num_1} échange(s)</strong>, vous tenter d'en faire <strong>{$nbtrocs}</strong>, veuillez ré-essayer:<br><br>";
            }
            else if ($maxitem>$p4->aqelem_param_num_1 && $p4->aqelem_param_num_1>0)
            {
                $enstock = false; // forcer la resaisie
                $form .= "Vous n'avez le droit qu'à <strong>{$p4->aqelem_param_num_1} objet(s) par échange(s)</strong>, Il y a un échange à <strong>{$maxitem}</strong>, veuillez ré-essayer:<br><br>";
            }
            else if ($nbitem>$p5->aqelem_param_num_1 && $p5->aqelem_param_num_1>0)
            {
                $enstock = false; // forcer la resaisie
                $form .= "Vous n'avez le droit qu'à <strong>{$p5->aqelem_param_num_1} objet(s) pour toute la transaction</strong>, Il y a au total <strong>{$nbitem}</strong> objet(s), veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs==0)
            {
                $enstock = false; // forcer la resaisie
                $form .= "Vous n'avez rien sélectionné, veuillez ré-essayer:<br><br>";
            }
        }

        // panneau de transaction
        if (!$enstock || $nbtrocs==0 || isset($_REQUEST["cancel"]))
        {
            // header de la forme
            $form .= '<form method="post" action="quete_auto.php">
            <input type="hidden" name="methode" value="dialogue">
            <input type="hidden" name="dialogue-echanger" value="dialogue">
            <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'"> 
            <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">            
            <table style="border: solid 1px #800000;"><tr><td style="width:20px; font-weight: bold">Qté</td><td style="min-width:400px; font-weight: bold">Objet à acquérir</td><td style="min-width:400px; font-weight: bold">Prix</td><td style="min-width:400px; font-weight: bold"></td></tr>';

            foreach ($p6_matos as $k => $elem)
            {
                $objet = new objet_generique();
                $objet->charge($elem->aqelem_misc_cod);

                $form_block = "" ;
                $first_col = true ;
                $rowstock = true;
                // Ici on bloucle sur les lignes de cout (car il peut y en avoir plusieurs)
                foreach ($p6_couts[$k] as $kk => $e)
                {
                    $prix = new objet_generique();
                    $prix->charge($e->aqelem_param_num_2);
                    $bzf = 1 * $e->aqelem_param_txt_1;

                    $enstock = true;
                    if ((!isset($inventaire[$e->aqelem_param_num_2]) || ($inventaire[$e->aqelem_param_num_2] < (int)$e->aqelem_param_num_3)) && ((int)$e->aqelem_param_num_3 > 0)) $enstock = false;
                    if ($perso->perso_po < (int)$e->aqelem_param_txt_1) $enstock = false;
                    if (!$enstock) $rowstock = false;

                    if ($first_col)
                    {
                        $first_col = false ;
                    }
                    else
                    {
                        // Ligne de cout additionnel, les lignes sont regroupées pour les 2 colonnes
                        $form_block .= '<tr style="color:#800000;  font-style: italic;">';
                    }

                    $form_block .= '<td style="border-top: inherit;">';
                    if ($bzf > 0) $form_block .= '&nbsp;' . $e->aqelem_param_txt_1 . '&nbsp;Bzf';
                    if (($e->aqelem_param_num_3 > 0) && (1 * $e->aqelem_param_num_2 > 0)) $form_block .= ($bzf > 0 ? '&nbsp;+' : '').'&nbsp;' . $e->aqelem_param_num_3 . ' x ' . $prix->gobj_nom;
                    $form_block .= '</td>';
                    if (($enstock) && (1 * $e->aqelem_param_num_2 > 0))
                        $form_block .= '<td style="border-top: inherit; color:darkgreen;">' . $inventaire[$e->aqelem_param_num_2] . ' dans l\'inventaire</td>';
                    else if (!$enstock)
                        $form_block .= '<td style="border-top: inherit; color:red;">' . $inventaire[$e->aqelem_param_num_2] . ' dans l\'inventaire</td>';
                        //$form_block .= '<td style="border-top: inherit;">Vous n\'avez pas les objets/Bzfs necessaires</td>';
                    else
                        $form_block .= '<td style="border-top: inherit;">&nbsp;</td>';
                    $form_block .= '</tr>';
                }

                // Mettre la première colone (maintenant que l'on sait si on a en stock tous les éléments)
                $form.='<tr style="color:#800000;  font-style: italic; border-top: solid 1px #800000;">
                                  <td style="border-top: inherit;" rowspan="'.count($p6_couts[$k]).'"><input ' . ($rowstock ? ((isset($_REQUEST["echange-{$k}"]) && (int)$_REQUEST["echange-{$k}"] >0) ? ' value='.$_REQUEST["echange-{$k}"] : '') : 'disabled ') . ' name="echange-' . $k . '" type="text" size="1" style="text-align: center;"></td>
                                  <td style="border-top: inherit;" rowspan="'.count($p6_couts[$k]).'">&nbsp;' . $elem->aqelem_param_num_1 . ' x ' . $objet->gobj_nom . '</td>';
                $form .= $form_block ;

            }
            // footer
            $form.= '</table><br><input class="test" type="submit" name="valider" value="Valider la transaction">&nbsp;&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="cancel" value="Ne rien acheter"></form>' ;
            $form .= '<br><br>Vous disposez de : <strong>'.$perso->perso_po.' Bzf</strong><br>';
            $form .= '<u><strong>ATTENTION</strong></u>:<br>';
            if ($p3->aqelem_param_num_1>0)
            {
                $form .= ' * Vous ne pouvez choisir que <strong>'.$p3->aqelem_param_num_1.'</strong> ligne(s) d\'échange(s) au maximum.<br>';
            }
            if ($p4->aqelem_param_num_1>0)
            {
                $form .= ' * Il n\'est possible de sélectionner que <strong>'.$p4->aqelem_param_num_1.'</strong> objet(s) au maximum par ligne(s) d\'échange.<br>';
            }
            if ($p5->aqelem_param_num_1>0)
            {
                $form .= ' * Au total, vous ne pouvez troquer que <strong>'.$p5->aqelem_param_num_1.'</strong> objet(s) au maximum.<br>';
            }

            $form .= ' * Vous quitterez cette étape en Validant ou Abandonnant cet échange et ne pourrez y revenir que si la quête le stipule.';
        }
        else
        {
            //print_r($_REQUEST); die();

            // Bilan de la transaction
            if ( $_REQUEST["dialogue-echanger"] == "dialogue-validation" )
            {
                $form.= "Vous réalisez l'échange suivant: ";
            }
            else
            {
                $form.= "Vous allez acquérir ";
            }

            // D'abord le matos acheté
            $k = 0 ;
            $troc_phrase = "" ;
            foreach ($trocs_matos as $obj => $nbobj)
            {
                $k++;
                $objet = new objet_generique();
                $objet->charge($obj);
                if ($k==count($trocs_matos) && $k>1) $troc_phrase .= ' et '; else if ($k>1) $troc_phrase .= ', ';
                $troc_phrase .= $nbobj.' x <strong>'.$objet->gobj_nom.'</strong>';
            }

            // ensuite le cout en bz et objet
            $k = 0 ;
            if ($trocs_bzf>0) $troc_phrase.= " pour {$trocs_bzf} Bzf";
            if (count($trocs)>0)  $troc_phrase.= (($trocs_bzf>0) ? " et " : "")." en échange de ";
            foreach ($trocs as $obj => $nbobj)
            {
                $k++;
                $objet = new objet_generique();
                $objet->charge($obj);
                if ($k==count($trocs) && $k>1) $troc_phrase .= ' et '; else if ($k>1) $troc_phrase .= ', ';
                $troc_phrase .= $nbobj.' x <strong>'.$objet->gobj_nom.'</strong>';
            }
            $form.= $troc_phrase."<br>" ;

            if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" )
            {
                // proposer une validation
                $form .= '<form method="post" action="quete_auto.php">
                <input type="hidden" name="methode" value="dialogue">
                <input type="hidden" name="dialogue-echanger" value="dialogue-validation">
                <input type="hidden" name="troc-phrase" value="'.htmlentities($troc_phrase).'">
                <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">                
                <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">'.$selected_item;
                $form.= '<input class="test" type="submit" value="Valider">&nbsp;&nbsp;&nbsp;&nbsp;<input style="text-align: center;" class="test" type="submit" name="cancel" value="Revoir la liste"></form>' ;
                $form .= '<br><strong>RAPPEL</strong>: Vous quitterez cette étape en Validant cet échange et ne pourrez y revenir que si la quête le stipule.';
            }

        }

        return $form ;
    }

    /**
     * Fonction pour mettre en forme le texte d'une étape du type reparer_objet: '[[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1]'
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_reparer_objet_form(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;
        $form = "" ;

        $etape_modele = $aqperso->get_etape_modele();

        // Vérifier que le perso est bien sur la case du PNJ (utilisation de la mini étape: action->move_perso
        if ( ! $aqperso->action->move_perso($aqperso) )
        {
            return "Pour faire réparer un objet, rendez-vous en sur un lieu proposant ce service." ;
        }

        // si le perso souhaite ne rien faire!
        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue") return "Vous avez décidé de ne rien réparer.";

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'type_objet', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'objet_generique', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'valeur')) return false ;                             // Nombre d'objet a reparer au max
        $tarif = $p5->aqelem_param_num_1;

        // préparer p6 avec la liste des objet réparable
        $req = "select obj_cod, obj_nom, tobj_libelle, obj_etat 
                      from perso_objets 
                      join objets on obj_cod=perobj_obj_cod 
                      join objet_generique on gobj_cod=obj_gobj_cod 
                      join type_objet on tobj_cod=gobj_tobj_cod 
                      where perobj_perso_cod=:perso_cod and obj_etat<100 ";

        // Filter sur le type de matos pris en charge par le PNJ
        $liste_p3 = "";
        foreach ($p3 as $k => $elem)
        {
            if ($elem->aqelem_misc_cod!=0)
            {
                $liste_p3 .= ",".$elem->aqelem_misc_cod ;
            }
        }
        // Filter sur le matos pris en charge par le PNJ
        $liste_p4 = "";
        foreach ($p4 as $k => $elem)
        {
            if ($elem->aqelem_misc_cod!=0)
            {
                $liste_p4 .= ",".$elem->aqelem_misc_cod ;
            }
        }

        // filter en fonction du type d'objet ou du générique
        if (($liste_p3 != "") && ($liste_p4 == ""))
        {
            $req .= " and gobj_tobj_cod in (".substr($liste_p3, 1).")";
        }
        else if (($liste_p3 == "") && ($liste_p4 != ""))
        {
            $req .= " and obj_gobj_cod in (".substr($liste_p4, 1).")";
        }
        else if (($liste_p3 != "") && ($liste_p4 != ""))
        {
            $req .= " and ( gobj_tobj_cod in (".substr($liste_p3, 1).") or obj_gobj_cod in (".substr($liste_p4, 1).") )";
        }
        else
        {
            $req .= " and false";       // ni objet generique ni type d'objet ? erreur QA
        }

        $req .= "order by obj_nom";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);
        if (!$p7 = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            return "Je ne vois rien que vous pouvez réparer ici!!!" ;
        }

        if ( count($p7) == 0 )
        {
            return "Je ne vois rien que vous pouvez réparer ici." ;
        }

        // charger le perso du joueru
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);


        $faire_reparation = false ;
        $nbtrocs = 0 ;
        $trocs_bzf = 0 ;
        $trocs_matos = "" ;
        $selected_item = "" ;
        $bourse = $perso->perso_po ;
        if (isset($_REQUEST["dialogue-echanger"]))
        {
            // le joueur a valider, on vérifie qu'il a les objets nécéssaires
            foreach ($p7 as $k => $objet)
            {
                if (isset($_REQUEST["echange-{$objet["obj_cod"]}"]))
                {
                    $nbtrocs ++ ;
                    $trocs_bzf = $trocs_bzf + ((100 - $objet["obj_etat"]) * $tarif);
                    $trocs_matos .= "<b>" . $objet["obj_nom"] . '</b> ( <i>' .  $objet["tobj_libelle"] . " </i>), ";
                    $bourse = $bourse - ((100 - $objet["obj_etat"]) * $tarif);
                    $selected_item.='<input type="hidden" name="echange-'.$objet["obj_cod"].'" value="'.$_REQUEST["echange-{$objet["obj_cod"]}"].'">';
                }
            }

            if ($bourse<0)
            {
                $form .= "Vous n'avez <strong>les brouzoufs</strong> nécessaires pour ses réparations, veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs == 0)
            {
                $form .= "Vous n'avez rien sélectionné, veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs>$p6->aqelem_param_num_1 && $p6->aqelem_param_num_1>0)
            {
                $form .= "Vous n'avez le droit qu'à <strong>{$p6->aqelem_param_num_1} réparation(s)</strong>, vous tenter d'en faire <strong>{$nbtrocs}</strong>, veuillez ré-essayer:<br><br>";
            }
            else
            {
                $faire_reparation = true ;
            }
        }

        // panneau de transaction
        if (!$faire_reparation || isset($_REQUEST["cancel"]))
        {
            // header de la forme
            $form .= '<form method="post" action="quete_auto.php">
            <input type="hidden" name="methode" value="dialogue">
            <input type="hidden" name="dialogue-echanger" value="dialogue">
            <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'"> 
            <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">            
            <table style="border: solid 1px #800000;"><tr><td style="width:20px; font-weight: bold">&nbsp;</td><td style="min-width:400px; font-weight: bold">Objet à réparer</td><td style="min-width:400px; font-weight: bold">Etat</td><td style="min-width:400px; font-weight: bold">Prix de la réparation</td></tr>';

            foreach ($p7 as $k => $objet)
            {
                // Mettre la première colone (maintenant que l'on sait si on a en stock tous les éléments)
                $form.='<tr style="color:#800000;  font-style: italic; border-top: solid 1px #800000;">
                              <td style="border-top: inherit;"><input name="echange-' . $objet["obj_cod"] . '" type="checkbox" style="text-align: center;"></td>
                              <td style="border-top: inherit;">&nbsp;' . $objet["obj_nom"] . ' ( <i>' .  $objet["tobj_libelle"] . '</i> )</td>
                              <td style="border-top: inherit;">&nbsp;' .  $this->get_etat($objet["obj_etat"]) . '</td>
                              <td style="border-top: inherit;">&nbsp;' . ((100 - $objet["obj_etat"])* $tarif) . ' Bzf</td>
                        </tr>';
            }
            // footer
            $form.= '</table><br><input class="test" type="submit" name="valider" value="Valider les réparations">&nbsp;&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="cancel" value="Ne rien réparer"></form>' ;
            $form .= '<br><br>Vous disposez de : <strong>'.$perso->perso_po.' Bzf</strong><br>';
            $form .= '<u><strong>ATTENTION</strong></u>:<br>';
            if ($p6->aqelem_param_num_1>0)
            {
                $form .= ' * Vous ne pouvez choisir que <strong>'.$p6->aqelem_param_num_1.'</strong> ligne(s) de réparation(s) au maximum.<br>';
            }
            $form .= ' * Vous quitterez cette étape en Validant ou Abandonnant cet réparation et ne pourrez y revenir que si la quête le stipule.';
        }
        else
        {

            // Bilan de la transaction
            $troc_phrase = "" ;
            if ( $_REQUEST["dialogue-echanger"] == "dialogue-validation" )
            {
                $form.= "Vous réparer les objets suivants:<br> " ;
            }
            else
            {
                $form.= "Vous souhaitez réparer les objets suivants ";
            }

            $troc_phrase .= $trocs_matos ;
            if ($trocs_bzf>0) $troc_phrase.= " pour {$trocs_bzf} Bzf";

            $form.=$troc_phrase."<br>" ;

            if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" )
            {
                // proposer une validation
                $form .= '<form method="post" action="quete_auto.php">
                <input type="hidden" name="methode" value="dialogue">
                <input type="hidden" name="dialogue-echanger" value="dialogue-validation">
                <input type="hidden" name="troc-phrase" value="'.htmlentities($troc_phrase).'">
                <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">                
                <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">'.$selected_item;
                $form.= '<input class="test" type="submit" value="Valider">&nbsp;&nbsp;&nbsp;&nbsp;<input style="text-align: center;" class="test" type="submit" name="cancel" value="Revoir la liste"></form>' ;
                $form .= '<br><strong>RAPPEL</strong>: Vous quitterez cette étape en Validant cet échange et ne pourrez y revenir que si la quête le stipule.';
            }

        }

        return $form ;
    }


    /**
     * Fonction pour mettre en forme le texte d'une étape du type recharger_objet: '[[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1]'
     * @param aquete_perso $aqperso
     * @return mixed|string
     */
    function get_recharger_objet_form(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;
        $form = "" ;

        $etape_modele = $aqperso->get_etape_modele();

        // Vérifier que le perso est bien sur la case du PNJ (utilisation de la mini étape: action->move_perso
        if ( ! $aqperso->action->move_perso($aqperso) )
        {
            return "Pour faire un rechargement d'objet un objet, rendez-vous en sur un lieu proposant ce service." ;
        }

        // si le perso souhaite ne rien faire!
        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue") return "Vous avez décidé de ne rien recharger.";

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'type_objet', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'objet_generique', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'valeur')) return false ;                             // Nombre d'objet a reparer au max
        $tarif = $p5->aqelem_param_num_1;

        // préparer p6 avec la liste des objets rechargeables
        $req = "select objsort_cod, obj_nom, tobj_libelle, objsort_nom, objsort_nb_utilisation_max, objsort_nb_utilisation
                      from perso_objets 
                      join objets on obj_cod=perobj_obj_cod 
                      join objet_generique on gobj_cod=obj_gobj_cod 
                      join type_objet on tobj_cod=gobj_tobj_cod 
                      join objets_sorts on objsort_obj_cod=obj_cod
                      where perobj_perso_cod=:perso_cod and objsort_nb_utilisation_max is not null and objsort_nb_utilisation > 0 ";

        // Filter sur le type de matos pris en charge par le PNJ
        $liste_p3 = "";
        foreach ($p3 as $k => $elem)
        {
            if ($elem->aqelem_misc_cod!=0)
            {
                $liste_p3 .= ",".$elem->aqelem_misc_cod ;
            }
        }
        // Filter sur le matos pris en charge par le PNJ
        $liste_p4 = "";
        foreach ($p4 as $k => $elem)
        {
            if ($elem->aqelem_misc_cod!=0)
            {
                $liste_p4 .= ",".$elem->aqelem_misc_cod ;
            }
        }

        // filter en fonction du type d'objet ou du générique
        if (($liste_p3 != "") && ($liste_p4 == ""))
        {
            $req .= " and gobj_tobj_cod in (".substr($liste_p3, 1).")";
        }
        else if (($liste_p3 == "") && ($liste_p4 != ""))
        {
            $req .= " and obj_gobj_cod in (".substr($liste_p4, 1).")";
        }
        else if (($liste_p3 != "") && ($liste_p4 != ""))
        {
            $req .= " and ( gobj_tobj_cod in (".substr($liste_p3, 1).") or obj_gobj_cod in (".substr($liste_p4, 1).") )";
        }
        else
        {
            $req .= " and false";       // ni objet generique ni type d'objet ? erreur QA
        }

        $req .= "order by obj_nom";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);
        if (!$p7 = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            return "Je ne vois rien que vous pouvez recharger ici!!!" ;
        }

        if ( count($p7) == 0 )
        {
            return "Je ne vois rien que vous pouvez recharger ici." ;
        }

        // charger le perso du joueru
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);


        $faire_reparation = false ;
        $nbtrocs = 0 ;
        $trocs_bzf = 0 ;
        $trocs_matos = "" ;
        $selected_item = "" ;
        $bourse = $perso->perso_po ;
        if (isset($_REQUEST["dialogue-echanger"]))
        {
            // le joueur a valider, on vérifie qu'il a les objets nécéssaires
            foreach ($p7 as $k => $objet)
            {
                if (isset($_REQUEST["echange-{$objet["objsort_cod"]}"]))
                {
                    $nbtrocs ++ ;
                    $trocs_bzf = $trocs_bzf + (($objet["objsort_nb_utilisation"]) * $tarif);
                    $trocs_matos .= "<b>" . $objet["objsort_nom"] . "</b> sur <b>" . $objet["obj_nom"] . '</b> ( <i>' .  $objet["tobj_libelle"] . " </i>), ";
                    $bourse = $bourse - (($objet["objsort_nb_utilisation"]) * $tarif);
                    $selected_item.='<input type="hidden" name="echange-'.$objet["objsort_cod"].'" value="'.$_REQUEST["echange-{$objet["objsort_cod"]}"].'">';
                }
            }

            if ($bourse<0)
            {
                $form .= "Vous n'avez <strong>les brouzoufs</strong> nécessaires pour ses recharges, veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs == 0)
            {
                $form .= "Vous n'avez rien sélectionné, veuillez ré-essayer:<br><br>";
            }
            else if ($nbtrocs>$p6->aqelem_param_num_1 && $p6->aqelem_param_num_1>0)
            {
                $form .= "Vous n'avez le droit qu'à <strong>{$p6->aqelem_param_num_1} recharge(s)</strong>, vous tenter d'en faire <strong>{$nbtrocs}</strong>, veuillez ré-essayer:<br><br>";
            }
            else
            {
                $faire_reparation = true ;
            }
        }

        // panneau de transaction
        if (!$faire_reparation || isset($_REQUEST["cancel"]))
        {
            // header de la forme
            $form .= '<form method="post" action="quete_auto.php">
            <input type="hidden" name="methode" value="dialogue">
            <input type="hidden" name="dialogue-echanger" value="dialogue">
            <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'"> 
            <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">            
            <table style="border: solid 1px #800000;"><tr><td style="width:20px; font-weight: bold">&nbsp;</td><td style="min-width:400px; font-weight: bold">Objet</td><td style="min-width:400px; font-weight: bold">Sort à recharger</td><td style="min-width:400px; font-weight: bold">Prix du rechragement</td></tr>';

            foreach ($p7 as $k => $objet)
            {
                // Mettre la première colone (maintenant que l'on sait si on a en stock tous les éléments)
                $form.='<tr style="color:#800000;  font-style: italic; border-top: solid 1px #800000;">
                              <td style="border-top: inherit;"><input name="echange-' . $objet["objsort_cod"] . '" type="checkbox" style="text-align: center;"></td>
                              <td style="border-top: inherit;">&nbsp;' . $objet["obj_nom"] . ' ( <i>' .  $objet["tobj_libelle"] . '</i> )</td>
                              <td style="border-top: inherit;">&nbsp;' .  $objet["objsort_nom"] . ' ( <i>' .  ((int)($objet["objsort_nb_utilisation_max"] - $objet["objsort_nb_utilisation"])) . ' / ' . $objet["objsort_nb_utilisation_max"]. '</i> )</td>
                              <td style="border-top: inherit;">&nbsp;' . (($objet["objsort_nb_utilisation"])* $tarif) . ' Bzf</td>
                        </tr>';
            }
            // footer
            $form.= '</table><br><input class="test" type="submit" name="valider" value="Valider les rechargements">&nbsp;&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="cancel" value="Ne rien recharger"></form>' ;
            $form .= '<br><br>Vous disposez de : <strong>'.$perso->perso_po.' Bzf</strong><br>';
            $form .= '<u><strong>ATTENTION</strong></u>:<br>';
            if ($p6->aqelem_param_num_1>0)
            {
                $form .= ' * Vous ne pouvez choisir que <strong>'.$p6->aqelem_param_num_1.'</strong> ligne(s) de rechargement(s) au maximum.<br>';
            }
            $form .= ' * Vous quitterez cette étape en Validant ou Abandonnant cet réparation et ne pourrez y revenir que si la quête le stipule.';
        }
        else
        {

            // Bilan de la transaction
            $troc_phrase = "" ;
            if ( $_REQUEST["dialogue-echanger"] == "dialogue-validation" )
            {
                $form.= "Vous recharger les sorts suivants:<br> " ;
            }
            else
            {
                $form.= "Vous souhaitez recharger les sorts suivants ";
            }

            $troc_phrase .= $trocs_matos ;
            if ($trocs_bzf>0) $troc_phrase.= " pour {$trocs_bzf} Bzf";

            $form.=$troc_phrase."<br>" ;

            if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" )
            {
                // proposer une validation
                $form .= '<form method="post" action="quete_auto.php">
                <input type="hidden" name="methode" value="dialogue">
                <input type="hidden" name="dialogue-echanger" value="dialogue-validation">
                <input type="hidden" name="troc-phrase" value="'.htmlentities($troc_phrase).'">
                <input type="hidden" name="quete" value="'.$aqperso->aqperso_aquete_cod.'">                
                <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">'.$selected_item;
                $form.= '<input class="test" type="submit" value="Valider">&nbsp;&nbsp;&nbsp;&nbsp;<input style="text-align: center;" class="test" type="submit" name="cancel" value="Revoir la liste"></form>' ;
                $form .= '<br><strong>RAPPEL</strong>: Vous quitterez cette étape en Validant cet échange et ne pourrez y revenir que si la quête le stipule.';
            }

        }

        return $form ;
    }


    /**
     * Retourne en texte de l'état d'un objet par son %
     */
    function get_etat($parm)
    {
        $retour = 'Comme neuf';
        if ($parm < 90)
        {
            $retour = 'Excellent';
        }
        if ($parm < 70)
        {
            $retour = 'Bon';
        }
        if ($parm < 50)
        {
            $retour = 'Mauvais';
        }
        if ($parm < 35)
        {
            $retour = 'Médiocre';
        }
        if ($parm < 10)
        {
            $retour = 'Déplorable';
        }
        return $retour;
    }

    /**
     * Fonction pour connaitre le "nom humain" des codes d'étape négatifs
     * @param $aqetape_cod
     * @return string
     */
    function  getNom($aqetape_cod)
    {
        if ($aqetape_cod<-3) return "Erreur n° etape";

        $nom = "" ;
        switch($aqetape_cod)
        {
            case 0:
                $nom = "Etape suivante" ;
            break;

            case -1:
                $nom = "Quitter/Abandonner" ;
            break;

            case -2:
                $nom = "Terminer avec succès" ;
            break;

            case -3:
                $nom = "Echec de la quête" ;
            break;

            default:
                $aquete_etape = new aquete_etape ;
                $aquete_etape->charge( $aqetape_cod );
                $nom = $aquete_etape->aqetape_nom ;
        }

        return $nom ;
    }

    /**
     * charge l'élément en fonction de aqetape_etape_cod
     * @global bdd_mysql $pdo
     * @param int $aqetape_etape_cod
     * @return $this|bool
     */
    function  chargeBy_aqetape_etape_cod(int $aqetape_etape_cod)
    {
        $pdo = new bddpdo;
        $req = "select aqetape_cod  from quetes.aquete_etape where aqetape_etape_cod = ? ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($aqetape_etape_cod),$stmt);
        if (!$result = $stmt->fetch()) return false ;

        $this->charge($result["aqetape_cod"]);

        return $this;
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
                ob_start();
                debug_print_backtrace();
                $out = ob_get_contents();
                error_log($out);
                die('Unknown method.');
        }
    }
}
