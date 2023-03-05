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

 Date: 05/03/2023 15:56:35
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
  INDEX `lft`(`lft`, `rgt`, `level`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of objects
-- ----------------------------
INSERT INTO `objects` VALUES (1, 'Родитель 1', 'описание', 0, 1, 1, 32);
INSERT INTO `objects` VALUES (2, 'Уровень 2.1', '', 1, 2, 2, 9);
INSERT INTO `objects` VALUES (3, 'Уровень 2.2', '', 1, 2, 10, 23);
INSERT INTO `objects` VALUES (4, 'Уровень 2.3', '', 1, 2, 24, 31);
INSERT INTO `objects` VALUES (5, 'Уровень 3.1', '', 2, 3, 3, 8);
INSERT INTO `objects` VALUES (6, 'Уровень 3.2', '', 3, 3, 11, 12);
INSERT INTO `objects` VALUES (7, 'Уровень 3.3', '', 3, 3, 13, 20);
INSERT INTO `objects` VALUES (8, 'Уровень 3.4', '', 3, 3, 21, 22);
INSERT INTO `objects` VALUES (9, 'Уровень 3.5', '', 4, 3, 25, 30);
INSERT INTO `objects` VALUES (10, 'Уровень 4.1', '', 5, 4, 4, 5);
INSERT INTO `objects` VALUES (11, 'Уровень 4.2', '', 5, 4, 6, 7);
INSERT INTO `objects` VALUES (12, 'Уровень 4.3', '', 7, 4, 14, 15);
INSERT INTO `objects` VALUES (13, 'Уровень 4.4', '', 7, 4, 16, 17);
INSERT INTO `objects` VALUES (14, 'Уровень 4.5', '', 7, 4, 18, 19);
INSERT INTO `objects` VALUES (15, 'Уровень 4.6', '', 9, 4, 26, 27);
INSERT INTO `objects` VALUES (16, 'Уровень 4.7', '', 9, 4, 28, 29);

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
