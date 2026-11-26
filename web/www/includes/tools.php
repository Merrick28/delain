<?php

//=======================================================================================
//=======================================================================================
// Fonctions utilitaires
//
// Version corrigée des fonctions d'édition des effets-auto (EA) :
//  - affichage : toutes les valeurs passées au JS sont encodées avec json_encode
//    (plus de rupture de script sur retour à la ligne, \, ", </script>, etc.)
//  - get_ea_trigger_param : plus de collision entre numéros d'EA (1 / 11 / 21...),
//    et json_encode sécurisé
//  - update_ea / insert_ea : plus d'index non définis (effet « Message » sans force,
//    durée, effet...), plus de warning PHP injecté dans la page
//
// Nécessite PHP >= 7.2 (JSON_INVALID_UTF8_SUBSTITUTE, opérateur ??)
//=======================================================================================
//=======================================================================================

//=======================================================================================
// == Fonction create_selectbox créer une boite de sélection à partir d'un tableau
//=======================================================================================
function create_selectbox($name, $data, $default='', $param=array())
{

    $out='<select ' .( isset($param["id"]) ? 'id="'.$param["id"].'"' : ''). ' name="' .$name. '" ' . (isset($param["style"]) ? $param["style"] :'') .">\n";

    $optgroup = isset($param["optgroup"]) ? $param["optgroup"] : [] ;
    $curgroup="";
    foreach($data as $key=>$val) {
        if (isset($optgroup[$key])) {
            if ($curgroup != $optgroup[$key]) {
                $curgroup = $optgroup[$key] ;
                if ($curgroup != "") $out.='</optgroup>'; // fermeture du precedent avant ouvertur du nouveau groupe
                $out.='<optgroup label="'.$curgroup.'">';
            }
        }
        $out.='<option value="' .$key. '"'. ($default==$key?' selected="selected"':'') .'>';
        $out.=$val;
        $out.="</option>\n";
    }
    if ($curgroup != "") $out.='</optgroup>'; // fermeture du dernier groupe
    $out.="</select>\n";

    return $out;

}#-# create_selectbox()

//=======================================================================================
// == Fonction create_selectbox_from_req créer une boite de sélection à partir d'une requete
//=======================================================================================
function create_selectbox_from_req($name, $req, $default='', $param=array())
{

    $pdo = new bddpdo;
    $stmt = $pdo->query($req);
    $data = array();
    $optgroup = array();
    while($result = $stmt->fetch(PDO::FETCH_NUM )) {
        $data[$result[0]] = $result[1] ;
        if (isset($result[2])) $optgroup[$result[0]] = $result[2] ;
    }
    if (sizeof($optgroup) >0 ) $param["optgroup"]=$optgroup;
    return create_selectbox($name, $data, $default, $param);

}#-# create_selectbox_from_table()


//=======================================================================================
// Fonction obj_diff retourne les diferences entre 2 objets pour mettre dans le log
//=======================================================================================
function obj_diff($obj1, $obj2, $texte="")
{
    $class_vars = get_class_vars(get_class($obj1));
    $diff = "" ;
    // la premère variable est la PK (primary key) on s'en passe
    $is_pk = true ;
    foreach ($class_vars as $name => $value) {
        if ((!$is_pk) && ($obj1->$name!=$obj2->$name)) $diff.= "      {$name} : {$obj1->$name} => {$obj2->$name}\n";
        $is_pk = false ;
    }
    if ($diff!="") $diff = $texte.$diff ;
    return $diff;
}


//=======================================================================================
// Fonction calul la moyenne d'une chaine au format dé rolliste
//=======================================================================================
function moy_des_roliste($des)
{
    $pdo = new bddpdo;

    if (is_int($des)) return $des;

    $val = explode("D", strtoupper("$des"));
    if ( sizeof($val) != 2 ) return (int)$val[0] ;

    if (strpos($val[1], "+") ) {
        $v = explode("+", $val[1] ) ;
        return round((int)$val[0] * (1 + (int)$v[0]) / 2,0) + (int)$v[1] ;
    } else if (strpos($val[1], "-") ) {
        $v = explode("-", $val[1] ) ;
        return round((int)$val[0] * (1 + (int)$v[0]) / 2,0) - (int)$v[1] ;
    }

    return round((int)$val[0] * (1 + (int)$val[1]) / 2,0) ;
}

