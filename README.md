[![Slack](https://robohome-slackin.herokuapp.com/badge.svg)](https://robohome-slackin.herokuapp.com)
[![Build Status](https://travis-ci.org/dbudwin/RoboHome-Web.svg?branch=master)](https://travis-ci.org/dbudwin/RoboHome-Web)
[![Code Climate](https://codeclimate.com/github/dbudwin/RoboHome-Web/badges/gpa.svg)](https://codeclimate.com/github/dbudwin/RoboHome-Web)
[![Coverage Status](https://coveralls.io/repos/github/dbudwin/RoboHome-Web/badge.svg)](https://coveralls.io/github/dbudwin/RoboHome-Web)

# RoboHome-Web

## What Is RoboHome-Web?

This repo is one of a few repos that make up the whole project.  RoboHome-Web is the codebase that represents the frontend of the RoboHome project.  The web interface provides a way to create users, add and manage devices, and an additional way to control devices.  RoboHome-Web is primarily built using PHP with [Laravel](https://laravel.com/) for MVC and routing, MySQL for the database, and Bootstrap for layout and basic mobile support.

## What Is the RoboHome Project?

RoboHome is a SaaS tool that also integrates with Amazon's Echo to enable control of semi-connected devices (think IR, and RF) in your house over wifi! This is done using an MQTT pub-sub network to send messages between the website or Echo to a microcontroller like the ESP8266 which has various transmitters hooked up to it (like IR and RF transmitters) to send signals to these devices. This can be used to control RF outlets with lights plugged into them, or to turn on your TV and change channels for instance.

## Developing RoboHome-Web

### Requirements :white_check_mark:

1. Web server with PHP 7.1.3 or greater with MySQL
2. SSL/TLS cert [available for Free with Let's Encrypt](https://www.letsencrypt.org/).  Note, we must use _slightly_ less secure ciphers than the max due to hardware limitations of the ESP8266 which will need to talk to the RoboHome-Web API over HTTPS to gather information about devices.  If set up correctly, by entering your website in [SSL Lab's SSL Server Test](https://www.ssllabs.com/ssltest/index.html), you should still get an "A" rating.  To get the appropiate ciphers for your server visit [Cipherli.st](https://cipherli.st/) and click "Yes, give me a ciphersuite that works with legacy / old software."
3. MQTT broker for pub-sub. I personally use [CloudMQTT](https://www.cloudmqtt.com/). This service is used to send messages from a web service to a microcontroller.
4. [Composer](https://getcomposer.org/) dependency manager for PHP

### Configuring :wrench:

1. Rename `.env.example` to `.env` and populate the information needed to connect to your MQTT broker and MySQL database (under `DB_CONNECTION`).
2. Run `composer install` from the root folder to download and install third-party PHP dependencies.
3. Run `php artisan key:generate` from the root folder.  This will populate the `APP_KEY` field in the `.env` file.
4. Run `php artisan migrate` from the root folder.  This will setup the database using the connection settings defined in the `.env` file.
5. Run `php artisan passport:install` from the root folder and save the output to a safe location.  The output will be needed later to help configure 3rd parties (like Amazon) to authenticate with RoboHome.
6. Run `php artisan serve` from the root folder to start the website.  This command will print to the terminal the local URL where you can visit the website in your browser.

### Docker :whale2:

This project uses [Docker Compose](https://docs.docker.com/compose/) to help easily emulate a test environment for rapid development and testing.  This container has the PHP, MySQL, and phpMyAdmin services.

1. Start Docker and execute `./StartDockerAndRunTests.sh`.  This will create a backup of the `.env` file, download and start all the containers, configure Laravel, run all the tests, and then restore the `.env` file.
    - Once running, visit `http://localhost` to view the website.  To access phpMyAdmin, navigate to `http://localhost:8183` and login with the following credentials; username: `root`, password: `password` (these credentials come from `.env.docker` if you want to change them).
    - This is a good step to execute before contributing code.
2. To run custom commands inside the container running Laravel, pass the command to `web.sh` like `./web.sh composer install`.

## Contributing

### How To Contribute :gift:

Contributions are always welcome!  Please fork this repo and open a pull request from a new branch with your code or feel free to make an issue.  All pull requests will need to be reviewed and pass automated checks.  If feedback is given on a pull request, please submit updates to the original pull request in the form of [fixup! commits](https://robots.thoughtbot.com/autosquashing-git-commits) which will later be squashed before the pull request is merged.

For more information, consult the [contributing guidelines](https://github.com/dbudwin/RoboHome-Web/blob/master/CONTRIBUTING.md).

This repo supports the principles of [Bob Martin's Clean Code](http://www.goodreads.com/book/show/3735293-clean-code).

### Notes :notebook:

- Before you release this application...
    - If you're using Docker Compose, please take the time to update the username and default password to be more secure.  The current implementation is designed for running this application locally.
    - Secure your Laravel installation by making the following changes to your `.env` file:
        - Change `APP_ENV` to `production`
        - Change `APP_DEBUG` to `false`
