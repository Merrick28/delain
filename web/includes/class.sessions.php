<?php

/**
 * class.sessions.php.php
 *
 * 
 * @author Stephane DEWITTE <stephane.dewitte@gmail.com>
 * @version 1.0
 * @filesource
 * @package default
 */
class sessions
{

    /**
     * @var integer
     * L'id
     */
    var $id;

    /**
     * @var text
     * le hash de la session
     */
    var $hash;

    function __construct()
    {
        if (rand(1, 100) <= 1)
        {
             $pdo = new bddpdo;
            $req = "delete from sessions
				where sess_date < now() - interval '12 hours'";
            $pdo->query($req);
        } //rand(1, 100) <= GC_GARBAGE
    }
    /**
     * fonction de chargement de la session
     * @param integer Id de la session
     */
    function charge($id)
    {
        $pdo = new bddpdo;

        $req        = "select sess_hash from sessions
			where sess_user_cod = ?";
        $stmt       = $pdo->prepare($req);
        $stmt       = $pdo->execute(array($id), $stmt);
        $result     = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id   = $id;
        $this->hash = $result['sess_hash'];
        return true;
    }

    /**
     * fonction de stockage de la session en bdd
     */
    function store()
    {
        $pdo  = new bddpdo;
        $req  = "insert into sessions (sess_user_cod,sess_hash)
			values
			(?,?)";

        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($this->id, $this->hash), $stmt);
        return true;
    }

}
