# Assessment
Assessment project

## Installation

- First clone the repository from github
- Set your .env file with necessary credentials
- CD into directory and run composer install / composer update

## Transaction import
- For this purpose I have placed a CSV file into public/files directory. so that you can run the bellow command
- From project root directory run "php artisan transaction:import transaction.csv"
- I save transactions to cache directory, so if you want to test the result again, please run "php artisan cache:clear"

## Testing
- And Run vendor/bin/phpunit or phpunit
