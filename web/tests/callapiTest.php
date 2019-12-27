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
        foreach ($tabnews as $mynew)
        {
            $news->charge($mynew->news_cod);
            $news->delete();
        }
    }


    /******************************
     * Test AUTH
     */

    public function testAuthNoToken()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'POST', '');
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Pas de token.');
    }

    public function testNoLogin()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'POST', '', array('password' => 'admin'));
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Pas de login.');
    }

    public function testNoPassword()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin'));
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Pas de password.');
    }

    public function testWrongLogin()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin', 'password' => 'admin2'));
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Authentification échouée.');
    }

    public function testLoginOk()
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'POST', '', array('login' => 'Admin', 'password' => 'admin'));
        $this->assertEquals($test[0]['http_code'], 200);
        $json = $test[1];
        $json = json_decode($json, true);
        $this->assertEquals($json['compte'], 2);
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
        $test    = $callapi->call(API_URL . '/compte', 'GET', '');
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Token non transmis');
    }

    public function testCompteWithMalformedToken()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/compte', 'GET', '123');
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Token non UUID');
    }

    public function testCompteWithWrongToken()
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/compte', 'GET', 'd5f60c54-2aac-4074-b2bb-cbedebb396b8');
        $this->assertEquals($test[0]['http_code'], 403);
        $this->assertEquals($test[1], 'Token non trouvé');
    }

    /**
     * @depends testLoginOk
     */
    public function testCompteWithGoodToken($token)
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/compte', 'GET', $token);
        $this->assertEquals($test[0]['http_code'], 200);
    }

    /**
     * @depends testLoginOk
     */
    public function testDeleteToken($token)
    : void
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/auth', 'DELETE', $token);
        $this->assertEquals($test[0]['http_code'], 200);
        $this->assertEquals($test[1], 'Token supprimé');
        $auth_token = new auth_token();
        $temp       = $auth_token->charge($token);
        $this->assertFalse($temp);
    }

    public function testInsertNews()
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/news', 'GET');
        $test    = json_decode($test[1], true);
        $this->assertCount(5, $test);
    }

    public function testGetNews()
    {
        $callapi = new callapi();
        $test    = $callapi->call(API_URL . '/news', 'GET');

        $this->assertEquals($test[0]['http_code'], 200);
        $tabnews = json_decode($test[1], true);
        foreach ($tabnews as $val)
        {
            $this->assertIsInt($val['news_cod']);
        }
    }

}
