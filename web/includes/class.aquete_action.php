<?php
/**
 * includes/class.aquete_action.php
 */

/**
 * Class aquete_action
 *
 * Gère les actions qui peuvent être réalisées par les quetes autos.
 * La classe ne traite pas la mécanique d'avcancement dans la quete, mais réalise uniquement les actions demandées
 * Le pilotage se fait par aquete_perso
 * Ici, on est vraiment dans une couche basse de l'outil, chaque action ne devrait utiliser que les éléments de son step.
 */
class aquete_action
{

    //==================================================================================================================
    /**
     * Function qui sert à préparer une liste d'éléments
     * si $nbelement est inférieur au nombre d'élément on retour la liste des $nbelement premiers éléments.
     * si $nbelement est superieur alors il y aura un exemplaire de chaque puis un complément aléatoire pour atteidre le nombre prévu.
     * @param $nbelement    nombre d'élément à choisir
     * @param $elements     tableau des elements
     * @return mixed        liste d'élément choisis
     */
    private function get_liste_element($nbelement, $elements)
    {
        $liste = array() ;
        $nbgenerique = count ($elements) ;

        if ($nbelement <= $nbgenerique)
        {
            // On donne les objets dans la limite demandé (aléatoirement)
            for ($i=0; $i<$nbelement; $i++) $liste[$i] = clone $elements[$i];
        }
        else
        {
            for ($i=0; $i<$nbgenerique; $i++) $liste[$i] = clone $elements[$i];        // Chaque objet au moins 1x
            for ($i=$nbgenerique; $i<$nbelement; $i++)
            {
                $liste[$i] = clone $elements[rand(0,($nbgenerique-1))];                    // Le reste est aléatoire
            }
        }

        return $liste;
    }

    //==================================================================================================================

    /**
     * @param $string
     * @return string en minuscule et néttoyée de tous les caractère de ponctuation.
     */
    private function clean_string($s){

        $s = str_replace(
            array('à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'),
            array('a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'),
            $s );
        $s = str_replace(' ', '-', $s);
        $s = preg_replace('/[^0-9A-Za-z0-9-]/', '', $s);
        $s = preg_replace('/-+/', ' ', $s);

        return trim(strtolower($s)) ;
    }

    //==================================================================================================================
    private function injection_journal($aqperso, $texte_lancer)
    {
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePageEtape($aqperso->aqperso_cod, $aqperso->aqperso_etape_cod);
        if (strpos($perso_journal->aqpersoj_texte, "[[#aqaction]") !== false) {
            $perso_journal->aqpersoj_texte = str_replace("[[#aqaction]]", $texte_lancer, $perso_journal->aqpersoj_texte);
            $perso_journal->stocke();
        }
    }


