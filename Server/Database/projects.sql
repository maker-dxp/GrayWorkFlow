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