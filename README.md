# tasker
Task manager project I've been working on using Phalcon PHP.

Uses Launchkey oauth for logins.

## API Keys Needed
You will need to sign up for an API Key with Launchkey. The client id and secret from Launchkey need to go into the
authenticators table in addition to the redirect url, 'userauths/login', and the path to the Launchkey public key.
I had it in app/files/launchkey.key


## Database Tables Needed

 CREATE TABLE `users` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `date_created` date NOT NULL,
   `last_login` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `first_name` varchar(50) COLLATE utf16_bin NOT NULL,
   `last_name` varchar(50) COLLATE utf16_bin NOT NULL,
   `email` varchar(100) COLLATE utf16_bin NOT NULL,
   `verified` tinyint(4) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

 CREATE TABLE `user_auths` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) NOT NULL DEFAULT '0',
   `authenticator_id` int(11) NOT NULL DEFAULT '0',
   `user_token` varchar(100) COLLATE utf16_bin DEFAULT '0',
   `refresh_token` varchar(100) COLLATE utf16_bin DEFAULT '0',
   `access_token` varchar(100) COLLATE utf16_bin DEFAULT NULL,
   `expires` datetime DEFAULT NULL,
   `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`),
   KEY `authenticator_id` (`authenticator_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;


 CREATE TABLE `tasks` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) NOT NULL DEFAULT '0',
   `project_id` int(11) DEFAULT '0',
   `task` varchar(1000) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `priority` varchar(25) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `due_date` date DEFAULT NULL,
   `due_time` time DEFAULT NULL,
   `date_completed` timestamp NULL DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

 CREATE TABLE `projects` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) NOT NULL,
   `name` varchar(255) COLLATE utf16_bin NOT NULL,
   `due_date` date DEFAULT NULL,
   `date_completed` timestamp NULL DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

 CREATE TABLE `authenticators` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(50) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `client_key` varchar(200) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `secret_key` varchar(200) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `redirect_url` varchar(100) COLLATE utf16_bin NOT NULL DEFAULT '0',
   `private_key` varchar(100) COLLATE utf16_bin DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;



