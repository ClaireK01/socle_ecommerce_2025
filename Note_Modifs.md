- Ancien code dans le UserType pour la modification des utilisateurs en requete ajax :
```
       if($options['type'] == self::TYPE['profile']){
            $builder->add($options['field']);
       }
```
Restaurer 'app_user_edit' et show_socials.html.twig (sur 'app_user') pour fonctionnement par ajax, laisser show.html.twig dans 'app_user' pour requete classique