//=======================================================================================
// Fonction bm_progressivite retourne une chaine avec l'evolution de d'un BM cumulatif
//=======================================================================================
function bm_progressivite($fonc_effet, $fonc_force)
{
    $pdo = new bddpdo;

    $req = "select bonus_progressivite(:bm, :force) as progressivite";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array( ":bm" => $fonc_effet, ":force" => moy_des_roliste($fonc_force) ), $stmt);
    if ($progres = $stmt->fetch())
    {
        return $progres["progressivite"];
    }
    else
    {
        return 'O' ;
    }
}

//=======================================================================================
// Helpers JSON
//=======================================================================================

/**
 * @param mixed $v
 * @return string
 * @throws Exception
 * json_encode pour le STOCKAGE en base (même format qu'avant : unicode échappé en \uXXXX).
 * Les octets UTF-8 invalides sont remplacés (U+FFFD) au lieu de faire échouer l'encodage,
 * car un json_encode qui renvoie false vide silencieusement fonc_trigger_param en base.
 */
function ea_json_encode($v)
{
    $json = json_encode($v, JSON_INVALID_UTF8_SUBSTITUTE);
    if ($json === false) {
        throw new Exception('EA : json_encode a échoué (' . json_last_error_msg() . ')');
    }
    return $json;
}

/**
 * @param mixed $v
 * @return string
 * json_encode pour l'INJECTION dans un bloc <script> : <, >, &, ' et " sont écrits en \uXXXX,
 * ce qui neutralise </script>, <!-- et toute sortie de chaîne.
 */
