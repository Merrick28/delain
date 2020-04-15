<?php // Gère l’envoi de messages dans le jeu.
class message
{
    public $corps;
    public $sujet;
    public $expediteur;
    public $destinataires;
    public $msg_cod;
    public $enReponseA;
    public $guilde;

    public function __construct()
    {
        $this->corps         = '';
        $this->sujet         = '';
        $this->expediteur    = '';
        $this->destinataires = array();
        $this->enReponseA    = 0;
        $this->guilde        = -1;
    }

    public static function Envoyer($expediteur, $destinataire, $sujet, $corps, $bloque_html = true)
    {
        $msg             = new message();
        $msg->corps      = $corps;
        $msg->expediteur = $expediteur;
        $msg->sujet      = $sujet;
        $msg->ajouteDestinataire($destinataire);
        $msg->envoieMessage($bloque_html);
    }

    public function ajouteDestinataire($numero)
    {
        if (false === array_search($numero, $this->destinataires))
        {
            $this->destinataires[] = $numero;
        }
    }


    public function envoieMessage($bloque_html = true)
    {
        if ($this->sujet === '' || $this->expediteur === '' || count($this->destinataires) === 0)
        {
            return false;
        }

        $sujet = $this->sujet;
        if ($bloque_html)
        {
            $corps = htmlspecialchars($this->corps);
        }

        $expediteur = $this->expediteur;
        $reponseA   = $this->enReponseA;

        $ajout_champs  = ($this->guilde >= 0) ? ', msg_guilde, msg_guilde_cod' : '';
        $ajout_valeurs = ($this->guilde >= 0) ? ", 'O', " . $this->guilde : '';

        $pdo = new bddpdo;

        // Création du message
        $req     = "INSERT INTO messages (msg_date2, msg_date, msg_titre, msg_corps, msg_init $ajout_champs)
			VALUES (now(), now(), :sujet, :corps, $reponseA $ajout_valeurs)
			RETURNING msg_cod";
        $stmt    = $pdo->prepare($req);
        $stmt    = $pdo->execute(array(
                                     ":sujet" => $sujet,
                                     ":corps" => $corps
                                 ), $stmt);
        $result  = $stmt->fetch();
        $msg_cod = $result['msg_cod'];

        // Gestion des fils de discussion
        if ($this->enReponseA === 0)
        {
            $this->enReponseA = $msg_cod;
            $reponseA         = $this->enReponseA;
            $req              = "UPDATE messages SET msg_init = $reponseA WHERE msg_cod = :msg";
            $stmt             = $pdo->prepare($req);
            $stmt             = $pdo->execute(array(":msg" => $msg_cod), $stmt);
        }

        // Insertion de l’expéditeur
        $req  = "insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
			values (:msg, :exp, 'N')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":msg" => $msg_cod, ":exp" => $expediteur), $stmt);

        // Insertion des destinataires
        foreach ($this->destinataires as $destinataire)
        {
            $req  = "insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu ,dmsg_archive)
				values (:msg, :dest, 'N', 'N')";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":msg" => $msg_cod, ":dest" => $destinataire), $stmt);
        }

        $this->msg_cod = $msg_cod;
        return true;
    }

    function mess_chef_coterie($titre, $corps, $coterie, $perso_cod)
    {
        $pdo  = new bddpdo;
        $req  = 'select groupe_chef from groupe, perso where groupe_cod = :coterie and groupe_chef = perso_cod';
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":coterie" => $coterie), $stmt);
        if ($result = $stmt->fetch())
        {
            $chef             = $result['groupe_chef'];
            $this->corps      = $corps;
            $this->sujet      = $titre;
            $this->expediteur = $perso_cod;
            $this->ajouteDestinataire($chef);
            $this->envoieMessage();
        }
        return 'ok';
    }

    function mess_all_coterie($titre, $corps, $coterie)
    {
        $pdo    = new bddpdo;
        $req    = 'select groupe_chef from groupe where groupe_cod = :coterie';
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":coterie" => $coterie), $stmt);
        $result = $stmt->fetch();
        $chef   = $result['groupe_chef'];

        $this->corps      = $corps;
        $this->sujet      = $titre;
        $this->expediteur = $chef;
        $req              = "select pgroupe_perso_cod
			from groupe_perso
			where pgroupe_groupe_cod = :coterie
			and pgroupe_statut =  1
			and pgroupe_messages = 1";
        $stmt             = $pdo->prepare($req);
        $stmt             = $pdo->execute(array(":coterie" => $coterie), $stmt);
        while ($result = $stmt->fetch())
        {
            $this->ajouteDestinataire($result['pgroupe_perso_cod']);
        }
        $this->envoieMessage();
        return 'ok';
    }
}
