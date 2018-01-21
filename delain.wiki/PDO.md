# Problématique

Le code php de Delain a été fait en utilisant une bibliothèque devenue obsolète (phplib). Il faut maintenant remplacer les appels de cette bibliothèque par du PDO.

# Qu'est-ce que PDO

PDO (PHP Data Objects) est une extension PHP qui permet d'utiliser une base de données en programmant avec un style orienté objet, et surtout qui permet de s'affranchir du SGBD. PDO n'utilise pas des fonctions au nom trop explicite comme mysql_query() ou sqlite_query(), ce qui facilite grandement la migration d'un SGBD à l'autre, voire l'utilisation simultanée ou alternée de plusieurs SGBD avec le même code PHP.

PDO est une solution d'abstraction de BDD en PHP. 

A terme, cela doit __totalement__ remplacer l'utilisation de la bibliothèque phplib, qui n'est plus maintenue.

# Fonctionnalités principales 

PDO permet de ne plus échapper les variables. En effet, c'est lui qui se charge de tous les échappements.

# Comment l'utiliser dans delain ?

## Exemple 1 :

```php
$pdo = new pdo // instanciation de l'objet
$req = "select * from compte where compt_nom = ?";
$stmt = $pdo->prepare($req); // je prépare la requête dans un objet $stmt
$stmt = $pdo->execute(array('Merrick'),$stmt); // je lance la requête préparée avant dans $stmt
// le point d'interrogation sera remplacée par 'Merrick'
// et je mets le résultat de ce lancement dans la variable $stmt
$retour = $stmt->fecth(); // je mets dans $retour le premier enregistrement résultant de ma requête
echo "Le compte est " . $retour['compt_cod'];
```

## Exemple 2 :
```php
$pdo = new pdo // instanciation de l'objet
$req = "select * from compte where compt_nom = :compte";
$stmt = $pdo->prepare($req); // je prépare la requête dans un objet $stmt
$stmt = $pdo->execute(array(":compte" => 'Merrick'),$stmt); // je lance la requête préparée avant dans $stmt
// la variable ":compte" sera remplacée par 'Merrick'
// et je mets le résultat de ce lancement dans la variable $stmt
$retour = $stmt->fecth(); // je mets dans $retour le premier enregistrement résultant de ma requête
echo "Le compte est " . $retour['compt_cod'];
```

## Exemple 3 :
```php
$pdo = new pdo // instanciation de l'objet
$req = "select * from compte where compt_admin_monstre = :admin";
$stmt = $pdo->prepare($req); // je prépare la requête dans un objet $stmt
$stmt = $pdo->execute(array(":admin" => 1),$stmt); // je lance la requête préparée avant dans $stmt
// la variable ":admin" sera remplacée par 1
// et je mets le résultat de ce lancement dans la variable $stmt
while($retour = $stmt->fetch())
{
   "Le compte " . $retour['compt_nom'] . " est un compte admin monstre.<br />";
}
```