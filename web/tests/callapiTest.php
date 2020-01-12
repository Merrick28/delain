<?php

declare(strict_types=1);

set_include_path('/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes:/usr/share/php');

require_once('delain_header.php');

use PHPUnit\Framework\TestCase;

class callapiTest
    extends
    TestCase
{

    public static function setUpBeforeClass()
    : void
    {
        if (defined('SERVER_PROD'))
        {
            if (SERVER_PROD)
            {
                die('Pas de tests unitaires en prod !');
            }
            $news = new news;
            for ($i = 1; $i < 10; $i++)
            {
                $news->news_titre       = "Titre " . $i;
                $news->news_auteur      = "Merrick";
                $news->news_texte       = "Texte de la news n°" . $i;
                $news->news_mail_auteur = "merrick@jdr-delain.net";
                $news->stocke(true);
            }
        }
    }

    public static function tearDownAfterClass()
    : void
    {
        // on efface les news
        $news    = new news;
        $tabnews = $news->getAll();
        /*foreach ($tabnews as $mynew)
        {
            $news->charge($mynew->news_cod);
            $news->delete();
        }*/
    }


    /******************************
     * Test AUTH
     */

    public function testAuthNoToken()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/auth', 'POST', ''));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Pas de token.');
    }

    public function testNoLogin()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/auth', 'POST', '', array('password' => 'admin')));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Pas de login.');
    }

    public function testNoPassword()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin')));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Pas de password.');

    }

    public function testWrongLogin()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin', 'password' => 'admin2')));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Authentification échouée.');
    }

    public function testLoginOk()
    {
        $callapi = new callapi();

        $this->assertTrue($callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin', 'password'
                                                                                      => 'admin')));
        $this->assertEquals($callapi->http_response, 200);
        $this->assertJson($callapi->content);

        $json  = json_decode($callapi->content, true);
        $token = $json['token'];
        return $token;

    }

    /*******************
     * COMPTE
     */
    public function testCompteNoToken()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/compte', 'GET', ''));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Token non transmis');
    }

    public function testCompteWithMalformedToken()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/compte', 'GET', '123'));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Token non UUID');
    }

    public function testCompteWithWrongToken()
    : void
    {
        $callapi = new callapi();
        $this->assertFalse($callapi->call(API_URL . '/compte', 'GET', 'd5f60c54-2aac-4074-b2bb-cbedebb396b8'));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Token non trouvé');
    }

    /**
     * @depends testLoginOk
     */
    public function testCompteWithGoodToken($token)
    : void
    {
        $callapi = new callapi();

        $this->assertTrue($callapi->call(API_URL . '/compte', 'GET', $token));
        $this->assertEquals($callapi->http_response, 200);
        $this->assertJson($callapi->content);
    }


    public function testGettNews()
    {
        $callapi = new callapi();
        $this->assertTrue($callapi->call(API_URL . '/news', 'GET'));
        $this->assertJson($callapi->content);
        $tabnews = json_decode($callapi->content, true);
        $this->assertCount(5, $tabnews['news']);
        $this->assertIsInt($tabnews['numberNews']);
        foreach ($tabnews['news'] as $val)
        {
            $this->assertIsInt($val['news_cod']);
        }
    }

    /**
     * @depends testLoginOk
     */
    public function testCreateBadPerso($token)
    : void
    {
        $callapi = new callapi();

        $array_good = array(
            "nom"   => "monperso",
            "force" => 12,
            "con"   => 12,
            "dex"   => 12,
            "intel" => 9,
            "voie"  => "guerrier",
            "poste" => "H",
            "race" => 1
        );

        //mauvais compte
        $a2 = $array_good;
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', 'd5f60c54-2aac-4074-b2bb-cbedebb396b8', $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Token non trouvé');

        // sans le nom
        $a2 = $array_good;
        unset($a2['nom']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Nom de personnage vide, ou perdu dans les limbes informatiques...');

        // sans les caracs
        $a2 = $array_good;
        unset($a2['force']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        $a2 = $array_good;
        unset($a2['con']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        $a2 = $array_good;
        unset($a2['intel']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        $a2 = $array_good;
        unset($a2['dex']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        $a2 = $array_good;
        unset($a2['race']);
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Race non choisie');

        // valeurs foireuses
        $a2          = $array_good;
        $a2['force'] = 45;
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        // valeurs foireuses
        $a2          = $array_good;
        $a2['force'] = 2;
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Erreur sur les valeurs choisies');

        $a2         = $array_good;
        $a2['voie'] = 'err';
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Vous devez choisir une voie');

        $a2          = $array_good;
        $a2['poste'] = 'err';
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Vous devez choisir un poste d\'entrée');

        // création du premier perso OK
        $a2          = $array_good;
        $this->assertTrue($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 200);
        print_r($callapi);
        $this->assertJson($callapi->content);
        $tab = json_decode($callapi->content,true);
        $this->assertIsInt($tab['perso']);

        // le second doit planter à cause du nom
        $a2          = $array_good;
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Un aventurier porte déjà ce nom');

        // un autre avec un autre nom
        $a2          = $array_good;
        $a2['nom'] = 'nouveau nom';
        $this->assertTrue($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 200);
        $this->assertJson($callapi->content);
        $tab = json_decode($callapi->content,true);
        $this->assertIsInt($tab['perso']);

        // on devrait avoir assez de persos
        $a2          = $array_good;
        $a2['nom'] = 'nouveau nom 2';
        $this->assertFalse($callapi->call(API_URL . '/perso', 'POST', $token, $a2));
        $this->assertEquals($callapi->http_response, 403);
        $this->assertEquals($callapi->content, 'Il semble que vous ayiez déjà assez de personnages comme cela');
    }
    /**
     * @depends testLoginOk
     */
    function testGetPersoCompte($token)
    {
        $callapi = new callapi();

        $this->assertTrue($callapi->call(API_URL . '/compte/persos', 'GET', $token));
        $this->assertEquals($callapi->http_response, 200);
        $this->assertJson($callapi->content);

        $tab_persos = json_decode($callapi->content, true);
        foreach($tab_persos['persos'] as $val)
        {
            $this->assertIsInt($val['perso_cod']);
        }
        foreach($tab_persos['sittes'] as $val)
        {
            $this->assertIsInt($val['perso_cod']);
        }
        return $tab_persos;
    }


    /**
     * @depends testLoginOk
     */
    public function testDeleteToken($token)
    : void
    {
        $callapi = new callapi();
        $this->assertTrue($callapi->call(API_URL . '/auth', 'DELETE', $token));
        $this->assertEquals($callapi->http_response, 200);
        $this->assertEquals($callapi->content, 'Token supprimé');

        $auth_token = new auth_token();
        $temp       = $auth_token->charge($token);
        $this->assertFalse($temp);
    }
}
