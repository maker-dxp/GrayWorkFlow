#配置表
CREATE TABLE `options`(
    `key` VARCHAR(128) NOT NULL,
    `value` TEXT,
    PRIMARY KEY(`key`)
)DEFAULT CHARSET="utf8mb4";