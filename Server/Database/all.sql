#配置表
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
               `key` varchar(128) NOT NULL,
               `value` text,
               PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

#视频文件表
DROP TABLE IF EXISTS `video_files`;
CREATE TABLE `video_files` (
           `video_id` int NOT NULL AUTO_INCREMENT,
           `video_origin_file` varchar(256) DEFAULT '',
           `video_final_file` varchar(256) DEFAULT '',
           `trans_title` varchar(256) DEFAULT '',
           `trans_draft` varchar(256) DEFAULT '',
           `trans_proof` varchar(256) DEFAULT '',
           `trans_final` varchar(256) DEFAULT '',
           `axis_draft` varchar(256) DEFAULT '',
           `axis_proof` varchar(256) DEFAULT '',
           `axis_final` varchar(256) DEFAULT '',
           `cover_untranslated` varchar(256) DEFAULT '',
           `cover_translated` varchar(256) DEFAULT '',
           PRIMARY KEY (`video_id`),
           KEY (`trans_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

#项目表
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
            `project_id` int NOT NULL AUTO_INCREMENT,
            `video_id` int NOT NULL,
            `video_name` VARCHAR(256) NOT NULL,
            `video_describe` TEXT,
            `create_user` int DEFAULT '0',
            `create_time` timestamp NULL DEFAULT NULL,
            `status` int DEFAULT '0',
            PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

#任务表
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
             `task_id` int NOT NULL AUTO_INCREMENT,
             `user_id` int NOT NULL,
             `proj_id` int NOT NULL,
             `type` int NOT NULL,
             `status` int DEFAULT '0',
             `level` int DEFAULT '0',
             PRIMARY KEY (`task_id`)
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

UPDATE `users` SET `uid` = 0 WHERE `uid` = 1;
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