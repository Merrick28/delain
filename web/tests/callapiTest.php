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
}
