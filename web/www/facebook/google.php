<?php require 'openid.php';
try {
    $openid = new LightOpenID('www.jdr-delain.net');
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
            header('Location: ' . $openid->authUrl());
        }
?>
<a href="<?php echo $_SERVER['PHP_SELF'] . "?login"?>">Login with Google</a>
<?php     } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        if($openid->validate())
        {
            echo 'User <b>' . $openid->identity . '</b> has logged in.<br>';
 
            echo "<h3>User information</h3>";
 
            $identity = $openid->identity;
            $attributes = $openid->getAttributes();
            $email = $attributes['contact/email'];
            $first_name = $attributes['namePerson/first'];
            $last_name = $attributes['namePerson/last'];
 
            echo "mode: " . $openid->mode . "<br>";
            echo "identity: " . $identity . "<br>";
            echo "email: " . $email . "<br>";
            echo "first_name: " . $first_name . "<br>";
            echo "last_name: " . $last_name . "<br>";
        }
        else
        {
            echo 'User ' . $openid->identity . 'has not logged in.';
        }
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
print_r($_SESSION);
?>


