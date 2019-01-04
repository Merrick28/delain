## Comment contribuer à Delain ?

Pour la partie technique, vous pouvez voir comment ça se passe ici : https://github.com/Merrick28/delain/wiki/Intervention-sur-le-code-source

### Conditions de contrôle

####Changement dans le fonctionnement du jeu

Les contributions qui amènent de nouvelles fonctonnalités au projet doivent être validées par l'équipe de jeu pour être acceptées

####Modifications techniques et bugfix

Ceci concerne TOUTES les modifications, y compris les évolutions fonctionnelles :

* Aucun nouveau code utilisant les requêtes phplib ne sera accepté. Toutes les requêtes doivent passer par pdo.
* Dans la mesure du possible, les requêtes doivent être incluses dans une classe, et pas directement dans la page.
* Aucun nouveau code utilisant l'ancien moteur de template (phplib) ne sera acceptée. Merci de passer sur Twig.
* Toutes les variables passées doivent être appelées en utilisant les tableaux $_GET, $_POST, ou $_REQUEST.


