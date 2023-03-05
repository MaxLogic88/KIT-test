/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50739 (5.7.39)
 Source Host           : localhost:3306
 Source Schema         : kit-test

 Target Server Type    : MySQL
 Target Server Version : 50739 (5.7.39)
 File Encoding         : 65001

 Date: 03/03/2023 15:09:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for objects
-- ----------------------------
DROP TABLE IF EXISTS `objects`;
CREATE TABLE `objects`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `parent_id` int(11) NULL DEFAULT 0,
  `level` int(11) NULL DEFAULT 1,
  `lft` int(11) NULL DEFAULT 0,
  `rgt` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_left_right`(`lft`, `rgt`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of objects
-- ----------------------------
INSERT INTO `objects` VALUES (1, 'Объект 1', 'Объект 1', 0, 1, 0, 0);
INSERT INTO `objects` VALUES (2, 'Объект 2', 'Объект 2', 0, 1, 0, 0);
INSERT INTO `objects` VALUES (3, 'Объект 1.1', 'Описание 1.1', 1, 2, 0, 0);
INSERT INTO `objects` VALUES (4, 'Объект 1.2', 'Описание 1.2', 1, 2, 0, 0);
INSERT INTO `objects` VALUES (5, 'Другой объект 1.1.1', 'объект 1.1.1', 3, 3, 0, 0);
INSERT INTO `objects` VALUES (6, 'Объект 3', 'Объект 3', 0, 1, 0, 0);
INSERT INTO `objects` VALUES (7, 'Объект 3.1', 'Объект 3.1', 6, 2, 0, 0);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'admin', '$2y$10$O4A6tlk894BxeXy9vzF5buux46Opor6Pvwg13Lpz4pqM8FP3x3ZVG');

SET FOREIGN_KEY_CHECKS = 1;
