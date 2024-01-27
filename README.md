

## Back-end Developer Test

### Run the following commands to set up the test

```
composer install

```

### Run the command below to run migrations and seed data into the application
```
php artisan migrate --seed

```


### Run the command below to configure the application

```
php artisan  app:configure-app

```


### Run the command below to run test cases of the application

```
php artisan test

```

### Run the command below to simulate the application

```
php artisan  app:simulate-app

```

### To start the app

```yaml
php artisan serve

The app should start running at http://127.0.0.1:8000

Test the api response at http://127.0.0.1:8000/users/1/achievements
```

### Response
