<?php function readNumber($in)
{
    $ret = sprintf('%d' , $in);
    if (empty($ret)) $ret = 0;
    return $ret;
}

switch ($methode)
{
case 'update_quete': // cod==0 ou duplicata -> nouvelle
    if (0 == $mod_quete_cod)
    {
        if (!isset($_POST['aquete_nom']) || $_POST['aquete_nom'] == '')
        {
            echo ' ERREUR: Pas de nom spécifié pour la quête';
            break;
        }
        $aquete_nom = pg_escape_string(str_replace("''","\'",$_POST['aquete_nom']));
        echo 'Création de la quête : ' , $aquete_nom ;
        // Créer une étape finale
        $eIdx = 1;
        $db->query('select etape_cod from quetes.etape
            order by etape_cod desc limit 1');
        if ($db->next_record())
        {
            $eIdx = $db->f('etape_cod') + 1;
        }
        $db->query('insert into quetes.etape
            (etape_cod, etape_nom)
            values (' . $eIdx . ' , e\'FIN - ' . $aquete_nom . '\')');
        // Créer une quête
        $qIdx = 1;
        $db->query('select aquete_cod from quetes.quete_automatique
            order by aquete_cod desc limit 1');
        if ($db->next_record())
        {
            $qIdx = $db->f('aquete_cod') + 1;
        }
        $db->query('insert into quetes.quete_automatique
            (aquete_cod, aquete_nom, aquete_etape_cod)
            values (' . $qIdx . ' , e\'' . $aquete_nom . '\' , ' . $eIdx . ')');
        // Associer les deux
        $aIdx = 1;
        $db->query('select equete_cod from quetes.quete_automatique_etape
            order by equete_cod desc limit 1');
        if ($db->next_record())
        {
            $aIdx = $db->f('equete_cod') + 1;
        }
        $db->query('insert into quetes.quete_automatique_etape
            (equete_cod , equete_aquete_cod , equete_etape_cod)
            values (' . $aIdx . ' , ' . $qIdx . ' , ' . $eIdx . ')');
    }
    else if ($_POST['duplicata'] == 'duplicata')
    {
        echo '[INVALIDE] Création de la quête :' , $_POST['aquete_nom'] ,
            ' sur la base de la quête ' , $mod_quete_cod;
    }
    else
    {
        if (isset($_POST['aquete_nom']) && $_POST['aquete_nom'] != '')
        {
            echo 'Modification de la quête - Nouveau nom : ' , $_POST['aquete_nom'];
            $req = 'update quetes.quete_automatique set aquete_nom = e\'' .
                pg_escape_string(str_replace("''","\'",$_POST['aquete_nom'])) .
                '\' where aquete_cod = ' . $mod_quete_cod;
            $db->query($req);
            $req = 'update quetes.etape set etape_nom = e\'FIN - ' .
                pg_escape_string(str_replace("''","\'",$_POST['aquete_nom'])) .
                '\' where etape_cod in (select etape_cod from quetes.etape,
                quetes.quete_automatique_etape where equete_etape_cod = etape_cod
                and etape_type_etape_cod = 0
                and equete_aquete_cod = ' . $mod_quete_cod . ')';
            $db->query($req);
        }
        else
        {
            echo 'Nouveau nom non défini !';
        }
    }
    break;
case 'cree_etape':
    if ($mod_quete_cod == 0)
    {
        echo 'ERREUR: Aucune quête définie pour y rattacher l\'étape';
        break;
    }
    // Crée l'étape
    if (!isset($_POST['etape_nom']) || $_POST['etape_nom'] == '')
    {
        echo 'ERREUR: Nom de l\'étape non mentionné';
        break;
    }
    if (!isset($_POST['etape_description']) || $_POST['etape_description'] == '')
    {
        echo 'ERREUR: Description de l\'étape non mentionnée';
        break;
    }
    $etape_nom = pg_escape_string(str_replace("''","\'",$_POST['etape_nom']));
    $etape_description = pg_escape_string(str_replace("''","\'",$_POST['etape_description']));
    $eIdx = 1;
    $db->query('select etape_cod from quetes.etape
        order by etape_cod desc limit 1');
    if ($db->next_record())
    {
        $eIdx = $db->f('etape_cod') + 1;
    }
    $db->query('insert into quetes.etape
        (etape_cod, etape_nom, etape_description, etape_type_etape_cod)
        values (' . $eIdx . ' , e\'' . $etape_nom . '\' ,
            e\'' . $etape_description . '\' , ' . $_POST['etape_type_etape'] . ')');
    
    // On insère après la dernière étape
    $reqEtapes = 'select quetes.liste_etapes_quete(' . $mod_quete_cod . ') as etapes';
    $db->query($reqEtapes);
    $db->next_record();
    $etapes = explode(' ' , $db->f('etapes'));
    $nEtapes = sizeof($etapes);
    if ($nEtapes < 3)
    {
        // Première vraie étape. On accroche entre la quête et la fin.
        $db->query('update quetes.quete_automatique
            set aquete_etape_cod = ' . $eIdx .
            ' where aquete_cod = ' . $mod_quete_cod);
    }
    else
    {
        // Sinon, on reroute la dernière vraie étape
        $db->query('update quetes.quete_automatique_etape
            set equete_etape_suivante = ' . $eIdx .
            ' where equete_aquete_cod = ' . $mod_quete_cod .
            ' and equete_etape_cod = ' . $etapes[$nEtapes - 3]);
    }

    // L'attache à la fin
    $aIdx = 1;
    $db->query('select equete_cod from quetes.quete_automatique_etape
        order by equete_cod desc limit 1');
    if ($db->next_record())
    {
        $aIdx = $db->f('equete_cod') + 1;
    }
    $db->query('insert into quetes.quete_automatique_etape
        (equete_cod , equete_aquete_cod , equete_etape_cod , equete_etape_suivante)
        values (' . $aIdx . ' , ' . $mod_quete_cod . ' , ' .
            $eIdx . ' , ' . $etapes[$nEtapes - 2] . ')');
    echo 'Étape ajoutée en fin de quête.';
    break;
case 'insere_etape': // Insère en fin, avant l'étape finQuete
    echo '[INVALIDE] Pas encore disponible.';
    break;
case 'modif_etape':
    $etape_nom = pg_escape_string(str_replace("''","\'",$_POST['etape_nom']));
    $etape_description = pg_escape_string(str_replace("''","\'",$_POST['etape_description']));
    $etape_cod = $_POST['etape_cod'];
    $etape_type_etape_cod = $_POST['etape_type_etape'];
    $etape_parametres = pg_escape_string(str_replace("''","\'",$_POST['etape_parametres']));
    $recompense_cod = $_POST['recompense_cod'];
    $recompense_pp = readNumber($_POST['recompense_pp']);
    $recompense_br = readNumber($_POST['recompense_br']);
    $recompense_px = readNumber($_POST['recompense_px']);
    $recompense_objets = pg_escape_string(str_replace("''","\'",$_POST['recompense_objets']));

    if ($recompense_cod == 0)
    {
        $recompense_cod = 1;
        $db->query('select recompense_cod from quetes.recompense
            order by recompense_cod desc limit 1');
        if ($db->next_record())
            $recompense_cod = $db->f('recompense_cod') + 1;
        $db->query('insert into quetes.recompense
            (recompense_cod, recompense_pp, recompense_brouzoufs, recompense_px,
             recompense_objets)
             values (' . $recompense_cod . ' , ' . $recompense_pp . ' , ' .
                $recompense_br . ' , ' . $recompense_px . ' , e\'' . $recompense_objets . '\')');
    }
    else
    {
        $db->query('update quetes.recompense set
            recompense_pp = ' . $recompense_pp . ' ,
            recompense_brouzoufs = ' . $recompense_br . ' ,
            recompense_px = ' . $recompense_px . ' ,
            recompense_objets = e\'' . $recompense_objets . '\'
            where recompense_cod = ' . $recompense_cod);
    }
    $db->query('update quetes.etape set
        etape_nom = e\'' . $etape_nom . '\' ,
        etape_description = e\'' . $etape_description . '\' ,
        etape_type_etape_cod = ' . $etape_type_etape_cod . ' ,
        etape_parametres = e\'' . $etape_parametres. '\' ,
        etape_recompense_cod = ' . $recompense_cod . '
        where etape_cod = ' . $etape_cod);
    echo 'Étape mise à jour.';
    break;
case 'monte_etape':
    $etape_cod = $_GET['etape'];
    $quete_cod = $_GET['quete'];
    // Schema initial: p2->p1->cod->suiv
    // Schema final: p2->cod->p1->suiv

    // Première précédente
    $req = 'select equete_etape_cod from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_suivante = ' . $etape_cod;
    $db->query($req);
    if (!$db->next_record())
    {   // Pas de quete précédente
        echo 'Erreur: Impossible de déplacer cette étape.';
        break;
    }
    $etape_p1 = $db->f('equete_etape_cod'); // Première précédente, change de place

    // Suivante
    $req = 'select equete_etape_suivante from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_cod = ' . $etape_cod;
    $db->query($req);
    if (!$db->next_record() || $db->f('equete_etape_suivante') == 0)
    {   // Pas de quete suivante
        echo 'Erreur: La dernière étape doit rester en fin !';
        break;
    }
    $etape_suiv = $db->f('equete_etape_suivante'); // Nouvelle destination de p1

    // Seconde précédente
    $req = 'select equete_etape_cod from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_suivante = ' . $etape_p1;
    $db->query($req);
    $change_tete = false;
    $etape_p2 = 0;
    if (!$db->next_record())
    {
        // La seconde précédente est la tête de liste
        $change_tete = true;
    }
    else
    {
        $etape_p2 = $db->f('equete_etape_cod');
    }

    // On sort etape_cod de la liste, en faisant pointer p1 sur suiv
    $db->query('update quetes.quete_automatique_etape
        set equete_etape_suivante = ' . $etape_suiv . '
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_cod = ' . $etape_p1);

    // On fait maintenant pointer etape_cod sur p1, toujours hors liste
    $db->query('update quetes.quete_automatique_etape
        set equete_etape_suivante = ' . $etape_p1 . '
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_cod = ' . $etape_cod);

    // Enfin, on fait pointer p2 sur cod, pour finir la réinsertion
    if ($change_tete)
    {
        $db->query('update quetes.quete_automatique
            set aquete_etape_cod = ' . $etape_cod . '
            where aquete_cod = ' . $quete_cod);
    }
    else
    {
        $db->query('update quetes.quete_automatique_etape
            set equete_etape_suivante = ' . $etape_cod . '
            where equete_aquete_cod = ' . $quete_cod . '
            and equete_etape_cod = ' . $etape_p2);
    }
    echo 'Étape déplacee';
    break;
case 'supprime_etape':
    $etape_cod = $_GET['etape'];
    $quete_cod = $_GET['quete'];
    // Schema initial: p2->p1->cod->suiv
    // Schema final: p2->p1->suiv

    // Suivante
    $req = 'select equete_etape_suivante from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_cod = ' . $etape_cod;
    $db->query($req);
    if (!$db->next_record())
    {   // Pas dans la quete
        echo 'Erreur: Cette étape n\'est pas dans la quête choisie.';
        break;
    }
    else if ($db->f('equete_etape_suivante') == 0)
    {   // Pas de quete suivante
        echo 'Erreur: La dernière étape ne peut être supprimée !';
        break;
    }
    $etape_suiv = $db->f('equete_etape_suivante'); // Nouvelle destination de p1

    // Étape précédente
    $req = 'select equete_etape_cod from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_suivante = ' . $etape_cod;
    $db->query($req);
    $change_tete = false;
    $etape_p1 = 0;
    if (!$db->next_record())
    {
        // La précédente est la tête de liste
        $change_tete = true;
    }
    else
    {
        $etape_p1 = $db->f('equete_etape_cod');
    }

    // On fait pointer p1 sur suiv
    if ($change_tete)
    {
        $db->query('update quetes.quete_automatique
            set aquete_etape_cod = ' . $etape_suiv . '
            where aquete_cod = ' . $quete_cod);
    }
    else
    {
        $db->query('update quetes.quete_automatique_etape
            set equete_etape_suivante = ' . $etape_suiv . '
            where equete_aquete_cod = ' . $quete_cod . '
            and equete_etape_cod = ' . $etape_p1);
    }

    // On sort etape_cod de la quete
    $db->query('delete from quetes.quete_automatique_etape
        where equete_aquete_cod = ' . $quete_cod . '
        and equete_etape_cod = ' . $etape_cod);

    echo 'Étape supprimée';
    break;
default:
    echo 'Méthode inconnue: [' , $methode , ']';
}
