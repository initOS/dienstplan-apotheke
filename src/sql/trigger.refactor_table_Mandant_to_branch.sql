CREATE DEFINER=`root`@`localhost` TRIGGER `refactor_table_Mandant_to_branch` AFTER INSERT ON `Mandant` FOR EACH ROW INSERT INTO `branch` SELECT * FROM INSERTED