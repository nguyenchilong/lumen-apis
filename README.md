# Testing APIs
- This repo assumption already login and have `access_token`
- This file for guideline use and local setup for run test

## Requirements
- Need install [Docker](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/), recommend install [Docker Desktop](https://docs.docker.com/desktop/)
- Install [Composer](https://getcomposer.org/download/)
- Install [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
- Install [Postman](https://www.postman.com/downloads/) or similar tool for test API
- Install IDE such as vscode, phpstorm, etc for easy write code

## Setup Localhost
- The first time then need clone this repo into localhost first
- `cd path_root_project` then start project with `start.sh` file
- Run command `chmod +x start.sh` then `sh start.sh` and wait for completed \
- Install packages via command `docker-compose run --rm composer install`
- Run migrate to import database `docker-compose run --rm artisan migrate` then insert mock data by seed `docker compose run --rm artisan db:seed`
- Then open postman to check (can import Collection & Environment to postman first from postman folder)

## Working with docker-compose
- **nginx** - `:80`
- **mysql** - `:3306`
- **php** - `:9000`
- In 'start.sh' file will stop & start all services when run
- But if you want start one service, run `artisan` or `composer` command then can open terminal and follow list command under
```shell
# this command for run "composer" and can install, update, etc
# "docker-compose run --rm" => run and delete after done
# "composer" => run "composer" service
# "update" => command "update" of composer
docker-compose run --rm composer update

# this command for run "artisan"
# "docker-compose run --rm" => run and delete after done
# "artisan" => run "artisan" service
# "migrate" => command "migrate" of artisan
docker-compose run --rm artisan migrate 

# this command for run only one service
docker-compose up -d {service_name}
```

## Persistent MySQL Storage
- By default, whenever you bring down the Docker network, your MySQL data will be removed after the containers are destroyed. If you would like to have persistent data that remains after bringing containers down and back up, do the following:
1. Create a `dbdata` folder inside `docker` folder.
2. Under the mysql service in your `docker-compose.yml` file, add the following lines:
```
volumes:
  - ./docker/dbdata:/var/lib/mysql
```
