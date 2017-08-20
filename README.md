# idea-machine

**0.1** : 
* Base du projet

**0.2** : 
* Version sans CSS/JS permettant d'ajouter des annonces, les modifiers, les supprimer et avec une gestion des users (pas encore de liaison entre une idée et un user)

**0.3** : 
* Ajout du CSS pour toutes les views

**0.4** : 
* Ajout de la liaison des users avec les annonces (une annonce appartient à un auteur) + Permettre l'édition/suppression des annonces seulement à leur auteur

**0.5** : 
* Ajout de la view "mine.html.twig", de la méthode "mineAction" ainsi que de la route "am_platform_mine" + création de getIdeaUser($userId)
* Ajout des liens d'édition/suppression sur les annonces de l'utilisateur dans "/" et "/idea/mine"

**0.6** : 
* Correction de l'entité "Idea" pour l'attribut "user" que j'ai passé en "ManyToOne"
* Ajout de l'Ajax sur la liste des idées et la liste des idées propre à un auteur