CREATE TABLE `admin_group`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name`        varchar(100)     NOT NULL DEFAULT '',
    `create_time` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `name` (`name`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8mb4 COMMENT ='权限分组';

INSERT INTO `admin_group`
VALUES (7, '系统管理员', '2019-05-28 11:21:03', '2019-05-28 11:21:03');

CREATE TABLE `admin_menu`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id`   int(10) unsigned NOT NULL DEFAULT '1',
    `name`        varchar(100)     NOT NULL DEFAULT '',
    `url`         varchar(255)     NOT NULL DEFAULT '',
    `icon`        varchar(30)      NOT NULL DEFAULT '',
    `description` varchar(255)     NOT NULL DEFAULT '',
    `sort`        int(10) unsigned NOT NULL DEFAULT '0',
    `create_time` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 10
  DEFAULT CHARSET = utf8mb4 COMMENT ='菜单';

INSERT INTO `admin_menu`
VALUES (1, 0, '系统设置', '', 'md-settings', '', 0, '2019-05-27 17:36:57', '2019-05-27 17:36:57');
INSERT INTO `admin_menu`
VALUES (2, 1, '菜单管理', '/system/menu', 'md-menu', '', 3, '2019-05-27 17:36:57', '2019-05-27 17:36:57');
INSERT INTO `admin_menu`
VALUES (3, 1, '管理员设置', '/system/user', 'ios-people', '', 0, '2019-05-27 17:36:57', '2019-05-27 17:36:57');
INSERT INTO `admin_menu`
VALUES (4, 1, '请求管理', '/system/request', 'ios-list', '', 1, '2019-05-27 17:36:57', '2019-05-27 17:36:57');
INSERT INTO `admin_menu`
VALUES (7, 1, '权限管理', '/system/authorize', 'ios-lock-outline', '', 2, '2019-05-27 17:36:57', '2019-05-27 17:36:57');

