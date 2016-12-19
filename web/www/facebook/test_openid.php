<?php if (!isset($_POST['submit'])) {
?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
   "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title></title>
  </head>
  <body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      Entrez votre OpenID: <br/>
      <input type="text" name="id" size="30" />
      <br />
      <input type="submit" name="submit" value="Log In" />
    </form>
  </body>
</html>
<?php } else {
  // vérifie les valeurs du formulaire
  if (trim($_POST['id'] == '')) {
    die("ERROR: Entrez un OpenID valide svp.");    
  }
  
  // fichiers inclus
  require_once "Auth/OpenID/Consumer.php";
  require_once "Auth/OpenID/FileStore.php";
  
  // démarrage de la session (requis pour YADIS)
  session_start();
  
  // crée une zone de stockage pour les données OpenID
  $store = new Auth_OpenID_FileStore('./oid_store');
  
  // crée un consommateur OpenID
  $consumer = new Auth_OpenID_Consumer($store);
  
  // commence le process d'authentification
  // crée une requête d'authentification pour le fournisseur OpenID
  $auth = $consumer->begin($_POST['id']);
  if (!$auth) {
    die("ERROR: Entrez un OpenID valide svp.");
  }
  
  // redirige vers le fournisseur OpenID pour l'authentification
  $url = $auth->redirectURL('http://consumer.example.com/', 'http://consumer.example.com/oid_return.php');
  header('Location: ' . $url);
}
?>    
