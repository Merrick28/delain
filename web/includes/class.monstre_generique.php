<?php

/**
 * includes/class.monstre_generique.php
 */

/**
 * Class monstre_generique
 *
 * Gère les objets BDD de la table monstre_generique
 */
class monstre_generique
{

    var $gmon_cod;
    var $gmon_nom;
    var $gmon_for;
    var $gmon_dex;
    var $gmon_int;
    var $gmon_con;
    var $gmon_race_cod;
    var $gmon_temps_tour    = 720;
    var $gmon_des_regen     = 1;
    var $gmon_valeur_regen  = 3;
    var $gmon_vue           = 3;
    var $gmon_amelioration_vue;
    var $gmon_amelioration_regen;
    var $gmon_amelioration_degats;
    var $gmon_amelioration_armure;
    var $gmon_niveau;
    var $gmon_nb_des_degats;
    var $gmon_val_des_degats;
    var $gmon_or;
    var $gmon_arme;
    var $gmon_armure;
    var $gmon_soutien;
    var $gmon_amel_deg_dist;
    var $gmon_vampirisme;
    var $gmon_taille;
    var $gmon_avatar;
    var $gmon_description;
    var $gmon_fonction_mort;
    var $gmon_type_ia       = 1;
    var $gmon_fonction_debut_dlt;
    var $gmon_fonction_tue;
    var $gmon_serie_arme_cod;
    var $gmon_serie_armure_cod;
    var $gmon_pv;
    var $gmon_pourcentage_aleatoire;
    var $gmon_nb_receptacle = 0;
    var $gmon_quete         = 'N';
    var $gmon_duree_vie     = 0;

    function __construct()
    {
        
    }