function ea_js_json($v)
{
    $json = json_encode(
        $v,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    return ($json === false) ? '""' : $json;
}

/**
 * @param mixed $v
 * @return string
 * Chaîne JS littérale (guillemets inclus) à partir d'une valeur quelconque.
 */
function ea_js_str($v)
{
    return ea_js_json((string)$v);
}

/**
 * @param string|null $raw contenu brut de fonc_trigger_param
 * @return string
 * Objet JS littéral à partir de fonc_trigger_param.
 * On décode puis on ré-encode : les champs à lignes multiples, stockés sous forme de chaîne
 * contenant du JSON (double encodage de get_ea_trigger_param), restent des chaînes,
 * ce que le JS attend (JSON.parse sur les chaînes).
 */
function ea_js_obj($raw)
{
    $raw = ($raw === null) ? '' : trim((string)$raw);
    $d = json_decode($raw === '' ? '{}' : $raw, true);
    if (!is_array($d)) {
        error_log("EA : fonc_trigger_param invalide : " . substr($raw, 0, 200));
        $d = [];
    }
    return $d ? ea_js_json($d) : '{}'; // {} et non [] quand c'est vide
}

//=======================================================================================
// Fonction retourne les paramètres specifiques pour les EA
//=======================================================================================
/**
 * @param array $post
 * @param int|string $numero numéro de l'EA dans le formulaire
 * @return array
 * Les champs sont nommés [checkbox_]fonc_trig_<nom><numero>.
 * Le numéro est découpé par expression régulière : l'ancien test « finit par le numéro »
 * confondait l'EA n° 1 avec les EA n° 11, 21... (et 2 avec 12, 22...).
 */
function get_ea_trigger_param($post, $numero)
{
    $fonc_trigger_param = array();
    $numero = (string)$numero;

    foreach ($post as $key => $val) {
        if (!preg_match('/^(checkbox_)?(fonc_trig_[a-zA-Z_]+)(\d+)$/', (string)$key, $m) || $m[3] !== $numero) {
            continue;
        }
        $nom = $m[2]; // nom du paramètre, sans le numéro

        if ($m[1] !== '') {
            // Case à cocher : le champ caché checkbox_xxx est toujours posté,
            // la case xxx n'est postée que si elle est cochée.
            $fonc_trigger_param[$nom] = isset($_POST[$nom . $numero]) ? 'O' : 'N';
        } elseif (isset($_POST['checkbox_' . $key])) {
            // visible case à cocher : sa valeur est gérée par le champ caché ci-dessus
            continue;
        } elseif (is_array($val)) {
            // Lignes multiples : on y fusionne les champs obj_<clé>_<nom>[]
            $prefixe = "obj_{$key}_";
            foreach ($_POST as $k => $v) {
                if (strpos((string)$k, $prefixe) === 0 && is_array($v)) {
                    $name = substr($k, strlen($prefixe));
                    foreach ($v as $kk => $vv) {
                        if (!isset($val[$kk]) || !is_array($val[$kk])) $val[$kk] = [];
                        $val[$kk][$name] = $vv;
                    }
                }
            }
            // Ce 1er encodage est volontaire : la valeur est stockée sous forme de chaîne JSON
            $fonc_trigger_param[$nom] = ea_json_encode($val);
        } else {
            $fonc_trigger_param[$nom] = $val;
        }
    }

    return $fonc_trigger_param;
}


/**
 * @param array $r une ligne de fonction_specifique
 * @param bool $validite
 * @param bool $heritage
 * @param bool $implantation
 * @return string
 * Génère UN appel JS EffetAuto.EcritEffetAutoExistant(...) avec toutes les valeurs correctement encodées.
 */
function ea_js_appel($r, $validite, $heritage, $implantation = false)
{
    $effet     = $r['fonc_effet'];
    $cumulatif = $r['fonc_cumulatif'];

    // on va enjoliver le champs cumulatif à l'affichage pour afficher les valeurs de progressivité.
    if ($cumulatif == 'O') $cumulatif = bm_progressivite($effet, $r['fonc_force']);

    $args = [
        ea_js_str($r['fonc_type']),
        ea_js_str($r['fonc_nom']),
        (int)$r['fonc_cod'],
        ea_js_str($r['fonc_force']),
        ea_js_str($r['fonc_duree']),
        ea_js_str($r['fonc_message']),      // plus de remplacement " -> ` : le message est restitué tel quel
        ea_js_str($effet),
        ea_js_str($cumulatif),
        ea_js_str($r['fonc_proba']),
        ea_js_str($r['fonc_type_cible']),
        ea_js_str($r['fonc_portee']),
        ea_js_str($r['fonc_nombre_cible']),
        ea_js_obj($r['fonc_trigger_param']),
        ea_js_str($validite ? ($r['validite'] ?? '0') : '0'),
        $heritage ? 'true' : 'false',
    ];
    if ($implantation) $args[] = 'true';

    return 'EffetAuto.EcritEffetAutoExistant(' . implode(', ', $args) . ");\n";
}

/**
 * @param $req
 * @param bool $validite
 * @param bool $heritage
 * @return string
 * affichage de javascript pour EffetAuto Existant
 */
function getJS_ea_existant($req, $validite=false, $heritage=false)
{
    $output="";

    $pdo = new bddpdo; // connection à la DB

    $implantation = [] ;
    $stmt = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $output .= ea_js_appel($result, $validite, $heritage);
        if ( $result['fonc_nom']=="ea_implantation_ea" ) $implantation[] = $result['fonc_effet'] ;
    }

    // Ajouter les EA implantées
    while (sizeof($implantation)>0) {

        $implantation2 = [] ;
        foreach($implantation as $fonc_cod) {
            $stmt = $pdo->prepare("select * from fonction_specifique where fonc_cod=:fonc_cod;");
            $stmt = $pdo->execute(array(":fonc_cod"         => $fonc_cod), $stmt);

            if ($result = $stmt->fetch())
            {
                $output .= ea_js_appel($result, $validite, $heritage, true);
                if ( $result['fonc_nom']=="ea_implantation_ea" ) $implantation2[] = $result['fonc_effet'] ;
            }
        }

        $implantation = $implantation2 ;
    }

    return $output ;
}


/**
 * @param $fonc_type
 * @return string
 * texte de déclenchement de l'effet
 */
