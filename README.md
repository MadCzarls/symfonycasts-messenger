# Description

https://symfonycasts.com/screencast/messenger

Sandbox for getting to know and learn Symfony Messenger component, based on https://symfonycasts.com/screencast/messenger/, but run on Docker, utilizing `docker compose`, with PHP 8.2 + Symfony 7.0 + nginx 1.19 + PostgreSQL 15 + Vue 3.
Sandboxes in previous Symfony versions are available at `legacy/symfony<VERSION>` branches - check them out if you need.

**Status: WIP**
- [x] Chapter 1
- [x] Chapter 2
- [x] Chapter 3
- [x] Chapter 4
- [x] Chapter 5
- [x] Chapter 6
- [x] Chapter 7
- [x] Chapter 8
- [x] Chapter 9
- [x] Chapter 10
- [x] Chapter 11
- [x] Chapter 12
- [x] Chapter 13
- [ ] Chapter 14
- [ ] Chapter 15
- [ ] Chapter 16
- [ ] Chapter 17
- [ ] Chapter 18
- [ ] Chapter 19
- [ ] Chapter 20
- [ ] Chapter 21
- [ ] Chapter 22
- [ ] Chapter 23
- [ ] Chapter 24
- [ ] Chapter 25
- [ ] Chapter 26
- [ ] Chapter 27
- [ ] Chapter 28
- [ ] Chapter 29
- [ ] Chapter 30
- [ ] Chapter 31
- [ ] Chapter 32
- [ ] Chapter 33
- [ ] Chapter 34
- [ ] Chapter 35
- [ ] Chapter 36
- [ ] Chapter 37
- [ ] Chapter 38
- [ ] Chapter 39
- [ ] Chapter 40
- [ ] Chapter 41
- [ ] Chapter 42
- [ ] Chapter 43
- [ ] Chapter 44
- [ ] Chapter 45
- [ ] Chapter 46
- [ ] Chapter 47
- [ ] Chapter 48

 By default, includes xdebug extension and PHP_CodeSniffer for easy development and basic configuration for opcache for production. Includes instruction for setting it in PhpStorm.

- https://symfony.com/
- https://www.docker.com/
- https://docs.docker.com/compose/
- https://www.php.net/
- https://www.nginx.com/
- https://www.postgresql.org/
- https://xdebug.org/
- https://github.com/squizlabs/PHP_CodeSniffer
- https://www.php.net/manual/en/intro.opcache.php
- https://www.jetbrains.com/phpstorm/

Clone and tweak it to your needs. Tested on Linux (Ubuntu 20.04):

1. `Docker` version 24.0.7
1. `docker compose` version 2.21.0

set up in WSL2, on Ubuntu 22.04.3 LTS (Jammy).

# Usage

1. Clone repository, `cd` inside.
1. Create `.env` file in `docker/php` directory according to your environment, one of - `dev`, `test`, `prod` - just copy correct template `.env.dist`, but remember to define your own `APP_SECRET`!
1. Review `compose.yaml` and change it according to the comments inside.
1. You can change PHP memory limit in `docker/php/config/docker-php-memlimit.init` file if you want.

Afterwards run:
<pre>
docker compose build
docker compose up
</pre>

After that log into container with `docker exec -it messenger.php bash`, where `messenger.php` is the default container name from `compose.yaml`. Then run:

<pre>
composer install
npm install
npm run dev
php bin/console doctrine:migrations:migrate
</pre>

From this point forward, application should be available under `http://localhost:8050/`, where port `8050` is default defined in `compose.yaml`.

### A note concerning Supervisor and Chapter 24

To mimic production environment and to follow `Chapter 24` repository contains Supervisor - tool to control processes on your system.
It is used to run - constantly - Symfony's Messenger's consumer. Configuration is stored in `docker/supervisor/*.conf`.

But since we are using dockerized environment there are a few issues with that:
* we can have (and probably should) have separate container for Supervisor - but since Messenger's `messenger:consume` command is integral part of Symfony we would need to include another copy of whole application in that container
* we can have Supervisor installed in the same container as PHP but, because of the way it starts (`CMD` command) it blocks PHP-FPM process from starting (only one `CMD` may be used in `Dockerfile`) (and this way you are treating Docker more like virtual machine for everything - and you should not)

