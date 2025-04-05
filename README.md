# Useful commands

### env setup
change `DATABASE_URL` in .env

### setup
```
symfony server:ca:install
composer install
npm install
php bin/console doctrine:database:create
```

### migration
```
php bin/console make:migration
php bin/console doctrine:migrations:list
php bin/console doctrine:migrations:migrate
```
### undo migration
```
php bin/console doctrine:migrations:execute <version> --down
```

### data fill
```
php bin/console app:backfill
```

### run dev mode
```
symfony serve
npm run watch
```

### clean cache
```
php bin/console cache:clear
```