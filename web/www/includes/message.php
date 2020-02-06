<?php // Gère l’envoi de messages dans le jeu.
class message
{
	var $corps;
	var $sujet;
	var $expediteur;
	var $destinataires;
	var $msg_cod;
	var $enReponseA;
	var $guilde;

	function message()
	{
		$this->corps = '';
		$this->sujet = '';
		$this->expediteur = '';
		$this->destinataires = array();
		$this->enReponseA = 0;
		$this->guilde = -1;
	}
	
	static function Envoyer($expediteur, $destinataire, $sujet, $corps, $bloque_html = true)
	{
	    $msg = new message();
	    $msg->corps = $corps;
	    $msg->expediteur = $expediteur;
	    $msg->sujet = $sujet;
	    $msg->ajouteDestinataire($destinataire);
	    $msg->envoieMessage($bloque_html);
	}

	function ajouteDestinataire($numero)
	{
        if (false === array_search($numero, $this->destinataires))
		    $this->destinataires[] = $numero;
	}

	function envoieMessage($bloque_html = true)
	{
		if ($this->sujet === '' || $this->expediteur === '' || count($this->destinataires) === 0)
			return false;

		$sujet = base_delain::format($this->sujet);
		$corps = base_delain::format($this->corps, true, true, $bloque_html);
		$expediteur = $this->expediteur;
		$reponseA = $this->enReponseA;

		$ajout_champs = ($this->guilde >= 0) ? ', msg_guilde, msg_guilde_cod' : '';
		$ajout_valeurs = ($this->guilde >= 0) ? ", 'O', " . $this->guilde : '';

		$pdo = new bddpdo;

		// Création du message
		$req = "INSERT INTO messages (msg_date2, msg_date, msg_titre, msg_corps, msg_init $ajout_champs)
			VALUES (now(), now(), :sujet, :corps, $reponseA $ajout_valeurs)
			RETURNING msg_cod";
		$stmt = $pdo->prepare($req);
		$stmt = $pdo->execute(array(
		    ":sujet" => $sujet,
            ":coprs" => $corps
                              ),$stmt);
		$result = $stmt->fetch();
		$msg_cod = $result['msg_cod'];
        
		// Gestion des fils de discussion
		if ($this->enReponseA === 0)
		{
			$this->enReponseA = $msg_cod;
			$reponseA = $this->enReponseA;
			$req = "UPDATE messages SET msg_init = $reponseA WHERE msg_cod = :msg";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":msg" => $msg_cod),$stmt);
		}

		// Insertion de l’expéditeur
		$req = "insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
			values (:msg, :exp, 'N')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":msg" => $msg_cod,":exp" => $expediteur),$stmt);

		// Insertion des destinataires
		foreach($this->destinataires as $destinataire)
		{
			$req = "insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu ,dmsg_archive)
				values (:msg, :dest, 'N', 'N')";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":msg" => $msg_cod,":dest" => $destinataire),$stmt);
		}

		$this->msg_cod = $msg_cod;
		return true;
	}
}