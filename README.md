# OC-API-Projet-7

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d9c4e7d477744e8d943295fa821004f4)](https://app.codacy.com/app/AlexandreTual/OC-API-Projet-7?utm_source=github.com&utm_medium=referral&utm_content=AlexandreTual/OC-API-Projet-7&utm_campaign=Badge_Grade_Dashboard)

Création d'une API RESTful dans le cadre de la formation PHP/ Symfony [OPENCLASSROOMS](https://openclassrooms.com/fr/)

### Auteur
[Alexandre TUAL](https://github.com/AlexandreTual)

### Technologies
PHP => 7.1 

### Librairies
[lexik/LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle) utilisé pour le système d'authentification et de création de JWT (Json Web Token).
[FriendsOfSymfony/FOSRestBundle
](https://github.com/FriendsOfSymfony/FOSRestBundle) utilisé pour facilité l'intégration de plusieurs librairies et le développement de l'API.
[JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle) utilisé pour avoir des fonctionnalités avancées de serialization

[willdurand/Hateoas](https://github.com/willdurand/Hateoas) utilisé pour avoir des fonctionnalités répondant au niveau 3, du modèle de maturité de Richardson.

[nelmio/NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle) facilite la création de la documentation de l'API.

[KnpLabs/KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) système de pagination.

[fzaninotto/Faker
](https://github.com/fzaninotto/Faker/blob/master/readme.md#fakerproviderdatetime) utilisé pour créer des données fictives afin de travailler plus rapidement.

### Installation
Pour installer ce projet veuillez suivre les indications en tapant dans votre terminal les commandes suivantes :
-  Cloner le projet
```sh
git clone https://github.com/AlexandreTual/OC-API-Projet-7.git
```

- Mettre a jour les dépendances du projet
```sh
composer install
```

Générer la clé SSH :
```sh 
$ mkdir -p config/jwt # For Symfony3+, no need of the -p option
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
Dans le fichier .env du projet.

Mettez à jour le mot de passe utilisé pour définir la clé SSH (ligne 33)

Modifiez la ligne 27 pour mettre vos identifiants et le nom que vous souhaitez pour la base de données. Si vous utilisez un autre SGBDR veuillez vous référer à la documentation de [symfony](https://symfony.com/doc/current/doctrine.html)
```yaml
DATABASE_URL=mysql: //db_user:db_password@127.0.0.1:3306/db_name
```
- Création de la base de données
```sh
php bin/console doctrine:database:create
```

- Mise à jour des tables
```sh 
php bin/console doctrine:schema:update --force
```

Si vous souhaitez insérer des données fictives pour tester le projet, tapez la commande suivante.
```sh 
php bin/console doctrine:fixtures:load
```

Pour les tests, les noms des clients ainsi que les mots de passe se trouvent dans le dossier Fixtures. 
La documentation de l'API est disponible à cette adresse (une fois le serveur lancé).
```sh 
/api.bilmo/doc
```

Vous pouvez à présent travailler sur le projet !!