<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Gestion de messagerie automatique</div><br />';

$erreur         = false;
$message_erreur = '';
$parm_cod       = 134; // Id du parametre global
$num_perso      = 1 * $num_perso;
$methode        = $_REQUEST['methode'];
switch ($methode)
{
    case 'mess_add':    // Modifie un paramètre global (ajout d'un element dans la liste)
        $erreur = ($num_perso<=0) ;
        $message_erreur = '';
        if ($erreur)
        {
            $message_erreur = 'Paramètres manquants.';
            break;
        }
        else
        {
            // on update memcached
            $param = new parametres();
            $param->charge($parm_cod);
            $parm_valeur = $param->parm_valeur_texte ;
            $list_cod = implode(",", array_map(function($cod){return 1*$cod;}, explode(",",$parm_valeur)));

            $req = "select perso_cod, perso_nom from perso where perso_cod={$num_perso} OR perso_cod in ({$list_cod}) order by perso_cod";

            $stmt = $pdo->query($req);
            $perso_cod_list = "";
            while ($result = $stmt->fetch()) $perso_cod_list.=$result['perso_cod'].',';
            if (strlen($perso_cod_list)>0)  $perso_cod_list = substr($perso_cod_list, 0, -1);

            $log .= "	Modification du paramètre n°$parm_cod « {$param->parm_desc} ».\n";
            $log .= "	Modification de la valeur : « {$param->parm_valeur_texte} » => « $perso_cod_list ».\n";

            $param->parm_valeur_texte = $perso_cod_list;
            $param->stocke();
        }
        break;

    case 'mess_del':    // Modifie un paramètre global (supression d'un element d'une liste)
        $erreur = ($num_perso<=0) ;
        $message_erreur = '';
        if ($erreur)
        {
            $message_erreur = 'Paramètres manquants.';
            break;
        }
        else
        {
            // on update memcached
            $param = new parametres();
            $param->charge($parm_cod);
            $parm_valeur = $param->parm_valeur_texte ;
            $list_cod = implode(",", array_map(function($cod){return 1*$cod;}, explode(",",$parm_valeur)));

            $req = "select perso_cod, perso_nom from perso where perso_cod<>{$num_perso} AND perso_cod in ({$list_cod}) order by perso_cod";

            $stmt = $pdo->query($req);
            $perso_cod_list = "";
            while ($result = $stmt->fetch()) $perso_cod_list.=$result['perso_cod'].',';
            if (strlen($perso_cod_list)>0)  $perso_cod_list = substr($perso_cod_list, 0, -1);

            $log .= "	Modification du paramètre n°$parm_cod « {$param->parm_desc} ».\n";
            $log .= "	Modification de la valeur : « {$param->parm_valeur_texte} » => « $perso_cod_list ».\n";

            $param->parm_valeur_texte = $perso_cod_list;
            $param->stocke();
        }
        break;
}
if (!$erreur && $log != '')
{
    echo "<div class='bordiv'><strong>Mise à jour des paramètres globaux</strong><br /><pre>$log</pre></div>";
    writelog($log,'params');
}
else if ($erreur && $message_erreur != '')
{
    echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>$message_erreur</pre></div>";
}

echo '<p>Liste de perso de joueurs à tenir informé par la messagerie interne (<em>équivalent au praramètre global '.$parm_cod.'</em>)</p>
	<table><tr>
		<td class="titre"><strong>Perso_cod</strong></td>
		<td class="titre"><strong>Perso</strong></td>
		<td class="titre"><strong>Supprimer ?</strong></td></tr>';
include 'sadmin.php';
echo "<tr><form name='login2' method='POST' action='#'>
	<td class='titre' style='padding:2px;'><input id='num_perso' name='num_perso' type='text' size='10' value=''/></td>
	<td class='titre' style='padding:2px;'><input type=\"text\" name=\"foo\" size='50' id=\"foo\" value=\"\" onkeyup=\"loadData(); document.getElementById('zoneResultats').style.visibility = 'hidden';\" />
	<ul id='zoneResultats' style=\"visibility: hidden; z-index: 999;float: right;position: absolute;margin-top: -2px;margin-left: 18px;border-color: black;border-width: thin;border-style: solid;border-radius: 10px;background-color: #BA9C6C;padding: 5px;\"></ul></td>
	<td class='titre' style='padding:2px;'><input type='hidden' name='methode' value='mess_add' /><input type='submit' value='Ajouter' class='test' /></td>
	</form></tr>";



  // on update memcached
  $param = new parametres();
  $param->charge($parm_cod);
  $parm_valeur = $param->parm_valeur_texte ;
  $list_cod = implode(",", array_map(function($cod){return 1*$cod;}, explode(",",$parm_valeur)));

  $req = "select perso_cod, perso_nom from perso where perso_cod in ({$list_cod}) order by perso_cod";
  //die($req);
  $stmt = $pdo->query($req);

  while ($result = $stmt->fetch())
  {
      $perso_cod = $result['perso_cod'];
      $perso_nom = str_replace('\'', '’', $result['perso_nom']);

      echo "<tr><form method='POST' action='#' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce perso de la liste ?\");'>
  		<td style='padding:2px;'><input disabled type='text' size='10' value='$perso_cod' /></td>
  		<td style='padding:2px;'><input disabled type='text' size='50' value='$perso_nom' /></td>";
      echo "<td style='padding:2px;'>
  		<input type='hidden' name='methode' value='mess_del' />
  		<input type='hidden' name='num_perso' value='$perso_cod' />
  		<input type='submit' value='Supprimer' class='test' />
  		</td></form></tr>";
  }
echo '</table></div>';