function get_texte_declenchement($fonc_type)
{
    $texteDeclenchement = "Inconnu" ;

    switch ($fonc_type) {
        case 'D':
            $texteDeclenchement = 'le déclenchement de la DLT.';
            break;
        case 'T':
            $texteDeclenchement = 'la mort de la cible du personnage.';
            break;
        case 'M':
            $texteDeclenchement = 'la mort du personnage.';
            break;
        case 'A':
            $texteDeclenchement = 'une attaque portée.';
            break;
        case 'AE':
            $texteDeclenchement = 'une attaque esquivée.';
            break;
        case 'AT':
            $texteDeclenchement = 'une attaque portée qui touche.';
            break;
        case 'AC':
            $texteDeclenchement = 'une attaque subie.';
            break;
        case 'ACE':
            $texteDeclenchement = 'une esquive.';
            break;
        case 'ACT':
            $texteDeclenchement = 'une attaque subie qui touche.';
            break;
        case 'BMC':
            $texteDeclenchement = 'un Bonus/Malus change.';
            break;
        case 'DEP':
            $texteDeclenchement = 'le perso se déplace.';
            break;
        case 'MAL':
            $texteDeclenchement = 'le perso lance un sort.';
            break;
        case 'MAC':
            $texteDeclenchement = 'le perso est ciblé par un sort.';
            break;
        case 'POS':
            $texteDeclenchement = 'lorsqu’il arrive ou quitte une case à EA.';
            break;
        case 'CES':
            $texteDeclenchement = 'le perso change d’état de santé.';
            break;
        case 'OTR':
            $texteDeclenchement = 'le perso reçoit une transaction.';
            break;
    }
    return $texteDeclenchement ;
}

/**
 * @param $post
 * @param $numero
 * @param $fonc_cod
 * @return string
 * @throws Exception
 * Mise à jour d'un effet auto
 */
function update_ea($post, $numero, $fonc_cod)
{
    $pdo = new bddpdo; // connection à la DB

    $message = "";

    //require "blocks/_block_admin_traitement_type_monstre_edit.php";
    // Certains effets (ex. « Message ») n'ont pas tous les champs : on lit avec ?? null
    $fonc_effet        = $post['fonc_effet' . $numero] ?? null;
    $fonc_unite_valid  = (int)($post['fonc_validite_unite' . $numero] ?? 1);
    $fonc_validite     = $fonc_unite_valid * (int)($post['fonc_validite' . $numero] ?? 0);
    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";
    if (!empty($post['fonc_cumulatif' . $numero]) && ($post['fonc_cumulatif' . $numero] == "on") && $fonc_effet !== null) $fonc_effet .= "+";
    $fonc_trigger_param = get_ea_trigger_param($post, $numero);

    $req       = "UPDATE fonction_specifique
						SET fonc_effet = :fonc_effet,
							fonc_force = :fonc_force,
							fonc_duree = :fonc_duree,
							fonc_type_cible = :fonc_type_cible,
							fonc_nombre_cible = :fonc_nombre_cible,
							fonc_portee =:fonc_portee,
							fonc_proba = :fonc_proba,
							fonc_message = :fonc_message,
							fonc_trigger_param = :fonc_trigger_param, 
							fonc_date_limite = {$fonc_validite_sql}
						WHERE fonc_cod = :fonc_cod
						RETURNING fonc_nom, fonc_type";
    $stmt      = $pdo->prepare($req);
    $stmt      = $pdo->execute(array(
        ":fonc_cod"              => $fonc_cod,
        ":fonc_effet"            => $fonc_effet,
        ":fonc_force"            => $post['fonc_force' . $numero] ?? null,
        ":fonc_duree"            => $post['fonc_duree' . $numero] ?? null,
        ":fonc_type_cible"       => $post['fonc_cible' . $numero] ?? null,
        ":fonc_nombre_cible"     => $post['fonc_nombre' . $numero] ?? null,
        ":fonc_portee"           => $post['fonc_portee' . $numero] ?? null,
        ":fonc_proba"            => $post['fonc_proba' . $numero] ?? null,
        ":fonc_message"          => $post['fonc_message' . $numero] ?? null,
        ":fonc_trigger_param"    => ea_json_encode($fonc_trigger_param)
    ), $stmt);
    $result    = $stmt->fetch();
    $fonc_nom  = $result['fonc_nom'];
    $fonc_type = $result['fonc_type'];

    $texteDeclenchement = get_texte_declenchement($fonc_type) ;

    $message .= "Modification d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";

    return $message ;
}

/**
 * @param $fonc_cod
 * @return string
 * @throws Exception
 * supression d'un effet auto
 */
