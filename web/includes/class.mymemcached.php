<?php

/**
 * Created by PhpStorm.
 *
 * Le but de cette classe est de stocker des objets dans memcached
 * afin d'accélérer les traitements.
 * On commence par y stocker les paramètres (table parametres en bdd)
 *
 *
 * User: NG38DCD
 * Date: 22/12/2016
 * Time: 13:24
 */
class mymemcached
{
    var $m;

    /**
     * memcached constructor.
     * Prépare le serveur
     */
    function __construct()
    {
        $this->m = new Memcached();
        $this->m->addServer('localhost', 11211);
    }

    /**
     * Stocke une variable en memcached
     * @param string $nom  Nom de la variable
     * @param mixed $valeur Valeur de la variable
     * @return bool
     */
    function put($nom, $valeur)
    {
        $this->m->set($nom, $valeur);
        if ($this->m->getResultCode() == Memcached::RES_NOTSTORED)
        {
            return false;
        }
        return true;
    }

    /**
     * Lit une variable dans memcached
     * Retourne false si non trouvé
     * @param $nom
     * @return bool|mixed
     */
    function get($nom)
    {
        $retour = $this->m->get($nom);
        if ($this->m->getResultCode() == Memcached::RES_NOTFOUND)
        {
            return false;
        }
        return $retour;
    }

    /**
     * Vide tout :)
     */
    function delete_all()
    {
        $this->m->flush();
    }
}