# 视频文件表
CREATE TABLE `videos`(
    `vid` INT,
    `video_name` VARCHAR(256) NOT NULL,
    `video_describe` TEXT,
    `video_origin_file` VARCHAR(256) DEFAULT "",
    `video_final_file` VARCHAR(256) DEFAULT "",
    `trans_title` VARCHAR(256) DEFAULT "",
    `trans_draft` VARCHAR(256) DEFAULT "",
    `trans_proof` VARCHAR(256) DEFAULT "",
    `trans_final` VARCHAR(256) DEFAULT "",
    `axis_draft` VARCHAR(256) DEFAULT "",
    `axis_proof` VARCHAR(256) DEFAULT "",
    `axis_final` VARCHAR(256) DEFAULT "",
    `cover_untranslated` VARCHAR(256) DEFAULT "",
    `cover_translated` VARCHAR(256) DEFAULT "",
    PRIMARY KEY(`vid`),
    KEY(`video_name`),
    KEY(`trans_title`)
)DEFAULT CHARSET="utf8mb4";