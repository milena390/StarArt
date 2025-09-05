-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: jogos
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pontuacoes_memoria`
--

DROP TABLE IF EXISTS `pontuacoes_memoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pontuacoes_memoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pontos` int NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuarios_id` int NOT NULL,
  PRIMARY KEY (`id`,`usuarios_id`),
  KEY `fk_pontuacoes_memoria_usuarios_idx` (`usuarios_id`),
  CONSTRAINT `fk_pontuacoes_memoria_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pontuacoes_memoria`
--

LOCK TABLES `pontuacoes_memoria` WRITE;
/*!40000 ALTER TABLE `pontuacoes_memoria` DISABLE KEYS */;
INSERT INTO `pontuacoes_memoria` VALUES (1,21,'2025-09-01 11:59:27',1),(2,11,'2025-09-01 12:04:58',2);
/*!40000 ALTER TABLE `pontuacoes_memoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pontuacoes_quiz`
--

DROP TABLE IF EXISTS `pontuacoes_quiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pontuacoes_quiz` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pontos` int NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuarios_id` int NOT NULL,
  PRIMARY KEY (`id`,`usuarios_id`),
  KEY `fk_pontuacoes_quiz_usuarios1_idx` (`usuarios_id`),
  CONSTRAINT `fk_pontuacoes_quiz_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pontuacoes_quiz`
--

LOCK TABLES `pontuacoes_quiz` WRITE;
/*!40000 ALTER TABLE `pontuacoes_quiz` DISABLE KEYS */;
INSERT INTO `pontuacoes_quiz` VALUES (1,11,'2025-09-01 12:03:37',2),(2,0,'2025-09-01 12:14:13',3);
/*!40000 ALTER TABLE `pontuacoes_quiz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Lola','lola@gmail','$2y$10$CLDOZQcrIstqUvzsrH3VWekUD77UVF0vDJqU/alWbwrkzJ8g2iw9q'),(2,'milena','milena@gmail.com','$2y$10$3pq.Givnecp.y4CVA68XfO73rXrVyreKtMM1oLGAwAGopdLuQQgYW'),(3,'Mel','Mel@gmail','$2y$10$Vbnxt39raImsMUroneV6De9Mk5eRQlO3lFvK5isscn/piL4830I9.');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'jogos'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-01 12:53:44
