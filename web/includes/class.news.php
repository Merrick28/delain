<?php

/**
 * includes/class.news.php
 */

/**
 * Class news
 *
 * Gère les objets BDD de la table news
 */
class news
{

    var $news_cod;
    var $news_titre;
    var $news_texte;
    var $news_date;
    var $news_auteur;
    var $news_mail_auteur;

    function __construct()
    {

        $this->news_date = date('Y-m-d H:i:s');
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
            $req  = "insert into news (
            news_titre,
            news_texte,
            news_date,
            news_auteur,
            news_mail_auteur                        )
                    values
                    (
                        :news_titre,
                        :news_texte,
                        :news_date,
                        :news_auteur,
                        :news_mail_auteur                        )
    returning news_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":news_titre" => $this->news_titre,
                ":news_texte" => $this->news_texte,
                ":news_date" => $this->news_date,
                ":news_auteur" => $this->news_auteur,
                ":news_mail_auteur" => $this->news_mail_auteur,
            ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update news
                    set
            news_titre = :news_titre,
            news_texte = :news_texte,
            news_date = :news_date,
            news_auteur = :news_auteur,
            news_mail_auteur = :news_mail_auteur                        where news_cod = :news_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
                ":news_cod" => $this->news_cod,
                ":news_titre" => $this->news_titre,
                ":news_texte" => $this->news_texte,
                ":news_date" => $this->news_date,
                ":news_auteur" => $this->news_auteur,
                ":news_mail_auteur" => $this->news_mail_auteur,
            ), $stmt);
        }
    }

    /**
     * Charge dans la classe un enregistrement de news
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo  = new bddpdo;
        $req  = "select * from news where news_cod = ?";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->news_cod         = $result['news_cod'];
        $this->news_titre       = $result['news_titre'];
        $this->news_texte       = $result['news_texte'];
        $this->news_date        = $result['news_date'];
        $this->news_auteur      = $result['news_auteur'];
        $this->news_mail_auteur = $result['news_mail_auteur'];
        return true;
    }

    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \news
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select news_cod  from news order by news_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp = new news;
            $temp->charge($result["news_cod"]);
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
                    $req    = "select news_cod  from news where " . substr($name, 6) . " = ? order by news_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp = new news;
                        $temp->charge($result["news_cod"]);
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
                    die('Unknown variable ' . substr($name, 6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }

    function getNumber()
    {
        $pdo      = new bddpdo;
        $max_news = "SELECT count(*) as c FROM news";
        $stmt     = $pdo->query($max_news);
        $result   = $stmt->fetch();
        return $result['c'];
    }

    function getNews($offset)
    {
        $retour    = array();
        $pdo       = new bddpdo;
        $recherche = "SELECT news_cod "
            . "FROM news order by news_cod desc "
            . "limit 5 offset ?";
        $stmt      = $pdo->prepare($recherche);
        $stmt      = $pdo->execute(array($offset), $stmt);
        while ($result = $stmt->fetch())
        {
            $news = new news;
            $news->charge($result['news_cod']);
            $retour[] = $news;
            unset($news);
        }
        return $retour;
    }

    function getNewsSup($code)
    {
        $retour    = array();
        $pdo       = new bddpdo;
        $recherche = "SELECT news_cod 
          FROM news where news_cod > ? order by news_cod desc 
          limit 3";
        $stmt      = $pdo->prepare($recherche);
        $stmt      = $pdo->execute(array($code), $stmt);
        while ($result = $stmt->fetch())
        {
            $news = new news;
            $news->charge($result['news_cod']);
            $retour[] = $news;
            unset($news);
        }
        return $retour;
    }

}
