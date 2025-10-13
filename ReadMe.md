### H5 Hire Technology Test

This is a test for the H5 Hire Technology position. 
The goal is to create a simple web application that allows users to search for project.
Application is built using Angular 20 and Symfony 7.3 with PHP 8.4.
---
##### How to set up the application

After cloning the repository, you can set up the application using Docker.

copy the `.env.example` file to `.env` and update the database connection settings.

The application will be available at http://localhost:8750, after running following commands.

````
    # BE

    docker compose up -d # to start the containers

    docker exec -it symfony_apache bash # to enter the Symfony container
    
    composer install # to install the dependencies

    /var/www/backend/bin/console doctrine:database:create # to create the database

    /var/www/backend/bin/console doctrine:migrations:migrate # to run the migrations
    
    /var/www/backend/bin/console doctrine:fixtures:load # to load the fixtures
    
    ___________________________________________________________________________________
    
    # FE
    
    cd /var/www/frontend # to enter the Angular container
    
    npm install # to install the dependencies
    
    ng build
````

---
##### How to close the application

````
   docker compose down
````
---
##### For FE/BE commands:

````
    /var/www/backend/bin/console    # Symfony backend
    
    php bin/console
___________________________________________________________
    
    cd /var/www/frontend  # Angular frontend
    
    ng build
````
---
##### API Documentation:

All API endpoints are start with `/api/` prefixed. (docker/apache/apche.config line: 36)

---
##### Schickling MailCatcher Documentation:

For symfony, MailCatcher is configured by setting the `MAILER_DSN` environment variable in your `.env` file:

```
MAILER_DSN=smtp://mailer:1025
```

For testing email functionality, we are using the Schickling MailCatcher. 
It is accessible at `http://localhost:2080`.
This allows you to view emails sent by the application without needing a real email server.
---
##### MySQL Adminer Documentation:
```
Host Machine Url: http://localhost:3400
user: root
password: root
```
---
##### RabbitMQ Information:

RabbitMQ is used for message queuing in the application.

It is accessible at `http://localhost:15672` with the following credentials:
```
Username: guest
Password: guest
```
---
##### Supervisord Information:

Supervisord is used to manage the background processes in the application.

Please check the `supervisord.conf` file for more information.

There is test command to run the supervisord in the background:`symfony/src/Command/TestCommand.php`

It is accessible at `http://localhost:9001` with the following credentials:
```
Username: guest
Password: guest
```
