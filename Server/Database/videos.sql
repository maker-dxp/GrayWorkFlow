# 视频文件表
DROP TABLE IF EXISTS `video_files`;
CREATE TABLE `video_files`(
    `video_id`              INT AUTO_INCREMENT,
    `video_origin_file`     VARCHAR(256) DEFAULT "",
    `video_final_file`      VARCHAR(256) DEFAULT "",
    `trans_title`           VARCHAR(256) DEFAULT "",
    `trans_draft`           VARCHAR(256) DEFAULT "",
    `trans_proof`           VARCHAR(256) DEFAULT "",
    `trans_final`           VARCHAR(256) DEFAULT "",
    `axis_draft`            VARCHAR(256) DEFAULT "",
    `axis_proof`            VARCHAR(256) DEFAULT "",
    `axis_final`            VARCHAR(256) DEFAULT "",
    `cover_untranslated`    VARCHAR(256) DEFAULT "",
    `cover_translated`      VARCHAR(256) DEFAULT "",
    PRIMARY KEY(`video_id`)
)DEFAULT CHARSET="utf8mb4";