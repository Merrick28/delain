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
// Fonction bm_progressivite retourne une chaine avec l'evolution de d'un BM cumulatif
//=======================================================================================
function get_ea_trigger_param($post, $numero)
{
    $fonc_trigger_param = array();
    foreach ($post as $key => $val) {
        if ((substr($key, 0, 10) == "fonc_trig_") && (substr($key, -strlen("{$numero}")) == $numero) && !isset($_POST['checkbox_' . $key])) {
            if (is_array($val)) {
                // regarder s'il y a des objets à imbriquer (nom = object_[fonc_trig_nom+n°]_XXXXX
                $base = substr($key, 0, -strlen("{$numero}"));
                foreach ($_POST as $k => $v) {
                    if (substr($k, 0, strlen("obj_{$key}_")) == "obj_{$key}_") {
                        $name = substr($k, strlen("obj_{$base}_") + 1);
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

    return $fonc_trigger_param;
}