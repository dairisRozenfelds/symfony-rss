# RSS reader Symfony APP

The app is built on Symfony 5.0.4

## Setup

1. Go to the project's root folder
2. Install composer dependencies ```composer install```
3. Install node packages ```npm install``` or ```yarn install```
4. Setup the app on the server, you need to follow the basic Symfony installation (available [here]: https://symfony.com/doc/current/setup.html)
5. Run migrations ```php bin/console doctrine:migrations:migrate```
6. Load fixtures ```php bin/console doctrine:fixtures:load```
7. Compile assets ```npm run dev``` or ```yarn run dev``` (development mode)

## Notes

The results from the RSS feed parser are cached and cache is invalidated every 4 hours.

## Tests

In the app there is one unit test that tests whether the word counter returns correct data.

To run the test, just execute ```phpunit``` from the root folder.
