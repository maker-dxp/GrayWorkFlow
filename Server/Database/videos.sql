# 视频文件表
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