# TODO_LIST
Project 8 du parcours "développeur d'applications PHP/Symfony" chez Openclassrooms.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/259d6c04c317430c9c35c8de9b5bc94a)](https://app.codacy.com/gh/fatialk/todolist/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

# Description
Vous venez d’intégrer une startup dont le cœur de métier est une application permettant de gérer ses tâches quotidiennes. L’entreprise vient tout juste d’être montée, et l’application a dû être développée à toute vitesse pour permettre de montrer à de potentiels investisseurs que le concept est viable (on parle de Minimum Viable Product ou MVP).

Le choix du développeur précédent a été d’utiliser le framework PHP Symfony, un framework que vous commencez à bien connaître !

Bonne nouvelle ! ToDo & Co a enfin réussi à lever des fonds pour permettre le développement de l’entreprise et surtout de l’application.

Votre rôle ici est donc d’améliorer la qualité de l’application. La qualité est un concept qui englobe bon nombre de sujets : on parle souvent de qualité de code, mais il y a également la qualité perçue par l’utilisateur de l’application ou encore la qualité perçue par les collaborateurs de l’entreprise, et enfin la qualité que vous percevez lorsqu’il vous faut travailler sur le projet.

Ainsi, pour ce dernier projet de spécialisation, vous êtes dans la peau d’un développeur expérimenté en charge des tâches suivantes :

l’implémentation de nouvelles fonctionnalités ;
la correction de quelques anomalies ;
et l’implémentation de tests automatisés.
Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

# Stack technique
   - Symfony 6.4
   - PHP 8

# Installation

1. Cloner le projet depuis le repository:

   - https://github.com/fatialk/todolist.git

2. Installer les dépendances:

   - composer install

3. Renommer le fichier .env.local en .env et Modifier la connexion à la base de données:

   - DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

4. Créer la base de données:

   - php bin/console doctrine:database:create

5. Créer la structure de la base de données et générer les fixtures:

   - php bin/console doctrine:schema:update --force
   - php bin/console doctrine:fixtures:load

6. Créer la base de données et générer les tables de test:

   - php bin/console doctrine:database:create --env=test
   - php bin/console doctrine:schema:update --force --env=test

# Tester l'application

1. Lancer les tests unitaires:

   - .\vendor\bin\phpunit --coverage-html public/test-coverage --testsuite unit

2. Lancer les tests fonctionnels:

   - .\vendor\bin\phpunit --coverage-html public/test-coverage --testsuite functional




