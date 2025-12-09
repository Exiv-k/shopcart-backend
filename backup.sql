-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: shopping
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_cart_items_cart_product` (`cart_id`,`product_id`),
  KEY `fk_cart_items_product` (`product_id`),
  CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (19,1,2,2),(23,3,2,1);
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,1,'2025-12-08 12:01:10','2025-12-08 12:01:10'),(2,2,'2025-12-09 05:56:52','2025-12-09 05:56:52'),(3,7,'2025-12-09 10:02:01','2025-12-09 10:02:01'),(4,8,'2025-12-09 10:22:35','2025-12-09 10:22:35');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `description` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (2,'Vacuum Cleaner A255',239.00,0,'Experience a new level of cleanliness with the Kogan™ Cat & Dog Bagless Vacuum Cleaner. Featuring a powerful motor, multi-cyclone filtration, and specialised tools, it provides effective cleaning for carpets, hard floors and more.','https://media.istockphoto.com/id/927031764/photo/vacuum-cleaner-isolated-on-white-background.jpg?s=612x612&w=0&k=20&c=p2WFCCgubSVDs3a3VhEkpWM9ZrqbncKvHtT4J7aaM9U=',1),(3,'Sonictrek QuietMix II Active Noise Canceling Wireless Headphones With SmartQ Silencing Technology',499.00,0,'The best wireless active noise canceling headphones in Australia now with fast, free shipping.\r\nLimited time! Take 30% off a 2nd unit. Check out for offer.\r\nBest in class music and podcasts. Hear every detail of your favorite songs thanks to Quietmix II’s 40mm drivers. The highly-flexible silk diaphragms reproduce thumping bass and crisp treble that extends up to 40kHz for improved clarity.','https://mifo.com.au/cdn/shop/files/sonictrek-quietmix-2.jpg?v=1724366505&width=600',1),(4,'Melodic 1/4 Size Acoustic Violin Kit 4 Strings Natural Varnish Finish with Case Bow',79.50,0,'Solid Build: Each component of the violin is made of high-grade materials and carefully chosen for a perfect blend. It is very likely to retain its value for years and years, meaning you can treat the violin as an investment in your future playing career.','https://m.media-amazon.com/images/I/61L4jqz+lIL._AC_SX679_.jpg',0),(5,'CORSAIR K70 MAX RGB Magnetic Mechanical Wired Gaming Keyboard MGX Adjustable Switches, Simultaneous SOCD and Rapid Trigger, PBT Double-Shot Keycaps, Sound Dampening, 8000Hz Polling, QWERTY NA, Black',349.00,0,'Adjustable Magnetic-Mechanical Switches: The entire keyboard is equipped with fully adjustable CORSAIR MGX switches, enabling you to set every key’s actuation point from a light 0.4mm to a strong 3.6mm in 0.1mm steps, putting you in control for fast keypresses or ultra-accurate typing','https://m.media-amazon.com/images/I/71FQQjIHBwL._AC_SX679_.jpg',1);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'USER',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'user1','$2y$10$.owroo3brXxq03/LOfQbN.JGyQgpU7eF/SI.b5THrkGa4snUexgO2','2025-12-08 03:33:58','2025-12-08 04:36:08','user'),(2,'admin1','$2y$10$TzMU2AZpLu8OnnKrG.3VYesnibxfkgGDYjbMQcxBKy5i.ZJ7Nc2KW','2025-12-09 04:31:43','2025-12-09 04:31:27','ADMIN'),(3,'user2','$2y$10$2/xv/mTGGYilRKoAeLmBVeocdCC1AEtzjGMj4xsIvLHHRPZ9Idbo.','2025-12-09 09:33:38','2025-12-09 09:33:38','USER'),(4,'user4','$2y$10$Vm5usqUgRQaxo/E/Hs1zRud/X55JDHY25CW1XMkYCHwVR4U7BhaT6','2025-12-09 09:36:18','2025-12-09 09:36:18','USER'),(5,'user3','$2y$10$iLSLGKh.uj.Duo6Rr2O6Bedl6K1Xn.pW/OBkzW7FeugJCdNBhYLBK','2025-12-09 09:39:38','2025-12-09 09:39:38','USER'),(6,'test1','$2y$10$xPMxubTF/teSby4vOdwCs.C1Rtk2MXYrHUfuAElneQcXM8tMenmg2','2025-12-09 09:44:37','2025-12-09 09:44:37','USER'),(7,'user10','$2y$10$MpWUjbuoCKiqAPJMpiWEqO6YR8gJmvXRtrvoKPyer33XtilnWrxTy','2025-12-09 10:01:41','2025-12-09 10:01:41','USER'),(8,'janeeyre','$2y$10$TNwulVrfLUdPN0..8C65fOG5.YV0EN.USKRuodyaVRqjDa.afNufG','2025-12-09 10:22:11','2025-12-09 10:22:11','USER');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-09 10:44:29