function delete_ea($fonc_cod)
{
    $pdo = new bddpdo; // connection à la DB

    $message = "";

    $req = "SELECT fonc_nom, fonc_type, fonc_effet FROM fonction_specifique WHERE fonc_cod = :fonc_cod";
    $stmt      = $pdo->prepare($req);
    $stmt      = $pdo->execute(array(  ":fonc_cod"  => $fonc_cod), $stmt);

    if ($result = $stmt->fetch())
    {
        $req = "DELETE FROM fonction_specifique WHERE fonc_cod = :fonc_cod";
        $stmt      = $pdo->prepare($req);
        $stmt      = $pdo->execute(array(  ":fonc_cod"  => $fonc_cod), $stmt);

        $fonc_type = $result['fonc_type'];
        $fonc_nom = $result['fonc_nom'];
        $fonc_effet = $result['fonc_effet'];

        // supprimer les enfants s'il y en a
        if ($fonc_nom == "ea_implantation_ea" ) $message.=delete_ea($fonc_effet);

        $texteDeclenchement = get_texte_declenchement($fonc_type);
        $message .= "Suppression d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";

    }

    return $message ;
}


/**
 * @param $post
 * @param $numero
 * @param $fonc_gmon_cod
 * @param $fonc_perso_cod
 * @return string
 * @throws Exception
 * Insertion nouvel effet auto
 */
function insert_ea($post, $numero, $fonc_gmon_cod, $fonc_perso_cod, $fonc_mode = null)
{
    $pdo = new bddpdo; // connection à la DB

    $message = "" ;

    $fonc_type = $post['declenchement_' . $numero];
    $fonc_nom = $post['fonction_type_' . $numero];
    // Certains effets (ex. « Message ») n'ont pas tous les champs : on lit avec ?? null
    $fonc_effet = $post['fonc_effet' . $numero] ?? null;
    if (!empty($post['fonc_cumulatif' . $numero]) && ($post['fonc_cumulatif' . $numero] == "on") && $fonc_effet !== null) $fonc_effet .= "+";

    $fonc_unite_valid = (int)($post['fonc_validite_unite' . $numero] ?? 1);
    $fonc_validite = $fonc_unite_valid * (int)($post['fonc_validite' . $numero] ?? 0);
    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";
    $fonc_trigger_param = get_ea_trigger_param($post, $numero);

    $req = "INSERT INTO fonction_specifique (fonc_nom, fonc_gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param, fonc_date_limite, fonc_mode)
						VALUES (:fonc_nom, :fonc_gmon_cod, :fonc_perso_cod, :fonc_type, :fonc_effet, :fonc_force, :fonc_duree, :fonc_type_cible, :fonc_nombre_cible, :fonc_portee, :fonc_proba, :fonc_message, :fonc_trigger_param, {$fonc_validite_sql}, :fonc_mode)
						RETURNING fonc_cod";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(
        ":fonc_nom" => $fonc_nom,
        ":fonc_gmon_cod" => $fonc_gmon_cod,
        ":fonc_perso_cod" => $fonc_perso_cod,
        ":fonc_type" => $fonc_type,
        ":fonc_effet" => $fonc_effet,
        ":fonc_force" => $post['fonc_force' . $numero] ?? null,
        ":fonc_duree" => $post['fonc_duree' . $numero] ?? null,
        ":fonc_type_cible" => $post['fonc_cible' . $numero] ?? null,
        ":fonc_nombre_cible" => $post['fonc_nombre' . $numero] ?? null,
        ":fonc_portee" => $post['fonc_portee' . $numero] ?? null,
        ":fonc_proba" => $post['fonc_proba' . $numero] ?? null,
        ":fonc_message" => $post['fonc_message' . $numero] ?? null,
        ":fonc_trigger_param" => ea_json_encode($fonc_trigger_param),
        ":fonc_mode" => $fonc_mode
    ), $stmt);
    $result    = $stmt->fetch();
    $fonc_cod  = $result['fonc_cod'];

    $texteDeclenchement = get_texte_declenchement($fonc_type) ;

    $message .= "Ajout d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";

    return ["fonc_cod"=>$fonc_cod, "message" => $message];
}

