-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: yeni123_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,NULL,'create_user',' kullanıcı oluşturdu.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/install','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"role\":\"admin\",\"status\":\"active\"}','2026-05-28 21:45:49','2026-05-28 21:45:49'),(2,1,'login','deneme123 sisteme giriş yaptı.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/install','[]','2026-05-28 21:45:50','2026-05-28 21:45:50'),(3,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:47:39','2026-05-28 21:47:39'),(4,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:47:39','2026-05-28 21:47:39'),(5,1,'edit_user','deneme123 kullanıcıyı düzenledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"edited_user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"role\":\"admin\",\"is_active\":true}','2026-05-28 21:47:39','2026-05-28 21:47:39'),(6,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:47:40','2026-05-28 21:47:40'),(7,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:47:43','2026-05-28 21:47:43'),(8,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:48:08','2026-05-28 21:48:08'),(9,1,'login','deneme123 sisteme giriş yaptı.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','[]','2026-05-28 21:48:08','2026-05-28 21:48:08'),(10,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:48:24','2026-05-28 21:48:24'),(11,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:48:45','2026-05-28 21:48:45'),(12,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:48:48','2026-05-28 21:48:48'),(13,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:48:49','2026-05-28 21:48:49'),(14,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/push/config','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:49:00','2026-05-28 21:49:00'),(15,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:49:11','2026-05-28 21:49:11'),(16,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:49:43','2026-05-28 21:49:43'),(17,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:49:49','2026-05-28 21:49:49'),(18,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:09','2026-05-28 21:50:09'),(19,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:12','2026-05-28 21:50:12'),(20,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:36','2026-05-28 21:50:36'),(21,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/upload-file?expires=1780005338&signature=2028f6a6c27f9df03669b682470f470452160397e3975432c6fa1f86fb164a67','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:41','2026-05-28 21:50:41'),(22,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:42','2026-05-28 21:50:42'),(23,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:51','2026-05-28 21:50:51'),(24,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:50:55','2026-05-28 21:50:55'),(25,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:03','2026-05-28 21:51:03'),(26,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:04','2026-05-28 21:51:04'),(27,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:05','2026-05-28 21:51:05'),(28,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:08','2026-05-28 21:51:08'),(29,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:09','2026-05-28 21:51:09'),(30,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:10','2026-05-28 21:51:10'),(31,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:12','2026-05-28 21:51:12'),(32,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:41','2026-05-28 21:51:41'),(33,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:44','2026-05-28 21:51:44'),(34,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:45','2026-05-28 21:51:45'),(35,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:46','2026-05-28 21:51:46'),(36,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:47','2026-05-28 21:51:47'),(37,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:50','2026-05-28 21:51:50'),(38,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:53','2026-05-28 21:51:53'),(39,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:51:54','2026-05-28 21:51:54'),(40,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:19','2026-05-28 21:52:19'),(41,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:20','2026-05-28 21:52:20'),(42,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:27','2026-05-28 21:52:27'),(43,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:47','2026-05-28 21:52:47'),(44,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:54','2026-05-28 21:52:54'),(45,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:55','2026-05-28 21:52:55'),(46,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:58','2026-05-28 21:52:58'),(47,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:52:59','2026-05-28 21:52:59'),(48,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:00','2026-05-28 21:53:00'),(49,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:01','2026-05-28 21:53:01'),(50,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:03','2026-05-28 21:53:03'),(51,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:28','2026-05-28 21:53:28'),(52,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:46','2026-05-28 21:53:46'),(53,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:47','2026-05-28 21:53:47'),(54,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:50','2026-05-28 21:53:50'),(55,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:51','2026-05-28 21:53:51'),(56,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:52','2026-05-28 21:53:52'),(57,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:53','2026-05-28 21:53:53'),(58,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:54','2026-05-28 21:53:54'),(59,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:53:59','2026-05-28 21:53:59'),(60,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:00','2026-05-28 21:54:00'),(61,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:07','2026-05-28 21:54:07'),(62,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:08','2026-05-28 21:54:08'),(63,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:09','2026-05-28 21:54:09'),(64,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:19','2026-05-28 21:54:19'),(65,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:20','2026-05-28 21:54:20'),(66,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:21','2026-05-28 21:54:21'),(67,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:22','2026-05-28 21:54:22'),(68,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:25','2026-05-28 21:54:25'),(69,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:35','2026-05-28 21:54:35'),(70,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:37','2026-05-28 21:54:37'),(71,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:38','2026-05-28 21:54:38'),(72,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:40','2026-05-28 21:54:40'),(73,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:41','2026-05-28 21:54:41'),(74,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:42','2026-05-28 21:54:42'),(75,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:45','2026-05-28 21:54:45'),(76,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:51','2026-05-28 21:54:51'),(77,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:54:58','2026-05-28 21:54:58'),(78,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:04','2026-05-28 21:55:04'),(79,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:07','2026-05-28 21:55:07'),(80,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:08','2026-05-28 21:55:08'),(81,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:09','2026-05-28 21:55:09'),(82,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:10','2026-05-28 21:55:10'),(83,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:12','2026-05-28 21:55:12'),(84,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:21','2026-05-28 21:55:21'),(85,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:22','2026-05-28 21:55:22'),(86,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:31','2026-05-28 21:55:31'),(87,1,'edit_site_setting','deneme123 site ayarlarını güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"site_name\":\"ArgnestCMS\",\"seo_title\":\"Argnest Haber-\\u0130lan Portal CMS\",\"maintenance_mode\":false,\"maintenance_ends_at\":null}','2026-05-28 21:55:31','2026-05-28 21:55:31'),(88,1,'maintenance_mode_disabled','deneme123 bakım modunu pasif etti.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"maintenance_mode\":false}','2026-05-28 21:55:31','2026-05-28 21:55:31'),(89,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:32','2026-05-28 21:55:32'),(90,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:34','2026-05-28 21:55:34'),(91,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:35','2026-05-28 21:55:35'),(92,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:37','2026-05-28 21:55:37'),(93,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:38','2026-05-28 21:55:38'),(94,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:41','2026-05-28 21:55:41'),(95,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:42','2026-05-28 21:55:42'),(96,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:43','2026-05-28 21:55:43'),(97,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:44','2026-05-28 21:55:44'),(98,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:57','2026-05-28 21:55:57'),(99,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:58','2026-05-28 21:55:58'),(100,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:55:59','2026-05-28 21:55:59'),(101,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:41','2026-05-28 21:56:41'),(102,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:44','2026-05-28 21:56:44'),(103,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:48','2026-05-28 21:56:48'),(104,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:53','2026-05-28 21:56:53'),(105,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:54','2026-05-28 21:56:54'),(106,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:55','2026-05-28 21:56:55'),(107,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:57','2026-05-28 21:56:57'),(108,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:56:58','2026-05-28 21:56:58'),(109,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:57:11','2026-05-28 21:57:11'),(110,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:57:15','2026-05-28 21:57:15'),(111,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:03','2026-05-28 21:58:03'),(112,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:05','2026-05-28 21:58:05'),(113,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/upload-file?expires=1780005785&signature=3c153d7f4aa2d3906d1b517b1f4fc60f1507294b192237deba31d3ec504a2243','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:06','2026-05-28 21:58:06'),(114,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:07','2026-05-28 21:58:07'),(115,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:29','2026-05-28 21:58:29'),(116,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:37','2026-05-28 21:58:37'),(117,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:54','2026-05-28 21:58:54'),(118,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:55','2026-05-28 21:58:55'),(119,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:56','2026-05-28 21:58:56'),(120,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:58:59','2026-05-28 21:58:59'),(121,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:00','2026-05-28 21:59:00'),(122,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/haberler','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:11','2026-05-28 21:59:11'),(123,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:12','2026-05-28 21:59:12'),(124,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:13','2026-05-28 21:59:13'),(125,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/ilanlar','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:14','2026-05-28 21:59:14'),(126,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:15','2026-05-28 21:59:15'),(127,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/videolar','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:16','2026-05-28 21:59:16'),(128,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:17','2026-05-28 21:59:17'),(129,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/galeriler','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:18','2026-05-28 21:59:18'),(130,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:19','2026-05-28 21:59:19'),(131,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:21','2026-05-28 21:59:21'),(132,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:22','2026-05-28 21:59:22'),(133,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:41','2026-05-28 21:59:41'),(134,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:42','2026-05-28 21:59:42'),(135,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:44','2026-05-28 21:59:44'),(136,1,'edit_site_setting','deneme123 site ayarlarını güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"site_name\":\"Argnest Haber-\\u0130lan Portal CMS\",\"seo_title\":\"Argnest Haber-\\u0130lan Portal CMS\",\"maintenance_mode\":false,\"maintenance_ends_at\":null}','2026-05-28 21:59:45','2026-05-28 21:59:45'),(137,1,'maintenance_mode_disabled','deneme123 bakım modunu pasif etti.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"maintenance_mode\":false}','2026-05-28 21:59:45','2026-05-28 21:59:45'),(138,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:45','2026-05-28 21:59:45'),(139,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:47','2026-05-28 21:59:47'),(140,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:48','2026-05-28 21:59:48'),(141,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:49','2026-05-28 21:59:49'),(142,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:50','2026-05-28 21:59:50'),(143,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 21:59:55','2026-05-28 21:59:55'),(144,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:14','2026-05-28 22:00:14'),(145,1,'edit_site_setting','deneme123 site ayarlarını güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"site_name\":\"Argnest Haber-\\u0130lan Portal CMS\",\"seo_title\":\"Argnest Haber-\\u0130lan Portal CMS\",\"maintenance_mode\":false,\"maintenance_ends_at\":null}','2026-05-28 22:00:15','2026-05-28 22:00:15'),(146,1,'maintenance_mode_disabled','deneme123 bakım modunu pasif etti.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"maintenance_mode\":false}','2026-05-28 22:00:15','2026-05-28 22:00:15'),(147,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:15','2026-05-28 22:00:15'),(148,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:18','2026-05-28 22:00:18'),(149,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:19','2026-05-28 22:00:19'),(150,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:20','2026-05-28 22:00:20'),(151,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:21','2026-05-28 22:00:21'),(152,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/mesajlar/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:22','2026-05-28 22:00:22'),(153,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:25','2026-05-28 22:00:25'),(154,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:36','2026-05-28 22:00:36'),(155,1,'edit_site_setting','deneme123 site ayarlarını güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"site_name\":\"Argnest Haber-\\u0130lan Portal CMS\",\"seo_title\":\"Argnest Haber-\\u0130lan Portal CMS\",\"maintenance_mode\":false,\"maintenance_ends_at\":null}','2026-05-28 22:00:36','2026-05-28 22:00:36'),(156,1,'maintenance_mode_disabled','deneme123 bakım modunu pasif etti.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"site_setting_id\":1,\"maintenance_mode\":false}','2026-05-28 22:00:36','2026-05-28 22:00:36'),(157,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:37','2026-05-28 22:00:37'),(158,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:44','2026-05-28 22:00:44'),(159,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:49','2026-05-28 22:00:49'),(160,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:50','2026-05-28 22:00:50'),(161,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:51','2026-05-28 22:00:51'),(162,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/bildirimler/count','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:00:52','2026-05-28 22:00:52'),(163,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:01:08','2026-05-28 22:01:08'),(164,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:01:09','2026-05-28 22:01:09'),(165,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:01:14','2026-05-28 22:01:14'),(166,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:01:15','2026-05-28 22:01:15'),(167,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:01:52','2026-05-28 22:01:52'),(168,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:13','2026-05-28 22:02:13'),(169,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:16','2026-05-28 22:02:16'),(170,1,'create_category','deneme123 yeni kategori oluşturdu.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"category_id\":1,\"name\":\"\\u0130\\u015eKUR\",\"slug\":\"iskur\"}','2026-05-28 22:02:16','2026-05-28 22:02:16'),(171,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:36','2026-05-28 22:02:36'),(172,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:40','2026-05-28 22:02:40'),(173,1,'create_category','deneme123 yeni kategori oluşturdu.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"category_id\":2,\"name\":\"KAMU ALIMI\",\"slug\":\"kamu-alimi\"}','2026-05-28 22:02:40','2026-05-28 22:02:40'),(174,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:51','2026-05-28 22:02:51'),(175,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:52','2026-05-28 22:02:52'),(176,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:02:54','2026-05-28 22:02:54'),(177,1,'create_category','deneme123 yeni kategori oluşturdu.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"category_id\":3,\"name\":\"S\\u0130YASET\",\"slug\":\"siyaset\"}','2026-05-28 22:02:54','2026-05-28 22:02:54'),(178,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:01','2026-05-28 22:03:01'),(179,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:13','2026-05-28 22:03:13'),(180,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:15','2026-05-28 22:03:15'),(181,1,'create_category','deneme123 yeni kategori oluşturdu.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"category_id\":4,\"name\":\"EKONOM\\u0130\",\"slug\":\"ekonomi\"}','2026-05-28 22:03:15','2026-05-28 22:03:15'),(182,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:49','2026-05-28 22:03:49'),(183,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:50','2026-05-28 22:03:50'),(184,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:03:52','2026-05-28 22:03:52'),(185,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/presence/heartbeat','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:04:52','2026-05-28 22:04:52'),(186,1,'update_user','deneme123 kullanıcı güncelledi.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Desktop','Opera','Windows','http://127.0.0.1:8047/livewire-605783ee/update','{\"user_id\":1,\"name\":\"deneme123\",\"email\":\"demo@gmail.com\",\"old_role\":\"admin\",\"new_role\":\"admin\",\"old_status\":\"active\",\"new_status\":\"active\"}','2026-05-28 22:05:21','2026-05-28 22:05:21');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `ad_type` varchar(255) NOT NULL DEFAULT 'image',
  `device_target` varchar(255) NOT NULL DEFAULT 'all',
  `page_target` varchar(255) NOT NULL DEFAULT 'all',
  `image` varchar(255) DEFAULT NULL,
  `html_code` longtext DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `ctr` decimal(8,2) NOT NULL DEFAULT 0.00,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `advertisements`
--

LOCK TABLES `advertisements` WRITE;
/*!40000 ALTER TABLE `advertisements` DISABLE KEYS */;
/*!40000 ALTER TABLE `advertisements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `is_headline` tinyint(1) NOT NULL DEFAULT 0,
  `is_breaking` tinyint(1) NOT NULL DEFAULT 0,
  `comments_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `announcements_slug_unique` (`slug`),
  KEY `announcements_category_id_foreign` (`category_id`),
  KEY `announcements_is_breaking_index` (`is_breaking`),
  CONSTRAINT `announcements_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'news',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'İŞKUR','iskur','announcement',1,0,'2026-05-28 22:02:16','2026-05-28 22:02:16'),(2,'KAMU ALIMI','kamu-alimi','announcement',1,0,'2026-05-28 22:02:40','2026-05-28 22:02:40'),(3,'SİYASET','siyaset','news',1,0,'2026-05-28 22:02:54','2026-05-28 22:02:54'),(4,'EKONOMİ','ekonomi','news',1,0,'2026-05-28 22:03:15','2026-05-28 22:03:15');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_slug_unique` (`slug`),
  KEY `cities_parent_id_foreign` (`parent_id`),
  CONSTRAINT `cities_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `commentable_type` varchar(255) NOT NULL,
  `commentable_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `ai_risk_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_risk_reasons`)),
  `ai_review_required` tinyint(1) NOT NULL DEFAULT 0,
  `moderated_by` bigint(20) unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_note` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  KEY `comments_moderated_by_foreign` (`moderated_by`),
  KEY `comments_ai_review_required_ai_risk_score_index` (`ai_review_required`,`ai_risk_score`),
  CONSTRAINT `comments_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_reports`
--

DROP TABLE IF EXISTS `community_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `reportable_type` varchar(255) DEFAULT NULL,
  `reportable_id` bigint(20) unsigned DEFAULT NULL,
  `reason` varchar(40) NOT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `subject_ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `subject_ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `moderator_note` text DEFAULT NULL,
  `resolution_action` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `community_reports_unique_user_subject` (`user_id`,`reportable_type`,`reportable_id`),
  KEY `community_reports_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`),
  KEY `community_reports_reviewed_by_foreign` (`reviewed_by`),
  KEY `community_reports_status_subject_ai_risk_score_index` (`status`,`subject_ai_risk_score`),
  KEY `community_reports_reason_created_at_index` (`reason`,`created_at`),
  CONSTRAINT `community_reports_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `community_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_reports`
--

LOCK TABLES `community_reports` WRITE;
/*!40000 ALTER TABLE `community_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversation_participants`
--

DROP TABLE IF EXISTS `conversation_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversation_participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `is_muted` tinyint(1) NOT NULL DEFAULT 0,
  `muted_until` timestamp NULL DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `pinned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversation_participants_conversation_id_user_id_unique` (`conversation_id`,`user_id`),
  KEY `conversation_participants_user_id_last_read_at_index` (`user_id`,`last_read_at`),
  CONSTRAINT `conversation_participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_participants`
--

LOCK TABLES `conversation_participants` WRITE;
/*!40000 ALTER TABLE `conversation_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversation_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'direct',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `requested_by` bigint(20) unsigned NOT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_requested_by_foreign` (`requested_by`),
  KEY `conversations_type_index` (`type`),
  KEY `conversations_status_index` (`status`),
  CONSTRAINT `conversations_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_badge_user`
--

DROP TABLE IF EXISTS `forum_badge_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_badge_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_badge_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_badge_user_forum_badge_id_user_id_unique` (`forum_badge_id`,`user_id`),
  KEY `forum_badge_user_user_id_foreign` (`user_id`),
  CONSTRAINT `forum_badge_user_forum_badge_id_foreign` FOREIGN KEY (`forum_badge_id`) REFERENCES `forum_badges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_badge_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_badge_user`
--

LOCK TABLES `forum_badge_user` WRITE;
/*!40000 ALTER TABLE `forum_badge_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_badge_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_badges`
--

DROP TABLE IF EXISTS `forum_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_badges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT 'slate',
  `type` varchar(255) NOT NULL DEFAULT 'reputation',
  `threshold` int(10) unsigned NOT NULL DEFAULT 0,
  `xp_reward` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_badges_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_badges`
--

LOCK TABLES `forum_badges` WRITE;
/*!40000 ALTER TABLE `forum_badges` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_categories`
--

DROP TABLE IF EXISTS `forum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_categories`
--

LOCK TABLES `forum_categories` WRITE;
/*!40000 ALTER TABLE `forum_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_topic_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `quoted_post_id` bigint(20) unsigned DEFAULT NULL,
  `content` longtext NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `ai_risk_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_risk_reasons`)),
  `ai_review_required` tinyint(1) NOT NULL DEFAULT 0,
  `moderated_by` bigint(20) unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_note` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_posts_forum_topic_id_foreign` (`forum_topic_id`),
  KEY `forum_posts_user_id_foreign` (`user_id`),
  KEY `forum_posts_moderated_by_foreign` (`moderated_by`),
  KEY `forum_posts_status_created_at_index` (`status`,`created_at`),
  KEY `forum_posts_parent_id_foreign` (`parent_id`),
  KEY `forum_posts_quoted_post_id_foreign` (`quoted_post_id`),
  KEY `forum_posts_ai_review_required_ai_risk_score_index` (`ai_review_required`,`ai_risk_score`),
  CONSTRAINT `forum_posts_forum_topic_id_foreign` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_posts_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forum_posts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_posts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forum_posts_quoted_post_id_foreign` FOREIGN KEY (`quoted_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forum_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_quest_user`
--

DROP TABLE IF EXISTS `forum_quest_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_quest_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_quest_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tracked_on` date NOT NULL,
  `progress` int(10) unsigned NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_quest_user_forum_quest_id_user_id_tracked_on_unique` (`forum_quest_id`,`user_id`,`tracked_on`),
  KEY `forum_quest_user_user_id_tracked_on_is_completed_index` (`user_id`,`tracked_on`,`is_completed`),
  CONSTRAINT `forum_quest_user_forum_quest_id_foreign` FOREIGN KEY (`forum_quest_id`) REFERENCES `forum_quests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_quest_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_quest_user`
--

LOCK TABLES `forum_quest_user` WRITE;
/*!40000 ALTER TABLE `forum_quest_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_quest_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_quests`
--

DROP TABLE IF EXISTS `forum_quests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_quests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `target` int(10) unsigned NOT NULL DEFAULT 1,
  `xp_reward` int(11) NOT NULL DEFAULT 0,
  `reputation_reward` int(11) NOT NULL DEFAULT 0,
  `is_daily` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_quests_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_quests`
--

LOCK TABLES `forum_quests` WRITE;
/*!40000 ALTER TABLE `forum_quests` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_quests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_reputation_events`
--

DROP TABLE IF EXISTS `forum_reputation_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_reputation_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `xp` int(11) NOT NULL DEFAULT 0,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_reputation_events_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `forum_reputation_events_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `forum_reputation_events_type_created_at_index` (`type`,`created_at`),
  CONSTRAINT `forum_reputation_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_reputation_events`
--

LOCK TABLES `forum_reputation_events` WRITE;
/*!40000 ALTER TABLE `forum_reputation_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_reputation_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_tag_topic`
--

DROP TABLE IF EXISTS `forum_tag_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_tag_topic` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_tag_id` bigint(20) unsigned NOT NULL,
  `forum_topic_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_tag_topic_forum_tag_id_forum_topic_id_unique` (`forum_tag_id`,`forum_topic_id`),
  KEY `forum_tag_topic_forum_topic_id_forum_tag_id_index` (`forum_topic_id`,`forum_tag_id`),
  CONSTRAINT `forum_tag_topic_forum_tag_id_foreign` FOREIGN KEY (`forum_tag_id`) REFERENCES `forum_tags` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_tag_topic_forum_topic_id_foreign` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_tag_topic`
--

LOCK TABLES `forum_tag_topic` WRITE;
/*!40000 ALTER TABLE `forum_tag_topic` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_tag_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_tags`
--

DROP TABLE IF EXISTS `forum_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#ef4444',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_tags_name_unique` (`name`),
  UNIQUE KEY `forum_tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_tags`
--

LOCK TABLES `forum_tags` WRITE;
/*!40000 ALTER TABLE `forum_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topic_bookmarks`
--

DROP TABLE IF EXISTS `forum_topic_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topic_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_topic_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_topic_bookmarks_forum_topic_id_user_id_unique` (`forum_topic_id`,`user_id`),
  KEY `forum_topic_bookmarks_user_id_foreign` (`user_id`),
  CONSTRAINT `forum_topic_bookmarks_forum_topic_id_foreign` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topic_bookmarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topic_bookmarks`
--

LOCK TABLES `forum_topic_bookmarks` WRITE;
/*!40000 ALTER TABLE `forum_topic_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_topic_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topic_likes`
--

DROP TABLE IF EXISTS `forum_topic_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topic_likes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_topic_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_topic_likes_forum_topic_id_user_id_unique` (`forum_topic_id`,`user_id`),
  KEY `forum_topic_likes_user_id_foreign` (`user_id`),
  CONSTRAINT `forum_topic_likes_forum_topic_id_foreign` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topic_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topic_likes`
--

LOCK TABLES `forum_topic_likes` WRITE;
/*!40000 ALTER TABLE `forum_topic_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_topic_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topics`
--

DROP TABLE IF EXISTS `forum_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_category_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('pending','published','hidden') NOT NULL DEFAULT 'published',
  `ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `ai_risk_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_risk_reasons`)),
  `ai_review_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_solved` tinyint(1) NOT NULL DEFAULT 0,
  `replies_closed` tinyint(1) NOT NULL DEFAULT 0,
  `slow_mode_seconds` smallint(5) unsigned NOT NULL DEFAULT 0,
  `moderator_note` text DEFAULT NULL,
  `views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_post_at` timestamp NULL DEFAULT NULL,
  `last_post_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_topics_slug_unique` (`slug`),
  KEY `forum_topics_forum_category_id_foreign` (`forum_category_id`),
  KEY `forum_topics_user_id_foreign` (`user_id`),
  KEY `forum_topics_status_is_pinned_last_post_at_index` (`status`,`is_pinned`,`last_post_at`),
  KEY `forum_topics_last_post_user_id_foreign` (`last_post_user_id`),
  KEY `forum_topics_ai_review_required_ai_risk_score_index` (`ai_review_required`,`ai_risk_score`),
  CONSTRAINT `forum_topics_forum_category_id_foreign` FOREIGN KEY (`forum_category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topics_last_post_user_id_foreign` FOREIGN KEY (`last_post_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forum_topics_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topics`
--

LOCK TABLES `forum_topics` WRITE;
/*!40000 ALTER TABLE `forum_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `galleries_slug_unique` (`slug`),
  KEY `galleries_user_id_foreign` (`user_id`),
  KEY `galleries_category_id_foreign` (`category_id`),
  CONSTRAINT `galleries_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `galleries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_images`
--

DROP TABLE IF EXISTS `gallery_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_images_gallery_id_foreign` (`gallery_id`),
  CONSTRAINT `gallery_images_gallery_id_foreign` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_images`
--

LOCK TABLES `gallery_images` WRITE;
/*!40000 ALTER TABLE `gallery_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `header_slots`
--

DROP TABLE IF EXISTS `header_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `header_slots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slot_type` varchar(255) NOT NULL DEFAULT 'button',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `display_position` varchar(255) NOT NULL DEFAULT 'topbar_after_home',
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `button_target` varchar(255) NOT NULL DEFAULT '_self',
  `button_background_color` varchar(255) DEFAULT NULL,
  `button_hover_color` varchar(255) DEFAULT NULL,
  `button_text_color` varchar(255) DEFAULT NULL,
  `button_size` varchar(255) NOT NULL DEFAULT 'medium',
  `button_radius` smallint(5) unsigned NOT NULL DEFAULT 6,
  `button_icon` varchar(255) DEFAULT NULL,
  `custom_css_class` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `banner_url` varchar(255) DEFAULT NULL,
  `banner_target` varchar(255) NOT NULL DEFAULT '_self',
  `banner_width` smallint(5) unsigned DEFAULT NULL,
  `banner_height` smallint(5) unsigned DEFAULT NULL,
  `banner_alt` varchar(255) DEFAULT NULL,
  `html_code` text DEFAULT NULL,
  `script_code` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `header_slots_is_active_display_position_sort_order_index` (`is_active`,`display_position`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `header_slots`
--

LOCK TABLES `header_slots` WRITE;
/*!40000 ALTER TABLE `header_slots` DISABLE KEYS */;
INSERT INTO `header_slots` VALUES (3,'DENEME','button',1,0,'topbar_after_home',NULL,NULL,'DENEME',NULL,'_blank',NULL,NULL,NULL,'large',10,NULL,NULL,NULL,NULL,'_self',NULL,NULL,NULL,NULL,NULL,'2026-05-28 21:56:49','2026-05-28 21:56:49'),(4,'DENEME2','banner',1,0,'topbar_after_home',NULL,NULL,NULL,NULL,'_self',NULL,NULL,NULL,'medium',6,NULL,NULL,'header-slots/01KSR9H6PAK3CQQ001YABNPV3E.jpg',NULL,'_blank',NULL,NULL,NULL,NULL,NULL,'2026-05-28 21:58:29','2026-05-28 21:58:29');
/*!40000 ALTER TABLE `header_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institutions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_settings`
--

DROP TABLE IF EXISTS `integration_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mail_mailer` varchar(255) DEFAULT NULL,
  `mail_host` varchar(255) DEFAULT NULL,
  `mail_port` int(10) unsigned DEFAULT NULL,
  `mail_username` varchar(255) DEFAULT NULL,
  `mail_password` text DEFAULT NULL,
  `mail_encryption` varchar(255) DEFAULT NULL,
  `mail_from_address` varchar(255) DEFAULT NULL,
  `mail_from_name` varchar(255) DEFAULT NULL,
  `recaptcha_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `recaptcha_site_key` varchar(255) DEFAULT NULL,
  `recaptcha_secret_key` text DEFAULT NULL,
  `webpush_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `webpush_vapid_public_key` text DEFAULT NULL,
  `webpush_vapid_private_key` text DEFAULT NULL,
  `webpush_vapid_subject` varchar(255) DEFAULT NULL,
  `google_client_id` varchar(255) DEFAULT NULL,
  `google_client_secret` text DEFAULT NULL,
  `facebook_app_id` varchar(255) DEFAULT NULL,
  `facebook_app_secret` text DEFAULT NULL,
  `captcha_required` tinyint(1) NOT NULL DEFAULT 1,
  `mysqldump_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_settings`
--

LOCK TABLES `integration_settings` WRITE;
/*!40000 ALTER TABLE `integration_settings` DISABLE KEYS */;
INSERT INTO `integration_settings` VALUES (1,'smtp',NULL,NULL,NULL,NULL,'tls',NULL,'Argnest Haber-İlan Portal CMS',0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `integration_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_activities`
--

DROP TABLE IF EXISTS `live_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(80) NOT NULL,
  `source` varchar(40) NOT NULL DEFAULT 'system',
  `severity` varchar(20) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `is_important` tinyint(1) NOT NULL DEFAULT 0,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_activities_user_id_foreign` (`user_id`),
  KEY `live_activities_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `live_activities_is_public_occurred_at_index` (`is_public`,`occurred_at`),
  KEY `live_activities_source_type_index` (`source`,`type`),
  KEY `live_activities_severity_occurred_at_index` (`severity`,`occurred_at`),
  KEY `live_activities_is_important_occurred_at_index` (`is_important`,`occurred_at`),
  CONSTRAINT `live_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_activities`
--

LOCK TABLES `live_activities` WRITE;
/*!40000 ALTER TABLE `live_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `live_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_chat_messages`
--

DROP TABLE IF EXISTS `live_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `ai_risk_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_risk_reasons`)),
  `ai_review_required` tinyint(1) NOT NULL DEFAULT 0,
  `moderated_by` bigint(20) unsigned DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `moderation_note` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_chat_messages_user_id_foreign` (`user_id`),
  KEY `live_chat_messages_moderated_by_foreign` (`moderated_by`),
  KEY `live_chat_messages_status_created_at_index` (`status`,`created_at`),
  KEY `live_chat_messages_ai_review_required_ai_risk_score_index` (`ai_review_required`,`ai_risk_score`),
  CONSTRAINT `live_chat_messages_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `live_chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_chat_messages`
--

LOCK TABLES `live_chat_messages` WRITE;
/*!40000 ALTER TABLE `live_chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `live_chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_assets`
--

DROP TABLE IF EXISTS `media_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `attachable_type` varchar(255) DEFAULT NULL,
  `attachable_id` bigint(20) unsigned DEFAULT NULL,
  `collection` varchar(40) NOT NULL DEFAULT 'forum',
  `disk` varchar(40) NOT NULL DEFAULT 'public',
  `visibility` varchar(20) NOT NULL DEFAULT 'public',
  `original_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `mime_type` varchar(120) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `checksum` varchar(64) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ready',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_assets_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  KEY `media_assets_user_id_collection_created_at_index` (`user_id`,`collection`,`created_at`),
  KEY `media_assets_attachable_type_attachable_id_collection_index` (`attachable_type`,`attachable_id`,`collection`),
  KEY `media_assets_collection_index` (`collection`),
  KEY `media_assets_visibility_index` (`visibility`),
  KEY `media_assets_checksum_index` (`checksum`),
  KEY `media_assets_status_index` (`status`),
  CONSTRAINT `media_assets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_assets`
--

LOCK TABLES `media_assets` WRITE;
/*!40000 ALTER TABLE `media_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_30_200140_create_news_table',1),(5,'2026_05_09_202505_create_announcements_table',1),(6,'2026_05_09_212834_add_new_fields_to_news_table',1),(7,'2026_05_10_091907_create_categories_table',1),(8,'2026_05_10_092516_create_institutions_table',1),(9,'2026_05_10_092826_create_cities_table',1),(10,'2026_05_10_140044_add_parent_id_to_cities_table',1),(11,'2026_05_10_140843_fix_add_parent_id_to_cities_table',1),(12,'2026_05_10_230421_create_advertisements_table',1),(13,'2026_05_13_011818_add_profile_fields_to_users_table',1),(14,'2026_05_13_015211_add_role_to_users_table',1),(15,'2026_05_13_020043_create_comments_table',1),(16,'2026_05_13_020050_create_user_punishments_table',1),(17,'2026_05_13_211619_create_notifications_table',1),(18,'2026_05_14_092905_add_category_id_to_news_table',1),(19,'2026_05_14_092911_add_category_id_to_announcements_table',1),(20,'2026_05_14_141751_add_status_fields_to_users_table',1),(21,'2026_05_14_144121_create_activity_logs_table',1),(22,'2026_05_14_153603_create_work_sessions_table',1),(23,'2026_05_14_191808_create_site_settings_table',1),(24,'2026_05_14_200712_add_advanced_fields_to_advertisements_table',1),(25,'2026_05_14_224323_add_is_active_to_users_table',1),(26,'2026_05_15_222955_add_auto_punishment_enabled_to_site_settings_table',1),(27,'2026_05_16_030213_add_online_tracking_to_users_table',1),(28,'2026_05_16_121959_create_videos_table',1),(29,'2026_05_16_122000_create_galleries_table',1),(30,'2026_05_16_122005_create_gallery_images_table',1),(31,'2026_05_16_132422_add_trending_fields_to_news_table',1),(32,'2026_05_16_203556_add_membership_settings_to_site_settings_table',1),(33,'2026_05_16_211105_create_roles_table',1),(34,'2026_05_16_211113_create_permissions_table',1),(35,'2026_05_16_211121_create_permission_role_table',1),(36,'2026_05_16_211128_add_role_id_to_users_table',1),(37,'2026_05_17_023403_create_seo_settings_table',1),(38,'2026_05_17_224453_add_community_live_fields_to_site_settings_table',1),(39,'2026_05_18_010000_create_forum_categories_table',1),(40,'2026_05_18_010100_create_forum_topics_table',1),(41,'2026_05_18_010200_create_forum_posts_table',1),(42,'2026_05_18_011000_add_soft_deletes_to_forum_tables',1),(43,'2026_05_18_012000_add_forum_topic_ux_fields',1),(44,'2026_05_18_013000_create_forum_interactions_tables',1),(45,'2026_05_18_014000_create_live_chat_messages_table',1),(46,'2026_05_18_031000_create_live_activities_table',1),(47,'2026_05_18_032000_add_admin_fields_to_live_activities_table',1),(48,'2026_05_18_033000_add_reply_quote_fields_to_forum_posts_table',1),(49,'2026_05_18_034000_add_controls_to_forum_topics_table',1),(50,'2026_05_18_035000_create_forum_tags_tables',1),(51,'2026_05_19_120000_add_ai_safety_fields_to_community_tables',1),(52,'2026_05_19_130000_create_forum_gamification_tables',1),(53,'2026_05_19_140000_create_community_reports_table',1),(54,'2026_05_19_150000_create_user_follows_table',1),(55,'2026_05_19_160000_create_private_messaging_tables',1),(56,'2026_05_19_161000_add_advanced_private_message_ux_fields',1),(57,'2026_05_19_170000_create_media_assets_table',1),(58,'2026_05_19_181000_create_push_subscriptions_table',1),(59,'2026_05_19_201000_create_search_queries_table',1),(60,'2026_05_21_120000_add_site_announcement_fields_to_site_settings_table',1),(61,'2026_05_21_130000_create_site_announcements_table',1),(62,'2026_05_21_140000_create_polls_table',1),(63,'2026_05_21_140100_create_poll_options_table',1),(64,'2026_05_21_140200_create_poll_votes_table',1),(65,'2026_05_21_150000_create_theme_settings_table',1),(66,'2026_05_21_160000_create_header_slots_table',1),(67,'2026_05_21_170000_add_is_breaking_to_news_and_announcements_tables',1),(68,'2026_05_25_000001_add_soft_deletes_to_users_table',1),(69,'2026_05_25_000002_create_integration_settings_table',1),(70,'2026_05_25_000003_add_mysqldump_path_to_integration_settings_table',1),(71,'2026_05_25_000004_add_advanced_seo_fields_to_seo_settings_table',1),(72,'2026_05_25_160000_add_home_module_settings_to_site_settings_table',1),(73,'2026_05_26_000001_add_maintenance_details_to_site_settings_table',1),(74,'2026_05_26_120000_add_weather_fallback_fields_to_site_settings_table',1),(75,'2026_05_26_121000_add_automatic_weather_settings_to_site_settings_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `news_type` varchar(255) NOT NULL DEFAULT 'normal',
  `share_facebook` tinyint(1) NOT NULL DEFAULT 0,
  `share_twitter` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `is_headline` tinyint(1) NOT NULL DEFAULT 0,
  `is_breaking` tinyint(1) NOT NULL DEFAULT 0,
  `comments_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `daily_views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `weekly_views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `monthly_views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `trend_score` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_id_foreign` (`category_id`),
  KEY `news_is_breaking_index` (`is_breaking`),
  CONSTRAINT `news_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1,14,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(2,1,13,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(3,1,20,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(4,1,19,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(5,1,4,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(6,1,3,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(7,1,2,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(8,1,5,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(9,1,8,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(10,1,7,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(11,1,6,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(12,1,9,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(13,1,12,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(14,1,1,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(15,1,16,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(16,1,10,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(17,1,11,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(18,1,18,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(19,1,17,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(20,1,15,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(21,2,20,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(22,2,19,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(23,2,4,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(24,2,3,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(25,2,2,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(26,2,8,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(27,2,7,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(28,2,6,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(29,2,1,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(30,2,18,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(31,2,17,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(32,3,14,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(33,3,1,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(34,3,15,'2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `group` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Panel Girisi','panel_giris','Panel',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(2,'Haber Goruntuleme','haber_gor','Haber',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(3,'Haber Ekleme','haber_ekle','Haber',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(4,'Haber Duzenleme','haber_duzenle','Haber',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(5,'Haber Silme','haber_sil','Haber',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(6,'Ilan Goruntuleme','ilan_gor','Ilan',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(7,'Ilan Ekleme','ilan_ekle','Ilan',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(8,'Ilan Duzenleme','ilan_duzenle','Ilan',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(9,'Ilan Silme','ilan_sil','Ilan',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(10,'SEO Yonetimi','seo_yonet','SEO',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(11,'Site Ayarlari','site_ayarlarini_yonet','Site',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(12,'Kullanici Yonetimi','kullanici_yonet','Kullanici',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(13,'Forum Yonetimi','forum_yonet','Forum',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(14,'Forum Moderasyonu','forum_moderasyonu','Forum',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(15,'Yorum Moderasyonu','yorum_moderasyonu','Moderasyon',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(16,'Reklam Yonetimi','reklam_yonet','Reklam',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(17,'Video Ekleme','video_ekle','Medya',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(18,'Video Duzenleme','video_duzenle','Medya',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(19,'Galeri Ekleme','galeri_ekle','Medya',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(20,'Galeri Duzenleme','galeri_duzenle','Medya',NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_options`
--

DROP TABLE IF EXISTS `poll_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `votes_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_options_poll_id_foreign` (`poll_id`),
  CONSTRAINT `poll_options_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll_options`
--

LOCK TABLES `poll_options` WRITE;
/*!40000 ALTER TABLE `poll_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_votes`
--

DROP TABLE IF EXISTS `poll_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned NOT NULL,
  `poll_option_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_hash` varchar(255) DEFAULT NULL,
  `voter_key` varchar(255) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_votes_poll_option_id_voter_key_unique` (`poll_option_id`,`voter_key`),
  KEY `poll_votes_user_id_foreign` (`user_id`),
  KEY `poll_votes_poll_id_voter_key_index` (`poll_id`,`voter_key`),
  CONSTRAINT `poll_votes_poll_id_foreign` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `poll_votes_poll_option_id_foreign` FOREIGN KEY (`poll_option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE,
  CONSTRAINT `poll_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll_votes`
--

LOCK TABLES `poll_votes` WRITE;
/*!40000 ALTER TABLE `poll_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `poll_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `share_results` tinyint(1) NOT NULL DEFAULT 1,
  `show_home_popup` tinyint(1) NOT NULL DEFAULT 0,
  `popup_cooldown_minutes` int(10) unsigned NOT NULL DEFAULT 1440,
  `allow_multiple` tinyint(1) NOT NULL DEFAULT 0,
  `allow_guests` tinyint(1) NOT NULL DEFAULT 1,
  `require_login` tinyint(1) NOT NULL DEFAULT 0,
  `duplicate_guard` varchar(255) NOT NULL DEFAULT 'user_session_ip',
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `polls_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_message_reactions`
--

DROP TABLE IF EXISTS `private_message_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_message_reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `private_message_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reaction` varchar(20) NOT NULL DEFAULT 'like',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pm_reactions_unique` (`private_message_id`,`user_id`,`reaction`),
  KEY `private_message_reactions_user_id_foreign` (`user_id`),
  KEY `private_message_reactions_private_message_id_reaction_index` (`private_message_id`,`reaction`),
  CONSTRAINT `private_message_reactions_private_message_id_foreign` FOREIGN KEY (`private_message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `private_message_reactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_message_reactions`
--

LOCK TABLES `private_message_reactions` WRITE;
/*!40000 ALTER TABLE `private_message_reactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_message_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_messages`
--

DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'sent',
  `ai_risk_score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ai_risk_label` varchar(20) NOT NULL DEFAULT 'low',
  `ai_risk_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_risk_reasons`)),
  `ai_review_required` tinyint(1) NOT NULL DEFAULT 0,
  `edited_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `private_messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  KEY `private_messages_sender_id_created_at_index` (`sender_id`,`created_at`),
  KEY `private_messages_status_index` (`status`),
  KEY `private_messages_ai_risk_label_index` (`ai_risk_label`),
  KEY `private_messages_ai_review_required_index` (`ai_review_required`),
  CONSTRAINT `private_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `private_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_messages`
--

LOCK TABLES `private_messages` WRITE;
/*!40000 ALTER TABLE `private_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `endpoint` text NOT NULL,
  `endpoint_hash` varchar(64) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `content_encoding` varchar(20) NOT NULL DEFAULT 'aes128gcm',
  `user_agent` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `failure_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_hash_unique` (`endpoint_hash`),
  KEY `push_subscriptions_user_id_is_enabled_index` (`user_id`,`is_enabled`),
  KEY `push_subscriptions_failed_at_failure_count_index` (`failed_at`,`failure_count`),
  KEY `push_subscriptions_is_enabled_index` (`is_enabled`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT 'primary',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','admin','danger',1,'Tam yetkili sistem yoneticisi.','2026-05-28 21:45:49','2026-05-28 21:45:49'),(2,'Editor','editor','warning',1,NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(3,'Moderator','moderator','success',1,NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49'),(4,'Kullanici','user','gray',1,NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search_queries`
--

DROP TABLE IF EXISTS `search_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search_queries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(120) NOT NULL,
  `normalized_query` varchar(120) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT 1,
  `last_searched_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_queries_normalized_query_unique` (`normalized_query`),
  KEY `search_queries_hits_last_searched_at_index` (`hits`,`last_searched_at`),
  KEY `search_queries_last_searched_at_index` (`last_searched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search_queries`
--

LOCK TABLES `search_queries` WRITE;
/*!40000 ALTER TABLE `search_queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `search_queries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_settings`
--

DROP TABLE IF EXISTS `seo_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(255) DEFAULT NULL,
  `site_description` text DEFAULT NULL,
  `site_keywords` varchar(255) DEFAULT NULL,
  `default_author` varchar(255) DEFAULT NULL,
  `default_language` varchar(10) NOT NULL DEFAULT 'tr',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `indexing` tinyint(1) NOT NULL DEFAULT 1,
  `robots_index` tinyint(1) NOT NULL DEFAULT 1,
  `robots_follow` tinyint(1) NOT NULL DEFAULT 1,
  `robots_txt` longtext DEFAULT NULL,
  `sitemap_cache_minutes` int(10) unsigned NOT NULL DEFAULT 60,
  `google_analytics` text DEFAULT NULL,
  `google_tag_manager` text DEFAULT NULL,
  `json_ld` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_settings`
--

LOCK TABLES `seo_settings` WRITE;
/*!40000 ALTER TABLE `seo_settings` DISABLE KEYS */;
INSERT INTO `seo_settings` VALUES (1,'Argnest Haber-İlan Portal CMS','Modern Haber, İlan ve Topluluk Yönetim Sistemi',NULL,NULL,'tr',NULL,NULL,NULL,NULL,NULL,NULL,'http://127.0.0.1:8000',1,1,1,'User-agent: *\nAllow: /\nSitemap: http://127.0.0.1:8000/sitemap.xml',60,NULL,NULL,NULL,'2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `seo_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_announcements`
--

DROP TABLE IF EXISTS `site_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `link_target` varchar(255) NOT NULL DEFAULT '_self',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_announcements`
--

LOCK TABLES `site_announcements` WRITE;
/*!40000 ALTER TABLE `site_announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forum_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `live_chat_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `live_stream_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `live_stream_title` varchar(255) DEFAULT NULL,
  `live_stream_description` text DEFAULT NULL,
  `live_stream_url` text DEFAULT NULL,
  `live_announcement_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `live_announcement_text` varchar(255) DEFAULT NULL,
  `live_announcement_type` varchar(255) NOT NULL DEFAULT 'info',
  `site_name` varchar(255) DEFAULT NULL,
  `site_slogan` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `telegram` varchar(255) DEFAULT NULL,
  `header_scripts` longtext DEFAULT NULL,
  `footer_scripts` longtext DEFAULT NULL,
  `google_analytics` longtext DEFAULT NULL,
  `adsense_code` longtext DEFAULT NULL,
  `footer_about` text DEFAULT NULL,
  `footer_copyright` text DEFAULT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_message` text DEFAULT NULL,
  `maintenance_ends_at` datetime DEFAULT NULL,
  `auto_punishment_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `home_news_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `home_announcements_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `home_forum_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `home_galleries_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `home_videos_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `home_polls_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `home_breaking_news_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `home_announcement_bar_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `weather_city` varchar(255) DEFAULT NULL,
  `weather_temperature_fallback` varchar(20) DEFAULT NULL,
  `weather_status_fallback` varchar(255) DEFAULT NULL,
  `weather_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `weather_local_fallback_city` varchar(255) DEFAULT NULL,
  `weather_cache_minutes` smallint(5) unsigned NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registration_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_verification_required` tinyint(1) NOT NULL DEFAULT 1,
  `membership_agreement` longtext DEFAULT NULL,
  `privacy_policy` longtext DEFAULT NULL,
  `community_rules` longtext DEFAULT NULL,
  `site_announcement_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `site_announcement_icon` varchar(255) DEFAULT NULL,
  `site_announcement_text` varchar(255) DEFAULT NULL,
  `site_announcement_starts_at` datetime DEFAULT NULL,
  `site_announcement_ends_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,1,1,0,NULL,NULL,NULL,0,NULL,'info','Argnest Haber-İlan Portal CMS','Modern Haber, İlan ve Topluluk Yönetim Sistemi',NULL,NULL,'Argnest Haber-İlan Portal CMS','Modern Haber, İlan ve Topluluk Yönetim Sistemi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026 Argnest Haber-İlan Portal CMS',0,NULL,NULL,0,1,1,1,1,1,1,1,1,NULL,NULL,NULL,1,'İstanbul',10,'2026-05-28 21:45:49','2026-05-28 22:00:36',1,0,'<p></p>','<p></p>','<p></p>',0,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme_settings`
--

DROP TABLE IF EXISTS `theme_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `primary_color` varchar(20) DEFAULT '#0878c9',
  `secondary_color` varchar(20) DEFAULT '#1e293b',
  `topbar_color` varchar(20) DEFAULT '#0878c9',
  `navbar_color` varchar(20) DEFAULT '#1e293b',
  `breaking_bar_color` varchar(20) DEFAULT '#dc2626',
  `announcement_bar_color` varchar(20) DEFAULT '#0f172a',
  `button_color` varchar(20) DEFAULT '#1d4ed8',
  `button_hover_color` varchar(20) DEFAULT '#1e40af',
  `link_color` varchar(20) DEFAULT '#1d4ed8',
  `heading_color` varchar(20) DEFAULT '#020617',
  `text_color` varchar(20) DEFAULT '#0f172a',
  `card_background_color` varchar(20) DEFAULT '#ffffff',
  `footer_color` varchar(20) DEFAULT '#0f172a',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme_settings`
--

LOCK TABLES `theme_settings` WRITE;
/*!40000 ALTER TABLE `theme_settings` DISABLE KEYS */;
INSERT INTO `theme_settings` VALUES (1,'#0878c9','#1e293b','#0878c9','#1e293b','#dc2626','#0f172a','#1d4ed8','#1e40af','#1d4ed8','#020617','#0f172a','#ffffff','#0f172a','2026-05-28 21:45:49','2026-05-28 21:45:49');
/*!40000 ALTER TABLE `theme_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_follows`
--

DROP TABLE IF EXISTS `user_follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint(20) unsigned NOT NULL,
  `followed_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_follows_follower_id_followed_id_unique` (`follower_id`,`followed_id`),
  KEY `user_follows_followed_id_created_at_index` (`followed_id`,`created_at`),
  KEY `user_follows_follower_id_created_at_index` (`follower_id`,`created_at`),
  CONSTRAINT `user_follows_followed_id_foreign` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_follows`
--

LOCK TABLES `user_follows` WRITE;
/*!40000 ALTER TABLE `user_follows` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_message_blocks`
--

DROP TABLE IF EXISTS `user_message_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_message_blocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blocker_id` bigint(20) unsigned NOT NULL,
  `blocked_id` bigint(20) unsigned NOT NULL,
  `muted_until` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_message_blocks_blocker_id_blocked_id_unique` (`blocker_id`,`blocked_id`),
  KEY `user_message_blocks_blocked_id_blocker_id_index` (`blocked_id`,`blocker_id`),
  CONSTRAINT `user_message_blocks_blocked_id_foreign` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_message_blocks_blocker_id_foreign` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_message_blocks`
--

LOCK TABLES `user_message_blocks` WRITE;
/*!40000 ALTER TABLE `user_message_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_message_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_punishments`
--

DROP TABLE IF EXISTS `user_punishments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_punishments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `moderator_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('warning','mute','temporary_ban','permanent_ban') NOT NULL,
  `reason` text NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_punishments_user_id_foreign` (`user_id`),
  KEY `user_punishments_moderator_id_foreign` (`moderator_id`),
  CONSTRAINT `user_punishments_moderator_id_foreign` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_punishments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_punishments`
--

LOCK TABLES `user_punishments` WRITE;
/*!40000 ALTER TABLE `user_punishments` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_punishments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `suspended_until` timestamp NULL DEFAULT NULL,
  `ban_reason` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `last_ip_address` varchar(255) DEFAULT NULL,
  `last_device` varchar(255) DEFAULT NULL,
  `last_browser` varchar(255) DEFAULT NULL,
  `last_platform` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `forum_reputation` int(11) NOT NULL DEFAULT 0,
  `community_trust_score` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `message_privacy` varchar(20) NOT NULL DEFAULT 'followers',
  `forum_xp` int(10) unsigned NOT NULL DEFAULT 0,
  `forum_level` smallint(5) unsigned NOT NULL DEFAULT 1,
  `forum_streak_days` smallint(5) unsigned NOT NULL DEFAULT 0,
  `forum_last_activity_date` date DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'deneme123','demo@gmail.com','admin',1,1,'active',NULL,NULL,'2026-05-28 21:45:49','$2y$12$fbDUJgZqtno0V8adr6nu7ebxHJ9hjdXTH0sVGaOGr8oRJzlUMGjA.','avkyOSJhharzXyol8YaVHJEq60fD1kzzlQUimAuG2Eof2surmd72ly3vFXNi','2026-05-28 22:05:21','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0','Chrome','Windows','2026-05-28 21:45:49','2026-05-28 22:05:21',NULL,NULL,0,50,'followers',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `video_type` varchar(255) NOT NULL DEFAULT 'youtube',
  `youtube_url` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `views` bigint(20) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `videos_slug_unique` (`slug`),
  KEY `videos_user_id_foreign` (`user_id`),
  KEY `videos_category_id_foreign` (`category_id`),
  CONSTRAINT `videos_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `videos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_sessions`
--

DROP TABLE IF EXISTS `work_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `work_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_sessions`
--

LOCK TABLES `work_sessions` WRITE;
/*!40000 ALTER TABLE `work_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_sessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-29  1:05:22
