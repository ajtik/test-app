Invoice Test App
========

Frontend part of this project was bootstrapped with [Create React App](https://github.com/facebook/create-react-app).


Requirements
------------

- Apache web server with PHP support

- PHP 8.0 or higher with Composer installed

- MySQL database server with a new database

- Node.js 10.12 or higher with Yarn installed

Note for Windows users: use `core.autocrlf = input` (code style tools require LF line endings)


Installation
------------

1. Copy `config/local.neon.dist` to `config/local.neon` and change it according to your environment.

2. Make directories `temp/` and `log/` writable for the web server.

3. Install PHP dependencies using composer:

		composer install

4. Run database migrations:

		bin/console migrations:migrate

5. Install frontend dependencies using yarn:

		yarn install

6. Optional: Copy `.env.local.dist` to `.env.local` and change it to configure frontend settings (see below).

7. Either:

	a) run frontend dev server:

		yarn start

	b) build frontend assets:

		yarn build


Configuration
-------------

For PHP configuration options, see https://doc.nette.org/cs/3.1/configuring.

You can adjust various development and production settings by setting environment variables in your shell or with [.env](adding-custom-environment-variables.md#adding-development-environment-variables-in-env).

> Note: You do not need to declare `REACT_APP_` before the below variables as you would with custom environment variables.

| Variable                | Development | Production | Usage                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| :---------------------- | :---------: | :--------: | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| BROWSER                 |   ✅ Used   | 🚫 Ignored | By default, Create React App will open the default system browser, favoring Chrome on macOS. Specify a [browser](https://github.com/sindresorhus/open#app) to override this behavior, or set it to `none` to disable it completely. If you need to customize the way the browser is launched, you can specify a node script instead. Any arguments passed to `npm start` will also be passed to this script, and the url where your app is served will be the last argument. Your script's file name must have the `.js` extension.                                                                                                                                      |
| BROWSER_ARGS            |   ✅ Used   | 🚫 Ignored | When the `BROWSER` environment variable is specified, any arguments that you set to this environment variable will be passed to the browser instance. Multiple arguments are supported as a space separated list. By default, no arguments are passed through to browsers.                                                                                                                                                                                                                                                                                                                                                                                               |
| HOST                    |   ✅ Used   | 🚫 Ignored | By default, the development web server binds to all hostnames on the device (`localhost`, LAN network address, etc.). You may use this variable to specify a different host.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| PUBLIC_HOST             |   ✅ Used   | 🚫 Ignored | By default, the development web server points to itself at `HOST` (see above). You may use this variable to specify a different host, e. g. when running it in the container.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| PORT                    |   ✅ Used   | 🚫 Ignored | By default, the development web server will attempt to listen on port 3000 or prompt you to attempt the next available port. You may use this variable to specify a different port.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| PUBLIC_ORIGIN           |   ✅ Used   | 🚫 Ignored | By default, the development web server assumes the backend is visible at any hostnames on the device (`localhost`, LAN network address, etc.) via `http` scheme at default port. You may use this variable to specify a different origin to fix CORS errors, e. g. when using virtual hosts or running the app in the container.                                                                                                                                                                                                                                                                                                                                         |
| PUBLIC_URL              |   ✅ Used   |  ✅ Used   | Create React App assumes your application is hosted at the serving web server's root. You may use this variable to force assets to be referenced verbatim to the url you provide (hostname included).                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| GENERATE_SOURCEMAP      | 🚫 Ignored  |  ✅ Used   | When set to `false`, source maps are not generated for a production build. This solves out of memory (OOM) issues on some smaller machines.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |

See https://create-react-app.dev/docs/advanced-configuration/ for more configuration options.


Scripts
-------

- List console commands:

		bin/console

- Generate a migration by comparing your current database to your mapping information:

		bin/console migrations:diff

- Execute migrations to the latest available version:

		bin/console migrations:migrate

- Check code style:

		composer check-cs

- Fix code style:

		composer fix-cs

- Run PHPStan:

		composer phpstan

- Run tests:

		composer test

- Run tests on Ubuntu:

		composer test -- -c tests/php-ubuntu.ini

- Run frontend dev server:

		yarn start

- Build frontend assets:

		yarn build

- Run JavaScript tests in watch mode:

		yarn test

- Lint JavaScript:

		yarn eslint

- Lint styles:

		yarn stylelint


Docker development
------------------

- Docker and Docker Compose is required


### Installation

1. Copy `.env.local.docker` and `config/local.neon.docker` to `.env.local` and `config/local.neon`, respectively.

2. Optional: Build docker images, specifying user and group ID:

		USER_ID=$( id -u ) GROUP_ID=$( id -g ) docker-compose build

3. Optional: Create directories `node_modules` and `vendor`. If you don't, Docker creates them while binding corresponding
volumes, with `root` ownership on Linux (see https://github.com/moby/moby/issues/26051).

4. Install frontend dependencies:

		docker-compose run --rm --no-deps webpack yarn install

5. Install backend dependencies:

		docker-compose run --rm --no-deps php composer install

6. Run with debug mode enabled:

		NETTE_DEBUG=1 docker-compose up

7. Run database migrations:

		docker-compose run --rm php bin/console migrations:migrate


### Database

- Adminer is bound to port 90, e. g. http://127.0.0.1:90

- User `root`

- Password `pass`

- Database `invoice-test-app` is created automatically


### Development

- Run scripts (see above) with `docker-compose run`:

	- `docker-compose run --rm php` for `bin/console` and `composer`

	- `docker-compose run --rm webpack` for `yarn`

- Run tests with `docker-compose run` and `tests/php-docker.ini`:

		docker-compose run --rm php composer test -- -c tests/php-docker.ini

- Use environment variable `NETTE_DEBUG` to control debug mode

- Dependency directories (`node_modules` and `vendor`) are not mounted via bind mount, but via named volumes. To use
them on host (e. g. for code completion), you can copy them with `docker cp` or `docker run`, i. e.:

		docker cp invoice-test-app_php_1:/app/vendor . && docker cp invoice-test-app_webpack_1:/app/node_modules .

	or

		docker run --rm -u $(id -u):$(id -g) -v invoice-test-app_node_modules:/node_modules -v invoice-test-app_vendor:/vendor -v $PWD:/app alpine cp -r /node_modules /vendor /app