    /**
     * Charge dans la classe un enregistrement de monstre_generique
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from monstre_generique where gmon_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->gmon_cod                   = $result['gmon_cod'];
        $this->gmon_nom                   = $result['gmon_nom'];
        $this->gmon_for                   = $result['gmon_for'];
        $this->gmon_dex                   = $result['gmon_dex'];
        $this->gmon_int                   = $result['gmon_int'];
        $this->gmon_con                   = $result['gmon_con'];
        $this->gmon_race_cod              = $result['gmon_race_cod'];
        $this->gmon_temps_tour            = $result['gmon_temps_tour'];
        $this->gmon_des_regen             = $result['gmon_des_regen'];
        $this->gmon_valeur_regen          = $result['gmon_valeur_regen'];
        $this->gmon_vue                   = $result['gmon_vue'];
        $this->gmon_amelioration_vue      = $result['gmon_amelioration_vue'];
        $this->gmon_amelioration_regen    = $result['gmon_amelioration_regen'];
        $this->gmon_amelioration_degats   = $result['gmon_amelioration_degats'];
        $this->gmon_amelioration_armure   = $result['gmon_amelioration_armure'];
        $this->gmon_niveau                = $result['gmon_niveau'];
        $this->gmon_nb_des_degats         = $result['gmon_nb_des_degats'];
        $this->gmon_val_des_degats        = $result['gmon_val_des_degats'];
        $this->gmon_or                    = $result['gmon_or'];
        $this->gmon_arme                  = $result['gmon_arme'];
        $this->gmon_armure                = $result['gmon_armure'];
        $this->gmon_soutien               = $result['gmon_soutien'];
        $this->gmon_amel_deg_dist         = $result['gmon_amel_deg_dist'];
        $this->gmon_vampirisme            = $result['gmon_vampirisme'];
        $this->gmon_taille                = $result['gmon_taille'];
        $this->gmon_avatar                = $result['gmon_avatar'];
        $this->gmon_description           = $result['gmon_description'];
        $this->gmon_fonction_mort         = $result['gmon_fonction_mort'];
        $this->gmon_type_ia               = $result['gmon_type_ia'];
        $this->gmon_fonction_debut_dlt    = $result['gmon_fonction_debut_dlt'];
        $this->gmon_fonction_tue          = $result['gmon_fonction_tue'];
        $this->gmon_serie_arme_cod        = $result['gmon_serie_arme_cod'];
        $this->gmon_serie_armure_cod      = $result['gmon_serie_armure_cod'];
        $this->gmon_pv                    = $result['gmon_pv'];
        $this->gmon_pourcentage_aleatoire = $result['gmon_pourcentage_aleatoire'];
        $this->gmon_nb_receptacle         = $result['gmon_nb_receptacle'];
        $this->gmon_quete                 = $result['gmon_quete'];
        $this->gmon_duree_vie             = $result['gmon_duree_vie'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into monstre_generique (
            gmon_nom,
            gmon_for,
            gmon_dex,
            gmon_int,
            gmon_con,
            gmon_race_cod,
            gmon_temps_tour,
            gmon_des_regen,
            gmon_valeur_regen,
            gmon_vue,
            gmon_amelioration_vue,
            gmon_amelioration_regen,
            gmon_amelioration_degats,
            gmon_amelioration_armure,
            gmon_niveau,
            gmon_nb_des_degats,
            gmon_val_des_degats,
            gmon_or,
            gmon_arme,
            gmon_armure,
            gmon_soutien,
            gmon_amel_deg_dist,
            gmon_vampirisme,
            gmon_taille,
            gmon_avatar,
            gmon_description,
            gmon_fonction_mort,
            gmon_type_ia,
            gmon_fonction_debut_dlt,
            gmon_fonction_tue,
            gmon_serie_arme_cod,
            gmon_serie_armure_cod,
            gmon_pv,
            gmon_pourcentage_aleatoire,
            gmon_nb_receptacle,
            gmon_quete,
            gmon_duree_vie                        )
                    values
                    (
                        :gmon_nom,
                        :gmon_for,
                        :gmon_dex,
                        :gmon_int,
                        :gmon_con,
                        :gmon_race_cod,
                        :gmon_temps_tour,
                        :gmon_des_regen,
                        :gmon_valeur_regen,
                        :gmon_vue,
                        :gmon_amelioration_vue,
                        :gmon_amelioration_regen,
                        :gmon_amelioration_degats,
                        :gmon_amelioration_armure,
                        :gmon_niveau,
                        :gmon_nb_des_degats,
                        :gmon_val_des_degats,
                        :gmon_or,
                        :gmon_arme,
                        :gmon_armure,
                        :gmon_soutien,
                        :gmon_amel_deg_dist,
                        :gmon_vampirisme,
                        :gmon_taille,
                        :gmon_avatar,
                        :gmon_description,
                        :gmon_fonction_mort,
                        :gmon_type_ia,
                        :gmon_fonction_debut_dlt,
                        :gmon_fonction_tue,
                        :gmon_serie_arme_cod,
                        :gmon_serie_armure_cod,
                        :gmon_pv,
                        :gmon_pourcentage_aleatoire,
                        :gmon_nb_receptacle,
                        :gmon_quete,
                        :gmon_duree_vie                        )
    returning gmon_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":gmon_nom"                   => $this->gmon_nom,
               ":gmon_for"                   => $this->gmon_for,
               ":gmon_dex"                   => $this->gmon_dex,
               ":gmon_int"                   => $this->gmon_int,
               ":gmon_con"                   => $this->gmon_con,
               ":gmon_race_cod"              => $this->gmon_race_cod,
               ":gmon_temps_tour"            => $this->gmon_temps_tour,
               ":gmon_des_regen"             => $this->gmon_des_regen,
               ":gmon_valeur_regen"          => $this->gmon_valeur_regen,
               ":gmon_vue"                   => $this->gmon_vue,
               ":gmon_amelioration_vue"      => $this->gmon_amelioration_vue,
               ":gmon_amelioration_regen"    => $this->gmon_amelioration_regen,
               ":gmon_amelioration_degats"   => $this->gmon_amelioration_degats,
               ":gmon_amelioration_armure"   => $this->gmon_amelioration_armure,
               ":gmon_niveau"                => $this->gmon_niveau,
               ":gmon_nb_des_degats"         => $this->gmon_nb_des_degats,
               ":gmon_val_des_degats"        => $this->gmon_val_des_degats,
               ":gmon_or"                    => $this->gmon_or,
               ":gmon_arme"                  => $this->gmon_arme,
               ":gmon_armure"                => $this->gmon_armure,
               ":gmon_soutien"               => $this->gmon_soutien,
               ":gmon_amel_deg_dist"         => $this->gmon_amel_deg_dist,
               ":gmon_vampirisme"            => $this->gmon_vampirisme,
               ":gmon_taille"                => $this->gmon_taille,
               ":gmon_avatar"                => $this->gmon_avatar,
               ":gmon_description"           => $this->gmon_description,
               ":gmon_fonction_mort"         => $this->gmon_fonction_mort,
               ":gmon_type_ia"               => $this->gmon_type_ia,
               ":gmon_fonction_debut_dlt"    => $this->gmon_fonction_debut_dlt,
               ":gmon_fonction_tue"          => $this->gmon_fonction_tue,
               ":gmon_serie_arme_cod"        => $this->gmon_serie_arme_cod,
               ":gmon_serie_armure_cod"      => $this->gmon_serie_armure_cod,
               ":gmon_pv"                    => $this->gmon_pv,
               ":gmon_pourcentage_aleatoire" => $this->gmon_pourcentage_aleatoire,
               ":gmon_nb_receptacle"         => $this->gmon_nb_receptacle,
               ":gmon_quete"                 => $this->gmon_quete,
               ":gmon_duree_vie"             => $this->gmon_duree_vie,
               ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update monstre_generique
                    set
            gmon_nom = :gmon_nom,
            gmon_for = :gmon_for,
            gmon_dex = :gmon_dex,
            gmon_int = :gmon_int,
            gmon_con = :gmon_con,
            gmon_race_cod = :gmon_race_cod,
            gmon_temps_tour = :gmon_temps_tour,
            gmon_des_regen = :gmon_des_regen,
            gmon_valeur_regen = :gmon_valeur_regen,
            gmon_vue = :gmon_vue,
            gmon_amelioration_vue = :gmon_amelioration_vue,
            gmon_amelioration_regen = :gmon_amelioration_regen,
            gmon_amelioration_degats = :gmon_amelioration_degats,
            gmon_amelioration_armure = :gmon_amelioration_armure,
            gmon_niveau = :gmon_niveau,
            gmon_nb_des_degats = :gmon_nb_des_degats,
            gmon_val_des_degats = :gmon_val_des_degats,
            gmon_or = :gmon_or,
            gmon_arme = :gmon_arme,
            gmon_armure = :gmon_armure,
            gmon_soutien = :gmon_soutien,
            gmon_amel_deg_dist = :gmon_amel_deg_dist,
            gmon_vampirisme = :gmon_vampirisme,
            gmon_taille = :gmon_taille,
            gmon_avatar = :gmon_avatar,
            gmon_description = :gmon_description,
            gmon_fonction_mort = :gmon_fonction_mort,
            gmon_type_ia = :gmon_type_ia,
            gmon_fonction_debut_dlt = :gmon_fonction_debut_dlt,
            gmon_fonction_tue = :gmon_fonction_tue,
            gmon_serie_arme_cod = :gmon_serie_arme_cod,
            gmon_serie_armure_cod = :gmon_serie_armure_cod,
            gmon_pv = :gmon_pv,
            gmon_pourcentage_aleatoire = :gmon_pourcentage_aleatoire,
            gmon_nb_receptacle = :gmon_nb_receptacle,
            gmon_quete = :gmon_quete,
            gmon_duree_vie = :gmon_duree_vie                        where gmon_cod = :gmon_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":gmon_cod"                   => $this->gmon_cod,
               ":gmon_nom"                   => $this->gmon_nom,
               ":gmon_for"                   => $this->gmon_for,
               ":gmon_dex"                   => $this->gmon_dex,
               ":gmon_int"                   => $this->gmon_int,
               ":gmon_con"                   => $this->gmon_con,
               ":gmon_race_cod"              => $this->gmon_race_cod,
               ":gmon_temps_tour"            => $this->gmon_temps_tour,
               ":gmon_des_regen"             => $this->gmon_des_regen,
               ":gmon_valeur_regen"          => $this->gmon_valeur_regen,
               ":gmon_vue"                   => $this->gmon_vue,
               ":gmon_amelioration_vue"      => $this->gmon_amelioration_vue,
               ":gmon_amelioration_regen"    => $this->gmon_amelioration_regen,
               ":gmon_amelioration_degats"   => $this->gmon_amelioration_degats,
               ":gmon_amelioration_armure"   => $this->gmon_amelioration_armure,
               ":gmon_niveau"                => $this->gmon_niveau,
               ":gmon_nb_des_degats"         => $this->gmon_nb_des_degats,
               ":gmon_val_des_degats"        => $this->gmon_val_des_degats,
               ":gmon_or"                    => $this->gmon_or,
               ":gmon_arme"                  => $this->gmon_arme,
               ":gmon_armure"                => $this->gmon_armure,
               ":gmon_soutien"               => $this->gmon_soutien,
               ":gmon_amel_deg_dist"         => $this->gmon_amel_deg_dist,
               ":gmon_vampirisme"            => $this->gmon_vampirisme,
               ":gmon_taille"                => $this->gmon_taille,
               ":gmon_avatar"                => $this->gmon_avatar,
               ":gmon_description"           => $this->gmon_description,
               ":gmon_fonction_mort"         => $this->gmon_fonction_mort,
               ":gmon_type_ia"               => $this->gmon_type_ia,
               ":gmon_fonction_debut_dlt"    => $this->gmon_fonction_debut_dlt,
               ":gmon_fonction_tue"          => $this->gmon_fonction_tue,
               ":gmon_serie_arme_cod"        => $this->gmon_serie_arme_cod,
               ":gmon_serie_armure_cod"      => $this->gmon_serie_armure_cod,
               ":gmon_pv"                    => $this->gmon_pv,
               ":gmon_pourcentage_aleatoire" => $this->gmon_pourcentage_aleatoire,
               ":gmon_nb_receptacle"         => $this->gmon_nb_receptacle,
               ":gmon_quete"                 => $this->gmon_quete,
               ":gmon_duree_vie"             => $this->gmon_duree_vie,
               ), $stmt);
        }
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return monstre_generique[] array
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select gmon_cod  from monstre_generique order by gmon_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new monstre_generique;
            $temp->charge($result["gmon_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }

    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6))
        {
            case 'getBy_':
                if (property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "select gmon_cod  from monstre_generique where " . substr($name, 6) . " = ? order by gmon_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new monstre_generique;
                        $temp->charge($result["gmon_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }
    
    function getRandom()
    {
        $pdo    = new bddpdo;
        $req = "SELECT gmon_cod
            FROM monstre_generique 
            where gmon_avatar is not null 
            and gmon_avatar != ? 
            order by random() limit 1";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array('default.png'),$stmt);
        $result = $stmt->fetch();
        return $this->charge($result['gmon_cod']);
        
        
        
    }

}
