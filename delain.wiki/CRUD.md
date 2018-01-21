# Crud : une nouvelle gestion des objets

Imaginons une table dans la bdd, appelée compte. Aujourd'hui, les requêtes se font en dur dans la base.

Demain, on va créer un objet $compte, qui sera une instance de la classe compte.

# pour comprendre : les exemples 

## Charger un objet

On utilise la fonction charge(), en passant en paramètre la clé primaire de la table. (on appelle cette fonction hydrater).

Par exemple : 

```php
$compte = new compte
$compte->charge(4); // je charge dans l'objet l'enregistrement correspondant dont la pk = 4
echo $compte->compt_nom; // va renvoyer 'Merrick';
```

## Modifier un objet

On va utiliser la fonction stocke(), sans arugments. Attention, il faut bien que l'objet soit chargé avant !

```php
$compte = new compte
$compte->charge(4); // je charge dans l'objet l'enregistrement correspondant dont la pk = 4
echo $compte->compt_nom; // va renvoyer 'Merrick';
$compte->compt_nom = 'Nouveau Merrick'; // a ce moment, seul l'objet php est modifié, aucun impact sur la bdd
$compte->stocke(); // maintenant, j'ai enregistré l'objet
```

## Créer un enregistrement

Nous allons utiliser la même fonction stocke(), mais en passant le paramètre _true_ (pour nouvel enregistrement). Cette fonction va créer un nouvel enregistrement, et charger immédiatement l'enregistrement nouvellement créé.

```php
$compte = new compte
$compte->compt_nom = 'Toto'; // a ce moment, seul l'objet php est modifié, aucun impact sur la bdd
$compte->compt_mail = 'stephane.dewitte@gmail.com';
$compte->stocke(true); // maintenant, j'ai enregistré l'objet
echo "Nouvel enregistrement inséré, id = " . $compte->compt_cod;
```

# Les fonction magiques

En plus de charge() et stocke() que nous avons vues, il existe deux autres fonctions :

## getAll()

Retourne un tableau avec une instance par entité du tableau.

Exemple :

```php
$matable = new matable;
$liste = $matable->getAll();
foreach($liste as $val)
{
   echo "Modification du l'id " . $val->cle_primaire;
   $val->champ = 'toto';
   $val->stocke();
}
```

Attention à ne pas utiliser sur les grosses tables !

## getBy_xxxx

Retourne un tableau avec une instance par entité du tableau, qui correspond la requête en fonction du nom. Un exemple est plus parlant 

```php
$compte = new compte
$listeAdmin = getBy_compt_admin('O');
foreach($listeAdmin as $val)
{
   echo "Le compte " . $val->compt_nom . " est admin<br />";
}
```
Il faut que la partie après getBy_ soit un attribut de la classe (ici, compt_admin), et on va rechercher dans la table tous les enregistrements dont la valeur du champ est égale à 'O';

# A terme

Toutes les modifications d'un objet, et toutes les requêtes doivent passer par ce système. Par exemple, pour un déplacement, on va créer dans la classe perso une fonction : 

```php
class perso
{
   ...
   function deplace($code)
   {
      $pdo = new bddpdo;
      $req = "select deplace_cod(?,?) as deplace";
      $stmt = $db->prepare($req);
      $stmt = $db->execute(array($this->perso_cod,$code),$stmt);
      $result = $stmt->fetch();
      return $result;
   }
}
```

Et dans la page de déplacement : 
```php
$perso = new perso;
$perso->charge($perso_cod); // on n'oublie pas d'hydrater l'objet
$retourDep = $perso->deplace($_REQUEST['code']);
```

# C'est bien beau tout ça, mais je les créé comment mes classes ?

Allez sur la page http://(project_url)/cree_classe.php?table=compte et remplacez le nom de la table par ce que vous voulez.

Copier le texte affiché, et créez le fichier public_html/includes/class.xxxx.php (en remplaçant aussi xxxx par le nom de la table), collez-y ce que vous avez copié, et sauvegardez.

Vous avez maintenant une bonne base pour créer d'autres fonctions si besoin.
