# Quizbaker-PHP
Port of quizbaker to PHP

The original quizbaker was created in classic ASP, using Microsoft SQL Server as a database. This port does not contain all the features yet that you can find in the development branch of quizbaker.
See https://github.com/blonkm/quizbaker

These are instructions to install the server side parts 

# Installation Instructions

## Introduction

For this project to work you will need 

*   Linux or Windows
*   Apache server
*   MySQL
You can use LAMP or WAMPServer

## Apache
*   Download and install apache, from rpm or download WAMP

## MySQL
*   Download and install mysql, from rpm on linux, on windows it is included in WAMP

## Settings
*   add a line to your hosts file (\windows\system32\drivers\etc\hosts or/etc/hosts) as follows
*   127.0.0.1 quizbaker

*   download all code from this project using a zip file or a clone and copy it under the site.
*   go into the file /DB/config.php and check the settings
*   test the site is up on whatever virtual host or localhost you put it: go to (e.g. http://quizbaker)

## create the MySQL database
*   Open The MySQL command line or a tool like PhpMyAdmin or MySQL Workbench.
*   Select the database server
*   download the sql script to create the database (src/DB/createDB.sql)
*   run the script 

## test a quiz
*   create a quiz using [Articulate Quizmaker](http://www.articulate.com/)
*   publish in WEB format
*   copy all files of this quiz to a folder under your site (e.g. a folder named 'quiz')
*   overwrite the quiz.html with the one in the source. You can also use the one in the examples folder
*   test the quiz (goto quiz.html in the browser)

