CREATE DATABASE IF NOT EXISTS `quiz-app`;
USE `quiz-app`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL
);

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL
);

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE `quizzes` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `created_by` INT(11) NOT NULL,
  `times_taken` INT(11) DEFAULT 0,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
);

DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `quiz_id` INT(11) NOT NULL,
  `question_text` TEXT NOT NULL,
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`)
);

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `question_id` INT(11) NOT NULL,
  `option_text` TEXT NOT NULL,
  `is_correct` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`)
);

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `quiz_id` INT(11) NOT NULL,
  `score` INT(11) DEFAULT 0,
  `taken_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`)
);

DROP TABLE IF EXISTS `chatrooms`;
CREATE TABLE `chatrooms` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `created_by` INT(11) NOT NULL,
  `user_count` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
);

DROP TABLE IF EXISTS `chatroom_users`;
CREATE TABLE `chatroom_users` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `chatroom_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  FOREIGN KEY (`chatroom_id`) REFERENCES `chatrooms`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
  `chatroom_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `message_text` TEXT NOT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`chatroom_id`) REFERENCES `chatrooms`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
