# mailerlite-backend
Mailerlite Application Test Tasks

# Guide
1. Clone this repo.
2. In the src folder rename .env.example to .env and put the following credentials:
     ```
    DB_HOST=aflores-api-db
    DB_USER=root
    DB_PASS=mailer
    DB_DATABASE=mailerlite
    ```
4. Open a terminal and CD to docker directory and do ``` docker-compose -p mailer-backend up ```.
5. Create a database called ``` mailerlite ``` by doing;
    ```
    docker exec -it aflores-api-db bash
    mysql -u root -p mailer
    create database mailerlite;

     CREATE TABLE `subscribers` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(255) DEFAULT NULL,
    `name` varchar(255) DEFAULT NULL,
    `last_name` varchar(255) DEFAULT NULL,
    `status` tinyint DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `email` (`email`)
    ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
    ```
7. Access the app container by using ```docker exec -it aflores-php-api bash``` and do ``` composer install ```.
8. Access the backend via ```localhost:8000```

# Other Information
1. Test is located at ```src/tests```.
2. Available Endpoints are:
   ```
   /subscriber             GET   Returns All Subscribers
   /subscriber             POST  Save Subscriber
   /subscriber/find/id=?   GET   Find a subscriber by id
   ```
4. Redis has been implemented as a MySQL Cache and can be found at ```src/app/Models/Subscriber.php```
