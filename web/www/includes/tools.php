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