For sandbox-learning purposes I have decided to go with the second approach and resolve the issue of not-starting PHP-FPM by starting it
automatically on `docker compose up` by using... Supervisor ;) - check `docker/supervisor/php-fpm.conf` file -
but in the end, since it's a sandbox, it's disabled - you can uncomment lines in `docker/php/Dockerfile`:
`CMD ["/usr/bin/supervisord"]` and `COPY supervisor/* /etc/supervisor/conf.d/` if you want to try it out.

## Running tests

Environment variable `APP_ENV` must be set to `test` to be able to run Kernel-/Web-TestCases based tests because
`Real environment variables win over .env files` and this is the case in docker-based environments.

# Overview

All PHP extensions can be installed via `docker-php-ext-install` command in `docker/php/Dockerfile`. Examples and usage:
`https://gist.github.com/giansalex/2776a4206666d940d014792ab4700d80`.

## PhpStorm configurations
_Based on PhpStorm version: 2023.2.2_

Open directory including cloned repository as directory in PhpStorm.

### Interpreter

1. `Settings` -> `PHP` -> `Servers`: create server with name `docker` (the same as in ENV variable `PHP_IDE_CONFIG`), host `localhost`, port `8050` (default from `compose.yaml`).
1. Tick `Use path mappings` -> set `File/Directory` <-> `Absolute path on the server` as: `</absolute/path>/app` <-> `/var/www/app` (default from `compose.yaml`).
1. `Settings` -> `PHP`: three dots next to the field `CLI interpreter` -> `+` button -> `From Docker, Vagrant(...)` -> tick `Docker compose`, choose server `Docker`, set `Configuration files` to `./compose.yaml`. After that `Service` list should be reloaded - pick `php` from there. In `Lifecycle` section ensure to pick `Always start a new container (...)`, in `General` refresh interpreter data.

### xdebug

1. `Settings` -> `PHP` -> `Debug`  -> `Xdebug` -> `Debug port`: `9003` (set by default) and check `Can accept external connections`.
1. Click `Start Listening for PHP Debug connections` -> `+` button, set breakpoints and refresh website.

### PHPCS

1. Copy `app/phpcs.xml.dist` and name it `phpcs.xml`. Tweak it to your needs.
1. `Settings` -> `PHP` -> `Quality Tools` -> `PHP_CodeSniffer` -> `Configuration`: three dots, add interpreter with `+` and validate paths. By default, there should be correct path mappings and paths already set to `/var/www/app/vendor/bin/phpcs` and `/var/www/app/vendor/bin/phpcbf`.
1. `Settings` -> `Editor` -> `Inspections` -> `PHP` -> `Quality tools` -> tick `PHP_CodeSniffer validation` -> tick `Show sniff name` -> set coding standard to `Custom` -> three dots and type `/var/www/app/phpcs.xml` (path in container).

### PostgreSQL

Open `Database` section on the right bar of IDE -> `Data Source` -> `PostgreSQL` -> set host to `localhost`, set user to `app_user`, pass `app_pass`, database to `messenger_app` (defaults from `compose.yaml`) Set url to `jdbc:postgresql://localhost:5432/app`.

### PHPUnit

1. Copy `phpunit.xml.dist` into `phpunit.xml`.
1. Login into `messenger.php` container where `messenger.php` is the default container name from `compose.yaml`, and run `./bin/phpunit`.
1. `Settings` -> `PHP` -> `Test frameworks`. Click `+` and `PHPUnit by Remote Intepreter` -> pick interpreter. In `PHPUnit library` tick `Path to phpunit.phar` and type `bin/phpunit`. Click refresh icon. In `Test runner` section set `Default configuration file` to `phpunit.xml` and `Default bootstrap file` to `tests/bootstrap.php`.

# Disclaimer

Although there are present different files for `prod` and `dev` environments these are only stubs - the idea was to create as much integral, self-contained and flexible environment for `development` as possible and these files are here merely to easily mimic `prod` env and point out differences in configuration.