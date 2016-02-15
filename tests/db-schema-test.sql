DROP TABLE IF EXISTS `multi_keys`;
DROP TABLE IF EXISTS `test`;

CREATE TABLE `multi_keys` (`key1` int(11) NOT NULL, `key2` int(11) NOT NULL, PRIMARY KEY (`key1`,`key2`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `test` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(32) NOT NULL DEFAULT '', PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
INSERT INTO `test` VALUES (1,'Jack'),(2,'Anabelle'),(3,'Alicia'),(4,'Betty'),(5,'Zorba'),(6,'Katie'),(7,'Carusso'),(8,'Yen'),(9,'Bill'),(10,'Steve'),(11,'Kevin'),(12,'Louie'),(13,'Mitchell'),(14,'Jack'),(15,'Jack');