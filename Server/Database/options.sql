#配置表
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
    `key` varchar(128) NOT NULL,
    `value` text,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
