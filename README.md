[![Build Status](https://travis-ci.org/dbudwin/RoboHome-Web.svg?branch=master)](https://travis-ci.org/dbudwin/RoboHome-Web)
[![Code Climate](https://codeclimate.com/github/dbudwin/RoboHome/badges/gpa.svg)](https://codeclimate.com/github/dbudwin/RoboHome)

#RoboHome-Web

##What Is RoboHome-Web?

This repo is one of a few repos that make up the whole project.  RoboHome-Web is the codebase that represents the frontend of the RoboHome project.  The web interface provides a way to create users, add and manage devices, and an additional way to control devices.  RoboHome-Web is primarily built using PHP with the [F3 Framework](https://fatfreeframework.com/home) for MVC and routing, MySQL for the database, and Bootstrap for layout and basic mobile support.

##What Is the RoboHome Project?

RoboHome is a SaaS tool that also integrates with Amazon's Echo to enable control of semi-connected devices (think IR, and RF) in your house over wifi! This is done using an MQTT pub-sub network to send messages between the website or Echo to a microcontroller like the NodeMCU which has various transmitters hooked up to it (like IR and RF transmitters) to send signals to these devices. This can be used to control RF outlets with lights plugged into them, or to turn on your TV and change channels for instance.

##Developing RoboHome-Web

###Requirements

1. Webserver with PHP 5.6 or greater with MySQL and a SSL/TLS cert [available for Free with Let's Encrypt](https://www.letsencrypt.org/). This is used to host the Bootstrap based website to add, delete, edit, and control devices from anywhere.
2. MQTT broker for pub-sub. I personally use [CloudMQTT](https://www.cloudmqtt.com/). This service is used to send messages a webservice and a microcontroller.
3. An account with [Amazon](https://www.amazon.com/) to be used for account registration using OAuth.
4. An account with [Login with Amazon](https://login.amazon.com/) to allow your website to use OAuth to verify users.  Tip, be sure to register both www and non-www versions of URLs for the "Allowed JavaScript Origins."

###Configuring

1. Open the `secrets.ini` file and populate the information needed to connect to your MQTT broker, MySQL database, and an Amazon login Client ID.

##Contributing

###How To Contribute

Contributions are always welcome!  Please open a PR with your code or feel free to make an issue.  All PRs will need to be reviewed and pass automated checks.  This repo supports the principles of [Bob Martin's Clean Code](http://www.goodreads.com/book/show/3735293-clean-code).

###Notes

- To avoid seeing the `secrets.ini` file (particulary when Git says you have unstaged changes during a rebase) in your repo and you don't want to ignore it, run `git update-index --assume-unchanged web/fatfree/app/secrets.ini`

*This is a new project and will be changing rapidly, more details will be provided when entering a beta state*
