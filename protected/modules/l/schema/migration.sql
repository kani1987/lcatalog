-- migration from old structure to a new one
-- don't forget to set query separator to '//'
--
-- -------------------------------------------------------------
--
-- list of tables still needed in an old site after migration:
--
-- reestrclient (12):
--
-- for cms:
-- reestrclient__categories
-- reestrclient__categoriestree
-- reestrclient__goods
-- reestrclient__prlists
-- reestrclient__properties
-- reestrclient__prvaluesfloat
-- reestrclient__prvalueslist
-- reestrclient__prvaluestext
--
-- for news system:
-- reestrclient__eurobathnews
-- reestrclient__newdoornews
-- reestrclient__newsofianews
-- reestrclient__sanmaxnews
--
-- -------------------------------------------------------------

DROP PROCEDURE IF EXISTS migrate//
CREATE PROCEDURE migrate()
BEGIN
	-- выбираем всех детей категории homeprice.ru
	INSERT INTO lcatalog__Category SELECT
		c.Id AS Id,
		c.Name AS Name,
		CONCAT(translit(c.Name),'_',c.Id) AS Alias,
		c.Description AS Description,
		ctree.`Left` AS LLeaf,
		ctree.`Right` AS RLeaf,
		ctree.`Level` AS `Level`,
		c.IsVisible AS IsVisible
	FROM reestrclient__categories AS c
	INNER JOIN reestrclient__categoriestree AS ctree ON ctree.Id=c.Id
	WHERE ctree.`Left` >= 1 AND ctree.`Right` <= 1020;
	
	
	-- наполняем товарами новые категории
	INSERT INTO lcatalog__Good SELECT
		g.Id AS Id,
		g.categoriesId AS categoryId,
		g.producersId AS producerId,
		g.Name AS Name,
		g.Description AS Description,
		g.IsVisible AS IsVisible,
		g.Moderated AS Moderated,
		g.Date AS createDate,
		NULL AS updateDate,
		CONCAT(CURDATE(),' ',CURTIME()) AS updateDate
	FROM reestrclient__goods AS g
	INNER JOIN lcatalog__Category AS c ON c.Id = g.categoriesId;
	
	-- выцепляем характеристики
	INSERT INTO lcatalog__Property SELECT
		prop.Id AS Id,
		prop.categoriesId AS categoryId,
		prop.Name AS Name,
		CONCAT(translit(prop.Name),'_',prop.Id) AS Alias,
		prop.Type AS Type,
		prop.IsVisible AS IsVisible,
		0 AS IsMultivalued,
		0 AS IsCustom,
		prop.Unit AS Unit
	FROM lcatalog__Category AS c
	INNER JOIN reestrclient__properties AS prop ON prop.categoriesId = c.Id;
	
	
	-- выцепляем значения характеристикам типа bit
	INSERT INTO lcatalog__PrvalueBit SELECT
		bit.Id AS Id,
		bit.goodsId AS goodId,
		bit.propertiesId AS propertyId,
		bit.value AS Value
	FROM reestrclient__prvaluesbit AS bit;
	
	-- выцепляем значения характеристикам типа float
	INSERT INTO lcatalog__PrvalueFloat SELECT
		f.Id AS Id,
		f.goodsId AS goodId,
		f.propertiesId AS propertyId,
		f.value AS Value
	FROM reestrclient__prvaluesfloat AS f;
	
	-- выцепляем значения характеристикам типа text
	INSERT INTO lcatalog__PrvalueText SELECT
		t.Id AS Id,
		t.goodsId AS goodId,
		t.propertiesId AS propertyId,
		t.value AS Value
	FROM reestrclient__prvaluestext AS t;
		
	-- выцепляем значения характеристикам типа list
	INSERT INTO lcatalog__PrvalueList SELECT
		lv.Id AS Id,
		lv.goodsId AS goodId,
		lv.propertiesId AS propertyId,
		lv.prlistsId AS valueId
	FROM reestrclient__prvalueslist AS lv;
	
	-- выцепляем возможные значения list'овых характеристик
	INSERT INTO lcatalog__PrListValue SELECT
		prl.Id AS Id,
		prl.propertiesId AS propertyId,
		prl.Name AS Name
	FROM reestrclient__prlists AS prl;
	
	-- выцепляем медиафайлы и их связи с товарами
	INSERT INTO lcatalog__mediafiles       SELECT * FROM reestrclient__mediafiles;
	INSERT INTO lcatalog__goods_mediafiles SELECT * FROM reestrclient__goods_mediafiles;
	INSERT INTO lcatalog__mediatypes       SELECT * FROM reestrclient__mediatypes;
	
	-- создаем основные "сущности"
	INSERT INTO lcatalog__Category VALUES
		(1,'Каталог'      ,'Catalog_1',NULL,0   ,1029,-1,2),
		(2,'Сущности'     ,'Entities' ,NULL,1021,1028,0,2),
		(3,'Производители','Producer' ,NULL,1022,1023,1,2),
		(4,'Серии'        ,'Seria'    ,NULL,1024,1025,1,2),
		(5,'Цвета'        ,'Color'    ,NULL,1026,1027,1,2)
	;
	
	-- вставляем производителей
	INSERT IGNORE INTO lcatalog__Good SELECT 
		producer.Id AS Id,
		3 AS categoryId,
		NULL AS producerId,
		producer.Name AS Name,
		producer.Description AS Description,
		producer.IsVisible AS IsVisible,
		producer.Moderated AS Moderate,
		producer.Date AS createDate,
		NULL AS updateDate,
		CONCAT(CURDATE(),' ',CURTIME()) AS updateDate
	FROM reestrclient__goods AS producer
	INNER JOIN reestrclient__goods AS g ON g.producersId=producer.Id
	INNER JOIN lcatalog__Category AS cat ON cat.Id=g.categoriesId;
	
	-- выцепляем существующие связи между категориями сущностей
	INSERT INTO lcatalog__Relation SELECT
		rel.Id AS Id,
		rel.categoriesId1 AS categoryId,
		rel.categoriesId2 AS entityCategoryId,
		CONCAT(translit(rel.Name),'_',rel.Id) AS Alias,
		rel.Name AS Description
	FROM reestrclient__relations AS rel;
	
	-- меняем id категорий "Цвета","Серии"
	UPDATE lcatalog__Relation SET entityCategoryId = 4 WHERE entityCategoryId = 110222;
	UPDATE lcatalog__Relation SET entityCategoryId = 5 WHERE entityCategoryId = 110221;
	
	-- наполняем товарами категории сущностей "Цвета","Серии"
	INSERT IGNORE INTO lcatalog__Good SELECT
		g.Id AS Id,
		g.categoriesId AS categoryId,
		g.producersId AS producerId,
		g.Name AS Name,
		g.Description AS Description,
		g.IsVisible AS IsVisible,
		g.Moderated AS Moderated,
		g.Date AS createDate,
		NULL AS updateDate,
		CONCAT(CURDATE(),' ',CURTIME()) AS updateDate
	FROM reestrclient__goods AS g
	WHERE g.categoriesId IN (110221,110222);

	-- категории "серии" и "цвета" передвинулись, нужно и у товаров это отметить
	UPDATE lcatalog__Good SET categoryId = 4 WHERE categoryId = 110222;
	UPDATE lcatalog__Good SET categoryId = 5 WHERE categoryId = 110221;
	
	
	-- выцепляем существующие связи с сущностями
	INSERT INTO lcatalog__PrvalueEntity SELECT
		NULL AS Id,
		gr.goodsId1 AS goodId,
		gr.relationsId AS relationId,
		gr.goodsId2 AS entityId
	FROM reestrclient__goods_relations AS gr
	WHERE gr.goodsId1 > 0 AND gr.goodsId2 > 0;
	
	
	-- выцепляем цены
	INSERT INTO lcatalog__prices SELECT
		Id,goodsId,shopId,Price,prlistsId,url,parserId,DateTime,Status,YML
	FROM reestrclient__prices;
	
	
	-- выцепляем спецпредложения магазиновы
	INSERT INTO lcatalog__offers      SELECT * FROM reestrclient__offers;
	INSERT INTO lcatalog__offer_price SELECT * FROM reestrclient__offer_price;
