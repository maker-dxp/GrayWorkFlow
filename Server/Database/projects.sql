# 项目表
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`(
    `project_id`        INT AUTO_INCREMENT,
    `video_file`        INT NOT NULL,
    `video_name`        VARCHAR(256) NOT NULL,
    `video_describe`    TEXT,
    `create_user`       INT DEFAULT 0,
    `create_time`       TIMESTAMP,
    `status`            INT DEFAULT 0,
    PRIMARY KEY(`project_id`),
    UNIQUE KEY(`video_file`)
)DEFAULT CHARSET="utf8mb4";