# Docker Compose for Symfony 4 with PHP, Apache, mySQL and Composer
This git provides a docker infrastructure for Symfony development.
## Configuration
You can modify PHP version, mySQL version, mySQL credentials and Apache HTTP port in the /.env environment file.
When you download this branch please create your environment settings. Don't forget to create mysql root and dev user passwords.

    $ cp .env.dist .env
After modification please run 

    $ docker-compose build
## How to install Symphony 4?
First start the docker containers:

    $ docker-compose up -d
Then enter the *php_web* container with the *dev* user to install Symphony 4 with composer: (Symfony does not allow to install via root user)

    $ docker exec -u dev -it redefacilbra_php_web bash
Change directory to */var/www* where is the mapped *html* folder and please run the standard composer command to install all the necessary codes. Important: always use the word *html* as the directory parameter.

    $ cd /var/www
    $ composer create-project symfony/website-skeleton html
In your code editor you will see the */html* folder contains all the Symfony codes. This folder mapped to the */var/www/html* path.

## How to prepare ownership
Change ownership and give full access to all groups/users:

    $ sudo chmod -R 777 html/
    $ sudo chown -R username:username html/
These commands should be used on host filesystem.

At this point, you can easily `git clone` a repo on the host system into the html folder and run composer.

This docker also includes Xdebug which integrates nicely with PHPStorm.