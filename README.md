# Nette GraphQL project
This is preconfigured PHP backend project base on Nette, Doctrine and php-graphql
It has preconfigured Dockerfile for development, Doctrine with PDO_MYSQL, fixtures, migrations and REDIS cache.

## Requirements
- Docker
- OpenSSL

Without DOCKER you need:    
- PHP>=8.1 with pdo_mysql, mbstring and bcmath extensions
- OpenSSL
- Redis tools

## Contains
[ https://doc.nette.org/en/application/bootstrap ]    
[ https://contributte.org/packages/contributte/apitte/ ]    
[ https://github.com/portiny/graphql-nette ]    
[ https://contributte.org/packages/contributte/doctrine-orm.html ]    
[ https://contributte.org/packages/contributte/doctrine-migrations.html ]    
[ https://contributte.org/packages/contributte/doctrine-dbal.html ]    
[ https://contributte.org/packages/contributte/doctrine-fixtures.html ]    
[ https://contributte.org/packages/contributte/console.html ]    
[ https://tracy.nette.org/ ]    
[ https://contributte.org/packages/contributte/redis.html ]    
[ https://contributte.org/packages/contributte/cache.html ]    
[ https://github.com/monderka/jwt-parser ]    
[ https://github.com/monderka/doctrine-tools ]

## Installation
```
composer create-project monderka/nette-graphql-project path/to/install
cd path/to/install
```

Build docker image
```
docker build -f Dockerfile.dev -t nette-graphql-project .
```

Run service
```
docker run nette-graphql-project:latest
```
