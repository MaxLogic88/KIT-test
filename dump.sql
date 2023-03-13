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

 Date: 14/03/2023 00:59:33
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
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of objects
-- ----------------------------
INSERT INTO `objects` VALUES (1, 'Родитель 1', 'описание', 0);
INSERT INTO `objects` VALUES (2, 'Уровень 2.1', '', 1);
INSERT INTO `objects` VALUES (3, 'Уровень 2.2', '', 1);
INSERT INTO `objects` VALUES (4, 'Уровень 2.3', '', 1);
INSERT INTO `objects` VALUES (5, 'Уровень 3.1', '', 2);
INSERT INTO `objects` VALUES (6, 'Уровень 3.2', '', 3);
INSERT INTO `objects` VALUES (7, 'Уровень 3.3', '', 3);
INSERT INTO `objects` VALUES (8, 'Уровень 3.4', '', 3);
INSERT INTO `objects` VALUES (9, 'Уровень 3.5', '', 4);
INSERT INTO `objects` VALUES (10, 'Уровень 4.1', '', 5);
INSERT INTO `objects` VALUES (11, 'Уровень 4.2', '', 5);
INSERT INTO `objects` VALUES (12, 'Уровень 4.3', '', 7);
INSERT INTO `objects` VALUES (13, 'Уровень 4.4', '', 7);
INSERT INTO `objects` VALUES (14, 'Уровень 4.5', '', 7);
INSERT INTO `objects` VALUES (15, 'Уровень 4.6', '', 9);
INSERT INTO `objects` VALUES (16, 'Уровень 4.7', '', 9);

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
