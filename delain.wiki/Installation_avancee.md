# Intelligence Artificielle

Attention, le lancement de l'IA consomme beaucoup de ressources. Si vous travaillez sur une machine un peu légère, vous risquez de le sentir passer.

## Méthode

Copier le fichier ia_init.service dans /etc/systemd/system

```
sudo cp /home/delain/delain/system/ia_init.service /etc/systemd/system
```

Commandes possibles 

```
sudo systemctl start ia_init  # lance le service
sudo systemctl stop ia_init   # stoppe le service
sudo systemctl enable ia_init # fait en sorte que le service démarre avec la machine
```

# Cron (tâches planifiées)

De nombreuses tâches planifiées permettent de gérer le projet. Elles peuvent être soit fonctionnelles (génération des monstres,...), soit techniques (nettoyage de la base, ...)

## Droits d'accès à la database

La liste des tâches planifiées se trouve dans le fichier system/crontab. Pour les activer, vous devez tout d'abord autoriser l'utilisateur webdelain à se connecter sans mot de passe à la database

```
sudo vi /etc/postgresql/9.5/main/pg_hba.conf
```
et changez la ligne
```
local   delain   webdelain   md5
```
par
```
local   delain   webdelain   trust
```
Puis redémarrez postgres
```
sudo systemctl restart postgres
```

## Lancer les tâches planifiées

Tout d'abord, vous devez switcher sur l'utilisateur delain

```
sudo su - delain
```
Puis
```
chmod 755 /home/delain/delain/shell/*
cat /home/delain/delain/system/crontab |crontab -
```

Vous pouvez vérifier en lançant la commande
```
crontab -l
````

