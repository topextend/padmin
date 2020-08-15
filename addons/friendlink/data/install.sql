INSERT INTO `la_system_menu` VALUES (15, 1, '站务管理', '', '', '#', '', '_self', 0, 1, '2020-08-12 21:06:12');
INSERT INTO `la_system_menu` VALUES (16, 15, '友情链接', 'layui-icon layui-icon-link', '', 'addons/friendlink/index', '', '_self', 0, 1, '2020-08-12 21:06:39');
CREATE TABLE `la_friend_link`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '友情链接名称',
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '友情链接地址',
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '友情链接图标',
  `target` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '友情链接打开方式',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '友情链接描述',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '友情链接状态(1使用,0禁用)',
  `sort` bigint(20) NULL DEFAULT 0 COMMENT '排序权重',
  `create_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 0 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;