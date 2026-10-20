# Déploiement sur AlwaysData

Une seule personne crée le compte et invite les autres (Administration › Permissions) ou partage l'accès SSH.

## 1. Base de données

1. **Bases de données › MySQL › Ajouter** : nom `coride` → AlwaysData le préfixe : `<compte>_coride`.
2. **Utilisateurs** : utilisez l'utilisateur principal ou créez-en un avec les droits sur cette base.
3. Ouvrez **phpMyAdmin** (lien dans la même page), sélectionnez la base, onglet **Importer** :
   `database/schema.sql` puis `database/seed.sql`.
4. Hôte MySQL : `mysql-<compte>.alwaysdata.net`.

## 2. Code

Via SSH (`ssh <compte>@ssh-<compte>.alwaysdata.net`) — plus simple pour suivre Git :

```bash
cd ~/www
git clone https://github.com/<equipe>/coride.git
cd coride
cp config/config.example.php config/config.php
nano config/config.php   # host, name, user, pass ; debug => false
```

Mises à jour ensuite : `cd ~/www/coride && git pull`.
(Sans SSH : envoyer les fichiers en SFTP avec FileZilla, même hôte.)

## 3. Site web

**Web › Sites › modifier le site** :
- Type : **PHP**, version 8.2 ou plus récente.
- **Répertoire racine** : `www/coride/public`
  → seul `public/` est exposé ; `config/`, `app/`, `database/` restent inaccessibles.

Si vous ne pouvez pas changer la racine, le `.htaccess` à la racine du projet redirige vers `public/` et bloque les dossiers sensibles — mais la première solution est préférable.

## 4. Vérifications après mise en ligne

- [ ] La page d'accueil s'affiche, le CSS est chargé.
- [ ] `https://<compte>.alwaysdata.net/config/config.php` ne renvoie rien d'exploitable.
- [ ] Connexion avec `lucas@coride.test` / `CoRide2026!`.
- [ ] Une erreur volontaire n'affiche pas de trace (`debug => false`).
- [ ] Le site est en HTTPS (certificat Let's Encrypt automatique).
- [ ] Changez les mots de passe des comptes de démonstration si l'URL est partagée.

## 5. Tâche planifiée (option, module C)

**Avancé › Tâches planifiées** : commande `php ~/www/coride/bin/<votre_script>.php`, fréquence au choix (toutes les 15 min par ex.).
