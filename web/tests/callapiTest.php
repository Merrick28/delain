<?php

declare(strict_types=1);

set_include_path('/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes:/usr/share/php');

require_once ('delain_header.php');

use PHPUnit\Framework\TestCase;

final class callapiTest extends TestCase
{
    var $token;
    /******************************
     * Test AUTH
     */

    public function testAuthNoToken(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/auth','POST','');
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Pas de token.');
    }

    public function testNoLogin(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/auth','POST','',array('password'=>'admin'));
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Pas de login.');
    }

    public function testNoPassword(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/auth','POST','',array('login'=>'Admin'));
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Pas de password.');
    }

    public function testWrongLogin(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/auth','POST','',array('login'=>'Admin','password' => 'admin2'));
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Authentification échouée.');
    }

    public function testLoginOk()
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/auth','POST','',array('login'=>'Admin','password' => 'admin'));
        $this->assertEquals($test[0]['http_code'],200);
        $json = $test[1];
        $json = json_decode($json,true);
        $this->assertEquals($json['compte'],2);
        $token = $json['token'];
        return $token;

    }
    /*******************
     * COMPTE
     */
    public function testCompteNoToken(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/compte','GET','');
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Token non transmis');
    }

    public function testCompteWithMalformedToken(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/compte','GET','123');
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Token non UUID');
    }

    public function testCompteWithWrongToken(): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/compte','GET','d5f60c54-2aac-4074-b2bb-cbedebb396b8');
        $this->assertEquals($test[0]['http_code'],403);
        $this->assertEquals($test[1],'Token non trouvé');
    }

    /**
     * @depends testLoginOk
     */
    public function testCompteWithGoodToken($token): void
    {
        $callapi = new callapi();
        $test = $callapi->call('http://172.17.0.1:9090/api/v2/compte','GET',$token);
        print_r($test[1]);
        $this->assertEquals($test[0]['http_code'],200);


    }

}