    //==================================================================================================================
    /**
     * On supprime tous les éléments de la quête créé dont la liste est passé en paramètre =>  '[1:element|0%0]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function nettoyage(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "", 0)) return false ;      // Problème lecture des paramètres

        foreach($p1 as $k => $element)
        {
            if ($element->aqelem_type == 'objet')
            {
                // supression de l'objet
                $objet = new objets();
                $objet->supprime($element->aqelem_misc_cod);        // On supprime l'objet !
            }
            else if ($element->aqelem_type == 'perso')
            {
                // désactivation du perso
                $perso = new perso();
                $perso->charge($element->aqelem_misc_cod);          // On charge le perso
                $perso->perso_actif = 'N';                          // pour le perso on ne va pas supprimer mais seulement le désactiver
                $perso->stocke();
            }
        }
    }

    //==================================================================================================================
    /**
     * On supprime tous les éléments de la quête créé dont la liste est passé en paramètre =>  '[[1:position|0%0],[2:valeur|1%1],[3:selecteur|1%1|{0~Non},{1~Oui}],[4:type_objet|0%0],[5:selecteur|1%1|{0~Non},{1~Oui}],[6:selecteur|1%1|{0~Non},{1~Oui}]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function nettoyage_zone(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "position", 0)) return 0 ;      // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur")) return 0 ;      // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "selecteur", 1)) return 0 ;         // Problème lecture passage à l'etape suivante
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "type_objet", 0)) return 0 ;         // Problème lecture passage à l'etape suivante
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "selecteur", 1)) return 0 ;         // Problème lecture passage à l'etape suivante
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, "selecteur", 1)) return 0 ;         // Problème lecture passage à l'etape suivante

        $dist = $p2->aqelem_param_num_1 ;
        foreach ($p1 as $k => $elem)
        {
            $pos = new positions();
            $pos->charge($elem->aqelem_misc_cod);

            if ($dist == 0) {
                $where_positions = " pos_etage=:pos_etage " ;
                $arrPos = [ ":pos_etage" => $pos->pos_etage];
            } else {
                $where_positions = " pos_etage=:pos_etage " .($dist == 0 ? "" :  " and pos_x >=:posx_min and pos_x <=:posx_max and pos_y >=:posy_min and pos_y <=:posy_max " ) ;
                $arrPos = [ ":pos_etage" => $pos->pos_etage,
                    ":posx_min" => $pos->pos_x - $dist,
                    ":posx_max" => $pos->pos_x + $dist,
                    ":posy_min" => $pos->pos_y - $dist,
                    ":posy_max" => $pos->pos_y + $dist ];
            }

            // nettoyer l'or?
            if ($p3->aqelem_misc_cod == 1)
            {
                $req    = "delete from or_position where por_cod in ( select por_cod from or_position join positions on pos_cod=por_pos_cod where {$where_positions} );";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute($arrPos, $stmt);
            }

            // nettoyer des objets
            foreach ($p4 as $t => $tobj)
            {
                $req    = "select f_del_objet(f.obj_cod) from (
                                select obj_cod from objets 
                                join objet_generique on gobj_cod=obj_gobj_cod 
                                join objet_position on pobj_obj_cod=obj_cod
                                join positions on pos_cod=pobj_pos_cod
                                where  {$where_positions} and gobj_tobj_cod=:tobj_cod
                            ) f ;";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array_merge([":tobj_cod" => $tobj->aqelem_misc_cod], $arrPos), $stmt);
            }

            // nettoyer les passages ?
            if ($p5->aqelem_misc_cod == 1)
            {
                $req    = "select lieu_cod, pos_cod from lieu
                                join lieu_position on lpos_lieu_cod=lieu_cod
                                join positions on pos_cod=lpos_pos_cod
                                where lieu_tlieu_cod=10 and {$where_positions}  ;";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute($arrPos, $stmt);
                $result   = $stmt->fetchAll();
                if ($result)
                {
                    foreach ($result as $l => $lieu)
                    {
                        $req  = "delete from lieu_position where lpos_lieu_cod = {$lieu["lieu_cod"]} ";
                        $stmt = $pdo->query($req);
                        $req  = "delete from lieu where lieu_cod= {$lieu["lieu_cod"]} ";
                        $stmt = $pdo->query($req);
                        $req  = "select init_automap_pos( {$lieu["pos_cod"]}) ";
                        echo "$req <br>";
                        $stmt = $pdo->query($req);
                    }
                }

            }

            // nettoyer les monstres ?
            if ($p5->aqelem_misc_cod == 1)
            {
                $req    = "select perso_cod from perso
                                join perso_position on ppos_perso_cod=perso_cod
                                join positions on pos_cod=ppos_pos_cod
                                where perso_type_perso=2 and perso_actif='O' and {$where_positions}  ;";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute($arrPos, $stmt);
                $result   = $stmt->fetchAll();
                if ($result)
                {
                    foreach ($result as $m => $monstre)
                    {
                        $req  = "update perso set perso_actif='O' where perso_cod = {$monstre["perso_cod"]} ";
                        $stmt = $pdo->query($req);
                    }
                }
            }
        }

    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction d'un tirage aléatoir =>  '[1:valeur|0%0],[2:etape|0%0]'
     * @param aquete_perso $aqperso
     * @return integer (n° d'étape)
     */
    function saut_condition_aleatoire(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "valeur", 0)) return 0 ;              // Problème lecture passage à l'etape suivante
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "etape", 0)) return 0 ;               // Problème lecture passage à l'etape suivante

        // Le nombre détape viable est la plus petite liste entre valeur et etape
        $nb_etape = min(sizeof($p1), sizeof($p2)) ;

        // on calcul la somme des valeurs
        $max_alea = 0 ;
        for ($i=0; $i<$nb_etape; $i++) $max_alea += $p1[$i]->aqelem_param_num_1 ;

        if ($max_alea < 100) $max_alea = 100;
        $alea = random_int (0, $max_alea ) ;

        $etape = 0 ;        // Par defaut on passe à l'étape suivante
        for ($i=0; $i<$nb_etape; $i++)
        {
            if ( $alea <= $p1[$i]->aqelem_param_num_1 )
            {
                $etape = $p2[$i]->aqelem_misc_cod ;
                break;
            }
            else
            {
                $alea = $alea - $p1[$i]->aqelem_param_num_1 ;
            }
        }

        // renvoyer le n° d'étape
        return $etape ;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction des conditions =>  '[1:perso_condition|0%0],[2:etape|1%1],[3:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_perso(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "etape", 1)) return 0 ;              // Problème lecture passage à l'etape suivante
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape", 1)) return 0 ;              // Problème lecture passage à l'etape suivante

        // Cette étape est totalement vérifié par la fonction quetes.aq_verif_perso_condition_etape

        $pdo = new bddpdo;
        $req = "select quetes.aq_verif_perso_condition_etape(:perso_cod, :etape_cod, :param_id, :aqperso_cod) as verification";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
                                ":perso_cod" => $aqperso->aqperso_perso_cod,
                                ":etape_cod" => $aqperso->aqperso_etape_cod,
                                ":param_id" => 1,
                                ":aqperso_cod" => $aqperso->aqperso_cod
                            ), $stmt);
        $result = $stmt->fetch();

        if ((int)$result["verification"] <= 0)  return $p3->aqelem_misc_cod ; // Les conditions ne sont pas remplies, passage à l'étape d'erreur

        // Le perso possède les conditions demandées, on valide le saut d'étape à l'étape demandé!
        return $p2->aqelem_misc_cod ;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction des conditions =>  '[1:perso_condition_liste|0%0],[2:etape|0%0],[3:etape|1%1'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_multiple_condition_perso(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "etape", 0)) return 0 ;              // Problème lecture passage à l'etape suivante
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape", 1)) return 0 ;              // Problème lecture passage à l'etape suivante

        // Cette étape est totalement vérifié par la fonction quetes.aq_verif_perso_condition_etape

        $pdo = new bddpdo;
        $req = "select aqelem_param_ordre from quetes.aquete_element where (aqelem_type='perso_condition' or aqelem_type='perso_condition_liste') and aqelem_misc_cod>0 
                          and aqelem_aqperso_cod IS NULL and aqelem_param_id=1 
                          and aqelem_aqetape_cod=:etape_cod 
                          and (aqelem_param_num_2=1 or aqelem_param_ordre=0)
                          order by aqelem_param_ordre ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":etape_cod" => $aqperso->aqperso_etape_cod
        ), $stmt);
        $cond_list = $stmt->fetchAll( PDO::FETCH_NUM );
        //echo "<pre>"; print_r([$cond_list, $p2]);

        // erreur dans les paramètre, sortie sur l'étape alternative
        if ((sizeof($cond_list) == 0) || (sizeof($cond_list) != sizeof($p2)))  return $p3->aqelem_misc_cod ;

        foreach ($cond_list as $k => $order)
        {
            $req = "select quetes.aq_verif_perso_condition_etape(:perso_cod, :etape_cod, :param_id, :aqperso_cod, :ordre) as verification";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":perso_cod" => $aqperso->aqperso_perso_cod,
                ":etape_cod" => $aqperso->aqperso_etape_cod,
                ":param_id" => 1,
                ":aqperso_cod" => $aqperso->aqperso_cod,
                ":ordre" => $order[0]
            ), $stmt);
            $result = $stmt->fetch();

            // groupe de conditions remplie, on dirige vers l'étape correspondante
            //print_r([$result, $k, $order]);
            if ((int)$result["verification"] > 0) return $p2[$k]->aqelem_misc_cod ;
        }

        return $p3->aqelem_misc_cod ; // Les conditions ne sont pas remplies, passage à l'étape d'erreur
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction des condition =>  '[1:competence|1%1],[2:valeur|1%1],[3:valeur|1%1],[4:etape|1%1],[5:etape|1%1],[6:etape|1%1],[7:etape|1%1],[8:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return integer (etape)
     */
    function saut_condition_competence(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "competence", 1)) return 0 ;         // Problème lecture passage à l'etape suivante
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur", 1)) return 0 ;             // (mini) Problème lecture passage à l'etape suivante
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "valeur", 1)) return 0 ;             // (dificulté) Problème lecture passage à l'etape suivante
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "etape", 1)) return 0 ;              // (reussite critique) Problème lecture passage à l'etape suivante
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "etape", 1)) return 0 ;              // (reussite speciale) Problème lecture passage à l'etape suivante
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, "etape", 1)) return 0 ;              // (reussite) Problème lecture passage à l'etape suivante
        if (!$p7 = $element->get_aqperso_element( $aqperso, 7, "etape", 1)) return 0 ;              // (echec) Problème lecture passage à l'etape suivante
        if (!$p8 = $element->get_aqperso_element( $aqperso, 8, "etape", 1)) return 0 ;              // (echec critqiue) Problème lecture passage à l'etape suivante

        $perso = new perso();
        if (!$perso->charge($aqperso->aqperso_perso_cod)) return $p7->aqelem_misc_cod ;         // Erreur de chargemetn du perso => echec classique

        $perso_competence = new perso_competences();
        if (!$perso_competence->getByPersoCompetenceNiveau($perso->perso_cod, $p1->aqelem_misc_cod))
        {
            $this->injection_journal($aqperso, "Vous ne disposez pas de la compétence.");
            return 0 ;
        }

        // Si le perso n'a pas le niveau requis
        if ( $perso_competence->pcomp_modificateur < $p2->aqelem_param_num_1)                          // etape suivante: pas le niveau
        {
            $this->injection_journal($aqperso, "Vous n'avez pas le niveau requis pour essayer.");
            return 0 ;
        }

        // On ajoute la difficulté au niveau de compétence du perso
        $competence = $perso_competence->pcomp_modificateur + $p3->aqelem_param_num_1;

        // Si la difficulté est supérieure au niveau du perso
        if ($competence <= 0 )
        {
            $this->injection_journal($aqperso, "Le difficulté est trop grande pour pouvoir faire quelque chose.");
            return 0 ;
        }
        // etape suivante: trop dificille

        // Lancé du jet de dé 1D100 en tenant compte des malédiction/bénédiction pour retourner les chances de réussite
        $pdo = new bddpdo;
        $req = "select lancer_des3(1,100, (valeur_bonus(:perso_cod, 'BEN') + valeur_bonus(:perso_cod, 'MAU'))::integer) as reussite";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":perso_cod" => $aqperso->aqperso_perso_cod
        ), $stmt);
        if ( !$result = $stmt->fetch() ) return $p7->aqelem_misc_cod ;        // echec classique
        $reussite = $result["reussite"];

        $texte_lancer = "Votre lancer de dé est : ".$reussite ;
        if ($reussite>96) {
            $etape = $p8->aqelem_misc_cod  ;        // Echec critique
            $texte_lancer.= ", il s'agit d'un echec critique.";
        } else if ($reussite>$competence) {
            $etape = $p7->aqelem_misc_cod  ;        // echec classique
            $texte_lancer.= ", il s'agit d'un echec.";
        } else if ($result["reussite"] <= 5 ) {
            $etape = $p4->aqelem_misc_cod  ;        // Réussite critique à 5
            $texte_lancer.= ", il s'agit d'une réussite critique.";
        } else if ($result["reussite"] <= ( 10*$competence/100) ) {
            $etape = $p5->aqelem_misc_cod  ;        // Réussite spéciale à 10% de la compétence
            $texte_lancer.= ", il s'agit d'une réussite spéciale.";
        } else {
            $etape = $p6->aqelem_misc_cod  ;        // Réussite standard
            $texte_lancer.= ", il s'agit d'une réussite.";
        }

        $this->injection_journal($aqperso, $texte_lancer);

        //echo "<pre>"; print_r([$etape, $reussite, $competence, $result]);die();

        // retourner l'étape !
        return $etape;
    }


    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction des condition =>  '[1:selecteur|1%1|{1~Force},{2~Dextérité},{3~Intelligence},{4~Constitution},{5~Vue}],[2:valeur|1%1],[3:valeur|1%1],[4:etape|1%1],[5:etape|1%1],[6:etape|1%1],[7:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return integer (etape)
     */
    function saut_condition_carac(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "selecteur", 1)) return 0 ;         // Problème lecture passage à l'etape suivante
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur", 1)) return 0 ;             // (mini) Problème lecture passage à l'etape suivante
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "valeur", 1)) return 0 ;             // (dificulté) Problème lecture passage à l'etape suivante
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "etape", 1)) return 0 ;              // (reussite critique) Problème lecture passage à l'etape suivante
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "etape", 1)) return 0 ;              // (reussite) Problème lecture passage à l'etape suivante
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, "etape", 1)) return 0 ;              // (echec) Problème lecture passage à l'etape suivante
        if (!$p7 = $element->get_aqperso_element( $aqperso, 7, "etape", 1)) return 0 ;              // (echec critique) Problème lecture passage à l'etape suivante

        $perso = new perso();
        if (!$perso->charge($aqperso->aqperso_perso_cod)) return $p6->aqelem_misc_cod ;         // Erreur de chargemetn du perso => echec classique

        // recupe de la carac en focntion du selecteur
        if ($p1->aqelem_misc_cod == 1)  {$coeff=3; $carac = $perso->perso_for ; }
        else if ($p1->aqelem_misc_cod == 2)  {$coeff=3; $carac = $perso->perso_dex ;}
        else if ($p1->aqelem_misc_cod == 3)  {$coeff=3; $carac = $perso->perso_int ;}
        else if ($p1->aqelem_misc_cod == 4)  {$coeff=3; $carac = $perso->perso_con ;}
        else if ($p1->aqelem_misc_cod == 5)  {$coeff=5; $carac = $perso->distance_vue() ;}
        else return $p6->aqelem_misc_cod ;

        //echo "<pre>"; print_r([$p1, $perso, $coeff,$carac ]); die();

        // Si le perso n'a pas le niveau requis dans la carac
        if ( $carac < $p2->aqelem_param_num_1)                              // etape suivante: pas le niveau
        {
            $this->injection_journal($aqperso, "Votre caracteristique est trop faible pour essayer.");
            return 0 ;
        }

        // Lancé du jet de dé 1D100 en tenant compte des malédiction/bénédiction pour retourner les chances de réussite
        $pdo = new bddpdo;
        $req = "select lancer_des3(1,100, (valeur_bonus(:perso_cod, 'BEN') + valeur_bonus(:perso_cod, 'MAU'))::integer) as reussite, lancer_des3(1,100, 0) as opposition";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":perso_cod" => $aqperso->aqperso_perso_cod
        ), $stmt);
        if ( !$result = $stmt->fetch() ) return $p7->aqelem_misc_cod ;        // echec classique
        $reussite = $result["reussite"];
        $opposition = $result["opposition"];

        $texte_lancer = "Votre lancer de dé est : ".$reussite ;
        if ($reussite>96) {
            $etape = $p7->aqelem_misc_cod  ;        // Echec critique
            $texte_lancer.= ", il s'agit d'un echec critique.";
        } else if ((($carac*$coeff)-$reussite) < (($p3->aqelem_param_num_1*$coeff) - $opposition)) {
            $etape = $p6->aqelem_misc_cod  ;        // echec classique
            $texte_lancer.= ", il s'agit d'un echec, le jet d'opposition ($opposition) sur la caractéristique est meilleur que le votre.";
        } else if ($result["reussite"] <= 5 ) {
            $etape = $p4->aqelem_misc_cod  ;        // Réussite critique à 5
            $texte_lancer.= ", il s'agit d'une réussite critique.";
        } else {
            $etape = $p5->aqelem_misc_cod  ;        // Réussite standard
            $texte_lancer.= ", il s'agit d'une réussite, votre jet de dé sous votre caractéristique est meilleur que le jet d'opposition ($opposition).";
        }

        $this->injection_journal($aqperso, $texte_lancer);

        //echo "<pre>"; print_r([$aqperso->aqperso_cod, $aqperso->aqperso_etape_cod, $perso_journal]);die();

        // retourner l'étape !
        return $etape;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction des condition =>  '[1:quete_etape|0%0],[2:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_etape(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "quete_etape", 0)) return 0 ;    // Problème lecture passage à l'etape suivante
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "etape", 1)) return 0 ;          // Problème lecture passage à l'etape suivante
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape", 1)) return 0 ;          // Problème lecture passage à l'etape suivante

        $filter_string = array() ;
        $filter = "aqperso_perso_cod=:aqperso_perso_cod AND ( " ;
        $filter_string[":aqperso_perso_cod"]= $aqperso->aqperso_perso_cod ;
        foreach ($p1 as $k => $elem)
        {
            if ($k>0)  $filter.= "OR ";
            $filter.= "(aqpersoj_etape_cod = :etape$k) ";
            $filter_string[":etape$k"] = $elem->aqelem_misc_cod ;
        }
        $filter.=") ";

        // On vérifie le passage par ces étapes
        $pdo = new bddpdo;
        $req = "select count(distinct aqpersoj_etape_cod) as count
                from quetes.aquete_perso_journal 
                join quetes.aquete_perso on aqperso_cod=aqpersoj_aqperso_cod
                where {$filter} ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute($filter_string, $stmt);
        if ($stmt->rowCount()==0) return $p3->aqelem_misc_cod ;                 // Les conditions ne sont pas remplies, passage à l'étape d'erreur

        $result = $stmt->fetch();
        if ((int)$result["count"] != count($p1))  return $p3->aqelem_misc_cod ; // Les conditions ne sont pas remplies, passage à l'étape d'erreur

        // Le perso est passé par autant d'étape que demandé, on valide le saut d'étape à l'étape demandé!
        return $p2->aqelem_misc_cod ;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie =>  '[1:perso|1%0],[2:valeur|1%1],[3:etape|1%1],[4:choix_etape|1%0]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_dialogue(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;

        // ON vérifie que le joueru a bien dis qq chose avant d'anlyser ses paraoles
        if (!isset($_REQUEST["dialogue"]) || $_REQUEST["dialogue"] == "")
        {
            return $retour;     // on ne compte pas ça comme une tentative!
        }
        $dialogue = $_REQUEST["dialogue"] ;
        $clean_dialogue = " ".$this->clean_string($dialogue)." ";

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur" )) return $retour ;                      // Problème lecture (blocage)
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "choix_etape", 0)) return $retour ;    // Problème lecture (blocage)
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "texte", 0)) return $retour ;    // Problème lecture (blocage)

        // Recherche du PNJ (vérifier que le perso est sur sa case pour discuter)
        $req = " select aqelem_cod, quete.perso_cod as pnj from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod,  perso_cod,ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=1 and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
        if (!$result = $stmt->fetch(PDO::FETCH_ASSOC)) return false;       // pas sur la case du pnj
        $pnj = new perso();
        $pnj->charge($result["pnj"]);


        // On note dans le journal la réponse du joueur
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        //Compléter la dernière parge avec le dialogue:
        $perso_journal->aqpersoj_texte .= "   Vous: {$dialogue}<br> ";
        $perso_journal->stocke();

        //Ajout d'une tentative !
        $p2->aqelem_param_num_2++;
        $p2->stocke();

        //passage en revue des mots attendus (dans l'ordre)
        foreach ($p4 as $e => $elem)
        {
            $nb_mots = 0 ;
            $mots_attendus = explode("|", $elem->aqelem_param_txt_1);
            $conjonction = $elem->aqelem_param_num_2;     // 0=>ET 1=>OU
            foreach ($mots_attendus as $m =>$mot)
            {
                if (strpos($clean_dialogue, " ".$this->clean_string($mot)." ") !== false )
                {
                    $nb_mots++ ;
                }
            }

            // Vérification condition remplies (au moins un mot si OU(1) et tous les mots si ET(0)!
            if (($nb_mots>0 && $conjonction==1) || ($nb_mots==count($mots_attendus) && $conjonction==0))
            {
                // On supprime tous les dialogues qui n'ont pas été choisis
                $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array($elem->aqelem_cod));

                $retour->status = true ;  // l'étape n'est pas terminée, sur l'étape répondu
                $retour->etape = $elem->aqelem_misc_cod;
                return $retour;
            }
        }

        // Sortie sur nombre de tentatives infructueuses
        if (($p2->aqelem_param_num_1>0) && ($p2->aqelem_param_num_2>=$p2->aqelem_param_num_1))
        {
            $retour->status = true ;  // l'étape n'est pas terminée, sur l'étape spécifique
            $retour->etape = $p3->aqelem_misc_cod;
            return $retour;
        }

        // On va aider le joueur avec des textes, mettre le texte en fonction du nombre de tentative dans le journal
        $tentative = $p2->aqelem_param_num_2 - 1 ;
        if (count($p5)<=$tentative)
        {
            $tentative = count($p5) - 1;    // le dernier texte
        }
        $bavardage = $p5[$tentative]->aqelem_param_txt_1 ;
        if ($bavardage != "")
        {
            $perso_journal->aqpersoj_texte .= "<br>   {$bavardage}<br> ";
            $perso_journal->stocke();
        }

        // Sortie par défaut!! demander une autre saisie du joueur
        return $retour;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie du joeuru dans le panneau de gestion des équipes!
     * La mécanique est différente des QA habituelle, l'étapes est globale à tous les joueurs (l'instance du joueur sera ignorée)
     *       + les joueurs ferront evoluer les parametres de la QA de base:
     *       + la validation du dernier joueur validera les quetes de tous.
     * => P5 txt1=nom equipe / num1=nb de joueur attendu dnas l'équipe
     * => P7 => liste de perso / num1 = index d'équipe (0 = 1ere equipe, 1=2eme equipe, etc...)
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_equipe(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;
        $perso_cod = $aqperso->aqperso_perso_cod ;

        // on va travailler avec les éléments de l'étape globale (pas ceux du perso)
        $element = new aquete_element();
        if (!$p2 = $element->get_etape_element( $aqperso->etape, 2, "craft" )) return $retour ;                      // Problème lecture (blocage)
        if (!$p3 = $element->get_etape_element( $aqperso->etape, 3, "selecteur" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p4 = $element->get_etape_element( $aqperso->etape, 4, "valeur", 0 )) return $retour ;                       // Problème lecture (blocage)
        if (!$p5 = $element->get_etape_element( $aqperso->etape, 5, "etape", 0)) return $retour ;    // Problème lecture (blocage)
        if (!$p6 = $element->get_etape_element( $aqperso->etape, 6, "etape")) return $retour ;    // Problème lecture (blocage)
        if (!$p7 = $element->get_etape_element( $aqperso->etape, 7, "perso", 0)) return $retour ;    // Problème lecture (blocage)

        $nb_equipe = $p2->aqelem_param_num_1;       // nombre d'équipe
        $equip_mini = $p2->aqelem_param_num_2 ;     // nombre mini par equipe
        $equip_maxi = $p2->aqelem_param_num_3 ;     // nombre maxi par equipe
        $triplette = $p3->aqelem_misc_cod ;         // 0 = tout autorisé, 1 = 1 joueur par triplette

        // final countdown!!!!
        $TeamsReady = ($p2->aqelem_param_txt_1 != "" && date( "Y-m-d H:i:s", strtotime($p2->aqelem_param_txt_1)) < date( "Y-m-d H:i:s") ) ? true : false ;

        //Si le compte a rebour est dépassé, on ne touche plus à rien on attent que tout le monde sorte de cette étape!
        if (!$TeamsReady) {

            // On commnece par un peu de nettoyage, suppression des persos qui ont quitté la quete (où sont passé a une autre etape) mais ils ne deoivent pas rester dans $p7!
            $pdo = new bddpdo;
            $req  = "select aqperso_perso_cod from quetes.aquete_perso where aqperso_aquete_cod=:aqperso_aquete_cod and aqperso_etape_cod = :aqperso_etape_cod and aqperso_actif='O' ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":aqperso_aquete_cod" => $aqperso->aqperso_aquete_cod,":aqperso_etape_cod" => $aqperso->aqperso_etape_cod ), $stmt);
            $perso_etape = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $p7 = array_filter($p7, function($e) use ($perso_etape) {  return in_array($e->aqelem_misc_cod, $perso_etape) ; });


            $where = "";
            foreach ($p7 as $k => $e) $where .= ((int)$e->aqelem_cod)."," ;
            $where = " and aqelem_misc_cod != 0 " . ( $where == "" ? "" :"and  aqelem_cod not in (". substr($where, 0, -1) .")" );

            $req    = "delete from quetes.aquete_element where aqelem_aqetape_cod = :aqelem_aqetape_cod and aqelem_aqperso_cod is null and aqelem_param_id = 7 $where ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":aqelem_aqetape_cod" => $aqperso->aqperso_etape_cod), $stmt);
        } else {

            // on vérifie si tout le monde est sortie de l'étape !
            $isLauched = true ;
            $nb_perso = 0 ;
            foreach ($p7 as $k => $p) {
                if ($p->aqelem_misc_cod > 0) {
                    $nb_perso ++;
                    if ($p->aqelem_param_num_3 < 2) {
                        $isLauched = false ;
                    }
                }
            }

            // Si le départ a été lancé alors on prepart pour un nouveau depat => netoyage
            if ($isLauched && ($nb_perso>0) ){
                $req    = "delete from quetes.aquete_element where aqelem_aqetape_cod = :aqelem_aqetape_cod and aqelem_aqperso_cod is null and aqelem_param_id = 7 and aqelem_misc_cod != 0 ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":aqelem_aqetape_cod" => $aqperso->aqperso_etape_cod), $stmt);

                // Sortie pour saisie d'une nouvelle équipe !
                return $retour;
            }

        }


        // Chargement de la dernière parge du journal du perso
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        // Le joueurs sort l'équipe, les equipes sont complètes ou il a deja un membre de sa triplette!
        if (isset($_REQUEST["cancel"])) {
            //Compléter la dernière parge avec le dialogue:
            $perso_journal->aqpersoj_texte .= "   Vous avez décidé de ne pas intégrer d'équipe!<br> ";
            $perso_journal->stocke();

            $retour->status = true ;  // => Le joueur souhaite ne pas intégrer d'équipe sortie etape d'erreur
            $retour->etape = $p6->aqelem_misc_cod ;
            return $retour ;
        }

        // vérifier  équipe dejà complète ou triplette non-autorisée ==========!
        $perso = new perso();
        $perso->charge($perso_cod);
        $perso_team = false ;
        $perso_team_row = -1 ;
        $perso_team_col = -1 ;
        $perso_idx = -1 ;
        $perso_triplette = false ;
        $equipe_perso = [] ; //comptage des membres de chaque equipe
        foreach ($p7 as $k => $p) {
            if ($p->aqelem_misc_cod > 0) {

                // repartition des équipe
                $team = $p->aqelem_param_num_1;
                $equipe_row = 0;
                while (isset($equipe_perso[$equipe_row][$p->aqelem_param_num_1])) $equipe_row++;
                $equipe_perso[$equipe_row][$team] = ["perso_cod" => $p->aqelem_misc_cod, "etat" => $p->aqelem_param_num_2];

                if ($p->aqelem_misc_cod == $perso_cod) {
                    // on a trouvé le perso, mais il a peut-êtrre dejà été dispacthé, et reivent pour la seconde fois, on va le rejetter
                    if ($p->aqelem_param_num_3 == 2) {
                        $perso_journal->aqpersoj_texte .= "   C'est trop tôt pour revenir dans une équipe!<br> ";
                        $perso_journal->stocke();

                        $retour->status = true ;  // => Plus de place pour ce joueur direction la sortie
                        $retour->etape = $p6->aqelem_misc_cod ;
                        return $retour;     //
                    }
                    // Si les perso ne boucle pas, on memo son id et équipe
                    $perso_idx = $k ;
                    $perso_team_row = $equipe_row ;
                    $perso_team_col = $p->aqelem_param_num_1 ;
                    $perso_team = true;
                }
                if (($p->aqelem_misc_cod != $perso_cod) && $perso->membreTriplette($p->aqelem_misc_cod)) {
                    $perso_triplette = true;
                }

            }
        }

        // Rejeter les anomalies: triplette non-autorisée ==========!
        if ($perso_triplette && $triplette) {
            //Compléter la dernière parge avec le dialogue:
            $perso_journal->aqpersoj_texte .= "   Des membres d'une même triplette ne sont pas autorisés dans les équipes!<br> ";
            $perso_journal->stocke();

            $retour->status = true ;  // => Plus de place pour ce joueur direction la sortie
            $retour->etape = $p6->aqelem_misc_cod ;
            return $retour;     //
        }

        // Rejeter les anomalies: équipe dejà complète ==========!
        if ( (!$perso_team || ($perso_team_row == -1) || ($perso_idx == -1)) && $TeamsReady) {
            //Compléter la dernière parge avec le dialogue:
            $perso_journal->aqpersoj_texte .= "   Les équipes sont maintenant complètes!<br> ";
            $perso_journal->stocke();

            $retour->status = true ;  // => Plus de place pour ce joueur direction la sortie
            $retour->etape = $p6->aqelem_misc_cod ;
            return $retour;     //
        }

        // Countdown is over??
        if ( !$TeamsReady ) return $retour ; // le joueur est toujours en cours de selection de sa trnasaction

        // dispatch en fonction des parametres 4 et 5
        $dispatch = $perso_team_row ;
        foreach($p4 as $k => $p){
            if (($dispatch < $p->aqelem_param_num_1) || ($p->aqelem_param_num_1 == 0)) {

                // Marqué le perso comme déjà déjà passé!
                $element = new aquete_element();
                $element->charge( $p7[$perso_idx]->aqelem_cod );
                $element->aqelem_param_num_3 = 2 ;
                $element->stocke();

                $retour->status = true ;  // => Plus de place pour ce joueur direction la sortie
                $retour->etape = $p5[$perso_team_col*count($p4)+$k]->aqelem_misc_cod ;
                //echo "<pre>"; print_r([$retour, $perso_team_row, $perso_team_col, $equipe_perso, $p4, $p5]); die();
                return $retour;
            }
            $dispatch = $dispatch - $p->aqelem_param_num_1 ;
        }

        // Sortie par défaut!! demander une autre saisie du joueur
        //echo "<pre>===>not found"; print_r([$retour, $perso_team_row, $equipe_perso, $p4]); die();
        return $retour;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie =>  '[1:valeur|1%0],[2:etape|1%1],[3:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_pa(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;

        // ON vérifie que le joueru a bien dis qq chose avant d'anlyser ses paraoles
        if (!isset($_REQUEST["dialogue"]) || $_REQUEST["dialogue"] == "")
        {
            return $retour;     // on ne compte pas ça comme une tentative!
        }
        $dialogue = $_REQUEST["dialogue"] ;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "valeur" )) return $retour ;                      // Problème lecture (blocage)
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "etape" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape" )) return $retour ;    // Problème lecture (blocage)

        $nb_pa = $p1->aqelem_param_num_1 ;
        $perso = new perso();
        $perso->charge( $aqperso->aqperso_perso_cod );

        // refus ou pas assez de PA
        if ($perso->perso_pa<$nb_pa || $dialogue != 'O')
        {
            $retour->status = true ;  // l'étape est pas terminée sur un fail !
            $retour->etape = $p2->aqelem_misc_cod;
            return $retour;
        }

        // Consommer les PA
        $perso->perso_pa = $perso->perso_pa - $nb_pa ;
        $perso->stocke();

        $retour->status = true ;  // l'étape est pas terminée sur un success !
        $retour->etape = $p3->aqelem_misc_cod;
        return $retour;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie =>  '[1:valeur|1%0],[2:etape|1%1],[3:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_code(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;

        // ON vérifie que le joueru a bien dis qq chose avant d'anlyser ses paraoles
        if (!isset($_REQUEST["dialogue"]) )
        {
            return $retour;     // on ne compte pas ça comme une tentative!
        }
        $dialogue =  $_REQUEST["dialogue"] ;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "selecteur" )) return $retour ;                      // Problème lecture (blocage)
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "texte" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "valeur" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "etape" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "etape" )) return $retour ;    // Problème lecture (blocage)

        $nb_pa = $p3->aqelem_param_num_1 ;
        $perso = new perso();
        $perso->charge( $aqperso->aqperso_perso_cod );

        // refus ou pas assez de PA
        if ($perso->perso_pa<$nb_pa || $dialogue == '')
        {
            $retour->status = true ;  // l'étape est terminée sur un fail !
            $retour->etape = $p4->aqelem_misc_cod;
            return $retour;
        }

        // Consommer les PA
        $perso->perso_pa = $perso->perso_pa - $nb_pa ;
        $perso->stocke();

        // Code faux
        if ($dialogue != $p2->aqelem_param_txt_1)
        {
            $retour->status = true ;  // l'étape est terminée sur un fail !
            $retour->etape = $p4->aqelem_misc_cod;
            return $retour;
        }

        $retour->status = true ;  // l'étape est terminée sur un success !
        $retour->etape = $p5->aqelem_misc_cod;
        return $retour;
    }



    //==================================================================================================================
    /**
     * saut suit état de mécanisme =>  '[1:meca_etat|0%0],[2:valeur|1%1],[3:etape|1%1],[4:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_meca(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape", 1)) return 0 ;                      // Problème lecture passage à l'etape suivante
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "etape", 1)) return $p3->aqelem_misc_cod ;   // Problème lecture passage à l'etape d'erreur
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, "meca_etat", 0)) return $p3->aqelem_misc_cod ;              // Problème lecture passage à l'etape suivante
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur", 1))return $p3->aqelem_misc_cod  ;   // Problème lecture passage à l'etape suivante

        // nombre de meca dans le bon état attendu.
        $nb_attendu = $p2->aqelem_misc_cod ? $p2->aqelem_misc_cod : count($p1) ;

        //passage en revue des mots attendus (dans l'ordre)
        $nb_meca_ok = 0 ;
        foreach ($p1 as $e => $elem)
        {
            $meca = new meca();
            $meca->charge($elem->aqelem_misc_cod);
            $etat = $meca->get_etat($elem->aqelem_param_num_3);

            if ($etat["nb_total"] > 0)
            {
                if (($elem->aqelem_param_num_1 == 0 && $etat["nb_actif"]==$etat["nb_total"]) ||($elem->aqelem_param_num_1 == -1 && $etat["nb_inactif"]==$etat["nb_total"]) )
                {
                    $nb_meca_ok ++ ; // le mécanisme est dans l'état demandé!
                }
            }
        }


        //y-a-t-il le quorum ?
        if ($nb_meca_ok < $nb_attendu) return $p3->aqelem_misc_cod ;

        // les conditions sont là !!!!
        return $p4->aqelem_misc_cod ;
    }


    //==================================================================================================================
    /**
     * saut en fonction d'un nombre d'objet détruit =>  '[1:valeur|1%1],[2:objet_generique|0%0],[3:etape|0%0],[4:etape|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_detruire_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                            // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, array('objet_generique', 'objet'), 0)) return false ;      // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'etape', 0)) return false ;      // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'etape')) return false ;      // Problème lecture des paramètres


        shuffle($p2);                                       // ordre aléatoire pour les objets

        $nbobj = $p1->aqelem_param_num_1 ;
        $nbgenerique = count ($p2) ;

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return  $p4->aqelem_misc_cod ;       // etape bizarre !! on n'attend aucun objet


        // Préparation de la liste des objets prendre en fonction du nombre de générique
        $liste_detruire = array() ;
        $liste_objet ="" ;
        $liste_generique ="" ;

        // On commence par chercher un exeplaire de chaque.
        foreach ($p2 as $k => $elem)
        {
            if ($elem->aqelem_type == "objet")
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_cod = ? order by random()";
            }
            else
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_gobj_cod = ? order by random() limit 1";
                $liste_generique .= $elem->aqelem_misc_cod . ",";
            }
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_objet .= $result["obj_cod"] . "," ;
                $liste_detruire[] = (int)$result["obj_cod"];
            }
            if (count($liste_detruire)==$nbobj) break;   // On en a assez!
        }

        // S'il y a plus de demande que de générique (et qu'il y a des generique) , il faut en prendre encore un peu
        if ((count($liste_detruire)<$nbobj) && ($liste_generique!="") && ($liste_objet!=""))
        {
            $nb = ($nbobj-count($liste_detruire));                               // nombre restant à prendre parmis les génériques
            $liste_generique = substr($liste_generique,0,-1);       // On retire les vigules finales
            $liste_objet = substr($liste_objet,0,-1);               // On retire les vigules finales
            $req = "select obj_cod, obj_poids 
                    from perso_objets 
                    join objets on obj_cod = perobj_obj_cod 
                    where perobj_perso_cod = ? 
                    and obj_gobj_cod in ({$liste_generique}) 
                    and obj_cod not in ({$liste_objet}) 
                    order by random() limit {$nb}";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod), $stmt);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_detruire[] = (int)$result["obj_cod"];
            }
        }

        if (count($liste_detruire) == 0) return  $p4->aqelem_misc_cod ; // on a rien trouvé on sort sur etape speciales

        // préparer l'index de l'étape de sortie
        $esortie = min (count($liste_detruire), count($p3)) - 1;

        // Il faut maintenant prendre les objets
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_detruire as $k => $obj_cod)
        {
            // Gestion de la suppression (detruire l'objet et ajouter evenement)
            $objet = new objets();
            if ($objet->charge($obj_cod))
            {
                $texte_evt = '[cible] s\'est séparé d\'un objet <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                  values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                    ":texte_evt"=> $texte_evt,
                    ":levt_attaquant" => $aqperso->aqperso_perso_cod,
                    ":levt_cible" => $aqperso->aqperso_perso_cod,
                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);

                // Suprimer l'objet
                $objet->supprime($obj_cod);        // On supprime l'objet !

                // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
                $elem = new aquete_element();
                $elem->aqelem_aquete_cod = $aqperso->aqperso_aquete_cod;
                $elem->aqelem_aqetape_cod = $aqperso->aqperso_etape_cod;
                $elem->aqelem_aqperso_cod = $aqperso->aqperso_cod;
                $elem->aqelem_quete_step = $aqperso->aqperso_quete_step;
                $elem->aqelem_param_id = 2;
                $elem->aqelem_type = 'objet';
                $elem->aqelem_misc_cod =  $objet->obj_cod ;
                $elem->aqelem_param_ordre =  $param_ordre ;         // On ordonne correctement !
                $param_ordre ++ ;
                $elem->stocke(true);                           // sauvegarde du clone forcément du type objet (instancié)
            }
        }

        // sortie en fonction du nombre d'objet détruits
        return $p3[$esortie]->aqelem_misc_cod ;

    }


    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie =>  '[1:position|1%0],[2:valeur|1%1],[3:etape|1%1],[4:choix_etape|1%0]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function saut_condition_interaction(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;

        // ON vérifie que le joueru a bien dis qq chose avant d'anlyser ses paraoles
        if (!isset($_REQUEST["dialogue"]) || $_REQUEST["dialogue"] == "")
        {
            return $retour;     // on ne compte pas ça comme une tentative!
        }
        $dialogue = $_REQUEST["dialogue"] ;
        $clean_dialogue = " ".$this->clean_string($dialogue)." ";

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, "valeur" )) return $retour ;                      // Problème lecture (blocage)
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, "etape" )) return $retour ;                       // Problème lecture (blocage)
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, "choix_etape", 0)) return $retour ;    // Problème lecture (blocage)
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, "texte", 0)) return $retour ;          // Problème lecture (blocage)

        // Recherche du PNJ (vérifier que le perso est sur sa case pour discuter)
        if ( !$aqperso->action->move_position($aqperso, 1) )
        {
            return false;       // Pas en position pour réaliser l'action!
        }


        // On note dans le journal la réponse du joueur
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        //Compléter la dernière parge avec le dialogue:
        $perso_journal->aqpersoj_texte .= "   Vous: {$dialogue}<br> ";
        $perso_journal->stocke();

        //Ajout d'une tentative !
        $p2->aqelem_param_num_2++;
        $p2->stocke();

        //passage en revue des mots attendus (dans l'ordre)
        foreach ($p4 as $e => $elem)
        {
            $nb_mots = 0 ;
            $mots_attendus = explode("|", $elem->aqelem_param_txt_1);
            $conjonction = $elem->aqelem_param_num_2;     // 0=>ET 1=>OU
            foreach ($mots_attendus as $m =>$mot)
            {
                if (strpos($clean_dialogue, " ".$this->clean_string($mot)." ") !== false )
                {
                    $nb_mots++ ;
                }
            }

            // Vérification condition remplies (au moins un mot si OU(1) et tous les mots si ET(0)!
            if (($nb_mots>0 && $conjonction==1) || ($nb_mots==count($mots_attendus) && $conjonction==0))
            {
                // On supprime tous les dialogues qui n'ont pas été choisis
                $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array($elem->aqelem_cod));

                $retour->status = true ;  // l'étape n'est pas terminée, sur l'étape répondu
                $retour->etape = $elem->aqelem_misc_cod;
                return $retour;
            }
        }

        // Sortie sur nombre de tentatives infructueuses
        if (($p2->aqelem_param_num_1>0) && ($p2->aqelem_param_num_2>=$p2->aqelem_param_num_1))
        {
            $retour->status = true ;  // l'étape n'est pas terminée, sur l'étape spécifique
            $retour->etape = $p3->aqelem_misc_cod;
            return $retour;
        }

        // On va aider le joueur avec des textes, mettre le texte en fonction du nombre de tentative dans le journal
        $tentative = $p2->aqelem_param_num_2 - 1 ;
        if (count($p5)<=$tentative)
        {
            $tentative = count($p5) - 1;    // le dernier texte
        }
        $bavardage = $p5[$tentative]->aqelem_param_txt_1 ;
        if ($bavardage != "")
        {
            $perso_journal->aqpersoj_texte .= "<br>   {$bavardage}<br> ";
            $perso_journal->stocke();
        }

        // Sortie par défaut!! demander une autre saisie du joueur
        return $retour;
    }

    //==================================================================================================================
    /**
     * On recherche le n° d'étape suivant en fonction de la saisie =>  '[1:valeur|1%1],[2:etape|1%1],[3:choix_etape|1%0]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function echange_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        // Vérification d'usage
        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'echange', 0)) return false ;              // Problème lecture des paramètres

        // Recherche du PNJ
        $req = " select aqelem_cod, quete.perso_cod as pnj from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod,  perso_cod,ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
        if (!$result = $stmt->fetch(PDO::FETCH_ASSOC)) return false;       // pas sur la case du pnj
        $pnj = new perso();
        $pnj->charge($result["pnj"]);

        // Préparation du journal pour indiquer le résultat de l'échange
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        // Condition de sortie sans echange
        if ( count($p6) == 0 )
        {
            $perso_journal->aqpersoj_texte .= "   Il n'y a plus rien à acheter ici.<br>";
            $perso_journal->stocke();
            return true; // aucun achat
        }                                                                              // pas/plus d'objet on passe à l'étape suivante
        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue")
        {
            $nbitem = count(array_filter($p6, function($e){ return $e->aqelem_misc_cod != 0 ? true : false ; } ) );
            $perso_journal->aqpersoj_texte .= "   ".$nbitem." objet(s) sont disponible(s) à l'achat, vous décidez de ne rien acheter.<br>";
            $perso_journal->stocke();
            return true; // aucun achat
        }

        // On attend que le jueur valide son choix
        if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" || isset($_REQUEST["cancel"]) ) return false ; // le joueur est toujours en cours de selection de sa trnasaction

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


        // Il a validé!!!! On vérifie d'abord que le perso à de quoi payer
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        // inventaire du perso
        $trocs_en_stock = [] ;
        $req = "select obj_gobj_cod, count(*) as count  from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? group by obj_gobj_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod), $stmt);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $trocs_en_stock[$result["obj_gobj_cod"]] = (int)$result["count"] ;           // pour controle
        }

        // le joueur a validé, on vérifie qu'il a les objets nécéssaires
        $nbtrocs = 0 ;
        $nbitem = 0 ;
        $maxitem = 0 ;
        $enstock = true ;
        $bourse = $perso->perso_po ;
        $trocs_bzf = 0 ;
        foreach ($p6_matos as $k => $elem)
        {
            if (isset($_REQUEST["echange-{$k}"]) && (int)$_REQUEST["echange-{$k}"]>0)
            {
                $nbtrocs++;
                $nb = (int)$_REQUEST["echange-{$k}"] ;
                $maxitem = MAX($maxitem, $nb) ;
                $nbitem +=  $nb ;
                // Ici on bloucle sur les lignes de cout (car il peut y en avoir plusieurs)
                foreach ($p6_couts[$k] as $kk => $e)
                {
                    // check brouzouf
                    $trocs_bzf += $nb * (int)$e->aqelem_param_txt_1;
                    $bourse = $bourse - $nb * (int)$e->aqelem_param_txt_1;
                    if ($bourse < 0)
                    {
                        $enstock = false;
                    }

                    if ((!isset($trocs_en_stock[$e->aqelem_param_num_2]) || ($trocs_en_stock[$e->aqelem_param_num_2] < $nb * (int)$e->aqelem_param_num_3)) && ((int)$e->aqelem_param_num_2 > 0 && (int)$e->aqelem_param_num_3 > 0))
                    {
                        $enstock = false;
                    }
                    else if ((int)$e->aqelem_param_num_2 > 0 && (int)$e->aqelem_param_num_3 > 0)
                    {
                        $trocs_en_stock[$e->aqelem_param_num_2] = $trocs_en_stock[$e->aqelem_param_num_2] - $nb * (int)$e->aqelem_param_num_3;
                    }
                }
            }
        }

        // Erreur la selection du joueur n'est pas valide
        if (!$enstock || $nbtrocs==0 || ($nbtrocs>$p3->aqelem_param_num_1 && $p3->aqelem_param_num_1>0) || ($maxitem>$p4->aqelem_param_num_1 && $p4->aqelem_param_num_1>0) || ($nbitem>$p5->aqelem_param_num_1 && $p5->aqelem_param_num_1>0)) return false;

        //=============================  On réalise la transaction a proprement dit!!! =======================================
        // On traite d'abord le cas de Bzf
        if ($trocs_bzf>0)
        {
            $texte_evt = '[cible] fait du troc avec [attaquant] et lui donne '.$trocs_bzf.' Brouzoufs';
            $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                                      values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                ":texte_evt" => $texte_evt,
                ":levt_attaquant" => $pnj->perso_cod,
                ":levt_cible" => $aqperso->aqperso_perso_cod), $stmt);
            // Supprimer l'objet
            //
            $perso->perso_po = $perso->perso_po - $trocs_bzf ;
            $perso->stocke();       // Mise à jour de la bourse
        }

        // Il faut maintenant prendre les objets du joueur et lui donner ses achats
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 5, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($p6_matos as $k => $elem)
        {
            if (isset($_REQUEST["echange-{$k}"]) && (int)$_REQUEST["echange-{$k}"]>0)
            {
                $nb = (int)$_REQUEST["echange-{$k}"] ;      //nombre de fois où la transaction doit être faites

                // Ici on boucle sur les lignes de cout (car il peut y en avoir plusieurs)
                foreach ($p6_couts[$k] as $kk => $e)
                {
                    if ((int)$e->aqelem_param_num_2 > 0 && (int)$e->aqelem_param_num_3 > 0) {
                        // selectionner les objets a supprimer de l'inventaire du joueur
                        $req = "select perobj_cod, obj_cod from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_gobj_cod= ? limit ? ";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array($aqperso->aqperso_perso_cod, $e->aqelem_param_num_2, $nb * $e->aqelem_param_num_3), $stmt);

                        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
                        {
                            $objet = new objets();
                            if ($objet->charge((int)$result["obj_cod"]))
                            {
                                $texte_evt = '[cible] fait du troc avec [attaquant] et lui donne un objet  <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                            values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                                $stmt2 = $pdo->prepare($req);
                                $stmt2 = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                                    ":texte_evt" => $texte_evt,
                                    ":levt_attaquant" => $pnj->perso_cod,
                                    ":levt_cible" => $aqperso->aqperso_perso_cod,
                                    ":levt_parametres" => "[obj_cod]=" . $objet->obj_cod), $stmt2);
                                // Supprimer l'objet
                                $objet->supprime();        // On supprime l'objet !
                            }
                        }
                    }

                    // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
                    $e->aqelem_param_ordre = $param_ordre;         // On ordonne correctement !
                    $param_ordre++;
                    $e->stocke(true);                           // sauvegarde du clone forcément du type objet (instancié)
                }

                // instancier les X objets à mettre dans l'inventaire du joueur
                for ($nbobj = 0; $nbobj < $nb * $elem->aqelem_param_num_1; $nbobj++)
                {
                    $req = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);
                    if ($result = $stmt->fetch())
                    {
                        if (1 * $result["obj_cod"] > 0)
                        {
                            $objet = new objets();
                            $objet->charge((int)$result["obj_cod"]);

                            $texte_evt = '[cible] fait du troc avec [attaquant] et reçoit un objet  <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                            $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                        values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                            $stmt2 = $pdo->prepare($req);
                            $stmt2 = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                                ":texte_evt" => $texte_evt,
                                ":levt_attaquant" => $pnj->perso_cod,
                                ":levt_cible" => $aqperso->aqperso_perso_cod,
                                ":levt_parametres" => "[obj_cod]=" . $objet->obj_cod), $stmt2);
                        }
                    }
                }

            }
        }


        $perso_journal->aqpersoj_texte .= "   ".count($p6)." objet(s) sont disponible(s) à l'échange.<br>";
        $perso_journal->aqpersoj_texte .= "    Vous réalisez l'échange suivant: ".html_entity_decode($_REQUEST["troc-phrase"])."<br>";
        $perso_journal->stocke();
        return true; // aucun achat

    }

    //==================================================================================================================
    /**
     * Réparation d'objet =>  '[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1],[6:valeur|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function reparer_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        // Vérification d'usage
        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'type_objet', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'objet_generique', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'valeur')) return false ;                             // Nombre d'objet a reparer au max
        $tarif = $p5->aqelem_param_num_1;

        // Recherche du PNJ
        $req = " select aqelem_cod, quete.perso_cod as pnj from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod,  perso_cod,ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
        if (!$result = $stmt->fetch(PDO::FETCH_ASSOC)) return false;       // pas sur la case du pnj
        $pnj = new perso();
        $pnj->charge($result["pnj"]);

        // Préparation du journal pour indiquer le résultat de l'échange
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue")
        {
            $perso_journal->aqpersoj_texte .= "Vous décidez de ne rien faire réparer!<br>";
            $perso_journal->stocke();
            return true; // aucun achat
        }


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

        $req .= " order by obj_nom";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);
        if (!$p7 = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            $perso_journal->aqpersoj_texte .= "   Je ne vois rien que vous pouvez réparer ici...!<br>";
            $perso_journal->stocke();
            return true; // aucune réparation
        }

        if ( count($p7) == 0 )
        {
            $perso_journal->aqpersoj_texte .= "   Je ne vois rien que vous pouvez réparer ici..!!<br>";
            $perso_journal->stocke();
            return true; // aucun réparation
        }

        // On attend que le joueur valide son choix
        if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" || isset($_REQUEST["cancel"]) ) return false ; // le joueur est toujours en cours de selection de sa trnasaction


        // Il a validé!!!! On vérifie d'abord que le perso à de quoi payer
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        $nbtrocs = 0 ;
        $trocs_bzf = 0 ;
        $bourse = $perso->perso_po ;

        // le joueur a valider, on vérifie qu'il a l'argent et les objets nécéssaires (en sa possèsion)
        foreach ($p7 as $k => $objet)
        {
            if (isset($_REQUEST["echange-{$objet["obj_cod"]}"]))
            {
                $nbtrocs ++ ;
                $trocs_bzf = $trocs_bzf + ((100 - $objet["obj_etat"]) * $tarif);
            }
        }

        // Erreur la selection du joueur n'est pas valide (manque tune, ou l'objet n'est plus en sa possession)
        if ($bourse<$trocs_bzf || $nbtrocs==0) return false;

        // Erreur le joueur essaye de réparer plus d'objet qu'autorisé
        if ($nbtrocs>$p6->aqelem_param_num_1 && $p6->aqelem_param_num_1>0) return false;


        //=============================  On réalise la réparation a proprement dit!!! =======================================
        // On traite d'abord le cas de Bzf
        if ($trocs_bzf>0)
        {
            $texte_evt = '[cible] fait réparer du matos auprès de [attaquant] et lui donne '.$trocs_bzf.' Brouzoufs';
            $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                                      values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                ":texte_evt" => $texte_evt,
                ":levt_attaquant" => $pnj->perso_cod,
                ":levt_cible" => $aqperso->aqperso_perso_cod), $stmt);
            // Supprimer l'objet
            //
            $perso->perso_po = $perso->perso_po - $trocs_bzf ;
            $perso->stocke();       // Mise à jour de la bourse
        }

        // Il faut maintenant réparer les objets du joueur
        foreach ($p7 as $k => $objet)
        {
            if (isset($_REQUEST["echange-{$objet["obj_cod"]}"]))
            {
                $obj = new objets();
                if ($obj->charge((int)$objet["obj_cod"]))
                {
                    $texte_evt = '[cible] fait réparer  <em>(' . $obj->obj_cod . ' / ' . $obj->get_type_libelle() . ' / ' . $obj->obj_nom . ')</em>';
                    $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                    $stmt2 = $pdo->prepare($req);
                    $stmt2 = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                        ":texte_evt" => $texte_evt,
                        ":levt_attaquant" => $pnj->perso_cod,
                        ":levt_cible" => $aqperso->aqperso_perso_cod,
                        ":levt_parametres" => "[obj_cod]=" . $objet->obj_cod), $stmt2);
                    // réparer l'objet
                    $obj->obj_etat = 100 ;
                    $obj->stocke();        // On supprime l'objet !
                }
            }
        }


        $perso_journal->aqpersoj_texte .= "    Vous réalisez les réparations suivantes: ".html_entity_decode($_REQUEST["troc-phrase"])."<br>";
        $perso_journal->stocke();
        return true; // aucun achat

    }



    //==================================================================================================================
    /**
     * Rechargement d'objet =>  '[1:delai|1%1],[2:perso|1%0],[3:type_objet|1%0],[4:objet_generique|1%0],[5:valeur|1%1],[6:valeur|1%1]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function recharger_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        // Vérification d'usage
        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'type_objet', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'objet_generique', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'valeur')) return false ;                             // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'valeur')) return false ;                             // Nombre d'objet a reparer au max
        $tarif = $p5->aqelem_param_num_1;

        // Recherche du PNJ
        $req = " select aqelem_cod, quete.perso_cod as pnj from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod,  perso_cod,ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
        if (!$result = $stmt->fetch(PDO::FETCH_ASSOC)) return false;       // pas sur la case du pnj
        $pnj = new perso();
        $pnj->charge($result["pnj"]);

        // Préparation du journal pour indiquer le résultat de l'échange
        $perso_journal = new aquete_perso_journal();
        $perso_journal->chargeDernierePage($aqperso->aqperso_cod, $aqperso->aqperso_nb_realisation);

        if (isset($_REQUEST["cancel"]) && isset($_REQUEST["dialogue-echanger"]) && $_REQUEST["dialogue-echanger"]=="dialogue")
        {
            $perso_journal->aqpersoj_texte .= "Vous décidez de ne rien faire recharger!<br>";
            $perso_journal->stocke();
            return true; // aucun achat
        }


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

        $req .= " order by obj_nom";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);
        if (!$p7 = $stmt->fetchAll(PDO::FETCH_ASSOC))
        {
            $perso_journal->aqpersoj_texte .= "   Je ne vois rien que vous pouvez recharger ici...!<br>";
            $perso_journal->stocke();
            return true; // aucune réparation
        }

        if ( count($p7) == 0 )
        {
            $perso_journal->aqpersoj_texte .= "   Je ne vois rien que vous pouvez recharger ici..!!<br>";
            $perso_journal->stocke();
            return true; // aucun réparation
        }

        // On attend que le joueur valide son choix
        if ( $_REQUEST["dialogue-echanger"] != "dialogue-validation" || isset($_REQUEST["cancel"]) ) return false ; // le joueur est toujours en cours de selection de sa trnasaction


        // Il a validé!!!! On vérifie d'abord que le perso à de quoi payer
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        $nbtrocs = 0 ;
        $trocs_bzf = 0 ;
        $bourse = $perso->perso_po ;

        // le joueur a valider, on vérifie qu'il a l'argent et les objets nécéssaires (en sa possèsion)
        foreach ($p7 as $k => $objet)
        {
            if (isset($_REQUEST["echange-{$objet["objsort_cod"]}"]))
            {
                $nbtrocs ++ ;
                $trocs_bzf = $trocs_bzf + (($objet["objsort_nb_utilisation"]) * $tarif);
            }
        }

        // Erreur la selection du joueur n'est pas valide (manque tune, ou l'objet n'est plus en sa possession)
        if ($bourse<$trocs_bzf || $nbtrocs==0) return false;

        // Erreur le joueur essaye de réparer plus d'objet qu'autorisé
        if ($nbtrocs>$p6->aqelem_param_num_1 && $p6->aqelem_param_num_1>0) return false;


        //=============================  On réalise le rechargement a proprement dit!!! =======================================
        // On traite d'abord le cas de Bzf
        if ($trocs_bzf>0)
        {
            $texte_evt = '[cible] fait recharger du matos auprès de [attaquant] et lui donne '.$trocs_bzf.' Brouzoufs';
            $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                                      values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                ":texte_evt" => $texte_evt,
                ":levt_attaquant" => $pnj->perso_cod,
                ":levt_cible" => $aqperso->aqperso_perso_cod), $stmt);
            // Supprimer l'objet
            //
            $perso->perso_po = $perso->perso_po - $trocs_bzf ;
            $perso->stocke();       // Mise à jour de la bourse
        }

        // Il faut maintenant recharger les objets du joueur
        foreach ($p7 as $k => $objet)
        {
            if (isset($_REQUEST["echange-{$objet["objsort_cod"]}"]))
            {
                $objsort = new objets_sorts();
                if ($objsort->charge((int)$objet["objsort_cod"]))
                {
                    $obj = new objets();
                    if ($obj->charge($objsort->objsort_obj_cod))
                    {
                        $texte_evt = '[cible] fait recharger  <em>(' . $obj->obj_cod . ' / ' . $objsort->objsort_nom . ' / ' . $obj->get_type_libelle() . ' / ' . $obj->obj_nom . ')</em>' ;
                        $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                        $stmt2 = $pdo->prepare($req);
                        $stmt2 = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod,
                            ":texte_evt" => $texte_evt,
                            ":levt_attaquant" => $pnj->perso_cod,
                            ":levt_cible" => $aqperso->aqperso_perso_cod,
                            ":levt_parametres" => "[objsort_cod]=" . $objsort->objsort_cod), $stmt2);
                        // recharger l'objet
                        $objsort->objsort_nb_utilisation = 0 ;
                        $objsort->stocke();        // On sauvegarde l'objet !
                    }
                }
            }
        }


        $perso_journal->aqpersoj_texte .= "    Vous réalisez les rechargements suivantes: ".html_entity_decode($_REQUEST["troc-phrase"])."<br>";
        $perso_journal->stocke();
        return true; // aucun achat

    }

    //==================================================================================================================
    /**
     * Distribution en PX PO => '[1:texte|1%1|Titre]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function change_impalpabilite(aquete_perso $aqperso)
    {
        $element = new aquete_element();

        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'selecteur')) return false ;      // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;      // Problème lecture des paramètres

        $nb_tour = (int)$p2->aqelem_param_num_1 ;
        $texte_evt = "[attaquant] a été rendu ". ( $nb_tour > 0 ? "impalpable." : "palpable." ) ;

        $perso = new perso();
        if ( ! $perso->charge($aqperso->aqperso_perso_cod) ) return false ;

        // Mettre l'impalpabilité sur le meneur
        if ($p1->aqelem_misc_cod==1 || $p1->aqelem_misc_cod==3)
        {
            $perso->perso_tangible = $nb_tour > 0 ? 'N' : 'O' ;
            $perso->perso_nb_tour_intangible = $nb_tour  ;
            $perso->stocke();
            $evt = new ligne_evt();
            $evt->evenement(54, $texte_evt, $aqperso->aqperso_perso_cod );
        }

        // Mettre l'impalpabilité sur le familier (s'il y en a un)
        if ($p1->aqelem_misc_cod==2 || $p1->aqelem_misc_cod==3)
        {
            if ($fam_cod = $perso->get_familier())
            {
                $familier = new perso();
                if ( $familier->charge($fam_cod) )
                {
                    $familier->perso_tangible = $nb_tour > 0 ? 'N' : 'O' ;
                    $familier->perso_nb_tour_intangible = $nb_tour  ;
                    $familier->stocke();
                    $evt = new ligne_evt();
                    $evt->evenement(54, $texte_evt, $fam_cod );
                }
            }
        }

        return true;
    }


    //==================================================================================================================
    /**
     * Distribution en PX PO => '[1:texte|1%1|Titre]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function recevoir_titre(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'texte')) return false ;      // Problème lecture des paramètres

        if ($p1->aqelem_param_txt_1 != '')
        {
            $pdo = new bddpdo;

            // On vérifie si le perso n'a pas déjà ce titre
            $req = "select ptitre_cod from perso_titre where ptitre_perso_cod=:perso_cod and  ptitre_titre=:titre and ptitre_type=8 ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod"        => $aqperso->aqperso_perso_cod,
                                        ":titre"            => $p1->aqelem_param_txt_1 ), $stmt);
            if ($stmt->rowCount()==0)
            {
                $req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values (:perso_cod,:titre,now(),8)";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":perso_cod"        => $aqperso->aqperso_perso_cod,
                                            ":titre"            => $p1->aqelem_param_txt_1 ), $stmt);
            }
        }
        return true;
    }

    //==================================================================================================================
    /**
     *Distribution en PX PO => '[1:valeur|1%1|px],[2:valeur|1%1|po],[3:perso|0%1]'
    * @param aquete_perso $aqperso
    * @return bool
    */
    function recevoir_po_px(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 =
            $element->get_aqperso_element($aqperso, 1, 'valeur')) return false;      // Problème lecture des paramètres
        if (!$p2 =
            $element->get_aqperso_element($aqperso, 2, 'valeur')) return false;      // Problème lecture des paramètres
        $p3 =
            $element->get_aqperso_element($aqperso, 3, 'perso');                           // Ce paramètre est facultatif

        $px =
            min(200, $p1->aqelem_param_num_1);        // On donne avec un max de 200PX (au cas ou celui qui a definit la quete a fait une bourde
        $po = min(100000, $p2->aqelem_param_num_1);        // et max 100000 Bz

        $pdo = new bddpdo;

        $perso = new perso;
        $perso->charge($aqperso->aqperso_perso_cod);
        $perso->perso_px = $perso->perso_px + $px;
        $perso->perso_po = $perso->perso_po + $po;
        $perso->stocke();
        unset($perso);


        // Ajout des evenements pour le perso !!!

        $quete = new aquete();
        $quete->charge($aqperso->aqperso_aquete_cod);
        if ($p3->aqelem_misc_cod > 0)
        {
            // la récompense est donnée par un perso on personalise l'evenement
            if ($px > 0)
            {
                $texte_evt = "[attaquant] a donné {$px} PX à [cible]." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);

                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $p3->aqelem_misc_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "[attaquant] a donné {$po} brouzoufs à [cible]." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);

                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" =>  $p3->aqelem_misc_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod, ":levt_cible" =>  $aqperso->aqperso_perso_cod ), $stmt);
            }
        }
        else
        {
            // Pas de perso, on met un texte generique
            if ($px>0)
            {
                $texte_evt = "[cible] reçoit {$px} PX." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "[cible] reçoit {$po} brouzoufs." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * On verifie si le perso est sur la case d'un autre (un parmi plusieurs) =>  '[1:delai|1%1],[2:perso|1%0]'
     * On utilise aussi cette fontion pour vérifier que le joueur est sur la case d'un PNJ dans ce cas le paramètre perso n'est pas forcément le N° 2
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function move_perso(aquete_perso $aqperso, $param_id=2)
    {
        // Il peut y avoir une liste de perso possible, on regarde directement par une requete s'il y en a un (plutôt que de faire une boucle sur tous les éléments)
        $pdo = new bddpdo;
        $req = " select aqelem_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=:perso_cod
                join 
                ( 
                    select aqelem_cod, ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=:param_id and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=:aqperso_cod
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);

        //print_r(array('req' =>$req, ':perso_cod' => $aqperso->aqperso_perso_cod, ':aqperso_cod' => $aqperso->aqperso_cod, ':param_id' => $param_id)); die();
        $stmt   = $pdo->execute(array(':perso_cod' => $aqperso->aqperso_perso_cod, ':aqperso_cod' => $aqperso->aqperso_cod, ':param_id' => $param_id), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }
        $result = $stmt->fetch();

        // On doit supprimer tous les autres éléments de ce step pour ce perso, on ne garde que le paramètre trouvé!
        $element = new aquete_element();
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, $param_id, array(0=>$result["aqelem_cod"]));

        return true;
    }

    //==================================================================================================================
    /**
     * On verifie si le perso est sur une position (un parmi plusieurs) =>  '[1:delai|1%1],[2:position|1%0]'
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function move_position(aquete_perso $aqperso, $param_id=2)
    {

        // Il peut y avoir une liste de position possible, on regarde directement par une requete s'il y en a un (plutôt que de faire une boucle sur tous les éléments)
        $pdo = new bddpdo;
        $req = " select aqelem_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=:perso_cod
                join 
                ( 
                    select aqelem_cod, aqelem_misc_cod as pos_cod
                    from quetes.aquete_perso
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=:param_id and aqelem_type='position'
                    where aqperso_cod=:aqperso_cod
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(':perso_cod' => $aqperso->aqperso_perso_cod, ':aqperso_cod' => $aqperso->aqperso_cod, ':param_id' => $param_id), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }
        $result = $stmt->fetch();

        // On doit supprimer tous les autres éléments de ce step pour ce perso, on ne garde que le lieu sur lequel il c'est rendu!
        $element = new aquete_element();
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, $param_id, array(0=>$result["aqelem_cod"]));

        return true;
    }
    //==================================================================================================================
    /**
     * On verifie si le perso est sur la case d'un lieu (un parmi plusieurs) =>  '[1:delai|1%1],[2:lieu|1%0]'
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function move_typelieu(aquete_perso $aqperso)
    {

        // Il peut y avoir une liste de perso possible, on regarde directement par une requete s'il y en a un (plutôt que de faire une boucle sur tous les éléments)
        $pdo = new bddpdo;
        $req = " select aqelem_cod, lieu_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod, lpos_pos_cod as pos_cod, lieu_cod
                    from quetes.aquete_perso
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='lieu_type'
                    join lieu on lieu_tlieu_cod=aqelem_misc_cod
                    join lieu_position on lpos_lieu_cod=lieu_cod
                    join positions on pos_cod=lpos_pos_cod                     
                    join etage on etage_numero=pos_etage and etage_reference<=aqelem_param_num_1 and etage_reference>=aqelem_param_num_2
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
        if ($stmt->rowCount()==0)
        {
            return false;
        }
        $result = $stmt->fetch();

        // On doit supprimer tous les autres éléments de ce step pour ce perso, on ne garde que le lieu sur lequel il c'est rendu!
        $element = new aquete_element();
        $element->charge($result["aqelem_cod"]);    // on charge avant de tout supprimer
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array());
        $element->aqelem_type = 'lieu';
        $element->aqelem_misc_cod =  (int)$result["lieu_cod"] ;   // Transforme l'élément type de lieu en lieu avec le code du lieu
        $element->stocke(true);                                // sauvegarde d'un nouvel élément
        return true;
    }

    //==================================================================================================================
    /**
     * Dans cette étape on demande au joueur d''explorer une ou plusieurs zones spécifiques ou de visiter un certain % d''étages =>  '[1:delai|1%1],[2:position|0%0],[3:valeur|0%0],[4:valeur|0%0]'
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function move_visiter_zone(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'position', 0)) return false ;                    // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur', 0)) return false ;                    // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur', 0)) return false ;                    // Problème lecture des paramètres


        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        $nb_zone = sizeof($p2);

        // etendre les parametres 3 et 4 s'ils sont inférieur au nombre de position
        for ($k = sizeof($p3); $k<$nb_zone; $k++ )
        {
            $p3[$k] = $p3[$k-1];
        }
        for ($k = sizeof($p4); $k<$nb_zone; $k++ )
        {
            $p4[$k] = $p4[$k-1];
        }

        //echo "<pre>"; print_r([$nb_zone, $p2, $p3, $p4]); //die();

        // vérifier toutes les conditions
        foreach ($p2 as $k => $elem)
        {
            $visite = $perso->visite_etage($elem->aqelem_misc_cod, $p3[$k]->aqelem_param_num_1 == 0 ? null : $p3[$k]->aqelem_param_num_1) ;
            //echo "\n<br> % etage visité {$elem->aqelem_misc_cod} %requis={$p4[$k]->aqelem_param_num_1}  => taille {$p3[$k]->aqelem_param_num_1} :".$visite;
            if ($visite < $p4[$k]->aqelem_param_num_1)
            {
                return false ;      // au moins une des conditions de visite n'est pas vérifiée
            }

        }

        return true;
    }



    //==================================================================================================================
    /**
     * Le joeuur reçoit un bonus/malus =>  '[1:valeur|1%1],[2:bonus|0%0]'
     * Nota: Etape automatiquement réussi
     * @param aquete_perso $aqperso
     * @return bool
     */
    function recevoir_bonus(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                    // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'bonus', 0)) return false ;       // Problème lecture des paramètres

        shuffle($p2);                                       // ordre aléatoire pour les objets

        $nbdon = $p1->aqelem_param_num_1 ;
        $nbbonus = count ($p2) ;

        // Vérification sur le nombre d'objet
        if ($nbdon <= 0) return true;       // etape bizarre !! on ne donne aucun bonus/malus

        // Préparation de la liste des bonus/malus donner en fonction du nombre demandé
        $liste_bonus = array() ;
        if ($nbdon > $nbbonus) $nbdon = $nbbonus;
        // On donne les bonus dans la limite demandé (aléatoirement)
        for ($i=0; $i<$nbdon; $i++) $liste_bonus[$i] = clone $p2[$i];

        // le sbonus sont appliqué directment
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_bonus as $k => $elem)
        {

            // instancier l'objet générique
            $req = "select ajoute_bonus(:perso_cod, tbonus_libc, :duree, :valeur) from bonus_type where tbonus_cod = :tbonus_cod ;  ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":tbonus_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod , ":duree" =>  $elem->aqelem_param_num_2, ":valeur" =>  $elem->aqelem_param_num_1), $stmt);
            if ($result = $stmt->fetch())
            {
                $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                $param_ordre ++ ;
                $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
            }
        }

        return true;
    }


    //==================================================================================================================
    /**
     * Le joeuur reçoit un objet =>  '[1:valeur|1%1],[2:objet_generique|0%0]'
     * Nota: Etape automatiquement réussi
     * @param aquete_perso $aqperso
     * @return bool
     */
    function recevoir_instant_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                              // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'objet_generique', 0)) return false ;       // Problème lecture des paramètres
        $p3 = $element->get_aqperso_element( $aqperso, 3, 'selecteur') ;        // ce parametre est optionnel(innexistant dans les premières versions)
        $p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur') ;           // ce parametre est optionnel(innexistant dans les premières versions)

        // Parametre de dispertion pour distribution au sol!
        $dispersion = $p4 ? $p4->aqelem_param_num_1 : 0 ;

        // Recherche de la zone centrale de départ
        $perso = new perso();
        $perso->charge( $aqperso->aqperso_perso_cod );
        $perso_pos_pos = $perso->get_position()["pos"]->pos_cod ;

        shuffle($p2);                                       // ordre aléatoire pour les objets

        $nbobj = $p1->aqelem_param_num_1 ;
        $nbgenerique = count ($p2) ;

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on ne donne aucun objet

        // Préparation de la liste des objets donner en fonction du nombre de générique et du nombre d'objet à donner
        $liste_objet = array() ;
        if ($nbobj <= $nbgenerique)
        {
            // On donne les objets dans la limite demandé (aléatoirement)
            for ($i=0; $i<$nbobj; $i++) $liste_objet[$i] = clone $p2[$i];
        }
        else
        {
            for ($i=0; $i<$nbgenerique; $i++) $liste_objet[$i] = clone $p2[$i];        // Chaque objet au moins 1x
            for ($i=$nbgenerique; $i<$nbobj; $i++)
            {
                $liste_objet[$i] = clone $p2[rand(0,($nbgenerique-1))];                    // Le reste est aléatoire
            }
        }


        // on fait l'échange, on génère les objets à partir du générique (la liste contient tout ce qui doit-être donné)
        // et ils sont directement mis dans l'inventaire du joueur
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_objet as $k => $elem)
        {
            if ($p3 && $p3->aqelem_misc_cod==1)
            {
                // drop de l'objet au sol
                $req = "select cree_objet_pos(:gobj_cod, pos_alentour(:pos_cod, :dispersion)) as obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":pos_cod" => $perso_pos_pos, ":dispersion" => $dispersion  ), $stmt);

                if ($result = $stmt->fetch())
                {
                    if ((int)$result["obj_cod"] > 0)
                    {
                        $objet = new objets();
                        $objet->charge((int)$result["obj_cod"]);

                        // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                        $elem->aqelem_type = 'objet';
                        $elem->aqelem_misc_cod =  $objet->obj_cod ;
                        $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                        $param_ordre ++ ;
                        $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                    }
                }
            }
            else
            {
                // mettre l'objet directement dans l'inventaire du meneur de quete

                // instancier l'objet générique
                $req = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod  ), $stmt);

                if ($result = $stmt->fetch())
                {
                    if ((int)$result["obj_cod"]>0)
                    {
                        $objet = new objets();
                        $objet->charge((int)$result["obj_cod"]);

                        if ($p3 && $p3->aqelem_misc_cod==2)
                        {   // objet directement équipé (=> avec déséquipement d'un objet si limite max d'objet equipable atteinte)
                            $req = "select equipe_objet(:perso_cod, :obj_cod, 0 ,1)"; // équiper en forçant le remplacement pour 0 PA
                            $stmt   = $pdo->prepare($req);
                            $stmt   = $pdo->execute(array(":obj_cod" => $objet->obj_cod, ":perso_cod" => $aqperso->aqperso_perso_cod  ), $stmt);
                        }

                        $texte_evt = '[attaquant] a reçu un objet  <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                        $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                              values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                        $stmt   = $pdo->prepare($req);
                        $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                            ":texte_evt"=> $texte_evt,
                            ":levt_attaquant" => $aqperso->aqperso_perso_cod ,
                            ":levt_cible" => $aqperso->aqperso_perso_cod ,
                            ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);
                        // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                        $elem->aqelem_type = 'objet';
                        $elem->aqelem_misc_cod =  $objet->obj_cod ;
                        $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                        $param_ordre ++ ;
                        $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                    }
                }
            }

        }

        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si le perso est sur la case du donateur =>  '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]'
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function recevoir_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                        // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, array('objet_generique', 'objet'), 0)) return false ;       // Problème lecture des paramètres

        shuffle($p4);                                       // ordre aléatoire pour les objets


        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);
        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification de la position!
        //$pnj = new perso();
        //$pnj->charge($p2->aqelem_misc_cod);
        //if ( $perso->get_position()["pos"]->pos_cod != $pnj->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son pnj

        // On peut maintenant avoir 1 parmi plusieurs, on regarde s'il y en a un qui est sur la case du joueur
        if ( !$aqperso->action->move_perso($aqperso, 2) )  return false;       // Pas en position pour réaliser l'action! (Aucun perso de la liste n'est sur la case du joueur

        // "move_perso" supprime les autres éléments de type perso, et ne garde que le PNJ qui a été choisi, on peut maintant le charger
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                                         // Problème lecture des paramètres
        $pnj = new perso();
        $pnj->charge($p2->aqelem_misc_cod);

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on ne donne aucun objet

        // Préparation de la liste des objets donner en fonction du nombre de générique et du nombre d'objet à donner
        $liste_objet = array() ;
        if ($nbobj <= $nbgenerique)
        {
            // On donne les objets dans la limite demandé (aléatoirement)
            for ($i=0; $i<$nbobj; $i++) $liste_objet[$i] = clone $p4[$i];
        }
        else
        {
            for ($i=0; $i<$nbgenerique; $i++) $liste_objet[$i] = clone $p4[$i];        // Chaque objet au moins 1x
            for ($i=$nbgenerique; $i<$nbobj; $i++)
            {
                $liste_objet[$i] = clone $p4[rand(0,($nbgenerique-1))];                    // Le reste est aléatoire
            }
        }


        // on fait l'échange, on génère les objets à partir du générique (la liste contient tout ce qui doit-être donné)
        // et ils sont directement mis dans l'inventaire du joueur
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_objet as $k => $elem)
        {
            $isExchanged = false ;  // par defaut l'objet n'est pas echangé!
            if ($elem->aqelem_type == 'objet_generique')
            {
                // Si c'est un objet générique alors l'instancier
                $req = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod  ), $stmt);
                $isExchanged = true ;   // l'objet n'a pas vraiment été échangé, il a été créé directement pour le perso mais on fait comme si.
            }
            else
            {
                // on s'assure que le pnj dispose bien de l'objet
                $req = "select count(*) count from perso_objets where perobj_perso_cod=:perobj_perso_cod and perobj_obj_cod=:perobj_obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":perobj_perso_cod" => $pnj->perso_cod, ":perobj_obj_cod" => $elem->aqelem_misc_cod), $stmt);

                $result = $stmt->fetch();

                if ((int)$result["count"]>0)
                {
                    // on retire l'objet du donneur
                    $req = "delete from perso_objets where perobj_perso_cod=:perobj_perso_cod and perobj_obj_cod=:perobj_obj_cod ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array(":perobj_perso_cod" => $pnj->perso_cod, ":perobj_obj_cod" => $elem->aqelem_misc_cod), $stmt);

                    // on l'ajoute dans l'inventaire de l'aventurier
                    $req = "select count(*) count from perso_identifie_objet where pio_obj_cod=:pio_obj_cod and pio_perso_cod=:pio_perso_cod  ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array(":pio_perso_cod" => $aqperso->aqperso_perso_cod, ":pio_obj_cod" => $elem->aqelem_misc_cod), $stmt);
                    $result = $stmt->fetch();

                    // on l'ajoute dans l'inventaire de l'aventurier
                    $req = "insert into perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie) values (:perobj_perso_cod, :perobj_obj_cod, :perobj_identifie) returning perobj_obj_cod as obj_cod  ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array(":perobj_perso_cod" => $aqperso->aqperso_perso_cod, ":perobj_obj_cod" => $elem->aqelem_misc_cod, ":perobj_identifie" => ((int)$result["count"] > 0 ? 'O' : 'N') ), $stmt);

                    $isExchanged = true ;
                }
            }
            if ($isExchanged && $result = $stmt->fetch())
            {
                if ((int)$result["obj_cod"]>0)
                {
                    $objet = new objets();
                    $objet->charge((int)$result["obj_cod"]);

                    $texte_evt = '[cible] a donné un objet à [attaquant] <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                    $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                              values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                                                    ":texte_evt"=> $texte_evt,
                                                    ":levt_attaquant" => $aqperso->aqperso_perso_cod ,
                                                    ":levt_cible" => $pnj->perso_cod ,
                                                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);

                    $texte_evt = '[attaquant] a donné un objet à [cible] <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                    $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                              values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $pnj->perso_cod ,
                                                    ":texte_evt"=> $texte_evt,
                                                    ":levt_attaquant" => $pnj->perso_cod ,
                                                    ":levt_cible" => $aqperso->aqperso_perso_cod ,
                                                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);
                    // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                    $elem->aqelem_type = 'objet';
                    $elem->aqelem_misc_cod =  $objet->obj_cod ;
                    $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                    $param_ordre ++ ;
                    $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                }
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si les transaction sont bonnes =>  '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]'
    * Attention à la place des objet générique il peut y avoir des objets réelement instanciés (on vérifiera les cod)
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function remettre_objet(aquete_perso $aqperso)
    {   //echo "<pre>"; print_r($aqperso);echo "<pre>"; die();
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso', 0)) return false ;                             // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                        // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, array('objet_generique', 'objet'), 0)) return false ;      // Problème lecture des paramètres

        shuffle($p4);                                       // ordre aléatoire pour les objets

        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);
        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification de la position: Il y a maintenant plusieur pnj possible p2 est une liste de perso
        //$pnj = new perso();
        //$pnj->charge($p2->aqelem_misc_cod);
        //if ( $perso->get_position()["pos"]->pos_cod != $pnj->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son pnj

        // on prépare la liste de pnj
        $pnj_cod = 0 ;
        $pnj_cod_list = "";
        foreach ($p2 as $k => $elem) $pnj_cod_list.=$elem->aqelem_misc_cod.",";
        $pnj_cod_list.= "0";       // on rajoute un code 0 pour être sûr de ne pas avoir une liste vide et une query qui planterai à cause de ça

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on n'attend aucun objet

        //préparer la liste transaction pour les objets attendu
        $liste_transaction = array() ;

        // Pour le comptage des exemplaires
        $exemplaires = array();
        for ($i=0; $i<count($p4); $i++) $exemplaires[$i] = clone $p4[$i];


        // Recherche des transaction en cours avec le perso qui correspondent aux objets attendus!
        $req = "select tran_cod, tran_obj_cod, tran_quantite, tran_acheteur from transaction where tran_acheteur in ({$pnj_cod_list}) and tran_vendeur=:tran_vendeur and tran_prix=0 ; ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array( ":tran_vendeur" => $aqperso->aqperso_perso_cod ), $stmt);


        $t = 0; // compteur de transaction
        $poids_transaction = 0 ;
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $pnj_cod = $result["tran_acheteur"]  ;      // on récupère le cod d'un pnj qui fait les transactions
            $tran_quantite = ((int)$result["tran_quantite"]) == 0 ? 1 : ((int)$result["tran_quantite"])  ;
            $objet = new objets();
            $objet->charge((int)$result["tran_obj_cod"]);

            // vérification un exemplaire de chaque !
            foreach ($exemplaires as $k => $elem)
            {
                if (    (($objet->obj_gobj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet_generique'))
                     || (($objet->obj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet')))
                {
                    $liste_transaction[$t] = $result ;
                    $liste_transaction[$t]["element"] = clone $elem ;   // agrder une trace de l'élément corespondant
                    $t ++ ;
                    unset($exemplaires[$k]);            // plus besoin de chercher celui-la
                    $tran_quantite -- ;
                    $poids_transaction += $objet->obj_poids ;
                    break;
                }
            }

            // peut-être qu'un même objet est présent en plusieurs exemplaires
            $flag = true ;
            while ( ($flag == true)  && ($tran_quantite>0) && ( $t<$nbobj))
            {
                $flag = false ;     // si on ne trouve pas un exemplaire on arrête de chercehr !
                foreach ($p4 as $k => $elem)
                {
                    if (    (($objet->obj_gobj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet_generique'))
                        || (($objet->obj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet')))
                    {
                        $liste_transaction[$t] = $result ;
                        $liste_transaction[$t]["element"] = clone $elem ;   // garder une trace de l'élément corespondant
                        $t ++ ;
                        $tran_quantite -- ;
                        $poids_transaction += $objet->obj_poids ;
                        $flag = true ;          // tant qu'on trouve on cherche
                        break;
                    }
                }
            }
        }


        // Si on demande plus d'objet qu'il y a de générique, il faut vérifier si on a un exemplaire de chaque
        if (($nbobj > $nbgenerique) && (count($exemplaires)>0)) return false;        // on a pas un exemplaire de chaque objet!
        if (count($liste_transaction)<$nbobj) return false;                          // tous les objets attendus ne sont pas là!

        // Il faut maintenant prendre les objets
        // Vérification du poids des ojets à transférer: on ne vérifie plus ça peut causer des blocages de QA et maintenant il peut y avaoit plusieurs pnj
        //if (($pnj->get_poids() + $poids_transaction) > (3 * $pnj->perso_enc_max))  return false; // un problème de surcharge du PNJ
        $obj_cod_liste = array();
        foreach ($liste_transaction as $k => $transac)
        {
            // Gestion de la transaction
            $objet = new objets();
            $objet->charge((int)$transac["tran_obj_cod"]);

            $req = "select accepte_transaction(:tran_cod) as resultat; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array( ":tran_cod" => (int)$transac["tran_cod"]), $stmt);
            if (!$result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                return false; // un problème lors de l'execution de la requete
            }
            $r = explode(";", $result["resultat"]);
            if ((int)$r[0]!=0)
            {
                return false; // un problème lors du transfert, le perso est peut-$etre trop chargé pour prendre plus d'objet
            }
            $obj_cod_liste[$k] = $objet->obj_cod;
            // si l'objet ne sert plus on le supprime //$objet->supprime();
        }

        // netoyyer l'étape de cette quete et mettre les objets réel
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array( $pnj_cod ) );
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer

        // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
        $param_ordre = 0 ;
        foreach ($liste_transaction as $k => $transac)
        {
            $elem = $transac["element"];
            $elem->aqelem_type = 'objet';
            $elem->aqelem_misc_cod = $obj_cod_liste[$k] ;
            $elem->aqelem_param_ordre = $param_ordre;         // On ordonne correctement !
            $param_ordre++;
            $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
        }
        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si les objets sont là =>  '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]'
    * Attention à la place des objet générique il peut y avoir des objets réelement instanciés (on vérifiera les cod)
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function remettre_objet_quete(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        //if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso', 0)) return false ;                                         // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                        // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, array('objet_generique', 'objet'), 0)) return false ;      // Problème lecture des paramètres

        shuffle($p4);                                       // ordre aléatoire pour les objets

        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);
        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification de la position:: Il y a maintenant une liste de perso et pas un seul PNJ
        //$pnj = new perso();
        //$pnj->charge($p2->aqelem_misc_cod);
        //if ( $perso->get_position()["pos"]->pos_cod != $pnj->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son pnj

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on n'attend aucun objet


        // Préparation de la liste des objets prendre en fonction du nombre de générique et du nombre d'objet à donner
        $liste_echange = array() ;
        $liste_objet ="" ;
        $liste_generique ="" ;

        // On commence par chercher un exeplaire de chaque.
        $poids_transaction = 0 ;
        foreach ($p4 as $k => $elem)
        {
            if ($elem->aqelem_type == "objet")
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_cod = ? order by random()";
            }
            else
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_gobj_cod = ? order by random() limit 1";
                $liste_generique .= $elem->aqelem_misc_cod . ",";
            }
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_objet .= $result["obj_cod"] . "," ;
                $liste_echange[] = (int)$result["obj_cod"];
                $poids_transaction += (float)$result["obj_poids"] ;
            }
            if (count($liste_echange)==$nbobj) break;   // On en a assez!
        }

        // S'il y a plus de demande que de générique (et qu'il y a des generique) , il faut en prendre encore un peu
        if (($nbobj > $nbgenerique) && ($liste_generique!="") && ($liste_objet!=""))
        {
            $nb = ($nbobj-$nbgenerique);                                         // nombre restant à prendre parmis les génériques
            $liste_generique = substr($liste_generique,0,-1);       // On retire les vigules finales
            $liste_objet = substr($liste_objet,0,-1);               // On retire les vigules finales
            $req = "select obj_cod, obj_poids 
                    from perso_objets 
                    join objets on obj_cod = perobj_obj_cod 
                    where perobj_perso_cod = ? 
                    and obj_gobj_cod in ({$liste_generique}) 
                    and obj_cod not in ({$liste_objet}) 
                    order by random() limit {$nb}";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod), $stmt);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_echange[] = (int)$result["obj_cod"];
                $poids_transaction += (float)$result["obj_poids"] ;
            }
        }

        if (count($liste_echange)<$nbobj) return false;       //il en manque !

        // Vérification du poids des ojets à transférer: on ne vérifie plus ça peut causer des blocages de QA et maintenant il peut y avaoit plusieurs pnj
        //if (($pnj->get_poids() + $poids_transaction) > (3 * $pnj->perso_enc_max))  return false; // un problème de surcharge du PNJ

        // On peut maintenant avoir 1 parmi plusieurs, on regarde s'il y en a un qui est sur la case du joueur
        if ( !$aqperso->action->move_perso($aqperso, 2) )  return false;       // Pas en position pour réaliser l'action! (Aucun perso de la liste n'est sur la case du joueur

        // "move_perso" supprime les autres éléments de type perso, et ne garde que le PNJ qui a été choisi, on peut maintant le charger
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                                         // Problème lecture des paramètres
        $pnj = new perso();
        $pnj->charge($p2->aqelem_misc_cod);


        // Il faut maintenant prendre les objets
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_echange as $k => $obj_cod)
        {
            // Gestion de la transaction (fare l'échange et ajouter evenement)
            $objet = new objets();
            if ($objet->charge($obj_cod))
            {

                // on retire l'objet du donneur (joueur)
                $req = "delete from perso_objets where perobj_perso_cod=:perobj_perso_cod and perobj_obj_cod=:perobj_obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":perobj_perso_cod" => $aqperso->aqperso_perso_cod, ":perobj_obj_cod" => $objet->obj_cod), $stmt);

                // on supprime aussi les eventuelles toutes les transactions sur cet objet
                $req = "delete from transaction where tran_obj_cod=:tran_obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array( ":tran_obj_cod" => $objet->obj_cod ), $stmt);

                // on l'ajoute dans l'inventaire du pnj (directement identifié pour lui)
                $req = "insert into perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie) values (:perobj_perso_cod, :perobj_obj_cod, 'O') returning perobj_obj_cod as obj_cod  ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":perobj_perso_cod" => $pnj->perso_cod, ":perobj_obj_cod" => $objet->obj_cod ), $stmt);

                $texte_evt = '[attaquant] a pris un objet à [cible] <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                  values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                    ":texte_evt"=> $texte_evt,
                    ":levt_attaquant" => $pnj->perso_cod,
                    ":levt_cible" => $aqperso->aqperso_perso_cod,
                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);

                // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
                $elem = new aquete_element();
                $elem->aqelem_aquete_cod = $aqperso->aqperso_aquete_cod;
                $elem->aqelem_aqetape_cod = $aqperso->aqperso_etape_cod;
                $elem->aqelem_aqperso_cod = $aqperso->aqperso_cod;
                $elem->aqelem_quete_step = $aqperso->aqperso_quete_step;
                $elem->aqelem_param_id = 4;
                $elem->aqelem_type = 'objet';
                $elem->aqelem_misc_cod =  $objet->obj_cod ;
                $elem->aqelem_param_ordre =  $param_ordre ;         // On ordonne correctement !
                $param_ordre ++ ;
                $elem->stocke(true);                           // sauvegarde du clone forcément du type objet (instancié)
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * On verifie si les objets sont là =>  '[1:delai|1%1],[2:valeur|1%1],[3:objet_generique|0%0],[4:position|1%0]'
     * Attention à la place des objets générique il peut y avoir des objets réelement instanciés (on vérifiera les cod)
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function remettre_detruire_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;                            // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 3, array('objet_generique', 'objet'), 0)) return false ;      // Problème lecture des paramètres
        $p5 = $element->get_aqperso_element( $aqperso, 4, 'position', 0) ;

        if ($p5 &&  $p5[0]->aqelem_misc_cod>0 && !$aqperso->action->move_position($aqperso, 4) )
        {
            return false;       // Pas en position pour réaliser l'action!
        }

        shuffle($p4);                                       // ordre aléatoire pour les objets

        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on n'attend aucun objet


        // Préparation de la liste des objets prendre en fonction du nombre de générique et du nombre d'objet à donner
        $liste_echange = array() ;
        $liste_objet ="" ;
        $liste_generique ="" ;

        // On commence par chercher un exeplaire de chaque.
        foreach ($p4 as $k => $elem)
        {
            if ($elem->aqelem_type == "objet")
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_cod = ? order by random()";
            }
            else
            {
                $req = "select obj_cod, obj_poids from perso_objets join objets on obj_cod=perobj_obj_cod where perobj_perso_cod=? and obj_gobj_cod = ? order by random() limit 1";
                $liste_generique .= $elem->aqelem_misc_cod . ",";
            }
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_objet .= $result["obj_cod"] . "," ;
                $liste_echange[] = (int)$result["obj_cod"];
            }
            if (count($liste_echange)==$nbobj) break;   // On en a assez!
        }

        // S'il y a plus de demande que de générique (et qu'il y a des generique) , il faut en prendre encore un peu
        if (($nbobj > $nbgenerique) && ($liste_generique!="") && ($liste_objet!=""))
        {
            $nb = ($nbobj-$nbgenerique);                                         // nombre restant à prendre parmis les génériques
            $liste_generique = substr($liste_generique,0,-1);       // On retire les vigules finales
            $liste_objet = substr($liste_objet,0,-1);               // On retire les vigules finales
            $req = "select obj_cod, obj_poids 
                    from perso_objets 
                    join objets on obj_cod = perobj_obj_cod 
                    where perobj_perso_cod = ? 
                    and obj_gobj_cod in ({$liste_generique}) 
                    and obj_cod not in ({$liste_objet}) 
                    order by random() limit {$nb}";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod), $stmt);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $liste_echange[] = (int)$result["obj_cod"];
            }
        }

        if (count($liste_echange)<$nbobj) return false;       //il en manque !


        // Il faut maintenant prendre les objets
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_echange as $k => $obj_cod)
        {
            // Gestion de la transaction (fare l'échange et ajouter evenement)
            $objet = new objets();
            if ($objet->charge($obj_cod))
            {
                $texte_evt = '[cible] s\'est séparé de d\'un objet <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                                  values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                    ":texte_evt"=> $texte_evt,
                    ":levt_attaquant" => $aqperso->aqperso_perso_cod,
                    ":levt_cible" => $aqperso->aqperso_perso_cod,
                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);

                // Suprimer l'objet
                $objet->supprime($obj_cod);        // On supprime l'objet !

                // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
                $elem = new aquete_element();
                $elem->aqelem_aquete_cod = $aqperso->aqperso_aquete_cod;
                $elem->aqelem_aqetape_cod = $aqperso->aqperso_etape_cod;
                $elem->aqelem_aqperso_cod = $aqperso->aqperso_cod;
                $elem->aqelem_quete_step = $aqperso->aqperso_quete_step;
                $elem->aqelem_param_id = 4;
                $elem->aqelem_type = 'objet';
                $elem->aqelem_misc_cod =  $objet->obj_cod ;
                $elem->aqelem_param_ordre =  $param_ordre ;         // On ordonne correctement !
                $param_ordre ++ ;
                $elem->stocke(true);                           // sauvegarde du clone forcément du type objet (instancié)
            }
        }

        return true;
    }
    //==================================================================================================================
    /**
     * On verifie si les brouzoufs sont là =>  '[1:delai|1%1],[2:valeur|1%1],[3:position|1%0]'
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return bool
     */
    function remettre_detruire_po(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;                            // Problème lecture des paramètres
        $p3 = $element->get_aqperso_element( $aqperso, 3, 'position', 0) ;

        if ($p3 &&  $p3[0]->aqelem_misc_cod>0 && !$aqperso->action->move_position($aqperso, 3) )
        {
            return false;       // Pas en position pour réaliser l'action!
        }

        $po = $p2->aqelem_param_num_1 ;

        $perso = $aqperso->get_perso();
        if ($perso->perso_po < $po ) return false;       //il en manque !

        $pdo = new bddpdo;
        $req = "update perso set perso_po = perso_po - :po where perso_cod = :perso_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(
            ":po"               => $po,
            ":perso_cod"        => $aqperso->aqperso_perso_cod), $stmt);


        $texte_evt = "[cible] perd {$po} brouzoufs." ;
        $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                  values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);

        return true;
    }

    //==================================================================================================================
    /**
     * Va générer des monstres/perso autour d'une position donnée =>  '[1:valeur|1%1],[2:position|1%1],[3:valeur|1%1],[4:monstre_generique|0%0]',
     * p1=nb_monstre, p2=position, p3=dispersion, p4=liste des monstres
     * @param aquete_perso $aqperso
     * @return bool
     */
    function monstre_position(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                                // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'position')) return false ;                              // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'monstre_generique', 0)) return false ;       // Problème lecture des paramètres

        shuffle($p4);                                       // ordre aléatoire pour les monstres

        $nbmonstre = $p1->aqelem_param_num_1 ;

        // Vérification sur le nombre d'objet
        if ($nbmonstre <= 0) return true;       // etape bizarre !! on ne créé aucun monstre

        // Préparation de la liste des mosntres générer en fonction du nombre de générique et du nombre demandé
        $liste_monstre = $this->get_liste_element($nbmonstre, $p4);

        // on génère les monstre à partir du générique (la liste contient tout ce qui doit-être généré)
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer (on a des éléments génériques on aura des id de persos)
        $param_ordre = 0 ;
        foreach ($liste_monstre as $k => $elem)
        {
            $req = "select cree_monstre_pos(:gmon_cod,pos_alentour(:pos_cod, :dispersion)) as perso_cod ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(  ":gmon_cod" => $elem->aqelem_misc_cod ,
                                            ":pos_cod" => $p2->aqelem_misc_cod ,
                                            ":dispersion" => $p3->aqelem_param_num_1 ), $stmt);

            if ($result = $stmt->fetch())
            {
                if ((int)$result["perso_cod"]>0)
                {
                    $perso = new perso();
                    $perso->charge((int)$result["perso_cod"]);

                    // Appliquer les paramètres spécifiques (type_perso, palpable, pnj) le perso est par défaut créé en tant que monstre palpable non pnj
                    if ($elem->aqelem_param_num_1>0) $perso->perso_type_perso = 1 ;                                                         // conversion en humain
                    if ($elem->aqelem_param_num_2>0) { $perso->perso_tangible = 'N'; $perso->perso_nb_tour_intangible = 999999 ;  }         // conversion en impalpable
                    if ($elem->aqelem_param_num_3>0) $perso->perso_pnj = 1 ;                                                                // conversion en pnj
                    if (($elem->aqelem_param_num_1>0) || ($elem->aqelem_param_num_2>0) || ($elem->aqelem_param_num_3>0)) $perso->stocke();  // sauvegarde du perso (seulement en cas de changement)

                    // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                    $elem->aqelem_type = 'perso';
                    $elem->aqelem_misc_cod =  $perso->perso_cod ;
                    $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                    $param_ordre ++ ;
                    $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                }
            }
        }

        return true;
    }
    //==================================================================================================================
    /**
     * Va générer des monstres/perso autour d'un perso donnée =>  '[1:valeur|1%1],[2:perso|1%1],[3:valeur|1%1],[4:monstre_generique|0%0]',
     * p1=nb_monstre, p2=perso ciblé (0=le perso de la quete), p3=dispersion, p4=liste des monstres
     * @param aquete_perso $aqperso
     * @return bool
     **/
    function monstre_perso(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                                // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                              // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'monstre_generique', 0)) return false ;       // Problème lecture des paramètres

        shuffle($p4);                                       // ordre aléatoire pour les monstres

        $nbmonstre = $p1->aqelem_param_num_1 ;

        // Vérification sur le nombre d'objet
        if ($nbmonstre <= 0) return true;       // etape bizarre !! on ne créé aucun monstre

        // Préparation de la liste des mosntres générer en fonction du nombre de générique et du nombre demandé
        $liste_monstre = $this->get_liste_element($nbmonstre, $p4);

        // Le perso ciblé (pour récupérer sa position) !
        $perso = new perso();
        $perso->charge( $p2->aqelem_misc_cod==0 ? $aqperso->aqperso_perso_cod : $p2->aqelem_misc_cod);
        $pos_cod = $perso->get_position()["pos"]->pos_cod ;

        // on génère les monstre à partir du générique (la liste contient tout ce qui doit-être généré)
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer (on a des éléments génériques on aura des id de persos)
        $param_ordre = 0 ;
        foreach ($liste_monstre as $k => $elem)
        {
            $req = "select cree_monstre_pos(:gmon_cod,pos_alentour(:pos_cod, :dispersion)) as perso_cod ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(  ":gmon_cod" => $elem->aqelem_misc_cod ,
                                            ":pos_cod" => $pos_cod ,
                                            ":dispersion" => $p3->aqelem_param_num_1 ), $stmt);

            if ($result = $stmt->fetch())
            {
                if ((int)$result["perso_cod"]>0)
                {
                    $perso = new perso();
                    $perso->charge((int)$result["perso_cod"]);

                    // Appliquer les paramètres spécifiques (type_perso, palpable, pnj) le perso est par défaut créé en tant que monstre palpable non pnj
                    if ($elem->aqelem_param_num_1>0) $perso->perso_type_perso = 1 ;                                                         // conversion en humain
                    if ($elem->aqelem_param_num_2>0) { $perso->perso_tangible = 'N'; $perso->perso_nb_tour_intangible = 999999 ;  }         // conversion en impalpable
                    if ($elem->aqelem_param_num_3>0) $perso->perso_pnj = 1 ;                                                                // conversion en pnj
                    if (($elem->aqelem_param_num_1>0) || ($elem->aqelem_param_num_2>0) || ($elem->aqelem_param_num_3>0)) $perso->stocke();  // sauvegarde du perso (seulement en cas de changement)

                    // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                    $elem->aqelem_type = 'perso';
                    $elem->aqelem_misc_cod =  $perso->perso_cod ;
                    $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                    $param_ordre ++ ;
                    $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                }
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * Va générer des monstres/perso autour d'un perso donnée =>  '[1:valeur|1%1],[2:perso|1%1],[3:valeur|1%1]',
     * p1=nb_monstre, p2=perso ciblé (0=le perso de la quete), p3=dispersion
     * à la différence de "monstre_perso", le type de monstre sera pris en fonction des caracs de l'étage
     * @param aquete_perso $aqperso
     * @return bool
     **/
    function monstre_armee(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;                                // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                              // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                // Problème lecture des paramètres

        $nbmonstre = $p1->aqelem_param_num_1 ;

        // Vérification sur le nombre d'objet
        if ($nbmonstre <= 0) return true;       // etape bizarre !! on ne créé aucun monstre

        // Le perso ciblé (pour récupérer sa position) !
        $perso = new perso();
        $perso->charge( $p2->aqelem_misc_cod==0 ? $aqperso->aqperso_perso_cod : $p2->aqelem_misc_cod);
        $position = $perso->get_position();
        $pos_cod = $position["pos"]->pos_cod ;
        $etage_numero = $position["etage"]->etage_numero ;

        // on génère les monstre à partir du générique (la liste contient tout ce qui doit-être généré)
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer (on a des éléments génériques on aura des id de persos)
        $param_ordre = 0 ;
        for ($k=0; $k<$nbmonstre ; $k++)
        {
            $req = "select cree_monstre_pos(choix_monstre_etage(:etage_numero, 0),pos_alentour(:pos_cod, :dispersion)) as perso_cod ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(  ":etage_numero" => $etage_numero ,
                                            ":pos_cod" => $pos_cod ,
                                            ":dispersion" => $p3->aqelem_param_num_1 ), $stmt);

            if ($result = $stmt->fetch())
            {
                if ((int)$result["perso_cod"]>0)
                {
                    $perso = new perso();
                    $perso->charge((int)$result["perso_cod"]);

                    // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                    $element->aqelem_aquete_cod = $aqperso->aqperso_aquete_cod;
                    $element->aqelem_aqetape_cod = $aqperso->aqperso_etape_cod;
                    $element->aqelem_aqperso_cod = $aqperso->aqperso_cod;
                    $element->aqelem_quete_step = $aqperso->aqperso_quete_step;
                    $element->aqelem_param_id = 4;
                    $element->aqelem_type = 'perso';
                    $element->aqelem_misc_cod =  $perso->perso_cod ;
                    $element->aqelem_param_ordre =  $param_ordre ;              // On ordone correctement !
                    $param_ordre ++ ;
                    $element->stocke(true);                                // sauvegarde forcément un nouveau monstre
                }
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * Le joueur doit tuer des persos  =>  '[1:delai|1%1],[2:perso|0%0],[3:valeur|1%1],[4:etape|1%1]',
     * p2=persos cibles p3=nombre de kill p4=etape si echec
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_perso(aquete_perso $aqperso)
    {
        $retour = new stdClass();
        $retour->status = false ;  // Par défaut, l'étape n'est pas terminée
        $retour->etape = 0 ;

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return $retour ;                             // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'etape')) return $retour ;                              // Problème lecture des paramètres

        // On réalise directement un requete de comptage !
        $req = "select count(*) nb_cible, sum(case when perso.perso_actif='N' then 1 else 0 end) as nb_dead, sum(case when coalesce(perso.perso_cible,0)=aqperso_perso_cod then 1 else 0 end) as nb_kill
                from quetes.aquete_perso
                join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso'
                join perso on perso_cod=aqelem_misc_cod
                where aqperso_cod=?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($aqperso->aqperso_cod), $stmt);

        if (!$result = $stmt->fetch())  return $retour;     // On arrive pas à lire la DB ?


        $nb_contrat = $p3->aqelem_param_num_1 ;     // Contrat: nombre de monstre à tuer
        $nb_cible = (int)$result["nb_cible"] ;           // Nombre de cible initial
        $nb_dead = (int)$result["nb_dead"] ;             // Nombre de cible déjà achevé
        $nb_kill = (int)$result["nb_kill"] ;             // Nombre de cible achevé par le joueur

        if ( $nb_contrat > $nb_kill + ($nb_cible - $nb_dead) )
        {
            // Echec, il ne reste pas assez de monstre pour terminer le contrat avec succes
            $retour->status = true ;
            $retour->etape = $p4->aqelem_misc_cod ;     // vers l'étape d'echec !
        }
        else if ( ($nb_contrat <= $nb_kill) && ( ($nb_cible - $nb_dead)==0 ) )
        {
            // C'est un success !! passage à l'étape suivante (voir plus tard pour tagger les monstres tuer par le joueur)
            $retour->status = true ;
        }
        //echo "<pre>"; print_r($retour); echo "</pre>"; die();
        // renvoyer l'état
        return $retour;
    }

    //==================================================================================================================
    /**
     * Le joueur doit tuer un certains nombre de représentant de race de monstre  =>  '[1:delai|1%1],[2:race|0%0],[3:valeur|1%1]',
     * p2=races cibles p3=nombre de kill
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_race(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'race', 0)) return false ;                  // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                              // Problème lecture des paramètres

        // le compteur initial de race va être stocké dans l'élément du type race (P2) dans aqelem_param_num_1
        // on se base sur les statistiques du "Tableau de chasse"
        foreach ($p2 as $k => $elem)
        {
           // au premier passage (compteur null) initalisation du compteur.
           if ($elem->aqelem_param_num_1 == null)
           {
                $req = "select race_cod, sum(ptab_total) as total, sum(ptab_solo) as solo 
                        from perso_tableau_chasse
                        inner join monstre_generique on ptab_gmon_cod = gmon_cod
                        inner join race on race_cod = gmon_race_cod
                        where ptab_perso_cod = ? and race_cod = ?
                        group by race_cod";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
                if (!$result = $stmt->fetch())
                {
                    $elem->aqelem_param_num_1 = 0;
                }
                else
                {
                    $elem->aqelem_param_num_1 = (int)$result["total"];
                }
                $elem->stocke();
           }
        }

        $nb_contrat = $p3->aqelem_param_num_1 ;     // Contrat: nombre de monstre à tuer

        // Comptage des kills
        $nb_kill = 0;
        $nb_race = 0;
        foreach ($p2 as $k => $elem)
        {
            if ($elem->aqelem_param_num_1 == null) return false; // compteur non initialisé

            $req = "select race_cod, sum(ptab_total) as total, sum(ptab_solo) as solo 
                    from perso_tableau_chasse
                    inner join monstre_generique on ptab_gmon_cod = gmon_cod
                    inner join race on race_cod = gmon_race_cod
                    where ptab_perso_cod = ? and race_cod = ?
                    group by race_cod";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch())
            {
                // Il y a eu des kill de cette race dans le tableau, vérification par rapport au début du contrat
                $kill_race = (int)$result["total"];
                if ($kill_race>$elem->aqelem_param_num_1)
                {
                    $nb_kill += $kill_race - $elem->aqelem_param_num_1;
                    $nb_race++;
                }
            }
        }

        // si le compteur de kill atteind le contrat et au moins un de chaque race si compteur supérieur au nombre de race
        if (($nb_kill>=$nb_contrat) && (($nb_contrat<count($p2)) || ($nb_race==count($p2)))) return true;

        // le contrat n'est pas encore rempli
        return false;
    }

    //==================================================================================================================
    /**
     * Le joueur doit tuer un certains nombre de représentant de type de monstre  =>  '[1:delai|1%1],[2:monstre_generique|0%0],[3:valeur|1%1]',
     * p2=type cibles p3=nombre de kill
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_type(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'type_monstre_generique', 0)) return false ;                  // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                              // Problème lecture des paramètres

        // le compteur initial de type va être stocké dans l'élément du type  (P2) dans aqelem_param_num_1
        // on se base sur les statistiques du "Tableau de chasse"
        foreach ($p2 as $k => $elem)
        {
           // au premier passage (compteur null) initalisation du compteur.
           if ($elem->aqelem_param_num_1 == null)
           {
                $req = "select ptab_gmon_cod, sum(ptab_total) as total, sum(ptab_solo) as solo 
                        from perso_tableau_chasse
                        where ptab_perso_cod = ? and ptab_gmon_cod = ?
                        group by ptab_gmon_cod";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
                if (!$result = $stmt->fetch())
                {
                    $elem->aqelem_param_num_1 = 0;
                }
                else
                {
                    $elem->aqelem_param_num_1 = (int)$result["total"];
                }
                $elem->stocke();
           }
        }

        $nb_contrat = $p3->aqelem_param_num_1 ;     // Contrat: nombre de monstre à tuer

        // Comptage des kills
        $nb_kill = 0;
        $nb_type = 0;
        foreach ($p2 as $k => $elem)
        {
            if ($elem->aqelem_param_num_1 == null) return false; // compteur non initialisé

            $req = "select ptab_gmon_cod, sum(ptab_total) as total, sum(ptab_solo) as solo 
                    from perso_tableau_chasse
                    where ptab_perso_cod = ? and ptab_gmon_cod = ?
                    group by ptab_gmon_cod";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch())
            {
                // Il y a eu des kill de ce type dans le tableau, vérification par rapport au début du contrat
                $kill_type = (int)$result["total"];
                if ($kill_type>$elem->aqelem_param_num_1)
                {
                    $nb_kill += $kill_type - $elem->aqelem_param_num_1;
                    $nb_type++;
                }
            }
        }

        // si le compteur de kill atteind le contrat et au moins un de chaque type si compteur supérieur au nombre de type
        if (($nb_kill>=$nb_contrat) && (($nb_contrat<count($p2)) || ($nb_type==count($p2)))) return true;

        // le contrat n'est pas encore rempli
        return false;
    }

    //==================================================================================================================
    /**
     * Le joueur doit tuer un certains nombre de représentant de type de monstre  =>  '[1:delai|1%1],[2:monstre_generique|0%0],[3:valeur|1%1]',
     * p2=type cibles p3=nombre de kill
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_tableau_chasse(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'selecteur')) return false ;                  // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'type_monstre_generique', 0)) return false ;                  // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;                              // Problème lecture des paramètres

        $type_contrat = $p2->aqelem_misc_cod ;     // type de contrat: global / solo
        $nb_contrat = $p4->aqelem_param_num_1 ;     // Contrat: nombre de monstre à tuer

        // Comptage des kills
        $nb_kill = 0;
        foreach ($p3 as $k => $elem)
        {
            $req = "select ptab_gmon_cod, sum(ptab_total) as total, sum(ptab_solo) as solo 
                    from perso_tableau_chasse
                    where ptab_perso_cod = ? and ptab_gmon_cod = ?
                    group by ptab_gmon_cod";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $elem->aqelem_misc_cod), $stmt);
            if ($result = $stmt->fetch())
            {
                // incermenter le nombre de kill de chaque type
                $nb_kill +=  $type_contrat == 0 ? (int)$result["total"] : (int)$result["solo"] ;
            }
        }

        // si le compteur de kill atteind le contrat
        if ($nb_kill>=$nb_contrat) return true;

        // le contrat n'est pas encore rempli
        return false;
    }

    //==================================================================================================================
    /**
     * Le joueur doit tuer un certains nombre de représentant de type de monstre  =>  '[1:delai|1%1],[2:position|1%1],[3:valeur|1%1],[4:type_monstre_generique|0%0],[5:type_monstre_generique|0%0]',
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_zone(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'position')) return false ;                  // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                  // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'type_monstre_generique', 0)) return false ;                  // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'type_monstre_generique', 0)) return false ;                  // Problème lecture des paramètres

        // compter le nombre de monstres autour de la position
        $req = "select count(*) as count from positions po
                    join positions pa on (pa.pos_etage=po.pos_etage) and (pa.pos_x>=po.pos_x-:rayon and pa.pos_x<=po.pos_x+:rayon) and (pa.pos_y>=po.pos_y-:rayon and pa.pos_y<=po.pos_y+:rayon)
                    join perso_position on ppos_pos_cod=pa.pos_cod
                    join perso on perso_cod=ppos_perso_cod and perso_actif='O' and perso_type_perso=2
                    where po.pos_cod=:pos_cod ";

        // monstres dans la liste
        $m_list ="";
        foreach ($p4 as $k => $elem)
        {
            if ((int)$elem->aqelem_misc_cod != 0) $m_list .= $elem->aqelem_misc_cod.",";
        }
        if ($m_list != "") $req.= " and perso_gmon_cod in (".(substr($m_list, 0,-1)).")";


        // monstres PAS dans la liste
        $m_list ="";
        foreach ($p5 as $k => $elem)
        {
            if ((int)$elem->aqelem_misc_cod != 0) $m_list .= $elem->aqelem_misc_cod.",";
        }
        if ($m_list != "") $req.= " and perso_gmon_cod not in (".(substr($m_list, 0,-1)).")";


        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pos_cod" => $p2->aqelem_misc_cod, ":rayon" => (int)$p3->aqelem_param_num_1), $stmt);
        if ($result = $stmt->fetch())
        {
            if ( (int)$result["count"] == 0 )
            {
                //echo "<pre>"; print_r([$p4, $p5, $req, array(":pos_cod" => $p2->aqelem_misc_cod, ":rayon" => (int)$p3->aqelem_param_num_1),$result]);die();
                return true;
            }
        }

        //echo "<pre>"; print_r([$p4, $p5, $req, array(":pos_cod" => $p2->aqelem_misc_cod, ":rayon" => (int)$p3->aqelem_param_num_1),$result]);die();
        return false;
    }

    //==================================================================================================================
    /**
     * Participer à la mort de monstre d''un type ou d''une race spécifique =>  '[1:delai|1%1],[2:valeur|1%1|],[3:selecteur|1%1|{0~Participer à la mort},{1~Tuer}],[4:selecteur|1%1|{0~Peu importe},{1~Au moins 1 monstre de chaque type ou race},{2~Au moins 1 monstre de chaque type et de chaque race}],[5:race|0%0],[6:type_monstre_generique|0%0]',
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function tuer_participer_mort(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;                  // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'selecteur')) return false ;                  // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'selecteur')) return false ;                  // Problème lecture des paramètres
        if (!$p5 = $element->get_aqperso_element( $aqperso, 5, 'race', 0)) return false ;                  // Problème lecture des paramètres
        if (!$p6 = $element->get_aqperso_element( $aqperso, 6, 'type_monstre_generique', 0)) return false ;                  // Problème lecture des paramètres

        // les logs d'evenements sont purgés regulièrement, on va devoir mémoriser le comptage à chaque passage.
        // On gardera la date de du dernier controle dans l'element $p2 (aqelem_param_txt_1), et le comptage dans chaque element race/type respectif (aqelem_param_num_1)
        $total_kill = (int) $p2->aqelem_param_num_1 ;
        $date_debut = $aqperso->aqperso_date_debut_etape;  // on ne remonte pas avant la date de début de l'étape
        $date_dernier_controle = ($p2->aqelem_param_txt_1 && $p2->aqelem_param_txt_1 != "") ? $p2->aqelem_param_txt_1 : $date_debut ;
        $date_now = date("Y-m-d H:i:s");


        $req = "SELECT perso_race_cod, perso_gmon_cod, count(*) as count FROM (SELECT perso_nom, perso_race_cod, perso_gmon_cod, count(*)
                        FROM public.ligne_evt
                        join perso on (perso_cod=levt_cible and levt_tevt_cod=10) or (perso_cod=levt_attaquant and levt_tevt_cod=48)
                        where levt_date >= :date_debut and levt_date >= :date_dernier_controle and levt_date <= :date_now ";

        if ($p3->aqelem_misc_cod == 1)
        {
            // le perso doit-être le tueur (evenement type 10)
            $req.= "and levt_perso_cod1=:perso_cod and levt_tevt_cod in (10) ";
        }
        else
        {
            // une participation à la mort seulement est requise (evenement tuer (10) mais aussi gain de px (48) )
            $req.= "and levt_perso_cod1=:perso_cod and levt_tevt_cod in (48, 10) ";
        }

        // tableau de comptage des kill par race et type
        $arr_race = [] ;
        $nb_race = 0 ;
        $arr_type = [] ;
        $nb_type = 0 ;

        // calcul des conditions sur liste de type de monstre
        $mr_list ="";
        foreach ($p5 as $k => $elem)
        {
            if ((int)$elem->aqelem_misc_cod != 0)
            {
                $mr_list .= $elem->aqelem_misc_cod.",";
                $arr_race[$elem->aqelem_misc_cod] = (int)$elem->aqelem_param_num_1;
                $nb_race ++ ;
            }
        }
        if ($mr_list != "") $mr_list = substr($mr_list, 0,-1);


        // calcul des conditions sur liste de type de monstre
        $mt_list ="";
        foreach ($p6 as $k => $elem)
        {
            if ((int)$elem->aqelem_misc_cod != 0)
            {
                $mt_list .= $elem->aqelem_misc_cod.",";
                $arr_type[$elem->aqelem_misc_cod] = (int)$elem->aqelem_param_num_1;
                $nb_type ++ ;
            }
        }
        if ($mt_list != "") $mt_list = substr($mt_list, 0,-1);

        // Ajout de la condition de recherche
        if (($mt_list != "" ) && ($mr_list != "" ))
        {
            $req.= " and ( perso_gmon_cod in ($mt_list) or perso_race_cod in ($mr_list) )";
        }
        else if ($mt_list != "" )
        {
            $req.= " and ( perso_gmon_cod in ($mt_list) )";
        }
        else if ($mr_list != "" )
        {
            $req.= " and ( perso_race_cod in ($mr_list) )";
        }
        else
        {
            return false;   // ni race ni type, erreur de saisie dans le QA
        }

        // ajout du regroupement (la mort d'un monstre peut générer plusieurs ligne de gain de px)
        $req.= " group by perso_nom, perso_race_cod, perso_gmon_cod) as subquery group by perso_race_cod, perso_gmon_cod ";

        //echo "<pre>"; print_r([$req,["race"=>[$nb_race,$arr_race], "type"=>[$nb_type,$arr_type]], array(":perso_cod" => $aqperso->aqperso_perso_cod, ":date_debut" => $date_debut, ":date_dernier_controle" => $date_dernier_controle, ":date_now" => $date_now )]);


        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perso_cod" => $aqperso->aqperso_perso_cod, ":date_debut" => $date_debut, ":date_dernier_controle" => $date_dernier_controle, ":date_now" => $date_now ), $stmt);

        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $arr_race[$result["perso_race_cod"]] += (int)$result["count"];       // count race
            $arr_type[$result["perso_gmon_cod"]] += (int)$result["count"];        // cunt type
        }
        //echo "<pre>"; print_r(["race"=>[$nb_race,$arr_race], "type"=>[$nb_type,$arr_type]]);

        // sauvegarder la mise à jour du comptage: date
        $e = new aquete_element();
        $e->charge( $p2->aqelem_cod );
        $e->aqelem_param_txt_1 = $date_now ;
        $e->stocke();

        // sauvegarder la mise à jour du comptage: race / relever les compteur au passage
        $count_race = 0 ;
        $race_all = ($nb_race == 0) ? false : true ;
        foreach ($p5 as $k => $elem)
        {
            if ((int)$elem->aqelem_misc_cod != 0)
            {
                $e = new aquete_element();
                $e->charge( $elem->aqelem_cod );
                $e->aqelem_param_num_1 = $arr_race[ (int)$elem->aqelem_misc_cod ] ;
                $e->stocke();
                $count_race += $arr_race[ (int)$elem->aqelem_misc_cod ]  ;
                if ( $arr_race[ (int)$elem->aqelem_misc_cod ] == 0 ) $race_all = false ;
            }
        }//

        // sauvegarder la mise à jour du comptage: type / relever les compteur au passage
        $count_type = 0 ;
        $type_all = ($nb_type == 0) ? false : true ; ;
        foreach ($p6 as $k => $elem)
        {
            $e = new aquete_element();
            $e->charge( $elem->aqelem_cod );
            $e->aqelem_param_num_1 = $arr_type[ (int)$elem->aqelem_misc_cod ] ;
            $e->stocke();
            $count_type += $arr_type[ (int)$elem->aqelem_misc_cod ]  ;
            if ( $arr_type[ (int)$elem->aqelem_misc_cod ] == 0 ) $type_all = false ;
        }

        // verification des conditions 1 de chaque race ou de chaque type
        if ($p4->aqelem_misc_cod == 2 && (!$race_all || !$type_all)) return false;
        if ($p4->aqelem_misc_cod == 1 && !$race_all && !$type_all) return false;

        // verification du nombre de kill

        if (($count_race>=$total_kill && $nb_race>0) || ($count_type>=$total_kill && $nb_type>0)) return true ;

        //echo "<pre>"; print_r([$req, $result,array(":perso_cod" => $aqperso->aqperso_perso_cod, ":date_debut" => $date_debut, ":date_dernier_controle" => $date_dernier_controle, ":date_now" => $date_now )]); die();

        return false;
    }

    //==================================================================================================================
    /**
     * Création d'un portail sur le perso  =>  '[1:perso|1%1],[2:position|1%1],[3:valeur|1%1],[4:valeur|1%1]',
     * p1=persos p2=position cible du portail p3=dispertion p4=nombre d'heure d'ouverture
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function teleportation_portail_perso(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'perso')) return false ;
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'position')) return false ;
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;

        // Si le perso est vide, on prend le meneur de quete
        $perso = new perso();
        $perso->charge($p1->aqelem_misc_cod == 0 ? $aqperso->aqperso_perso_cod : $p1->aqelem_misc_cod );
        $portail_pos = $perso->get_position()["pos"]->pos_cod ;

        // Préparation des paramètre de destination
        $req = "select pos_alentour(:pos_cod, :dispersion) as portail_dest ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pos_cod" => $p2->aqelem_misc_cod, ":dispersion" => $p3->aqelem_param_num_1), $stmt);
        if (!$result = $stmt->fetch()) return false;
        $portail_dest = $result["portail_dest"];
        $portail_delai = date("Y-m-d H:i:s", strtotime (date("Y-m-d H:i:s")." +{$p4->aqelem_param_num_1} hours") );

        // Création du Portail au départ
        $req = "insert into lieu (lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
                values(10,'Passage magique','Un passage crée par la magie...<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src=\"../avatars/passage_entree.gif\"><br>','N','passage.php',?,?)
                returning lieu_cod ;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($portail_dest, $portail_delai), $stmt);
        if (!$result = $stmt->fetch()) return false;

        $req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values (?,?);";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_pos, $result["lieu_cod"]), $stmt);

        // Création du Portail à l'arrivée
        $req = "insert into lieu (lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
                values(10,'Passage magique','Un passage crée par la magie. Il est fermé et ne peut être pris dans ce sens<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src=\"../avatars/passage_sortie.gif\"><br>','N','passage_b.php',0,?)
                returning lieu_cod ;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($portail_delai), $stmt);
        if (!$result = $stmt->fetch()) return false;

        $req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values (?,?);";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_dest, $result["lieu_cod"]), $stmt);

        //- automap (mise à jour de l'automap avec les portail nouvellement créé)
        $req = "select init_automap_pos(?) ; ";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_pos), $stmt);

        $req = "select init_automap_pos(?) ; ";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_dest), $stmt);


        return true;
    }

    //==================================================================================================================
    /**
     * Création d'un portail sur une position  =>  '[1:position|1%1],[2:position|1%1],[3:valeur|1%1],[4:valeur|1%1]',
     * p1=position départ p2=position cible du portail p3=dispertion p4=nombre d'heure d'ouverture
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function teleportation_portail_position(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'position')) return false ;
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'position')) return false ;
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;

        // La position de départ du portail
        $portail_pos = $p1->aqelem_misc_cod ;

        // Préparation des paramètre de destination
        $req = "select pos_alentour(:pos_cod, :dispersion) as portail_dest ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pos_cod" => $p2->aqelem_misc_cod, ":dispersion" => $p3->aqelem_param_num_1), $stmt);
        if (!$result = $stmt->fetch()) return false;
        $portail_dest = $result["portail_dest"];
        $portail_delai = date("Y-m-d H:i:s", strtotime (date("Y-m-d H:i:s")." +{$p4->aqelem_param_num_1} hours") );

        // Création du Portail au départ
        $req = "insert into lieu (lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
                values(10,'Passage magique','Un passage crée par la magie...<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src=\"../avatars/passage_entree.gif\"><br>','N','passage.php',?,?)
                returning lieu_cod ;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($portail_dest, $portail_delai), $stmt);
        if (!$result = $stmt->fetch()) return false;

        $req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values (?,?);";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_pos, $result["lieu_cod"]), $stmt);

        // Création du Portail à l'arrivée
        $req = "insert into lieu (lieu_tlieu_cod,lieu_nom,lieu_description,lieu_refuge,lieu_url,lieu_dest,lieu_port_dfin)
                values(10,'Passage magique','Un passage crée par la magie. Il est fermé et ne peut être pris dans ce sens<br><br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;<img src=\"../avatars/passage_sortie.gif\"><br>','N','passage_b.php',0,?)
                returning lieu_cod ;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($portail_delai), $stmt);
        if (!$result = $stmt->fetch()) return false;

        $req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values (?,?);";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_dest, $result["lieu_cod"]), $stmt);

        //- automap (mise à jour de l'automap avec les portail nouvellement créé)
        $req = "select init_automap_pos(?) ; ";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_pos), $stmt);

        $req = "select init_automap_pos(?) ; ";
        $stmt   = $pdo->prepare($req);
        $pdo->execute(array($portail_dest), $stmt);


        return true;
    }

    //==================================================================================================================
    /**
     * Création d'un portail sur une position  =>  '[1:position|1%1],[2:valeur|1%1],[3:selecteur|1%1|{1~le meneur de quête seul},{2~le meneur et sa triplette à proximité},{3~le meneur et sa coterie à proximité},{4~tous les aventuriers à proximité}],[4:valeur|1%1]',
     * p1=position cible du portail p2=dispertion P3=type de perso a téléporter p4=ditance de proximité
     * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function teleportation_perso(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'position')) return false ;
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'selecteur')) return false ;
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, 'valeur')) return false ;
        $p5 = $element->get_aqperso_element( $aqperso, 5, 'texte') ;

        // Recherche de la zone centrale de départ
        $perso = new perso();
        $perso->charge( $aqperso->aqperso_perso_cod );
        $depart_pos = $perso->get_position()["pos"]->pos_cod ;

        $perso_compte = new perso_compte();     // compte du meneur de quete
        $compt_cod = $perso_compte->getBy_pcompt_perso_cod( $aqperso->aqperso_perso_cod )[0]->pcompt_compt_cod ;

        $groupe_perso = new groupe_perso();     // coterie du meneur de quete
        $groupe_perso = $groupe_perso->getBy_pgroupe_perso_cod($aqperso->aqperso_perso_cod);
        $coterie =  ($groupe_perso && $groupe_perso[0]->pgroupe_statut==1)  ? $groupe_perso[0]->pgroupe_groupe_cod : null ;

        // Recherche de tous les perso a proximité
        $req = "select perso_cod, perso_nom, ppos_cod, pcompt_compt_cod, pgroupe_groupe_cod
                from positions pos_ref
                join positions pos_cible on pos_cible.pos_etage=pos_ref.pos_etage and pos_cible.pos_x between pos_ref.pos_x-:proximite and pos_ref.pos_x+:proximite and pos_cible.pos_y between pos_ref.pos_y-:proximite and pos_ref.pos_y+:proximite
                join perso_position on ppos_pos_cod=pos_cible.pos_cod
                join perso on perso_cod=ppos_perso_cod and ((perso_type_perso=1 and perso_pnj=0) or perso_cod=:perso_cod)
                join perso_compte on pcompt_perso_cod=perso_cod
                left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1 and perso_actif='O'
                where pos_ref.pos_cod=:pos_cod";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":proximite" => $p4->aqelem_param_num_1, ":pos_cod" => $depart_pos, ":perso_cod" => $aqperso->aqperso_perso_cod), $stmt);

        while ($result = $stmt->fetch())
        {

            //print_r($result);

            if (     ($result["perso_cod"]==$aqperso->aqperso_perso_cod)                                         // 1: Cas du meneur (il passe toujours)
               || ($p3->aqelem_misc_cod>=2 && $result["pcompt_compt_cod"]==$compt_cod)                        // 2: Meneur et sa triplette
               || ($p3->aqelem_misc_cod>=3 && $coterie && $result["pgroupe_groupe_cod"]==$coterie)            // 3: Meneur sa triplette et sa coterie
               || ($p3->aqelem_misc_cod==4)                                                                   // 4: Tout le monde
            )
            {
                // ============================= Traitement des TP depuis/vers arenes/donjon
                // Capturer la position de départ
                $req_position = "select pos_etage, pos_cod, etage_arene, coalesce(coalesce(f_perso_monture(ppos_perso_cod), f_perso_cavalier(ppos_perso_cod)), 0) as perso_equipage
				from perso_position
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				where ppos_perso_cod = {$result["perso_cod"]} ";
                $stmtT         = $pdo->query($req_position);
                $resultT       = $stmtT->fetch();
                $anc_pos_etage = $resultT['pos_etage'];
                $anc_pos_cod  = $resultT['pos_cod'];
                $anc_arene    = $resultT['etage_arene'];
                $perso_equipage = $resultT['perso_equipage'];

                // Téléporter !!!
                $req = "update perso_position set ppos_pos_cod=pos_alentour(:pos_cod, :dispersion) where ppos_perso_cod=:ppos_perso_cod; ";
                $stmt2   = $pdo->prepare($req);
                $pdo->execute(array(":pos_cod" => $p1->aqelem_misc_cod,
                                    ":dispersion" => $p2->aqelem_param_num_1,
                                    ":ppos_perso_cod" => $result["perso_cod"]), $stmt2);

                // Supression des locks de combat !!!
                $req = "delete from lock_combat where lock_cible = :perso_cod or lock_attaquant = :perso_cod ; ";
                $stmt2   = $pdo->prepare($req);
                $pdo->execute(array(":perso_cod" => $result["perso_cod"]), $stmt2);

                // ============================= Traitement des TP depuis/vers arenes/donjon
                // Capturer la position d'arrivée
                $req_position = "select pos_etage, pos_cod, etage_arene, coalesce(coalesce(f_perso_monture(ppos_perso_cod), f_perso_cavalier(ppos_perso_cod)), 0) as perso_equipage
                                    from perso_position
                                    inner join positions on pos_cod = ppos_pos_cod
                                    inner join etage on etage_numero = pos_etage
                                    where ppos_perso_cod = {$result["perso_cod"]} ";
                $stmtT         = $pdo->query($req_position);
                $resultT       = $stmtT->fetch();
                $new_pos_etage = $resultT['pos_etage'];
                $new_pos_cod  = $resultT['pos_cod'];
                $new_arene    = $resultT['etage_arene'];


                // Activer les EA de déplacement à l'arrivée (cela peut casser des mécanismes de ne pas le faire)
                $req  = "select execute_fonctions(".$result["perso_cod"].", null, 'DEP', json_build_object('pilote'".$result["perso_cod"].",'ancien_pos_cod',$anc_pos_cod,'ancien_etage',$anc_pos_etage,'nouveau_pos_cod',$new_pos_cod,'nouveau_etage',$new_pos_etage)) ";
                $stmt2 = $pdo->query($req);

                // EA déclenché par la monture ou le cavalier qui lui est attaché
                if ($perso_equipage > 0 && $anc_pos_etage==$new_pos_etage)
                {
                    $req  = "select execute_fonctions($perso_equipage, null, 'DEP', json_build_object('pilote',".$result["perso_cod"].",'ancien_pos_cod',$anc_pos_cod,'ancien_etage',$anc_pos_etage,'nouveau_pos_cod',$new_pos_cod,'nouveau_etage',$new_pos_etage)) ";
                    $stmt2 = $pdo->query($req);
                }

                // ============================= Traitement des TP depuis/vers arenes/donjon
                switch ($anc_arene . $new_arene)
                {
                    case 'NO':    // D’un étage normal vers une arène
                        $req  = "delete from perso_arene where parene_perso_cod = {$result["perso_cod"]} ";
                        $stmtT = $pdo->query($req);
                        $req  = "insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree) values(".$result["perso_cod"].", $new_pos_etage, $anc_pos_cod, now()) ";
                        $stmtT = $pdo->query($req);
                        break;

                    case 'OO':    // D’une arène vers une autre
                        $req  = "update perso_arene set parene_etage_numero = $new_pos_etage where parene_perso_cod = {$result["perso_cod"]} ";
                        $stmtT = $pdo->query($req);
                        break;

                    case 'ON':    // D’une arène vers un étage normal
                        $req  = "delete from perso_arene where parene_perso_cod = {$result["perso_cod"]} ";
                        $stmtT = $pdo->query($req);
                        // Si on ne le supprimait pas, on empêcherait le perso de rentrer à nouveau en arène...
                        break;

                    case 'NN':    // D’un étage normal vers un étage normal
                        // Rien à faire
                        break;
                }


                // texte de l'evenement (si defini)
                if ($p5 && $p5->aqelem_param_txt_1 != '') {

                    $texte_evt = $p5->aqelem_param_txt_1;
                    $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                                      values(54, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";

                    //echo "<pre"; print_r([$p5,$result, $req]); die();

                    $stmt2 = $pdo->prepare($req);
                    $stmt2 = $pdo->execute(array(":levt_perso_cod1" => $result["perso_cod"],
                        ":texte_evt" => $texte_evt,
                        ":levt_attaquant" => $aqperso->aqperso_perso_cod,
                        ":levt_cible" => $result["perso_cod"]), $stmt2);
                }
            }
        }
        return true;
    }

    //==================================================================================================================
    /**
     * declenchement d'un mécanisme =>  '[1:meca|0%0]',
     * p1=meca
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function meca_declenchement(aquete_perso $aqperso)
    {

        $pdo = new bddpdo;
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'meca', 0)) return false ;

        // Recherche de la zone centrale de départ
        $perso = new perso();
        $perso->charge( $aqperso->aqperso_perso_cod );
        $perso_pos_cod = $perso->get_position()["pos"]->pos_cod ;

        foreach ($p1 as $k => $elem)
        {
            $chance = $elem->aqelem_param_num_2 > 0 ? $elem->aqelem_param_num_2 : 100;
            if ( rand(0, 10000)/100 < $chance )
            {
                // declencher !!!
                $req = "select meca_declenchement(:meca_cod,:sens,:position,:perso_pos_cod) as result; ";
                $stmt   = $pdo->prepare($req);
                $pdo->execute(array(":meca_cod"         => $elem->aqelem_misc_cod,
                                    ":sens"             =>  $elem->aqelem_param_num_1,
                                    ":position"         =>  $elem->aqelem_param_num_3,
                                    ":perso_pos_cod"    => $perso_pos_cod), $stmt);
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * declenchement d'un mécanisme =>  '[1:quete|1%0]',
     * p1=quete
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function quete_desactivation(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'quete')) return false ;
        $p2 = $element->get_aqperso_element( $aqperso, 2, "selecteur" ) ;

        $aquete_cod = $p1->aqelem_misc_cod ? $p1->aqelem_misc_cod  : $aqperso->aqperso_aquete_cod ;

        if ($aquete_cod)
        {
            $quete = new aquete();
            $quete->charge($aquete_cod);

            if ($p2 && $p2->aqelem_misc_cod==1)
            {
                // supprimer la règle si elle existe "OU perso_cod = meneur (si c'est pas déjà le cas)
                $elem = new aquete_element();
                $elist = $elem->getBy_etape_param_id($quete->aquete_etape_cod, 0);
                $hasPersoCondition = false ;
                foreach ($elist as $k => $e)
                {
                    if ($e->aqelem_misc_cod == 27 && $e->aqelem_param_txt_2 == $aqperso->aqperso_perso_cod && $e->aqelem_param_txt_1 == "=")
                    {
                        $hasPersoCondition = true ;
                        $req = "delete from quetes.aquete_element where aqelem_cod=:aqelem_cod ";
                        $stmt   = $pdo->prepare($req);
                        $pdo->execute(array(":aqelem_cod"    => $e->aqelem_cod), $stmt);
                        break;
                    }
                }
                if ($hasPersoCondition && count($elist)==1)
                {
                    // s'était le dernier perso, on désactive completment
                    $quete->aquete_actif = 'N';
                    $quete->stocke();
                } else if (!$hasPersoCondition && $quete->aquete_actif == 'O') {
                    // la quete est ouverte et le perso n'avait pas de condition d'accès,on lui interdit l'ccès par une condition de refus
                    $elem->aqelem_aquete_cod = $quete->aquete_cod ;
                    $elem->aqelem_aqetape_cod = $quete->aquete_etape_cod ;
                    $elem->aqelem_param_id = 0 ;
                    $elem->aqelem_type = "perso_condition" ;
                    $elem->aqelem_misc_cod = 27 ;
                    $elem->aqelem_param_num_1 = 0 ;
                    $elem->aqelem_param_txt_1 = "!=" ;
                    $elem->aqelem_param_txt_2 = $aqperso->aqperso_perso_cod ;
                    $elem->stocke(true);
                }
            }
            else
            {
                // pas de consition on supprime pour tout le monde
                $quete->aquete_actif = 'N';
                $quete->stocke();
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * declenchement d'un mécanisme =>  '[1:quete|1%0]',
     * p1=quete
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function quete_activation(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'quete')) return false ;
        $p2 = $element->get_aqperso_element( $aqperso, 2, "selecteur" ) ;

        $aquete_cod = $p1->aqelem_misc_cod ;

        if ($aquete_cod)
        {
            $quete = new aquete();
            $quete->charge($aquete_cod);
            $quete->aquete_actif = 'O' ;
            $quete->stocke();

            if ($p2 && $p2->aqelem_misc_cod==1)
            {
                // ajouter règle "OU perso_cod = meneur (si c'est pas déjà le cas)
                $elem = new aquete_element();
                $elist = $elem->getBy_etape_param_id($quete->aquete_etape_cod, 0);
                $hasPersoCondition = false ;
                foreach ($elist as $k => $e)
                {
                    if ($e->aqelem_misc_cod == 27 && $e->aqelem_param_txt_2 == $aqperso->aqperso_perso_cod)
                    {
                        if ($e->aqelem_param_txt_1 == "!=")
                        {
                            $req = "delete from quetes.aquete_element where aqelem_cod=:aqelem_cod ";
                            $stmt   = $pdo->prepare($req);
                            $pdo->execute(array(":aqelem_cod"    => $e->aqelem_cod), $stmt);
                        }
                        $hasPersoCondition = true ;
                        break;
                    }
                }
                if (!$hasPersoCondition )
                {
                    $elem->aqelem_aquete_cod = $quete->aquete_cod ;
                    $elem->aqelem_aqetape_cod = $quete->aquete_etape_cod ;
                    $elem->aqelem_param_id = 0 ;
                    $elem->aqelem_type = "perso_condition" ;
                    $elem->aqelem_misc_cod = 27 ;
                    $elem->aqelem_param_num_1 = 1 ;
                    $elem->aqelem_param_txt_1 = "=" ;
                    $elem->aqelem_param_txt_2 = $aqperso->aqperso_perso_cod ;
                    $elem->stocke(true);
                }
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
     * declenchement d'un mécanisme =>  '[1:texte|1%1]',
     * p1=quete
     * @param aquete_perso $aqperso
     * @return stdClass
     **/
    function quete_pause(aquete_perso $aqperso)
    {
        global $__aquete_action_quete_pause;
        if (!isset($__aquete_action_quete_pause) || $__aquete_action_quete_pause == 0) {
            $__aquete_action_quete_pause = 1 ;
            return true;        // on passe pour la première fois, passage auto
        }
        //echo "<pre>"; print_r($nb_etape_run); die();
        // on est déjà passé ici durant le run de cette quete, on fait une pause
        return false;
    }

    //==================================================================================================================
/*
echo "<pre>"; print_r($p1); echo "</pre>";
die();
 */
}
