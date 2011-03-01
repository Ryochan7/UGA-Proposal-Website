SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `uga_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `uga_test` ;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `userType` TINYINT(1) NOT NULL ,
  `userName` VARCHAR(45) NOT NULL ,
  `passHash` VARCHAR(45) NOT NULL ,
  `ulid` VARCHAR(45) NOT NULL ,
  `status` TINYINT(1) NOT NULL ,
  `steamId` VARCHAR(45) NULL ,
  `xboxId` VARCHAR(45) NULL ,
  `psnId` VARCHAR(45) NULL ,
  `wiiId` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `userName_UNIQUE` (`userName` ASC) ,
  UNIQUE INDEX `ulid_UNIQUE` (`ulid` ASC) ,
  INDEX `userName_INDEX` (`userName` ASC) ,
  INDEX `status_INDEX` (`status` ASC) ,
  INDEX `steamId_INDEX` (`steamId` ASC) ,
  INDEX `xboxId_INDEX` (`xboxId` ASC) ,
  INDEX `psnId_INDEX` (`psnId` ASC) ,
  INDEX `wiiId_INDEX` (`wiiId` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','admin',3,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



-- -----------------------------------------------------
-- Table `platform`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `platform` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `events` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `userId` INT NOT NULL ,
  `platformId` INT NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `description` TEXT NOT NULL ,
  `sanctioned` TINYINT(1)  NOT NULL ,
  `date` INT NOT NULL ,
  `status` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`id`, `userId`, `platformId`) ,
  INDEX `fk_events_users1` (`userId` ASC) ,
  INDEX `fk_events_Platform1` (`platformId` ASC) ,
  INDEX `sanctioned_INDEX` (`sanctioned` ASC) ,
  INDEX `date_INDEX` (`date` ASC) ,
  INDEX `status_INDEX` (`status` ASC) ,
  CONSTRAINT `fk_events_users1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_Platform1`
    FOREIGN KEY (`platformId` )
    REFERENCES `platform` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `attendance`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `attendance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `userId` INT(11) NOT NULL ,
  `eventId` INT(11) NOT NULL ,
  PRIMARY KEY (`id`, `userId`, `eventId`) ,
  INDEX `fk_attendance_users1` (`userId` ASC) ,
  INDEX `fk_attendance_events1` (`eventId` ASC) ,
  CONSTRAINT `fk_attendance_users1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_attendance_events1`
    FOREIGN KEY (`eventId` )
    REFERENCES `events` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `articles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `articles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `userId` INT NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `content` TEXT NOT NULL ,
  `postDate` INT NOT NULL ,
  `updateDate` INT NOT NULL ,
  `published` TINYINT(1)  NOT NULL ,
  `tags` VARCHAR(200) NOT NULL ,
  PRIMARY KEY (`id`, `userId`) ,
  INDEX `fk_article_users1` (`userId` ASC) ,
  INDEX `postDate_INDEX` (`postDate` ASC) ,
  INDEX `updateDate_INDEX` (`updateDate` ASC) ,
  INDEX `published_INDEX` (`published` ASC) ,
  CONSTRAINT `fk_article_users1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pages` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `userId` INT NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `content` TEXT NOT NULL ,
  `template` VARCHAR(255) NOT NULL ,
  `published` TINYINT(1)  NOT NULL ,
  PRIMARY KEY (`id`, `userId`) ,
  INDEX `fk_pages_users1` (`userId` ASC) ,
  INDEX `published_INDEX` (`published` ASC) ,
  CONSTRAINT `fk_pages_users1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `authToken`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `authToken` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `userId` INT NOT NULL ,
  `token` VARCHAR(45) NOT NULL ,
  `expireTime` INT NOT NULL ,
  PRIMARY KEY (`id`, `userId`) ,
  INDEX `fk_authToken_users1` (`userId` ASC) ,
  INDEX `expireTime` (`expireTime` ASC) ,
  INDEX `token_INDEX` (`token` ASC) ,
  CONSTRAINT `fk_authToken_users1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `articleTag`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `articleTag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `taggedArticle`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `taggedArticle` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `articleId` INT NOT NULL ,
  `tagId` INT NOT NULL ,
  PRIMARY KEY (`id`, `tagId`, `articleId`) ,
  INDEX `fk_taggedArticle_articles1` (`articleId` ASC) ,
  INDEX `fk_taggedArticle_articleTag1` (`tagId` ASC) ,
  CONSTRAINT `fk_taggedArticle_articles1`
    FOREIGN KEY (`articleId` )
    REFERENCES `articles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_taggedArticle_articleTag1`
    FOREIGN KEY (`tagId` )
    REFERENCES `articleTag` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `albums`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `albums` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `photos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `photos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `albumId` INT(11) NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `description` TEXT NOT NULL ,
  `fileLoc` VARCHAR(255) NOT NULL ,
  `thumbLoc` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`, `albumId`) ,
  INDEX `fk_photos_albums1` (`albumId` ASC) ,
  INDEX `title_INDEX` (`title` ASC) ,
  CONSTRAINT `fk_photos_albums1`
    FOREIGN KEY (`albumId` )
    REFERENCES `albums` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
