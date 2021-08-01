# 用户表
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
         `user_id` int NOT NULL AUTO_INCREMENT,
         `user_name` varchar(64) DEFAULT '',
         `user_password` varchar(128) DEFAULT '',
         `nick_name` varchar(64) DEFAULT '',
         `avatar` varchar(256) DEFAULT '',
         `authority` int DEFAULT '0',
         `qq` varchar(16) DEFAULT '',
         `point` int DEFAULT '0',
         `create_time` timestamp NULL DEFAULT NULL,
         `last_time` timestamp NULL DEFAULT NULL,
         PRIMARY KEY (`user_id`),
         UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users`
    VALUE(
          0,
          "nobody",
          "",
          "用户已注销",
          "",
          0,
          "",
          0,
          "2000-01-01",
          "2000-01-01"
    );

UPDATE `users` SET `user_id` = 0 WHERE `user_id` = 1;
#AUTO_INCREMENT会把uid变成1,故重新更新为0

INSERT INTO `users`
VALUE(
     1,
     "su",
     "b4dce90b1ceadd4616c6581c7d0fbf5579ce7a8e9620062ca78817493bf2219f",
     "超级管理员",
     "",
     0,
     "",
     0,
     "2021-07-23",
     "2021-07-23"
    );