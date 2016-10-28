[![Build Status](https://travis-ci.org/dbudwin/RoboHome-Web.svg?branch=master)](https://travis-ci.org/dbudwin/RoboHome-Web)
[![Code Climate](https://codeclimate.com/github/dbudwin/RoboHome-Web/badges/gpa.svg)](https://codeclimate.com/github/dbudwin/RoboHome-Web)
[![Test Coverage](https://codeclimate.com/github/dbudwin/RoboHome-Web/badges/coverage.svg)](https://codeclimate.com/github/dbudwin/RoboHome-Web/coverage)

#RoboHome-Web

##What Is RoboHome-Web?

This repo is one of a few repos that make up the whole project.  RoboHome-Web is the codebase that represents the frontend of the RoboHome project.  The web interface provides a way to create users, add and manage devices, and an additional way to control devices.  RoboHome-Web is primarily built using PHP with the [F3 Framework](https://fatfreeframework.com/home) for MVC and routing, MySQL for the database, and Bootstrap for layout and basic mobile support.

##What Is the RoboHome Project?

RoboHome is a SaaS tool that also integrates with Amazon's Echo to enable control of semi-connected devices (think IR, and RF) in your house over wifi! This is done using an MQTT pub-sub network to send messages between the website or Echo to a microcontroller like the NodeMCU which has various transmitters hooked up to it (like IR and RF transmitters) to send signals to these devices. This can be used to control RF outlets with lights plugged into them, or to turn on your TV and change channels for instance.

##Developing RoboHome-Web

###Requirements :white_check_mark:

1. Webserver with PHP 5.6 or greater with MySQL and a SSL/TLS cert [available for Free with Let's Encrypt](https://www.letsencrypt.org/) (This last part isn't needed if using Docker, see below). This is used to host the Bootstrap based website to add, delete, edit, and control devices from anywhere.
2. MQTT broker for pub-sub. I personally use [CloudMQTT](https://www.cloudmqtt.com/). This service is used to send messages from a webservice to a microcontroller.
3. An account with [Amazon](https://www.amazon.com/) to be used for account registration using OAuth.
4. An account with [Login with Amazon](https://login.amazon.com/) to allow your website to use OAuth to verify users.  Tip, be sure to register both www and non-www versions of URLs for the "Allowed JavaScript Origins."
5. [Composer](https://getcomposer.org/) dependency manager for PHP

###Configuring :wrench:

1. Open the `secrets.ini` file and populate the information needed to connect to your MQTT broker, MySQL database, and an Amazon login Client ID.
2. Run `composer install` from the root folder to download and install third party PHP dependencies.

###Docker :whale2:

This project uses [Docker Compose](https://docs.docker.com/compose/) to help easily emulate a test environment for rapid development and testing.  This container has the PHP, MySQL, and phpMyAdmin services.  For login to work, you'll need to add `http://localhost` to the "Allowed JavaScript Origins" in your Login With Amazon App Console.  Once you have Docker installed and running, execute `docker-compose up` and visit `http://localhost/fatfree` to view the website.  To access phpMyAdmin, navigate to `http://localhost:8183` and login with the following credentials; Server: `db`, user: `root`, password: `password`.

##Contributing

###How To Contribute :gift:

Contributions are always welcome!  Please fork this repo and open a PR with your code or feel free to make an issue.  All PRs will need to be reviewed and pass automated checks.  If feedback is given on a PR, please submit updates to the original PR in the form of [fixup! commits](https://robots.thoughtbot.com/autosquashing-git-commits) which will later be squashed before the PR is merged.

This repo supports the principles of [Bob Martin's Clean Code](http://www.goodreads.com/book/show/3735293-clean-code).

###Notes :notebook:

- To avoid seeing the `secrets.ini` file (particulary when Git says you have unstaged changes during a rebase) in your repo and you don't want to ignore it, run `git update-index --assume-unchanged fatfree/app/secrets.ini`
- Before you release this application...
    - If you're using Docker Compose, please take the time to update the username and default password to be more secure.  The current implementation is designed for locally running this application.
    - Update the `config.ini` file and set `DEBUG = 0` for added security to prevent displaying overly detailed debug logs to users.


*This is a new project and will be changing rapidly, more details will be provided when entering a beta state*
