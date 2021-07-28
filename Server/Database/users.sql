# 用户表
CREATE TABLE `users`(
    `uid` INT AUTO_INCREMENT,
    `user_name` VARCHAR(64) DEFAULT "",
    `user_password` VARCHAR(128) DEFAULT "",
    `display_name` VARCHAR(64) DEFAULT "",
    `avatar` VARCHAR(256) DEFAULT "",
    `jobs` VARCHAR(1024) DEFAULT "",
    `point` INT DEFAULT 0,
    `create_time` DATE,
    `last_time` TIMESTAMP,
    PRIMARY KEY(`uid`),
    UNIQUE KEY(`user_name`)
)DEFAULT CHARSET="utf8mb4";

INSERT INTO `users`
    VALUE(
          0,
          "nobody",
          "",
          "用户已注销",
          "",
          "a:0:{}",
          0,
          "2000-01-01",
          "2000-01-01"
    );

INSERT INTO `users`
VALUE(
     1,
     "su",
     "b4dce90b1ceadd4616c6581c7d0fbf5579ce7a8e9620062ca78817493bf2219f",
     "超级管理员",
     "",
     "a:2:{i:0;s:2:\"su\";i:1;s:5:\"admin\";}",
     0,
     "2021-07-23",
     "2021-07-23"
    );