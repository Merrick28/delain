<?php

/**
 * Created by PhpStorm.
 * User: NG38DCD
 * Date: 23/12/2016
 * Time: 10:07
 */
class mymenu
{
    var $perso_cod;
    var $pa_dep;
    var $is_enchanteur;
    var $is_enlumineur;
    var $is_refuge;
    var $is_milice;
    var $is_fam;
    var $is_intangible;
    var $gerant = 'N';
    var $admin_dieu;
    var $fidele_gerant;
    var $pa;
    var $nom_perso;
    var $admin_echoppe;
    var $admin_echoppe_noir;
    var $is_vampire;
    var $potions;
    var $religion;
    var $transaction;

    function __construct($perso_cod)
    {
        $this->perso_cod = $perso_cod;
    }


    function get_values()
    {
        $perso = new perso;
        $perso->charge($this->perso_cod);
        // pa
        $this->pa_dep = $perso->get_pa_dep();
        // is_enchanteur
        $this->is_enchanteur = $perso->is_enchanteur();
        $this->is_enlumineur = $perso->is_enlumineur();
        $this->is_refuge     = $perso->is_refuge();
        $this->is_milice     = $perso->is_milice();
        $this->is_fam        = $perso->is_fam();
        $this->is_intangible = $perso->isIntangible();
        $this->admin_dieu    = $perso->is_admin_dieu();
        $this->fidele_gerant = $perso->is_fidele_gerant();
        $this->potions       = $perso->is_potions();
        $this->religion      = $perso->is_religion();
        $this->transaction   = $perso->transactions();
        //
        $this->pa                 = $perso->perso_pa;
        $this->nom_perso          = $perso->perso_nom;
        $this->admin_echoppe      = $perso->perso_admin_echoppe;
        $this->admin_echoppe_noir = $perso->perso_admin_echoppe_noir;
        $this->is_vampire         = $perso->perso_niveau_vampire;
        //
        $mg = new magasin_gerant();
        if ($mg->getByPersoCod($this->perso_cod))
        {
            $this->gerant = 'O';
        }


    }


}