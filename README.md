# Mini Aspire App

This is a simple loan app that allows users to apply for a loan, view their loan details, and make loan repayments. 

## Prerequisites

Before running the application, make sure you have the following:

- PHP >= 7.3
- Composer
- MySQL

## Installation

1. Clone the repository to your local machine:

```
git clone https://github.com/kanchijain94/mini-aspire-app.git
```

2. Change into the project directory:

```
cd mini-aspire-app
```

3. Install the application dependencies using Composer:

```
composer install
```

4. Create a new `.env` file:

```
cp .env.example .env
```

5. Generate a new `APP_KEY`:

```
php artisan key:generate
```

6. Configure the `.env` file with your database credentials:

```
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

7. Migrate the database:

```
php artisan migrate
```

8. Seed the database with test data:

```
php artisan db:seed

```

9. Setup OAuth2 authentication

```
php artisan passport:install

```

## Usage

1. Start the PHP development server:

```
php artisan serve
```

2. We need to register first to start use the program with this curl
```
curl --location 'http://localhost:8000/api/v1/users/register' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "{{name}}",
    "email": "{{email}}",
    "password": "{{password}}"
}'
```

3. Once registered, you can login with the user details
```
curl --location 'http://localhost:8000/api/v1/users/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "{{email}}",
    "password": "{{password}}"
}'
```

4. To login as an admin, use the following credentials:

```
Email: admin@mail.com
Password: 123456
```

5. Once logged in, you can view your loan details, apply for a loan, and make loan repayments.

6. To see the loan list use this curl(admin can see all, use can only see their loan)
```
curl --location --request GET 'http://localhost:8000/api/v1/users/loan/show-loans' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer {{access_token from login response}}' 
```

7. To create new loan request, use this API
```
curl --location 'http://localhost:8000/api/v1/users/loan/new-loan-request' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer {{access_token from login response}}' \
--data '{
        "principal_amount": {{amount of loan in numeric}},
        "term": {{term of payment in numeric}}
    }'
```

8. To approve loan use this API, only admin can approve the loan
```
curl --location --request PUT 'http://localhost:8000/api/v1/users/admin/loan/approve-loan' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer {{access_token from login response}}' \
--data '{
        "loan_id": {{loan id for which the loan needs to be approved}}
    }'
```

9.  To pay based on loan_id use this API
```
curl --location 'http://localhost:8000/api/v1/users/loan/weekly-repay' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer {{access_token from login response}}' \
--data '{
        "payable_amount": {{amount of payment in numeric}},
        "loan_id": {{loan id for which the amount needs to be paid}}
    }'
```

9.  To logout a user, use this API
```
curl --location --request GET 'http://localhost:8000/api/v1/users/logout' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer {{access_token from login response}}'
```

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

