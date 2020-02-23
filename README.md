# RSS reader Symfony APP

The app is built on Symfony 5.0.4

## Setup

1. Go to the project's root folder
2. Create a new .env.local file and configure it ```cp .env .env.local``` (database configuration docs: https://symfony.com/doc/current/doctrine.html#configuring-the-database)
3. Install composer dependencies ```composer install```
4. Install node packages ```npm install``` or ```yarn install```
5. Setup the app on the server, you need to follow the basic Symfony installation (available: https://symfony.com/doc/current/setup.html)
6. Run migrations ```php bin/console doctrine:migrations:migrate```
7. Load fixtures ```php bin/console doctrine:fixtures:load```
8. Compile assets ```npm run dev``` or ```yarn run dev``` (development mode)

## Notes

The results from the RSS feed parser are cached and cache is invalidated every 4 hours.

## Tests

In the app there is one unit test that tests whether the word counter returns correct data.

To run the test, just execute ```phpunit``` from the root folder.
