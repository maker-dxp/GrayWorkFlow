# 任务表
DROP TABLE IF EXISTS`tasks`;
CREATE TABLE `tasks`(
  `task_id` INT AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `proj_id` INT NOT NULL,
  `type`    INT NOT NULL,
  `status`  INT DEFAULT 0,
  `level`   INT DEFAULT 0,
  PRIMARY KEY(`task_id`)
);