CREATE TABLE `admin_menu_group`
(
    `id`             int(10) unsigned NOT NULL AUTO_INCREMENT,
    `admin_group_id` int(10) unsigned NOT NULL,
    `admin_menu_id`  int(10) unsigned NOT NULL,
    `create_time`    timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time`    timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `unique` (`admin_group_id`, `admin_menu_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 12
  DEFAULT CHARSET = utf8mb4 COMMENT ='菜单分组关联';

INSERT INTO `admin_menu_group`
VALUES (7, 7, 1, '2019-05-28 11:26:38', '2019-05-28 11:26:38');
INSERT INTO `admin_menu_group`
VALUES (8, 7, 3, '2019-05-28 11:26:38', '2019-05-28 11:26:38');
INSERT INTO `admin_menu_group`
VALUES (9, 7, 4, '2019-05-28 11:26:38', '2019-05-28 11:26:38');
INSERT INTO `admin_menu_group`
VALUES (10, 7, 7, '2019-05-28 11:26:38', '2019-05-28 11:26:38');
INSERT INTO `admin_menu_group`
VALUES (11, 7, 2, '2019-05-28 11:26:38', '2019-05-28 11:26:38');

CREATE TABLE `admin_request`
(
    `id`          int(10) unsigned    NOT NULL AUTO_INCREMENT,
    `type`        tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '请求类型',
    `name`        varchar(100)        NOT NULL DEFAULT '',
    `action`      varchar(100)        NOT NULL DEFAULT '',
    `call`        varchar(100)        NOT NULL DEFAULT '',
    `create_time` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `action` (`action`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 32
  DEFAULT CHARSET = utf8mb4 COMMENT ='请求';

INSERT INTO `admin_request`
VALUES (1, 1, '登录', '/login', '\\Baiy\\Admin\\System\\Index::login', '2019-05-27 17:36:57', '2019-05-27 17:36:57');
INSERT INTO `admin_request`
VALUES (2, 1, '退出', '/logout', '\\Baiy\\Admin\\System\\Index::logout', '2019-05-27 17:36:57', '2019-05-27 18:28:43');
INSERT INTO `admin_request`
VALUES (3, 1, '初始数据加载', '/load', '\\Baiy\\Admin\\System\\Index::load', '2019-05-27 17:36:58', '2019-05-27 18:28:47');
INSERT INTO `admin_request`
VALUES (5, 1, '菜单管理-列表数据', '/system/menu/lists', '\\Baiy\\Admin\\System\\Menu::lists', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (6, 1, '管理员设置-列表数据', '/system/user/lists', '\\Baiy\\Admin\\System\\User::lists', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (8, 1, '请求管理-请求保存', '/system/request/save', '\\Baiy\\Admin\\System\\Request::save', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (9, 1, '请求管理-请求删除', '/system/request/remove', '\\Baiy\\Admin\\System\\Request::remove', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (10, 1, '请求管理-列表数据', '/system/request/lists', '\\Baiy\\Admin\\System\\Request::lists', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (11, 1, '管理员设置-用户保存', '/system/user/save', '\\Baiy\\Admin\\System\\User::save', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (12, 1, '管理员设置-用户删除', '/system/user/remove', '\\Baiy\\Admin\\System\\User::remove', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (13, 1, '菜单管理-排序', '/system/menu/sort', '\\Baiy\\Admin\\System\\Menu::sort', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (14, 1, '菜单管理-菜单保存', '/system/menu/save', '\\Baiy\\Admin\\System\\Menu::save', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (15, 1, '菜单管理-菜单删除', '/system/menu/remove', '\\Baiy\\Admin\\System\\Menu::remove', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (16, 1, '权限管理-列表数据', '/system/authorize/lists', '\\Baiy\\Admin\\System\\Authorize::lists', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (17, 1, '权限管理-分组保存', '/system/authorize/save', '\\Baiy\\Admin\\System\\Authorize::save', '2019-05-27 17:36:58',
        '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (18, 1, '权限管理-分组删除', '/system/authorize/remove', '\\Baiy\\Admin\\System\\Authorize::remove',
        '2019-05-27 17:36:58', '2019-05-27 17:36:58');
INSERT INTO `admin_request`
VALUES (19, 1, '权限管理-获取未分配请求', '/system/authorize/getRequestAssign',
        '\\Baiy\\Admin\\System\\Authorize::getRequestAssign', '2019-05-27 17:36:58', '2019-05-28 11:06:45');
INSERT INTO `admin_request`
VALUES (20, 1, '权限管理-分配请求', '/system/authorize/assignRequest', '\\Baiy\\Admin\\System\\Authorize::assignRequest',
        '2019-05-27 17:36:58', '2019-05-28 11:24:25');
INSERT INTO `admin_request`
VALUES (21, 1, '权限管理-移除分配请求', '/system/authorize/removeRequest', '\\Baiy\\Admin\\System\\Authorize::removeRequest',
        '2019-05-27 17:36:58', '2019-05-28 11:06:34');
INSERT INTO `admin_request`
VALUES (22, 1, '权限管理-获取用户分配', '/system/authorize/getUserAssign', '\\Baiy\\Admin\\System\\Authorize::getUserAssign',
        '2019-05-27 17:36:58', '2019-05-28 11:22:01');
INSERT INTO `admin_request`
VALUES (23, 1, '权限管理-分配用户', '/system/authorize/assignUser', '\\Baiy\\Admin\\System\\Authorize::assignUser',
        '2019-05-27 17:36:58', '2019-05-28 11:07:07');
INSERT INTO `admin_request`
VALUES (30, 1, '权限管理-获取菜单分配', '/system/authorize/getMenuAssign', '\\Baiy\\Admin\\System\\Authorize::getMenuAssign',
        '2019-05-28 11:23:26', '2019-05-28 11:23:26');
INSERT INTO `admin_request`
VALUES (31, 1, '权限管理-分配菜单', '/system/authorize/assignMenu', '\\Baiy\\Admin\\System\\Authorize::assignMenu',
        '2019-05-28 11:23:38', '2019-05-28 11:23:38');

CREATE TABLE `admin_request_group`
(
    `id`               int(10) unsigned NOT NULL AUTO_INCREMENT,
    `admin_group_id`   int(10) unsigned NOT NULL,
    `admin_request_id` int(10) unsigned NOT NULL,
    `create_time`      timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time`      timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `unique` (`admin_group_id`, `admin_request_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 49
  DEFAULT CHARSET = utf8mb4 COMMENT ='请求分组关联';

INSERT INTO `admin_request_group`
VALUES (29, 7, 31, '2019-05-28 11:30:09', '2019-05-28 11:30:09');
INSERT INTO `admin_request_group`
VALUES (30, 7, 30, '2019-05-28 11:30:10', '2019-05-28 11:30:10');
INSERT INTO `admin_request_group`
VALUES (31, 7, 23, '2019-05-28 11:30:11', '2019-05-28 11:30:11');
INSERT INTO `admin_request_group`
VALUES (32, 7, 22, '2019-05-28 11:30:12', '2019-05-28 11:30:12');
INSERT INTO `admin_request_group`
VALUES (33, 7, 21, '2019-05-28 11:30:13', '2019-05-28 11:30:13');
INSERT INTO `admin_request_group`
VALUES (34, 7, 20, '2019-05-28 11:30:15', '2019-05-28 11:30:15');
INSERT INTO `admin_request_group`
VALUES (35, 7, 19, '2019-05-28 11:30:16', '2019-05-28 11:30:16');
INSERT INTO `admin_request_group`
VALUES (36, 7, 18, '2019-05-28 11:30:17', '2019-05-28 11:30:17');
INSERT INTO `admin_request_group`
VALUES (37, 7, 17, '2019-05-28 11:30:19', '2019-05-28 11:30:19');
INSERT INTO `admin_request_group`
VALUES (38, 7, 16, '2019-05-28 11:30:20', '2019-05-28 11:30:20');
INSERT INTO `admin_request_group`
VALUES (39, 7, 15, '2019-05-28 11:30:21', '2019-05-28 11:30:21');
INSERT INTO `admin_request_group`
VALUES (40, 7, 14, '2019-05-28 11:30:22', '2019-05-28 11:30:22');
INSERT INTO `admin_request_group`
VALUES (41, 7, 13, '2019-05-28 11:30:24', '2019-05-28 11:30:24');
INSERT INTO `admin_request_group`
VALUES (42, 7, 12, '2019-05-28 11:30:25', '2019-05-28 11:30:25');
INSERT INTO `admin_request_group`
VALUES (43, 7, 11, '2019-05-28 11:30:26', '2019-05-28 11:30:26');
INSERT INTO `admin_request_group`
VALUES (44, 7, 10, '2019-05-28 11:30:27', '2019-05-28 11:30:27');
INSERT INTO `admin_request_group`
VALUES (45, 7, 9, '2019-05-28 11:30:28', '2019-05-28 11:30:28');
INSERT INTO `admin_request_group`
VALUES (46, 7, 8, '2019-05-28 11:30:29', '2019-05-28 11:30:29');
INSERT INTO `admin_request_group`
VALUES (47, 7, 6, '2019-05-28 11:30:31', '2019-05-28 11:30:31');
INSERT INTO `admin_request_group`
VALUES (48, 7, 5, '2019-05-28 11:30:32', '2019-05-28 11:30:32');

CREATE TABLE `admin_token`
(
    `id`            bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `token`         varchar(32)         NOT NULL DEFAULT '',
    `admin_user_id` int(10) unsigned    NOT NULL,
    `expire_time`   timestamp           NULL     DEFAULT NULL,
    `create_time`   timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `token` (`token`) USING BTREE,
    KEY `expire_time` (`expire_time`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8mb4 COMMENT ='登录token';


CREATE TABLE `admin_user`
(
    `id`              int(10) unsigned    NOT NULL AUTO_INCREMENT,
    `username`        varchar(100)        NOT NULL DEFAULT '',
    `password`        varchar(255)        NOT NULL DEFAULT '',
    `last_login_ip`   varchar(15)         NOT NULL DEFAULT '',
    `last_login_time` timestamp           NULL     DEFAULT NULL,
    `status`          tinyint(1) unsigned NOT NULL DEFAULT '1',
    `create_time`     timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time`     timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8mb4 COMMENT ='管理员用户';

INSERT INTO `admin_user`
VALUES (1, 'admin', '$2y$10$Sv4GiiGqma8Xz4DSgallvOq//M0PZxG.qSRZMmz/PRyflXhStQNLO', '172.18.0.1', '2019-05-28 14:32:08',
        1, '2019-05-27 17:36:58', '2019-05-28 14:32:08');

CREATE TABLE `admin_user_group`
(
    `id`             int(10) unsigned NOT NULL AUTO_INCREMENT,
    `admin_group_id` int(10) unsigned NOT NULL,
    `admin_user_id`  int(10) unsigned NOT NULL,
    `create_time`    timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time`    timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `unique` (`admin_group_id`, `admin_user_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8mb4 COMMENT ='用户分组关联';

INSERT INTO `admin_user_group`
VALUES (7, 7, 1, '2019-05-28 11:30:00', '2019-05-28 11:30:00');