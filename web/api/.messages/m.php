<?php

/**
 * Indique si un message est lisible ou non
 *
 * @param[in] $msg_cod: Identifiant du message dans la base de données
 * @param[in] $perso_cod: Identifiant du personnage dans la base de données
 * @return
 */
function isMessageLisible($msg_cod, $perso_cod) {
  return true;
}

/**
 * Récupère la liste des destinataires d'un message
 *
 * @param[in] $msg_cod: Identifiant du message dans la base de données
 * @return la liste de tous les destinataires d'un messages
 */
function getDestinataires($msg_cod) {
  $destinataires = array();

  $requete = sprintf("...", $msg_cod);
  $db->query($requete);
  while ($db->next_record())
  {
    $destinataires[] = (object)array(
      'perso_cod' => $db->f('perso_cod'),
      'nom' => $db->f('perso_nom'),
      'lu' => (bool)($db->f('dmsg_lu') == 'O'),
      'archive' => (bool)($db->f('dmsg_archive') == 'O'),
      'efface' => (bool)($db->f('dmsg_efface') == 1),
    );
  }

  return $destinataires;
}

/**
 * Récupère un message
 *
 * @param[in] $msg_cod: Identifiant du message dans la base de données
 * @return un message 
 */
function getMessage($msg_cod) {
  global $db;

  $requete = sprintf("
select to_char(msg_date2,'DD/MM/YYYY hh24:mi:ss') as date_mes,
	msg_init,
	perso_nom,
	msg_titre,
	msg_cod,
	msg_corps,
	dmsg_cod,
	emsg_perso_cod,
	msg_guilde,
	msg_guilde_cod,
	dmsg_lu
from messages, messages_exp, perso, messages_dest
where msg_cod = %d
 and emsg_msg_cod = msg_cod
and emsg_perso_cod = perso_cod
and dmsg_msg_cod = msg_cod
order by msg_cod desc

", $msg_cod);
  $db->query($requete);
  $db->next_record();

  $message = (object)array(
    'msg_cod'  => $msg_cod,
    'titre'    => str_replace(chr(127), ';', $db->f('msg_titre')),
    'corps'    => str_replace(chr(127), ';', $db->f('msg_corps')),
    'date'     => $db->f('date_mes'),
    //'msg_init' => $db->f('msg_init'),
    'exp'      => (object)array(
      'perso_cod' => $db->f('perso_cod'),
      'nom'       => str_replace("'", "\'", $db->f('perso_nom')) . ";",
    ),
    //'dest' => getDestinataires($msg_cod),
  );
var_dump($message);
exit;
  return $message;
}
