- Ancien code dans le UserType pour la modification des utilisateurs en requete ajax :
```
       if($options['type'] == self::TYPE['profile']){
            $builder->add($options['field']);
       }
```
Restaurer 'app_user_edit' et show_socials.html.twig (sur 'app_user') pour fonctionnement par ajax, laisser show.html.twig dans 'app_user' pour requete classique

- Gestion des adresses : pour permettre les commandes anonyme, déplacement des infos de l'adresse directement sur order, merge de "adresse" et "adresse/add" : Si user connecter ET a des adresse


