#### Exemple de socle Ecommerce en Symfony 6

> Installation base de données Docker

Afin d'avoir un utilisateur administrateur prêt à l'utilisation, on utilisera le script init.sql conçut pour être lancé à la création votre container. 
Adaptez le chemin indiqué dans les volumes pour qu'il redirige vers le chemin du projet sur votre ordinateur :

```
    #docker-compose.yaml
    
    volumes:
      - database_data:/var/lib/postgresql
      - #ADAPTER AU CHEMIN DU PROJET
      - C:\chemin\vers\socle_ecommerce_2025\docker-entrypoint-initdb.d:/docker-entrypoint-initdb.d:ro
```

Puis lancez ```docker-compose up -d```

> Installation du projet

- Installez les dépendances composer avec ```composer install``` (PHP 8.1)
- Installez les dépendances npm avec ```npm install```
- Installez bien les assets des librairies artgris et tinymce ou votre front risque d'avoir des problèmes de fonctionnement : ```php bin/console assets:install public```
- Compilez le front avec ```npm run build```
- Créez un chemin ```uploads/file_manager``` dans ```public/``` pour le file manager (le chemin doit correspondre à celui indiqué dans ```artgris_file_manager.yaml```)
- Mettez la base de données à jour avec ```symfony console doctrine:schema:update --force``` (ou la méthode propre avec les migrations)


> Connexion à l'administration et ajout de produits

Si votre script SQL s'est bien lancé, vous devriez pouvoir accèder à l'administration avec les identifiants suivants :

```
admin@admin.fr
admin
```

Pour administrer la base de manière à permettre un tunnel de paiement complet, il faudra créer (de préférence dans l'ordre indiqué) :

- Une catégorie
- Au moins une TVA
- Un produit (nécéssite une catégorie et TVA crée au préalable)
- Un mode de paiement (cette version de test redirigera automatiquement vers Stripe)
- Un mode de livraison


L'API Stirpe nécéssite également une clé STRIPE_SECRET et une clé STRIPE_KEY dans votre .env pour fonctionner. Vous pouvez consulter le site Stripe pour accèder à des cartes de test.