# Laravel 7 - RESTful API

## This is an example of a RESTful API built 100% in Laravel 7, also an excuse to practice and show all Laravel features that I've been working with since 2016.

### Environment details

- Ubuntu 19.10
- PHP 7.3.11
- Laravel 7.3.0

### Requirements (Using Laravel Homestead)

- Composer
- Virtual Box
- Vagrant
- Node (As JavaScript server)
- NPM (As frontend “composer”)
- Postman (to test API)

```
composer require laravel/homestead
composer require laravel/ui
php vendor/bin/homestead make
```
- Edit Homestead.yaml

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
- cat ~/.ssh/id_rsa.pub
- 192.168.10.14 restfulapi.test (host)
- vagrant up
- vagrant ssh


### Requirements for running in your local environment (PHP 7)

- ext-dom or XML ( sudo apt install php-xml )
- bcmath ( sudo apt-get install php7.3-bcmath )
- php7.3-common 
- json (sudo apt-get install php7.3-json)
- Mbstring
- OpenSSL
- Tokenizer

### Extra requirements no matter your environment
```
composer require guzzlehttp/guzzle
composer require spatie/laravel-fractal
```
- In config/app.php, add this line in package service providers
Spatie\Fractal\FractalServiceProvider::class,
```
npm install
npm run dev
composer require laravel/passport
```
- In config/app.php, add this line in package service providers
Laravel\Passport\PassportServiceProvider::class,


### What does this project cover?