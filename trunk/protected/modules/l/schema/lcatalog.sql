-- phpMyAdmin SQL Dump

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `newhp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__Category`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Category` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(256) NOT NULL,
  `Alias` varchar(50) NOT NULL,
  `Description` varchar(1000) DEFAULT NULL,
  `LLeaf` int(11) NOT NULL,
  `RLeaf` int(11) NOT NULL,
  `Level` int(11) NOT NULL,
  `IsVisible` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Alias_UNIQUE` (`Alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__Good`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Good` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `producerId` int(11) NOT NULL,
  `Name` varchar(256) NOT NULL,
  `Description` varchar(1000) DEFAULT NULL,
  `IsVisible` tinyint(1) NOT NULL,
  `Moderate` enum('good','moderate','bad') NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `lcatalog__Good` ADD `creatorId` INT NOT NULL DEFAULT '0' ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__goods_mediafiles`
--

CREATE TABLE IF NOT EXISTS `lcatalog__goods_mediafiles` (
  `goodsId` int(11) NOT NULL,
  `mediafilesId` int(11) NOT NULL,
  PRIMARY KEY (`goodsId`,`mediafilesId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__mediafiles`
--

CREATE TABLE IF NOT EXISTS `lcatalog__mediafiles` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Number` int(11) NOT NULL,
  `Priority` int(11) NOT NULL,
  `TypeId` int(11) NOT NULL,
  `Name` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `Description` text CHARACTER SET cp1251 NOT NULL,
  `Previews` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__mediatypes`
--

CREATE TABLE IF NOT EXISTS `lcatalog__mediatypes` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Extension` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Postfix` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Url` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Description` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Method` enum('goods_thousands','all_together') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__prices`
--

CREATE TABLE IF NOT EXISTS `lcatalog__prices` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL COMMENT 'Связа с названием таблицы + владелец',
  `Price` float NOT NULL,
  `prlistsId` int(11) NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `parserId` int(11) NOT NULL,
  `DateTime` datetime DEFAULT NULL,
  `Status` enum('0','1','2') NOT NULL,
  `YML` enum('0','1') NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `goodsId` (`goodsId`),
  KEY `shopId` (`shopId`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrListValue`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrListValue` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `propertyId` int(11) NOT NULL,
  `Name` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__Property`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Property` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `Name` varchar(256) NOT NULL,
  `Alias` varchar(50) NOT NULL,
  `Type` enum('list','float','bit','text') NOT NULL,
  `IsVisible` tinyint(1) NOT NULL DEFAULT '1',
  `IsMultivalued` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Can a good with this property have multiple values',
  `Unit` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Alias_UNIQUE` (`Alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `lcatalog__Property` ADD `IsCustom` INT( 1 ) NOT NULL COMMENT 'Настраеваемая характеристика' AFTER `IsMultivalued` ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrvalueBit`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrvalueBit` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `Value` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrvalueEntity`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrvalueEntity` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodId` int(11) NOT NULL,
  `relationId` int(11) NOT NULL,
  `entityId` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrvalueFloat`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrvalueFloat` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `Value` float NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrvalueList`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrvalueList` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `valueId` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__PrvalueText`
--

CREATE TABLE IF NOT EXISTS `lcatalog__PrvalueText` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `goodId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `Value` varchar(1000) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__Relation`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Relation` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `entityCategoryId` int(11) NOT NULL,
  `Alias` varchar(50) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `categoryId` (`categoryId`,`entityCategoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__Shop`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Shop` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` longtext NOT NULL,
  `Type` enum('first','second','third') NOT NULL DEFAULT 'first',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Структура таблицы `lcatalog__Shop_User`
--

CREATE TABLE IF NOT EXISTS `lcatalog__Shop_User` (
  `userId` int(11) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`shopId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__FilterOption`
--

CREATE TABLE IF NOT EXISTS `lcatalog__FilterOption` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Template` text NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `categoryId` (`categoryId`,`Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Структура таблицы `lcatalog__offers`
--

CREATE TABLE IF NOT EXISTS `lcatalog__offers` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(256) NOT NULL,
  `Description` varchar(2000) NOT NULL,
  `FullDescription` longtext NOT NULL,
  `Start` date NOT NULL,
  `Finish` date NOT NULL,
  `Icon` varchar(2000) NOT NULL,
  `shopId` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lcatalog__offer_price`
--

CREATE TABLE IF NOT EXISTS `lcatalog__offer_price` (
  `priceId` int(11) NOT NULL,
  `offerId` int(11) NOT NULL,
  `Price` float NOT NULL,
  PRIMARY KEY (`priceId`,`offerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
