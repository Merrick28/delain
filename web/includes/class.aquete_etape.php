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


    /**
     * Fonction pour mettre en forme le texte d'une étape alors que l'étape n'a pas encore démarrée.
     * Les éléments de l'étapes n'ont pas encore été instanciés, on se base sur le template
     * Param1 => Element déclencheur, Param2 => Choix
     * @param $trigger_nom
     * @return string
     */
    function get_initial_texte( perso $perso, $trigger_nom )
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
                $param_num = (int)$params[0] ;

                if (substr($params[0],0,1)=="#")
                {
                    $hydrate_texte .=  $perso->get_champ(substr($params[0],1));
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
                            $link = "/jeu_test/quete_auto.php?methode=start&quete=".$this->aqetape_aquete_cod."&choix=".$e->aqelem_cod ;
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
    function get_texte_form(aquete_perso $aqperso)
    {
        $etape_modele = $aqperso->get_etape_modele();

        return '<form method="post" action="quete_auto.php">
        <input type="hidden" name="methode" value="dialogue">
        <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">
        &nbsp;&nbsp;&nbsp;Vous : <input name="dialogue" type="text" size="80"><br>
        <br>&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="choix_etape" value="Valider" >
        </form>' ;
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
            return "Pour faire un achat, rendez-vous sur la case d'un PNJ proposant ce service." ;
        }

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                              // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'echange', 0)) return false ;              // Problème lecture des paramètres

        if ( count($p4) == 0 )
        {
            return "Il n'y a plus rien à acheter ici." ;
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
        $enstock = true ;
        $bourse = $perso->perso_po ;
        $selected_item = "" ;

        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue") return "Vous avez décidé de ne rien acheter.";

        if (isset($_REQUEST["dialogue-echanger"]))
        {
            // le joueur a valider, on vérifie qu'il a les objets nécéssaires
            foreach ($p4 as $k => $elem)
            {
                if (isset($_REQUEST["echange-{$k}"]) && $_REQUEST["echange-{$k}"]=="on")
                {
                    $nbtrocs ++ ;
                    $selected_item.='<input type="hidden" name="echange-'.$k.'" value="on">';
                    $trocs_matos[$elem->aqelem_misc_cod] = (!isset($trocs_matos[$elem->aqelem_misc_cod])) ? (int)$elem->aqelem_param_num_1 : $trocs_matos[$elem->aqelem_misc_cod] + (int)$elem->aqelem_param_num_1 ;

                    // check breouzouf
                    $bourse =  $bourse - (int)$elem->aqelem_param_txt_1 ;
                    $trocs_bzf =  $trocs_bzf + (int)$elem->aqelem_param_txt_1 ;
                    if ($bourse < 0 )
                    {
                        $enstock = false ;
                    }

                    if ((!isset($trocs_en_stock[$elem->aqelem_param_num_2]) || ($trocs_en_stock[$elem->aqelem_param_num_2]<(int)$elem->aqelem_param_num_3)) && ((int)$elem->aqelem_param_num_2>0 && (int)$elem->aqelem_param_num_3>0))
                    {
                        $enstock = false ;
                    }
                    else if ((int)$elem->aqelem_param_num_2>0 && (int)$elem->aqelem_param_num_3>0)
                    {
                        $trocs[$elem->aqelem_param_num_2] = (!isset($trocs[$elem->aqelem_param_num_2])) ? (int)$elem->aqelem_param_num_3 : $trocs[$elem->aqelem_param_num_2] + (int)$elem->aqelem_param_num_3 ;
                        $trocs_en_stock[$elem->aqelem_param_num_2] = $trocs_en_stock[$elem->aqelem_param_num_2] - (int)$elem->aqelem_param_num_3 ;
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
            } else if ($nbtrocs==0) {
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
            <table><tr><td style="width:20px;"></td><td style="min-width:400px; font-weight: bold">Objets à acquérir</td><td style="min-width:400px; font-weight: bold">Prix</td><td style="min-width:400px; font-weight: bold"></td></tr>';

            foreach ($p4 as $k => $elem)
            {
                $objet = new objet_generique();
                $objet->charge($elem->aqelem_misc_cod);

                $prix = new objet_generique();
                $prix->charge($elem->aqelem_param_num_2);
                $bzf = 1 * $elem->aqelem_param_txt_1 ;

                $enstock = true ;
                if ((!isset($inventaire[$elem->aqelem_param_num_2]) || ($inventaire[$elem->aqelem_param_num_2]<(int)$elem->aqelem_param_num_3)) && ((int)$elem->aqelem_param_num_3>0)) $enstock = false ;
                if ($perso->perso_po<(int)$elem->aqelem_param_txt_1) $enstock = false ;

                $form .= '<tr style="color:#800000;  font-style: italic;">
                      <td><input '.($enstock ? ( (isset($_REQUEST["echange-{$k}"]) && $_REQUEST["echange-{$k}"]=="on") ? 'checked ' : '') : 'disabled ').'name="echange-'.$k.'" type="checkbox"></td>
                      <td>&nbsp;'.$elem->aqelem_param_num_1.' x '.$objet->gobj_nom.'</td>';

                $form .= '<td>';
                if ($bzf>0) $form .= '&nbsp;'.$elem->aqelem_param_txt_1.'&nbsp;Bzf';
                if (($elem->aqelem_param_num_3>0) && (1*$elem->aqelem_param_num_2>0)) $form .= '&nbsp;'.$elem->aqelem_param_num_3.' x '.$prix->gobj_nom;
                $form .= '</td>';
                if (($enstock) && (1*$elem->aqelem_param_num_2>0))
                    $form .= '<td>'.$inventaire[$elem->aqelem_param_num_2].' dans l\'inventaire</td>';
                else if (!$enstock)
                    $form .= '<td>Vous n\'avez pas les objets/Bzfs necessaires</td>';
                $form .= '</tr>';
            }
            // footer
            $form.= '</table><br><input class="test" type="submit" name="valider" value="Valider la transaction">&nbsp;&nbsp;&nbsp;&nbsp;<input class="test" type="submit" name="cancel" value="Ne rien acheter"></form>' ;
            $form .= '<br><br>Vous disposez de : <strong>'.$perso->perso_po.' Bzf</strong><br>';
            if ($p3->aqelem_param_num_1>0) {
                $form .= '<u>ATTENTION</u>: Vous pouvez sélectionner <strong>'.$p3->aqelem_param_num_1.'</strong> ligne(s) au maximum, il n\'y aura qu\'<u><strong>une seule transaction</strong></u> possible.<br>';
            }
            else {
                $form .= '<u>ATTENTION</u>: Vous pouvez acquerir autant d\'objet que vous le souhaitez, mais en <u>une seule transaction</u>.<br>';
            }
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
            if ($trocs_bzf>0) $troc_phrase.= " au prix de {$trocs_bzf} Bzf";
            if (count($trocs)>0)  $troc_phrase.= (($trocs_bzf>0) ? " et " : "")." contre ";
            foreach ($trocs as $obj => $nbobj)
            {
                $k++;
                $objet = new objet_generique();
                $objet->charge($obj);
                if ($k==count($trocs) && $k>1) $troc_phrase .= 'et '; else if ($k>1) $troc_phrase .= ', ';
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
                <input type="hidden" name="modele" value="'.$etape_modele->aqetapmodel_tag.'">'.$selected_item;
                $form.= '<input class="test" type="submit" value="Valider">&nbsp;&nbsp;&nbsp;&nbsp;<input style="text-align: center;" class="test" type="submit" name="cancel" value="Revoir la liste"></form>' ;
            }

        }

        return $form ;
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
                die('Unknown method.');
        }
    }
}