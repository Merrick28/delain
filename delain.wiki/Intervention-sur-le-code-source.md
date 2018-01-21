# Généralités

Vous ne pouvez pas intervenir directement sur le code de Delain. Afin de pouvoir participer, vous devez créer un fork.
Pour cela, cliquez sur le bouton "fork" en haut à droite, à la racine du projet Delain. Cela va créer une copie du projet Delain dans votre compte.

De là, vous pouvez l'installer en suivant la documentation d'installation, en changeant l'url du clone par celui de votre projet.

# Pull request

Une fois que vous avez fait vos modifications sur votre fork, il faut les commiter et les pousser sur GitHub.
Quand vous êtes sur que votre code est ok, cliquez sur "new pull request". Cela va alerter les développeurs du projet Delain, qui vont analyser le code proposé, et décider (ou non) de l'intégrer dans le code principal.

# Modifications de la structure de la base de données

Pour chaque modification de la base de données (structure, fonctions, data...), vous devez écrire un fichier dans le répertoire sql. Ce fichier doit être daté YYYYMMDD-<fonctionnalité> (par exemple 20161220-amelioration_magie).

Ce fichier peut contenir autant de requêtes que nécessaires, séparées par des points-virgule.

