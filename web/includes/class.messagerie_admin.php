<?php
class messagerie_admin
{
    var $PARAM_ADMIN_LIST = 134;    // liste d'admin concernés par les messages (paramètre global n°134)

    function send_message($titre, $corps, $emetteur="")
    {
        $param = new parametres();
        $param->charge($this->PARAM_ADMIN_LIST);
        $admin_perso_cod_list = explode(',', $param->parm_valeur_texte) ;

        // Controle
        if (sizeof($admin_perso_cod_list)<=0) return -1;  // liste d'admin non défini

        $pdo = new bddpdo;
        $emsg_perso_cod = 1*$emetteur ; // par défaut, l'emetteur est un perso_cod
        if (($emsg_perso_cod==0) && ($emetteur!=0) && ($emetteur!=""))
        {
            // L'emetteur est un nom de perso, il faut récupérer l'id
            $req = "select f_cherche_perso(?) as emsg_perso_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array($emetteur), $stmt);
            $result = $stmt->fetch();
            $emsg_perso_cod = $result['emsg_perso_cod'];
        }
        if ($emsg_perso_cod==0)
        {
            $emsg_perso_cod = 1 ;   // Guildwen par défaut !
        }

        // D'abord le message !
        $req    = "INSERT INTO messages (msg_date2,msg_date,msg_titre,msg_corps) values (now(),now(),:msg_titre,:msg_corps) RETURNING msg_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(  ":msg_titre" => $titre,
                                        ":msg_corps" => $corps), $stmt);
        $result = $stmt->fetch();
        $msg_cod = $result['msg_cod'];

        // Ajout dans la table expediteur !
        $req    = "INSERT INTO messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) values (:emsg_msg_cod,:emsg_perso_cod,'N');";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(  ":emsg_msg_cod" => $msg_cod,
                                        ":emsg_perso_cod" => $emsg_perso_cod), $stmt);

        /*******************************/
        /* On envoi à tous les admins */
        /******************************/
        foreach($admin_perso_cod_list as $admin_perso_cod)
        {
            $dmsg_perso_cod = 1*$admin_perso_cod;
            if ($dmsg_perso_cod>0)
            {
                // Ajout dans la table destinataires !
                $req    = "INSERT INTO messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values (:dmsg_msg_cod,:dmsg_perso_cod,'N','N');";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(  ":dmsg_msg_cod" => $msg_cod,
                                                ":dmsg_perso_cod" => $dmsg_perso_cod), $stmt);
            }
        }
    }
}