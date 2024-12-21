-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: SmartStep
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
-- Table structure for table `atencionalcliente`
--

DROP TABLE IF EXISTS `atencionalcliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atencionalcliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(30) NOT NULL,
  `Asunto` varchar(500) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atencionalcliente`
--

LOCK TABLES `atencionalcliente` WRITE;
/*!40000 ALTER TABLE `atencionalcliente` DISABLE KEYS */;
INSERT INTO `atencionalcliente` VALUES (15,'EllianOperador','fassfaafs','asfafsafsfas'),(16,'EllianOperador','asfasfasffas','afsafsfasfas'),(17,'EllianOperador','fassafafs','fasfasfasasfasffas'),(19,'Usuario','asfsafafsasf','asfsfaafsfasfas'),(20,'Usuario','cfaasfsaf','afssafasfafs');
/*!40000 ALTER TABLE `atencionalcliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalogo`
--

DROP TABLE IF EXISTS `catalogo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalogo` (
  `Genero` enum('H','M','I') NOT NULL,
  `Marca` varchar(50) NOT NULL,
  `Nombre` varchar(30) NOT NULL,
  `Precio` decimal(10,2) NOT NULL CHECK (`Precio` > 0),
  `Descripcion` text DEFAULT NULL,
  `Tallas` varchar(100) NOT NULL,
  `Stock` int(11) NOT NULL DEFAULT 0 CHECK (`Stock` >= 0),
  `Disponible` enum('Sí','No') GENERATED ALWAYS AS (if(`Stock` > 0,'Sí','No')) STORED,
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogo`
--

LOCK TABLES `catalogo` WRITE;
/*!40000 ALTER TABLE `catalogo` DISABLE KEYS */;
INSERT INTO `catalogo` VALUES ('I','New Balance','373 Kids',850.00,'Diseño clásico con detalles actuales','17-23',39,'Sí'),('H','New Balance','574',1899.00,'Estilo atemporal y cómodo','24-31',36,'Sí'),('I','New Balance','574 Kids',850.00,'Versátiles y cómodos para el uso diario','17-23',30,'Sí'),('H','Adidas','Adizero Boston 10',3799.00,'Ideales para maratones y distancias largas','25-31',24,'Sí'),('M','Nike','Air Force 1',2999.00,'Clásicos de Nike, ideales para el día a día','23-30',40,'Sí'),('I','Nike','Air Max 270 RT',999.00,'Diseño atrevido y moderno','18-22',40,'Sí'),('H','Nike','Air Max 90',3599.00,'Diseño retro con amortiguación de aire visible','25-30',40,'Sí'),('M','Nike','Air Max Dia',2499.00,'Diseño estilizado para uso casual','23-28',40,'Sí'),('H','Skechers','Arch Fit',2499.00,'Suela con soporte para el arco del pie','25-30',40,'Sí'),('M','Vans','Authentic',1499.00,'Clásicos para cualquier ocasión','22-28',40,'Sí'),('M','Puma','Cali Sport',2299.00,'Estilo deportivo con detalles de lujo','23-28',40,'Sí'),('M','Puma','Carina Lift',1799.00,'Estilo urbano con suela elevada','22-27',40,'Sí'),('I','Vans','Checkerboard Slip-On',799.00,'Estilo icónico con diseño de tablero de ajedrez','17-24',40,'Sí'),('H','Converse','Chuck 70',1499.00,'Edición premium de un clásico','24-30',40,'Sí'),('I','Converse','Chuck Taylor All Star',649.00,'Tela clásica, diseño icónico','17-23',40,'Sí'),('M','Adidas','Cloudfoam Pure',1899.00,'Ligeros, cómodos y con un ajuste perfecto','23-29',40,'Sí'),('I','Nike','Flex Runner 2',899.00,'Sin cordones, fáciles de poner y quitar','18-22',40,'Sí'),('I','Adidas','Fortarun EL',749.00,'Suela acolchada para máximo confort','18-24',40,'Sí'),('M','Nike','Free RN 5.0',2299.00,'Flexibles y ligeros, ideales para carreras cortas','23-28',40,'Sí'),('I','Skechers','Go Run 650',699.00,'Ligeros, ideales para correr','18-22',40,'Sí'),('M','Skechers','Go Walk 5',1799.00,'Ligeros, ideales para caminar largas distancias','23-27',40,'Sí'),('I','Adidas','Lite Racer Adapt',749.00,'Ligeros y transpirables','18-24',40,'Sí'),('H','Reebok','Nano X3',2899.00,'Ideales para entrenamiento intensivo','25-31',40,'Sí'),('M','Vans','Old Skool',1699.00,'Diseño clásico con líneas laterales blancas','22-28',40,'Sí'),('I','Vans','Old Skool Kids',799.00,'Diseño clásico con líneas laterales','18-22',40,'Sí'),('I','Converse','One Star Kids',699.00,'Suela vulcanizada para mayor durabilidad','18-22',40,'Sí'),('H','Nike','Pegasus 40',3499.00,'Creados para entrenamiento y carrera diaria','25-32',40,'Sí'),('M','Adidas','Puremotion Adapt',1899.00,'Creados para máxima comodidad','23-27',40,'Sí'),('I','Nike','Revolution 6',899.00,'Ligeros, ideales para actividades diarias','18-23',40,'Sí'),('H','Puma','RS-X',3199.00,'Diseño futurista con amortiguación extra','24-30',40,'Sí'),('H','Vans','Sk8-Hi',1999.00,'Botín clásico con detalles acolchados','25-31',40,'Sí'),('I','Skechers','Skech-Air Radiance',749.00,'Suela con cámara de aire para mayor confort','18-24',40,'Sí'),('M','Adidas','Superstar',2799.00,'Estilo icónico con detalles metálicos','22-29',40,'Sí'),('I','Skechers','Twinkle Toes',849.00,'Decorados con luces y brillos','17-23',40,'Sí'),('H','Adidas','Ultraboost 22',4999.00,'Máximo confort para correr largas distancias','25-32',40,'Sí');
/*!40000 ALTER TABLE `catalogo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `Usuario` varchar(50) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Cargo` enum('Admin','Usuario','Operador') NOT NULL DEFAULT 'Usuario',
  PRIMARY KEY (`Usuario`),
  UNIQUE KEY `Correo` (`Correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('Admin','Admin@gmail.com','Admin','Admin'),('Operador','Operador@gmail.com','Operador','Operador'),('Usuario','Usuario@gmail.com','Usuario','Usuario');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-21  9:41:37
