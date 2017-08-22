# idea-machine

**Version  1.0 :**
* Ajout d'un dump de la BDD avec idées/users/images/votes par défaut
* Corrections CSS

**Version  0.9 :**
* Ajout du responsive de la home + loader bouton "Afficher la suite"

**Version  0.8 :**
* Modification du gulpfile.js pour ajouter la minification des fichiers JS (et le rename) + modification des repertoires JS/CSS
* Ajout du vote en AJAX et modification de l'AJAX pour le "Load More" + Correction d'une erreur sur la méthode deleteAction dans le controller (IdeaController)

**Version  0.7 :**
* Ajout de la prévisualisation de l'image dans la view form (pour la page d'édition) + rendre non obligatoire l'image (uniquement pour l'édition)

**Version  0.6 :**
* Correction de l'entité "Idea" pour l'attribut "user" que j'ai passé en "ManyToOne"
* Ajout de l'Ajax sur la liste des idées et la liste des idées propre à un auteur

**Version  0.5 :**
* Ajout de la view "mine.html.twig", de la méthode "mineAction" ainsi que de la route "am_platform_mine" + création de getIdeaUser($userId)
* Ajout des liens d'édition/suppression sur les annonces de l'utilisateur dans "/" et "/idea/mine"

**Version  0.4 :**
* Ajout de la liaison des users avec les annonces (une annonce appartient à un auteur) + Permettre l'édition/suppression des annonces seulement à leur auteur

**Version  0.3 :**
* Ajout du CSS pour toutes les views

**Version 0.2 :**
* Version sans CSS/JS permettant d'ajouter des annonces, les modifiers, les supprimer et avec une gestion des users (pas encore de liaison entre une idée et un user)

**Version 0.1 :**
* Base du projet