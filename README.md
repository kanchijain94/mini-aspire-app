## Aspire Loan API

Its easy to use. just go with this instructions

- `git clone https://github.com/saddamsegwitz/aspire-loan.git`
- `composer update`
- rename the `.env.example` file as `.env`
- set 
    
       DB_DATABASE=YOURDBNAME
       DB_USERNAME=YOURDBUSERNAME
       DB_PASSWORD=YOURDBPASSWORD
      
- `php artisan key:generate`
- `php artisan migrate:fresh --seed`
- `php artisan passport:install`
- `php artisan config:cache`
- `php artisan serve`

Yup that's it you are ready to test postman collection 

postman collection link : https://www.getpostman.com/collections/b362b53aebcaa0272587