END//
	


DROP FUNCTION IF EXISTS translit//
CREATE FUNCTION translit(word VARCHAR(50)) RETURNS VARCHAR(50)
BEGIN
RETURN replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(
replace(word
,'А','A')
,'Б','B')
,'В','V')
,'Г','G')
,'Д','D')
,'Е','E')
,'Ё','Yo')
,'Ж','Zh')
,'З','Z')
,'И','I')
,'Й','Y')
,'К','K')
,'Л','L')
,'М','M')
,'Н','N')
,'О','O')
,'П','P')
,'Р','R')
,'С','S')
,'Т','T')
,'У','U')
,'Ф','F')
,'Х','Kh')
,'Ц','Ts')
,'Ч','Ch')
,'Ш','Sh')
,'Щ','Sch')
,'Ъ','')
,'Ы','Y')
,'Ь','')
,'Э','E')
,'Ю','Yu')
,'Я','Ya')
,'а','a')
,'б','b')
,'в','v')
,'г','g')
,'д','d')
,'е','e')
,'ё','yo')
,'ж','zh')
,'з','z')
,'и','i')
,'й','y')
,'к','k')
,'л','l')
,'м','m')
,'н','n')
,'о','o')
,'п','p')
,'р','r')
,'с','s')
,'т','t')
,'у','u')
,'ф','f')
,'х','kh')
,'ц','ts')
,'ч','ch')
,'ш','sh')
,'щ','sch')
,'ъ','')
,'ы','y')
,'ь','')
,'э','e')
,'ю','yu')
,'я','ya')
,' ','');
END//
