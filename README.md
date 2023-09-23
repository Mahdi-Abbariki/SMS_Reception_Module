
### All rights reserved for Mahdi Abbariki.


# How To Run

first install the libraries needed.

```shell
composer install
```

create the .env file
check this file and edit it as needed

```shell
cp ./.env.example ./.env
```

generate the key

```shell
php artisan key:generate
```

#### For sqlite
create the database (sqlite)

```shell
touch ./database/database.sqlite
```

run the migrations to create database structure
```shell
php artisan migrate
```

run the server
```shell
php artisan serve
```

open `127.0.0.1:8000` in the browser


enter the config of one of providers in the tab opened. 

run this in a shell and keep it open to see the automatic tasks that have been done in the system.
```shell
php artisan schedule:run
```

## Notes

you can see app/Library/SMS directory files to implement new SMS provider API.

i tested sms20 configurations in various ways and it did not work correctly. some of the code that  have tested the SOAP web service with it is still available in SMS20 class.

to see how all of this code works i've used Kavenegar as another provider. 

thanks to the **Design Pattern** used in this code, any other provider can be easily implemented and configured without any further changes in the logic of the system.




