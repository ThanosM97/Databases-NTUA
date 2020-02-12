# Database Systems: HotelFinder website
This repository hosts the lab exercise for the Database Systems course of ECE-NTUA.

## About the project
The goal of this project was to design a website that will work as a search engine for hotel rooms. Via this website, the user is able to search for hotel rooms using multiple filters, see the available rooms for his search and even book the room without redirecting to the hotel's website. 

## Contributors
- [Antoniadis Panagiotis](https://github.com/PanosAntoniadis)
- [Bazotis Nikolaos](https://github.com/Nick-Buzz)
- [Masouris Athanasios](https://github.com/ThanosM97)

## Project Structure
- `DDLs/` dumped database files
- `Relational Model/` relational model graphs for the database
- `src/` source code for the website
  - `images/` png files used in the website
  - `scripts/` javascript files 
  - `styles/` css files
  - php files
- `ProjectReport.pdf` the project report written in greek
- `Website Presentation.pdf` a thorough presentation of the website's features
- `Sql.pdf` the sql queries used for the website's functions

## Setup Instructions
1. Download and install MySQL and MySQL server from the official [MySQL website](https://www.mysql.com/).
2. Download and install Apache2 from the official [APACHE website](https://httpd.apache.org/).
3. Download and install phpMyAdmin from the official [phpMyAdmin website](https://www.phpmyadmin.net/).
4. Hit `localhost/phpmyadmin`  via a browser of your choice and login using the credentials you entered during the above installation.
5. Import the [sql files](DDLs/).
6. Copy all the files in the [src](src/) directory to the following destination: `/var/www/html`
7. You can visit our website using the following url: `localhost/HotelFinder.php`

For further information please check [`Installation Instructions.pdf`](https://github.com/ThanosM97/Databases-NTUA/blob/master/Installation%20Instructions.pdf)


## Technologies Used
- Back-end
  - MySQL
- Front-end
  - PHP
  - Javascript
