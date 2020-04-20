# RESTful API with Laravel 7 
#
#
>
> REST API built with laravel 7
> based on a project I built a few years ago
> and updated to Laravel 7, the latest version so far.
> Here I can show most of Laravel features I've been
> working with since 2016

### Main Laravel features used in this project:
- Eloquent Eager Loading
- Events & Listeners
- Image Storage
- Email Setup
- Failing-Prone actions
- Middlewares (very important in this project)
- Rate Limiting
- Transformers with Fractal (Security and Maintenance)
- Sorting and Filtering Collections
- Pagination
- Cache
- HATEOAS (content negotiation)
- Middlewares to resolve transformations
- Auth in sessions (Web Client) 
- Laravel Passport
- Limit CSRF only for Web client
- OAuth 2.0
- Laravel MIX
- Scopes on OAuth 2.0
- Policies and Gates
- CORS (Already included in Laravel 7)

#####
##### I built this project on Ubuntu 19.10 where I had already all requirements for this project.
##### but, I encourage you to use [Laravel Homestead](https://laravel.com/docs/7.x/homestead) so you don't have to deal with requirements,
##### also, that's a good practice since that is used in development teams so everybody has the same environment, no matter what you are using in your local environment.

#
#
##### Versions
- PHP 7.3.11
- Laravel 7.3.0

### Requirements (Ubuntu 19.10 with PHP 7)
- ext-dom or XML ( sudo apt install php-xml )
- bcmath ( sudo apt-get install php7.3-bcmath )
- php7.3-common 
- json (sudo apt-get install php7.3-json)
- Mbstring
- OpenSSL
- Tokenizer
- XAMPP / LAMP environment ready
- Composer
- Node (As JavaScript server)
- NPM (As frontend “composer”)
- Postman (to test API)
### Requirements (Using Laravel Homestead)
- Composer
- Virtual Box
- Vagrant
- Node (As JavaScript server)
- NPM (As frontend “composer”)
- Postman (to test API)

### Start building the project
#
```
composer create-project laravel/laravel RESTfulAPI 7.x
composer require laravel/homestead
composer require laravel/ui
php vendor/bin/homestead make
```
### Edit Homestead.yaml.
#
#
```
ip: 192.168.10.14
memory: 512
cpus: 1
provider: virtualbox
authorize: ~/.ssh/id_rsa.pub
keys:
    - ~/.ssh/id_rsa
folders:
    -
        map: /var/www/RESTfulAPI
        to: /home/vagrant/restfulapi
sites:
    -
        map: restfulapi.test
        to: /home/vagrant/restfulapi/public
databases:
    - homestead
features:
    -
        mariadb: false
    -
        ohmyzsh: false
    -
        webdriver: false
name: restfulapi
hostname: restfulapi

```
### SSH
#
#
```
- cat ~/.ssh/id_rsa.pub
- 192.168.10.14 restfulapi.test (host)
- vagrant up
- vagrant ssh
```


### Install the following packages 
```
composer require guzzlehttp/guzzle
composer require spatie/laravel-fractal
```
### In config/app.php, add this line in package service providers
```
Spatie\Fractal\FractalServiceProvider::class,
Laravel\Passport\PassportServiceProvider::class,
```
### In config/app.php, add this line in 'guards' key
```
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
        'hash' => false,
    ],
],
```
### In app/Http/Kernel.php ($routeMiddleware) add:
```
'client.credentials' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,
```
### Install NPM
```
npm install
npm run watch
composer require laravel/passport
```

### In resources/js/app.js, comment first line matching below and then add 3 lines:

```
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
```
```
Vue.component('passport-personal-access-tokens',require('./components/passport/PersonalAccessTokens.vue').default);
```
```
Vue.component('passport-clients',require('./components/passport/Clients.vue').default);
```
```
Vue.component('passport-authorized-clients',require('./components/passport/AuthorizedClients.vue').default);
```

### Run migrations to get all Laravel Passport tables and also the ones I already included in source code

```
php artisan migrate
php artisan passport:install
```

### Update app/Providers/AuthServiceProvider.php
#### At the end of boot() method,  add all these lines:
```
$this->registerPolicies();
Passport::routes();
Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
Passport::enableImplicitGrant();
//Scopes
Passport::tokensCan([
    'purchase product' => 'Create a new transaction',
    'manage-products' => 'Read, Create, Edit and Delete products',
    'manage-account' => 'Read your account data. If you are admin user, '
                         . 'also Create and Edit your account data. '
                         . 'Your password cannot be readed and your account cannot be deleted.',
    'read-general' => 'Grant readonly access to all sections'
]);
```
#### At top of file, add these lines:
```
use Laravel\Passport\Passport;
use Carbon\Carbon; 
```

### In routes/api.php, add these lines:
```
#PASSPORT
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
```

### Laravel Passport
```
php artisan passport:client
php artisan passport:client --password
php artisan vendor:publish --tag=passport-components
```

### In resources/js/app.js add lines
```
php artisan passport:client
php artisan passport:client --password
php artisan vendor:publish --tag=passport-components
```

### test [auth code] grant type
```
http://test.larapi.com/oauth/authorize?client_id=14&response_type=code&redirect_uri=http://localhost
```

### test [implicit] grant type
```
http://test.larapi.com/oauth/authorize?client_id=14&response_type=token&redirect_uri=http://localhost
```