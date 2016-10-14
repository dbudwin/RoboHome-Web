[![Build Status](https://travis-ci.org/dbudwin/RoboHome.svg?branch=master)](https://travis-ci.org/dbudwin/RoboHome)
[![Code Climate](https://codeclimate.com/github/dbudwin/RoboHome/badges/gpa.svg)](https://codeclimate.com/github/dbudwin/RoboHome)

#RoboHome

##What Is RoboHome?

RoboHome is a SaaS tool that also integrates with Amazon's Echo to enable control of semi-connected devices (think IR, and RF) in your house over wifi! This is done using an MQTT pub-sub network to send messages between the website or Echo to a microcontroller like the NodeMCU which has various transmitters hooked up to it (like IR and RF transmitters) to send signals to these devices. This can be used to control RF outlets with lights plugged into them, or to turn on your TV and change channels for instance.

##Developing

###Requirements

1. Webserver with PHP and MySQL and a SSL/TLS cert [Available for Free with Let's Encrypt](https://www.letsencrypt.org/). This is used to hosted the Bootstrap based website to add, delete, edit, and control devices from anywhere.
2. MQTT broker for pub-sub. I personally use [CloudMQTT](https://www.cloudmqtt.com/). This service is used to send messages a webservice and a microcontroller.
3. A [NodeMCU](http://www.nodemcu.com/index_en.html) with the desired transmitters like a 433MHz RF transmitter. This hardware is needed to transmit signals to controllable devices.
4. An account with [Amazon](https://www.amazon.com/) to be used for account registration using OAuth.
5. An account with [Login with Amazon](https://login.amazon.com/) to allow your website to use OAuth to verify users.
6. Optional for using the Echo Smart Home skill: An Amazon Echo and a developer account with AWS. Integration with the Echo is a big reason for the dependency on Amazon services, Smart Home skills require an account and since most Echo owners already have an Amazon account, it was a natural service to use.

###Configuring

1. Open the `secrets.ini` file and populate the information needed to connect to your MQTT broker, MySQL database, and an Amazon login token.
2. [Flash your NodeMCU](https://nodemcu.readthedocs.io/en/dev/en/flash/) with an image from https://www.nodemcu-build.com/
  1. Make sure you select the following modules: GPIO, HTTP, MQTT, file, net, node, timer, UART, wifi

###Notes

- To avoid seeing the `secrets.ini` file (particulary when Git says you have unstaged changes during a rebase) in your repo and you don't want to ignore it, run `git update-index --assume-unchanged web/fatfree/app/secrets.ini`

*This is a new project and will be changing rapidly, more details will be provided when entering a beta state*
