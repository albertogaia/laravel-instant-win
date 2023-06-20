## HOW TO:

-   Clone the project
-   Go to the folder application using cd command on your cmd or terminal
-   Run composer install on your cmd or terminal
-   Copy .env.example file to .env on the root folder. You can type copy .env.example .env if using command prompt Windows or cp .env.example .env if using terminal, Ubuntu
-   Create on your phpMyAdmin a new db
-   Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
-   Run php artisan key:generate
-   Run php artisan migrate
-   Run php artisan serve
-   run npm install to install eventually the dependencies
-   Enjoy the contest

Alberto Gaia
