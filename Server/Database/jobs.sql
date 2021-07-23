# 工作表
CREATE TABLE `tasks`(
    `tid` INT,
    `video` INT DEFAULT 0,
    `video_upload_user` INT DEFAULT 0,
    `translate_status` VARCHAR(32) DEFAULT "",
    `trans_maker_user` INT DEFAULT 0,
    `trans_proof_user` INT DEFAULT 0,
    `axis_status` VARCHAR(32) DEFAULT "",
    `axis_maker_user` INT DEFAULT 0,
    `axis_proof_user` INT DEFAULT 0,
    `cover_status` VARCHAR(32) DEFAULT "",
    `cover_trans_user` INT DEFAULT 0,
    `cover_proof_user` INT DEFAULT 0,
    `video_status` VARCHAR(32) DEFAULT "",
    `effect_maker_user` INT DEFAULT 0,
    `video_maker_user` INT DEFAULT 0,
    PRIMARY KEY(`tid`),
    KEY(`video`)
)DEFAULT CHARSET="utf8mb4";