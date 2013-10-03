-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 28, 2013 at 08:03 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.6-13ubuntu3.9

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ChurchDoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `baptism`
--

DROP TABLE IF EXISTS `baptism`;
CREATE TABLE IF NOT EXISTS `baptism` (
  `baptismId` varchar(50) NOT NULL,
  `parishId` int(11) NOT NULL,
  `baptizeeId` varchar(50) NOT NULL,
  `dateOfBaptism` date NOT NULL,
  `fatherId` varchar(50) NOT NULL,
  `motherId` varchar(50) NOT NULL,
  `godFatherName` varchar(50) NOT NULL,
  `godFatherAddress` varchar(250) NOT NULL,
  `godMotherName` varchar(50) NOT NULL,
  `godMotherAddress` varchar(250) NOT NULL,
  `minister` varchar(50) NOT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`baptismId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Holds baptism records';

-- --------------------------------------------------------

--
-- Table structure for table `burrial`
--

DROP TABLE IF EXISTS `burrial`;
CREATE TABLE IF NOT EXISTS `burrial` (
  `burrialId` varchar(20) NOT NULL DEFAULT '',
  `dateOfBurrial` date NOT NULL,
  `personId` varchar(20) NOT NULL,
  `parishId` int(11) NOT NULL,
  `domicile` varchar(50) NOT NULL,
  `causeOfDeath` varchar(200) NOT NULL,
  `dateOfDeath` date NOT NULL,
  `placeOfBurrial` varchar(50) NOT NULL,
  `minister` varchar(50) NOT NULL,
  `remarks` varchar(200) NOT NULL,
  PRIMARY KEY (`burrialId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='burrial Data';

-- --------------------------------------------------------

--
-- Table structure for table `conformation`
--

DROP TABLE IF EXISTS `conformation`;
CREATE TABLE IF NOT EXISTS `conformation` (
  `conformationId` int(11) NOT NULL AUTO_INCREMENT,
  `personId` varchar(50) NOT NULL,
  `parishId` int(11) NOT NULL,
  `minister` varchar(50) NOT NULL,
  PRIMARY KEY (`conformationId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

DROP TABLE IF EXISTS `family`;
CREATE TABLE IF NOT EXISTS `family` (
  `familyId` varchar(50) NOT NULL,
  `houseNo` varchar(50) NOT NULL,
  `addressLine1` varchar(50) NOT NULL,
  `addressLine2` varchar(50) DEFAULT NULL,
  `ward` varchar(50) NOT NULL,
  `parishId` int(11) NOT NULL,
  `originParish` varchar(50) NOT NULL,
  `contactNumber` varchar(12) NOT NULL,
  PRIMARY KEY (`familyId`),
  KEY `fk_family_parish` (`parishId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores family details.';

-- --------------------------------------------------------

--
-- Table structure for table `LinkedMarriage`
--

DROP TABLE IF EXISTS `LinkedMarriage`;
CREATE TABLE IF NOT EXISTS `LinkedMarriage` (
  `marriageId` varchar(50) NOT NULL,
  `groomId` varchar(50) DEFAULT NULL,
  `brideId` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`marriageId`),
  UNIQUE KEY `groomId` (`groomId`),
  UNIQUE KEY `brideId` (`brideId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores Marriage Links';

-- --------------------------------------------------------

--
-- Table structure for table `marriage`
--

DROP TABLE IF EXISTS `marriage`;
CREATE TABLE IF NOT EXISTS `marriage` (
  `marriageId` varchar(50) NOT NULL,
  `parishId` int(11) NOT NULL,
  `marriageDate` date NOT NULL,
  `groomName` varchar(50) NOT NULL,
  `groomSurname` varchar(50) NOT NULL,
  `groomFatherName` varchar(50) NOT NULL,
  `groomMotherName` varchar(50) NOT NULL,
  `groomAge` int(3) NOT NULL,
  `groomDomicile` varchar(50) NOT NULL,
  `groomProfession` varchar(50) NOT NULL,
  `groomStatus` varchar(50) NOT NULL,
  `brideName` varchar(50) NOT NULL,
  `brideSurname` varchar(50) NOT NULL,
  `brideFatherName` varchar(50) NOT NULL,
  `brideMotherName` varchar(50) NOT NULL,
  `brideAge` int(3) NOT NULL,
  `brideDomicile` varchar(50) NOT NULL,
  `brideProfession` varchar(50) NOT NULL,
  `brideStatus` varchar(50) NOT NULL,
  `exHusbandName` varchar(50) DEFAULT NULL,
  `witness1Name` varchar(50) NOT NULL,
  `witness1Surname` varchar(50) NOT NULL,
  `witness1Domicile` varchar(50) NOT NULL,
  `witness2Name` varchar(50) NOT NULL,
  `witness2Surname` varchar(50) NOT NULL,
  `witness2Domicile` varchar(50) NOT NULL,
  `minister` varchar(50) NOT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`marriageId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores Marriage details';

-- --------------------------------------------------------

--
-- Table structure for table `parish`
--

DROP TABLE IF EXISTS `parish`;
CREATE TABLE IF NOT EXISTS `parish` (
  `parishId` int(11) NOT NULL AUTO_INCREMENT,
  `parishName` varchar(200) NOT NULL,
  `locality` varchar(50) NOT NULL,
  `dioceseId` int(11) NOT NULL,
  PRIMARY KEY (`parishId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='stores parish details' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `personId` varchar(50) NOT NULL,
  `familyId` varchar(50) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `middleName` varchar(20) DEFAULT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `gender` varchar(1) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `registeredStages` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Registration bit masking.',
  `profession` varchar(50) DEFAULT NULL,
  `nationality` varchar(50) NOT NULL,
  PRIMARY KEY (`personId`),
  KEY `fk_person_family` (`familyId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='person Information';

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `userName` varchar(20) NOT NULL,
  `pssword` varchar(512) NOT NULL,
  `permissions` int(11) unsigned DEFAULT NULL COMMENT 'Permission Bit masking.',
  `displayName` varchar(30) DEFAULT NULL,
  `parishId` int(11) DEFAULT NULL,
  `addedBy` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`userName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Login user details';

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_baptismRecord`
--
DROP VIEW IF EXISTS `vw_baptismRecord`;
CREATE TABLE IF NOT EXISTS `vw_baptismRecord` (
`baptismId` varchar(50)
,`baptizeeId` varchar(50)
,`parishId` int(11)
,`dateOfBaptism` date
,`dateOfBirth` date
,`firstName` varchar(20)
,`middleName` varchar(20)
,`lastName` varchar(20)
,`fathersName` varchar(62)
,`mothersName` varchar(62)
,`fathersNationality` varchar(50)
,`fathersAddress` varchar(206)
,`fathersProfession` varchar(50)
,`godFatherName` varchar(50)
,`godFatherAddress` varchar(250)
,`godMotherName` varchar(50)
,`godMotherAddress` varchar(250)
,`minister` varchar(50)
,`placeOfBaptism` varchar(252)
,`registeredStages` int(10) unsigned
,`remarks` varchar(200)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_personDetail`
--
DROP VIEW IF EXISTS `vw_personDetail`;
CREATE TABLE IF NOT EXISTS `vw_personDetail` (
`parishId` int(11)
,`personId` varchar(50)
,`familyId` varchar(50)
,`firstName` varchar(20)
,`middleName` varchar(20)
,`lastName` varchar(20)
,`fathersName` varchar(62)
,`mothersName` varchar(62)
,`dateOfBirth` date
,`gender` varchar(1)
,`profession` varchar(50)
);
-- --------------------------------------------------------

--
-- Table structure for table `Ward`
--

DROP TABLE IF EXISTS `Ward`;
CREATE TABLE IF NOT EXISTS `Ward` (
  `WardId` varchar(50) NOT NULL,
  `WardName` varchar(50) NOT NULL,
  `parishId` int(11) NOT NULL,
  PRIMARY KEY (`WardId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Ward information of a Parish';

-- --------------------------------------------------------

--
-- Structure for view `vw_baptismRecord`
--
DROP TABLE IF EXISTS `vw_baptismRecord`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_baptismRecord` AS select `baptism`.`baptismId` AS `baptismId`,`baptism`.`baptizeeId` AS `baptizeeId`,`baptism`.`parishId` AS `parishId`,`baptism`.`dateOfBaptism` AS `dateOfBaptism`,`bap`.`dateOfBirth` AS `dateOfBirth`,`bap`.`firstName` AS `firstName`,`bap`.`middleName` AS `middleName`,`bap`.`lastName` AS `lastName`,concat(`fa`.`firstName`,' ',`fa`.`middleName`,' ',`fa`.`lastName`) AS `fathersName`,concat(`mo`.`firstName`,' ',`mo`.`middleName`,' ',`mo`.`lastName`) AS `mothersName`,`fa`.`nationality` AS `fathersNationality`,concat(`family`.`houseNo`,', ',`family`.`addressLine1`,',\n',`family`.`addressLine2`,', ',`Ward`.`WardName`) AS `fathersAddress`,`fa`.`profession` AS `fathersProfession`,`baptism`.`godFatherName` AS `godFatherName`,`baptism`.`godFatherAddress` AS `godFatherAddress`,`baptism`.`godMotherName` AS `godMotherName`,`baptism`.`godMotherAddress` AS `godMotherAddress`,`baptism`.`minister` AS `minister`,concat(`parish`.`parishName`,', ',`parish`.`locality`) AS `placeOfBaptism`,`bap`.`registeredStages` AS `registeredStages`,`baptism`.`remarks` AS `remarks` from ((((((`baptism` join `person` `bap`) join `person` `fa`) join `person` `mo`) join `family`) join `Ward`) join `parish`) where ((`baptism`.`parishId` = `parish`.`parishId`) and (`fa`.`familyId` = `family`.`familyId`) and (`family`.`ward` = `Ward`.`WardId`) and (`fa`.`personId` = `baptism`.`fatherId`) and (`mo`.`personId` = `baptism`.`motherId`) and (`bap`.`personId` = `baptism`.`baptizeeId`));

-- --------------------------------------------------------

--
-- Structure for view `vw_personDetail`
--
DROP TABLE IF EXISTS `vw_personDetail`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_personDetail` AS select `family`.`parishId` AS `parishId`,`person`.`personId` AS `personId`,`person`.`familyId` AS `familyId`,`person`.`firstName` AS `firstName`,`person`.`middleName` AS `middleName`,`person`.`lastName` AS `lastName`,ifnull(`vw_baptismRecord`.`fathersName`,'') AS `fathersName`,ifnull(`vw_baptismRecord`.`mothersName`,'') AS `mothersName`,`person`.`dateOfBirth` AS `dateOfBirth`,`person`.`gender` AS `gender`,`person`.`profession` AS `profession` from ((`person` left join `vw_baptismRecord` on((`person`.`personId` = `vw_baptismRecord`.`baptizeeId`))) left join `family` on((`person`.`familyId` = `family`.`familyId`)));
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

INSERT INTO  `ChurchDoc`.`parish` (
`parishId` ,
`parishName` ,
`locality` ,
`dioceseId`
)
VALUES (
'0',  'Admin Parish',  'Admin Locality',  '0'
);