/**
 * @param $fonctions_implantation
 * @param $post
 * @param $numero
 * @param $fonc_gmon_cod
 * @param $fonc_perso_cod
 * @return array
 * @throws Exception
 * Sauvegarde un nouvel EA et eventuellement son EA fils (recursivement)
 * return le message de sauvegarde et le fonc_cod du nouvel EA
 */
function insert_eas($fonctions_implantation, $post, $numero, $fonc_gmon_cod, $fonc_perso_cod, $fonc_mode = null)
{
    $message = "";

    // s'il s'agit d'une EA implantrice, l'EA d'implantantée doit être sauvegardée avant (le fonc_cod de l'implantée devient le fonc_effet de l'implanteur)
    if (in_array($numero, $fonctions_implantation["implantrices"])){
        // sauvegarde de l'implantée (sans rattachment ni monstre ni perso)
        $result= insert_eas($fonctions_implantation, $post, $fonctions_implantation["implantation"][$numero], null, null, 'ea_implantation');

        // lier les 2 EA
        $post['fonc_effet' . $numero] = $result["fonc_cod"] ;
        $message.= $result["message"];
    }

    // maintenant qu'une eventuelle EA implantée est sauvegardée on peut sauver l'EA normale ou implantatrice
    $result= insert_ea($post, $numero, $fonc_gmon_cod, $fonc_perso_cod, $fonc_mode);
    $message.= $result["message"];

    return ["fonc_cod"=>$result["fonc_cod"], "message" => $message];
}


/**
 * @param $post
 * @param $fonc_gmon_cod
 * @param $fonc_perso_cod
 * @return string
 * sauvegarde (ajour/modif/supression) des effets auto
 */
function save_effet_auto($post, $fonc_gmon_cod, $fonc_perso_cod)
{
    $pdo = new bddpdo; // connection à la DB

    // Modification des effets automatiques
    $fonctions_supprimees = explode(',', trim(fonctions::format($post['fonctions_supprimees']), ','));
    $fonctions_annulees   = explode(',', trim(fonctions::format($post['fonctions_annulees']), ','));
    $fonctions_existantes = explode(',', trim(fonctions::format($post['fonctions_existantes']), ','));
    $fonctions_ajoutees   = explode(',', trim(fonctions::format($post['fonctions_ajoutees']), ','));

    $fonctions_implantation = array( "implantation" => [], "implantrices"=>[], "implantees"=>[] ) ;

    // On commence par recuppérer les numeros de toutes les ea_implantées (leur sauvegarde est particulières)
    foreach ($post as $key => $val) {
        if (substr($key, 0, 15) == "ea_implantation")
        {
            $numero = substr($key, 15);
            $fonctions_implantation["implantrices"][] = $numero;
            $fonctions_implantation["implantees"][] = $val;
            $fonctions_implantation["implantation"][$numero] = $val;
        }
    }


    $message = '';
    // Ajout d’effet --------------------------------------
    foreach ($fonctions_ajoutees as $numero)
    {
        if (!empty($numero) || $numero === 0)
        {
            // on ne sauvegarde pas les annulées, ni les implanté qui seront sauvegardée lors de la sauvegarde de l'implantrice
            if (!in_array($numero, $fonctions_annulees) && !in_array($numero, $fonctions_implantation["implantees"]))
            {
                $status = insert_eas($fonctions_implantation, $post, $numero, $fonc_gmon_cod, $fonc_perso_cod);

                $message.= $status["message"];
            }
        }
    }

    // Modification d’effet --------------------------------------
    foreach ($fonctions_existantes as $numero)
    {
        if (!empty($numero) || $numero === 0)
        {
            if (!isset($post['fonc_id' . $numero])) {
                continue; // EA non modifié côté client, ses champs n'ont pas été soumis
            }
            $fonc_cod = fonctions::format($post['fonc_id' . $numero]);
            if (!in_array($fonc_cod, $fonctions_supprimees))
            {
                $message.= update_ea($post, $numero, $fonc_cod);
            }
        }
    }

    // Suppression d’effet --------------------------------------
    foreach ($fonctions_supprimees as $cod)
    {
        if (!empty($cod) || $cod === 0)
        {
            $message.= delete_ea($cod);
        }
    }

    return $message ;
}