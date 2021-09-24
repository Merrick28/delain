<?php

//=======================================================================================
//=======================================================================================
// Fonctions utilitaires

//=======================================================================================
// == Fonction create_selectbox créer une boite de sélection à partir d'un tableau
//=======================================================================================
function create_selectbox($name, $data, $default='', $param=array())
{

    $out='<select ' .( isset($param["id"]) ? 'id="'.$param["id"].'"' : ''). ' name="' .$name. '" ' . (isset($param["style"]) ? $param["style"] :'') .">\n";

    foreach($data as $key=>$val) {
        $out.='<option value="' .$key. '"'. ($default==$key?' selected="selected"':'') .'>';
        $out.=$val;
        $out.="</option>\n";
    }
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
    while($result = $stmt->fetch(PDO::FETCH_NUM )) $data[$result[0]] = $result[1] ;
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
// Fonction bm_progressivite retourne une chaine avec l'evolution de d'un BM cumulatif
//=======================================================================================
function bm_progressivite($fonc_effet, $fonc_force)
{
    $pdo = new bddpdo;

    $req = "select bonus_progressivite(:bm, :force) as progressivite";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array( ":bm" => $fonc_effet, ":force" => $fonc_force ), $stmt);
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
// Fonction retourne les paramètres specifiques pour les EA
//=======================================================================================
function get_ea_trigger_param($post, $numero)
{
    $fonc_trigger_param = array();
    foreach ($post as $key => $val) {
        if ((substr($key, 0, 10) == "fonc_trig_") && (substr($key, -strlen("{$numero}")) == $numero) && !isset($_POST['checkbox_' . $key])) {
            if (is_array($val)) {
                // regarder s'il y a des objets à imbriquer (nom = obj_[fonc_trig_nom+n°]_XXXXX
                $base = substr($key, 0, -strlen("{$numero}"));

                foreach ($_POST as $k => $v) {
                    if (substr($k, 0, strlen("obj_{$key}_")) == "obj_{$key}_") {
                        $name = substr($k, strlen("obj_{$base}_") + strlen("{$numero}"));
                        foreach ($v as $kk => $vv) {
                            if (!is_array($val[$kk])) $val[$kk] = [];
                            $val[$kk][$name] = $vv;
                        }
                    }
                }
                $fonc_trigger_param[substr($key, 0, -strlen("{$numero}"))] = json_encode($val);
            } else {
                $fonc_trigger_param[substr($key, 0, -strlen("{$numero}"))] = $val;
            }
        } else if ((substr($key, 0, 19) == "checkbox_fonc_trig_") && (substr($key, -strlen("{$numero}")) == $numero)) {
            if (isset($_POST[substr($key, 9)]))
                $fonc_trigger_param[substr($key, 9, -strlen("{$numero}"))] = 'O';
            else
                $fonc_trigger_param[substr($key, 9, -strlen("{$numero}"))] = 'N';
        }
    }

    //echo "<pre>"; print_r(array("post"=>$post, "numero"=>$numero, "fonc_trigger_param"=>$fonc_trigger_param)); echo "</pre>";
    return $fonc_trigger_param;
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
        $fonc_id                = $result['fonc_cod'];
        $fonc_type              = $result['fonc_type'];
        $fonc_nom               = $result['fonc_nom'];
        $fonc_effet             = $result['fonc_effet'];
        $fonc_cumulatif         = $result['fonc_cumulatif'];
        $fonc_force             = $result['fonc_force'];
        $fonc_duree             = $result['fonc_duree'];
        $fonc_type_cible        = $result['fonc_type_cible'];
        $fonc_nombre_cible      = $result['fonc_nombre_cible'];
        $fonc_portee            = $result['fonc_portee'];
        $fonc_proba             = $result['fonc_proba'];
        $fonc_message           = $result['fonc_message'];
        $fonc_trigger_param     = $result['fonc_trigger_param'] == "" ? "{}" : $result['fonc_trigger_param'];
        if ($validite) {
            $fonc_validite      = $result['validite'];
        } else         {
            $fonc_validite      ="0";
        }
        if ($heritage) {
            $fonc_heritage      = "true";
        } else         {
            $fonc_heritage      = "false";
        }

        // on va enjoliver le champs cumulatif à l'affichage pour afficher les valeurs de progressivité.
        if ($fonc_cumulatif=='O') $fonc_cumulatif = bm_progressivite($fonc_effet, $fonc_force);

        $fonc_message = str_replace('"', '`', $fonc_message);
        $output.= "EffetAuto.EcritEffetAutoExistant(\"$fonc_type\", \"$fonc_nom\", $fonc_id, \"$fonc_force\", \"$fonc_duree\", \"$fonc_message\", \"$fonc_effet\", \"$fonc_cumulatif\", \"$fonc_proba\", \"$fonc_type_cible\", \"$fonc_portee\", \"$fonc_nombre_cible\", $fonc_trigger_param, \"$fonc_validite\", $fonc_heritage);
                  ";
        if ( $fonc_nom=="ea_implantation_ea" ) $implantation[] = $fonc_effet ;
    }

    // Ajouter les EA implantées
    while (sizeof($implantation)>0) {

        $implantation2 = [] ;
        foreach($implantation as $fonc_cod) {
            $stmt = $pdo->prepare("select * from fonction_specifique where fonc_cod=:fonc_cod;");
            $stmt = $pdo->execute(array(":fonc_cod"         => $fonc_cod), $stmt);

            if ($result = $stmt->fetch())
            {
                $fonc_id                = $result['fonc_cod'];
                $fonc_type              = $result['fonc_type'];
                $fonc_nom               = $result['fonc_nom'];
                $fonc_effet             = $result['fonc_effet'];
                $fonc_cumulatif         = $result['fonc_cumulatif'];
                $fonc_force             = $result['fonc_force'];
                $fonc_duree             = $result['fonc_duree'];
                $fonc_type_cible        = $result['fonc_type_cible'];
                $fonc_nombre_cible      = $result['fonc_nombre_cible'];
                $fonc_portee            = $result['fonc_portee'];
                $fonc_proba             = $result['fonc_proba'];
                $fonc_message           = $result['fonc_message'];
                $fonc_trigger_param     = $result['fonc_trigger_param'] == "" ? "{}" : $result['fonc_trigger_param'];
                if ($validite) {
                    $fonc_validite      = $result['validite'];
                } else         {
                    $fonc_validite      ="0";
                }
                if ($heritage) {
                    $fonc_heritage      = "true";
                } else         {
                    $fonc_heritage      = "false";
                }

                // on va enjoliver le champs cumulatif à l'affichage pour afficher les valeurs de progressivité.
                if ($fonc_cumulatif=='O') $fonc_cumulatif = bm_progressivite($fonc_effet, $fonc_force);

                $output.= "EffetAuto.EcritEffetAutoExistant(\"$fonc_type\", \"$fonc_nom\", $fonc_id, \"$fonc_force\", \"$fonc_duree\", \"$fonc_message\", \"$fonc_effet\", \"$fonc_cumulatif\", \"$fonc_proba\", \"$fonc_type_cible\", \"$fonc_portee\", \"$fonc_nombre_cible\", $fonc_trigger_param, \"$fonc_validite\", $fonc_heritage);
                  ";
                if ( $fonc_nom=="ea_implantation_ea" ) $implantation2[] = $fonc_effet ;
            };
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
    $fonc_effet  = $post['fonc_effet' . $numero];
    $fonc_unite_valid  = $post['fonc_validite_unite' . $numero];
    $fonc_validite     = $fonc_unite_valid * $post['fonc_validite' . $numero];
    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";
    if (!empty($post['fonc_cumulatif' . $numero]) && ($post['fonc_cumulatif' . $numero] == "on")) $fonc_effet .= "+";
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
        ":fonc_force"            => $post['fonc_force' . $numero],
        ":fonc_duree"            => $post['fonc_duree' . $numero],
        ":fonc_type_cible"       => $post['fonc_cible' . $numero],
        ":fonc_nombre_cible"     => $post['fonc_nombre' . $numero],
        ":fonc_portee"           => $post['fonc_portee' . $numero],
        ":fonc_proba"            => $post['fonc_proba' . $numero],
        ":fonc_message"          => $post['fonc_message' . $numero],
        ":fonc_trigger_param"    => json_encode($fonc_trigger_param)
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
function insert_ea($post, $numero, $fonc_gmon_cod, $fonc_perso_cod)
{
    $pdo = new bddpdo; // connection à la DB

    $message = "" ;

    $fonc_type = $post['declenchement_' . $numero];
    $fonc_nom = $post['fonction_type_' . $numero];
    $fonc_effet = $post['fonc_effet' . $numero];
    if (!empty($post['fonc_cumulatif' . $numero]) && ($post['fonc_cumulatif' . $numero] == "on")) $fonc_effet .= "+";

    $fonc_unite_valid = 1 * $post['fonc_validite_unite' . $numero];
    $fonc_validite = $fonc_unite_valid * $post['fonc_validite' . $numero];
    $fonc_validite_sql = ($fonc_validite === 0) ? "NULL" : "now() + '$fonc_validite minutes'::interval";
    $fonc_trigger_param = get_ea_trigger_param($post, $numero);

    $req = "INSERT INTO fonction_specifique (fonc_nom, fonc_gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message, fonc_trigger_param, fonc_date_limite)
						VALUES (:fonc_nom, :fonc_gmon_cod, :fonc_perso_cod, :fonc_type, :fonc_effet, :fonc_force, :fonc_duree, :fonc_type_cible, :fonc_nombre_cible, :fonc_portee, :fonc_proba, :fonc_message, :fonc_trigger_param, {$fonc_validite_sql})
						RETURNING fonc_cod";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(
        ":fonc_nom" => $fonc_nom,
        ":fonc_gmon_cod" => $fonc_gmon_cod,
        ":fonc_perso_cod" => $fonc_perso_cod,
        ":fonc_type" => $fonc_type,
        ":fonc_effet" => $fonc_effet,
        ":fonc_force" => $post['fonc_force' . $numero],
        ":fonc_duree" => $post['fonc_duree' . $numero],
        ":fonc_type_cible" => $post['fonc_cible' . $numero],
        ":fonc_nombre_cible" => $post['fonc_nombre' . $numero],
        ":fonc_portee" => $post['fonc_portee' . $numero],
        ":fonc_proba" => $post['fonc_proba' . $numero],
        ":fonc_message" => $post['fonc_message' . $numero],
        ":fonc_trigger_param" => json_encode($fonc_trigger_param)
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
function insert_eas($fonctions_implantation, $post, $numero, $fonc_gmon_cod, $fonc_perso_cod)
{
    $message = "";

    // s'il s'agit d'une EA implantrice, l'EA d'implantantée doit être sauvegardée avant (le fonc_cod de l'implantée devient le fonc_effet de l'implanteur)
    if (in_array($numero, $fonctions_implantation["implantrices"])){
        // sauvegarde de l'implantée (sans rattachment ni monstre ni perso)
        $result= insert_eas($fonctions_implantation, $post, $fonctions_implantation["implantation"][$numero], null, null);

        // lier les 2 EA
        $post['fonc_effet' . $numero] = $result["fonc_cod"] ;
        $message.= $result["message"];
    }

    // maintenant qu'une eventuelle EA implantée est sauvegardée on peut sauver l'EA normale ou implantatrice
    $result= insert_ea($post, $numero, $fonc_gmon_cod, $fonc_perso_cod);
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
