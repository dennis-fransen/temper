#Requirements: 

- PHP 8
- composer

#Setup:
- Clone the repository
- run `cp .env.example .env`
- run `composer install`
- run `mkdir storage/app/test-data && cp -R storage/test-data/ storage/app/test-data/`  
- run `php artisan key:generate`
- run `php artisan serve`

You can now visit: http://127.0.0.1:8000/api/statistics/onboarding/list to see the output from the endpoint

#usage: 
There is a Postman collection in the docs folder with an example call
