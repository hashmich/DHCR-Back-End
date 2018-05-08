-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.7.20-0ubuntu0.16.04.1 - (Ubuntu)
-- Server Betriebssystem:        Linux
-- HeidiSQL Version:             9.3.0.5121
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle dhregistry.cc_config_acos_aros
DROP TABLE IF EXISTS `cc_config_acos_aros`;
CREATE TABLE IF NOT EXISTS `cc_config_acos_aros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_config_menu_id` int(11) NOT NULL COMMENT 'the aco foreignkey',
  `aro_key_value` int(11) NOT NULL,
  `aro_key_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the field name of the used Aro Object (eg. user_role_id)',
  PRIMARY KEY (`id`),
  KEY `aro_key_name` (`aro_key_name`),
  KEY `aro_key_value` (`aro_key_value`),
  KEY `FK_cc_config_acos_aros_cc_config_menus` (`cc_config_menu_id`),
  CONSTRAINT `FK_cc_config_acos_aros_cc_config_menus` FOREIGN KEY (`cc_config_menu_id`) REFERENCES `cc_config_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table makes connection between the access controlled objects (ACOs) and the access requesting objects (AROs), which are only virtually bound to this table by means of a complex key. This is, to keep the schema flexible enough to connect to AROs in various manners, eg. individual users or to user groups/roles by either the User.id field or User.user_role_id field..\r\nThe ACO is the top-level object on a menu hierarchy, a menu group item, which in turn is connected itself to either all available actions for a table (or controller) or individual actions.';

-- Exportiere Daten aus Tabelle dhregistry.cc_config_acos_aros: ~2 rows (ungefähr)
/*!40000 ALTER TABLE `cc_config_acos_aros` DISABLE KEYS */;
INSERT INTO `cc_config_acos_aros` (`id`, `cc_config_menu_id`, `aro_key_value`, `aro_key_name`) VALUES
	(5, 11, 1, 'user_role_id'),
	(6, 12, 1, 'user_role_id'),
	(7, 14, 2, 'user_role_id');
/*!40000 ALTER TABLE `cc_config_acos_aros` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle dhregistry.cc_config_actions
DROP TABLE IF EXISTS `cc_config_actions`;
CREATE TABLE IF NOT EXISTS `cc_config_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_config_table_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The final Auth-check is performed against this path. Consider possible plugin routes here.',
  `url_pattern` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the pattern to check the current request url against, if access will be allowed',
  `html_options` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'a JSON array of html options for the CakePhp Html.link method',
  `contextual` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Makes clear if the action belongs into a record''s context or not. If yes, the record identifier will be appended, if bulk_processing ability is false. If not, it will also appear in more general areas such as the main menu or on top of an index view.',
  `has_form` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the view contains a form, option lists will be generated from related models.',
  `bulk_processing` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the action is contextual, if it accepts a POSTed array of record IDs for bulk processing or not.',
  `has_view` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If the action does not have a view, it cannot have child-actions that appear in the view. ',
  `comment` text COLLATE utf8_unicode_ci,
  `position` int(3) DEFAULT NULL COMMENT 'Consider this as a default positioning',
  `controller_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin_app_override` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_cc_config_actions_cc_config_tables` (`cc_config_table_id`),
  CONSTRAINT `FK_cc_config_actions_cc_config_tables` FOREIGN KEY (`cc_config_table_id`) REFERENCES `cc_config_tables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Daten aus Tabelle dhregistry.cc_config_actions: ~170 rows (ungefähr)
/*!40000 ALTER TABLE `cc_config_actions` DISABLE KEYS */;
INSERT INTO `cc_config_actions` (`id`, `cc_config_table_id`, `name`, `label`, `url`, `url_pattern`, `html_options`, `contextual`, `has_form`, `bulk_processing`, `has_view`, `comment`, `position`, `controller_name`, `plugin_name`, `plugin_app_override`) VALUES
	(518, 126, 'index', 'List', '/db-webclient/cc_config_acos_aros/index', '/db-webclient/cc_config_acos_aros/index', NULL, 0, 0, 0, 1, NULL, 1, 'CcConfigAcosArosController', 'Cakeclient', 0),
	(519, 126, 'add', 'Add', '/db-webclient/cc_config_acos_aros/add', '/db-webclient/cc_config_acos_aros/add', NULL, 0, 1, 0, 1, NULL, 2, 'CcConfigAcosArosController', 'Cakeclient', 0),
	(520, 126, 'view', 'View', '/db-webclient/cc_config_acos_aros/view', '/db-webclient/cc_config_acos_aros/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CcConfigAcosArosController', 'Cakeclient', 0),
	(521, 126, 'edit', 'Edit', '/db-webclient/cc_config_acos_aros/edit', '/db-webclient/cc_config_acos_aros/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CcConfigAcosArosController', 'Cakeclient', 0),
	(522, 126, 'delete', 'Delete', '/db-webclient/cc_config_acos_aros/delete', '/db-webclient/cc_config_acos_aros/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CcConfigAcosArosController', 'Cakeclient', 0),
	(523, 127, 'index', 'List', '/db-webclient/cc_config_actions/index', '/db-webclient/cc_config_actions/index', NULL, 0, 0, 0, 1, NULL, 1, 'CcConfigActionsController', 'Cakeclient', 0),
	(524, 127, 'add', 'Add', '/db-webclient/cc_config_actions/add', '/db-webclient/cc_config_actions/add', NULL, 0, 1, 0, 1, NULL, 2, 'CcConfigActionsController', 'Cakeclient', 0),
	(525, 127, 'view', 'View', '/db-webclient/cc_config_actions/view', '/db-webclient/cc_config_actions/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CcConfigActionsController', 'Cakeclient', 0),
	(526, 127, 'edit', 'Edit', '/db-webclient/cc_config_actions/edit', '/db-webclient/cc_config_actions/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CcConfigActionsController', 'Cakeclient', 0),
	(527, 127, 'delete', 'Delete', '/db-webclient/cc_config_actions/delete', '/db-webclient/cc_config_actions/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CcConfigActionsController', 'Cakeclient', 0),
	(528, 128, 'index', 'List', '/db-webclient/cc_config_fielddefinitions/index', '/db-webclient/cc_config_fielddefinitions/index', NULL, 0, 0, 0, 1, NULL, 1, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(529, 128, 'add', 'Add', '/db-webclient/cc_config_fielddefinitions/add', '/db-webclient/cc_config_fielddefinitions/add', NULL, 0, 1, 0, 1, NULL, 2, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(530, 128, 'view', 'View', '/db-webclient/cc_config_fielddefinitions/view', '/db-webclient/cc_config_fielddefinitions/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(531, 128, 'edit', 'Edit', '/db-webclient/cc_config_fielddefinitions/edit', '/db-webclient/cc_config_fielddefinitions/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(532, 128, 'delete', 'Delete', '/db-webclient/cc_config_fielddefinitions/delete', '/db-webclient/cc_config_fielddefinitions/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(533, 128, 'reset_order', 'Reset Order', '/db-webclient/cc_config_fielddefinitions/reset_order', '/db-webclient/cc_config_fielddefinitions/reset_order', NULL, 0, 0, 1, 0, NULL, 6, 'CcConfigFielddefinitionsController', 'Cakeclient', 0),
	(534, 129, 'index', 'List', '/db-webclient/cc_config_menus/index', '/db-webclient/cc_config_menus/index', NULL, 0, 0, 0, 1, NULL, 1, 'CcConfigMenusController', 'Cakeclient', 0),
	(535, 129, 'add', 'Add', '/db-webclient/cc_config_menus/add', '/db-webclient/cc_config_menus/add', NULL, 0, 1, 0, 1, NULL, 2, 'CcConfigMenusController', 'Cakeclient', 0),
	(536, 129, 'view', 'View', '/db-webclient/cc_config_menus/view', '/db-webclient/cc_config_menus/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CcConfigMenusController', 'Cakeclient', 0),
	(537, 129, 'edit', 'Edit', '/db-webclient/cc_config_menus/edit', '/db-webclient/cc_config_menus/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CcConfigMenusController', 'Cakeclient', 0),
	(538, 129, 'delete', 'Delete', '/db-webclient/cc_config_menus/delete', '/db-webclient/cc_config_menus/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CcConfigMenusController', 'Cakeclient', 0),
	(539, 129, 'create_default_trees', 'Create Default Trees', '/db-webclient/cc_config_menus/create_default_trees', '/db-webclient/cc_config_menus/create_default_trees', NULL, 0, 0, 1, 0, NULL, 6, 'CcConfigMenusController', 'Cakeclient', 0),
	(540, 129, 'add_aco', 'Add Aco', '/db-webclient/cc_config_menus/add_aco', '/db-webclient/cc_config_menus/add_aco/+', NULL, 1, 0, 0, 0, NULL, 7, 'CcConfigMenusController', 'Cakeclient', 0),
	(541, 130, 'index', 'List', '/db-webclient/cc_config_tables/index', '/db-webclient/cc_config_tables/index', NULL, 0, 0, 0, 1, NULL, 1, 'CcConfigTablesController', 'Cakeclient', 0),
	(542, 130, 'add', 'Add', '/db-webclient/cc_config_tables/add', '/db-webclient/cc_config_tables/add', NULL, 0, 1, 0, 1, NULL, 2, 'CcConfigTablesController', 'Cakeclient', 0),
	(543, 130, 'view', 'View', '/db-webclient/cc_config_tables/view', '/db-webclient/cc_config_tables/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CcConfigTablesController', 'Cakeclient', 0),
	(544, 130, 'edit', 'Edit', '/db-webclient/cc_config_tables/edit', '/db-webclient/cc_config_tables/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CcConfigTablesController', 'Cakeclient', 0),
	(545, 130, 'delete', 'Delete', '/db-webclient/cc_config_tables/delete', '/db-webclient/cc_config_tables/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CcConfigTablesController', 'Cakeclient', 0),
	(546, 130, 'reset_order', 'Reset Order', '/db-webclient/cc_config_tables/reset_order', '/db-webclient/cc_config_tables/reset_order', NULL, 0, 0, 1, 0, NULL, 6, 'CcConfigTablesController', 'Cakeclient', 0),
	(547, 131, 'index', 'List', '/db-webclient/cities/index', '/db-webclient/cities/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(548, 131, 'add', 'Add', '/db-webclient/cities/add', '/db-webclient/cities/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(549, 131, 'view', 'View', '/db-webclient/cities/view', '/db-webclient/cities/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(550, 131, 'edit', 'Edit', '/db-webclient/cities/edit', '/db-webclient/cities/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(551, 131, 'delete', 'Delete', '/db-webclient/cities/delete', '/db-webclient/cities/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(552, 132, 'index', 'List', '/db-webclient/countries/index', '/db-webclient/countries/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(553, 132, 'add', 'Add', '/db-webclient/countries/add', '/db-webclient/countries/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(554, 132, 'view', 'View', '/db-webclient/countries/view', '/db-webclient/countries/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(555, 132, 'edit', 'Edit', '/db-webclient/countries/edit', '/db-webclient/countries/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(556, 132, 'delete', 'Delete', '/db-webclient/countries/delete', '/db-webclient/countries/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(557, 133, 'index', 'List', '/db-webclient/course_parent_types/index', '/db-webclient/course_parent_types/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(558, 133, 'add', 'Add', '/db-webclient/course_parent_types/add', '/db-webclient/course_parent_types/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(559, 133, 'view', 'View', '/db-webclient/course_parent_types/view', '/db-webclient/course_parent_types/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(560, 133, 'edit', 'Edit', '/db-webclient/course_parent_types/edit', '/db-webclient/course_parent_types/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(561, 133, 'delete', 'Delete', '/db-webclient/course_parent_types/delete', '/db-webclient/course_parent_types/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(562, 134, 'index', 'List', '/db-webclient/course_types/index', '/db-webclient/course_types/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(563, 134, 'add', 'Add', '/db-webclient/course_types/add', '/db-webclient/course_types/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(564, 134, 'view', 'View', '/db-webclient/course_types/view', '/db-webclient/course_types/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(565, 134, 'edit', 'Edit', '/db-webclient/course_types/edit', '/db-webclient/course_types/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(566, 134, 'delete', 'Delete', '/db-webclient/course_types/delete', '/db-webclient/course_types/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(567, 135, 'index', 'List', '/courses/index', '/courses/index', NULL, 0, 0, 0, 1, NULL, 1, 'CoursesController', '', 0),
	(568, 135, 'add', 'Add', '/courses/add', '/courses/add', NULL, 0, 1, 0, 1, NULL, 2, 'CoursesController', '', 0),
	(569, 135, 'view', 'View', '/courses/view', '/courses/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CoursesController', '', 0),
	(570, 135, 'edit', 'Edit', '/courses/edit', '/courses/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CoursesController', '', 0),
	(571, 135, 'delete', 'Delete', '/courses/delete', '/courses/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CoursesController', '', 0),
	(572, 135, 'statistic', 'Statistic', '/courses/statistic', '/courses/statistic', NULL, 0, 0, 1, 0, NULL, 6, 'CoursesController', '', 0),
	(573, 135, 'revalidate', 'Revalidate', '/courses/revalidate', '/courses/revalidate/+', NULL, 1, 0, 1, 0, NULL, 7, 'CoursesController', '', 0),
	(574, 136, 'index', 'List', '/db-webclient/courses_disciplines/index', '/db-webclient/courses_disciplines/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(575, 136, 'add', 'Add', '/db-webclient/courses_disciplines/add', '/db-webclient/courses_disciplines/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(576, 136, 'view', 'View', '/db-webclient/courses_disciplines/view', '/db-webclient/courses_disciplines/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(577, 136, 'edit', 'Edit', '/db-webclient/courses_disciplines/edit', '/db-webclient/courses_disciplines/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(578, 136, 'delete', 'Delete', '/db-webclient/courses_disciplines/delete', '/db-webclient/courses_disciplines/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(584, 138, 'index', 'List', '/db-webclient/courses_tadirah_activities/index', '/db-webclient/courses_tadirah_activities/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(585, 138, 'add', 'Add', '/db-webclient/courses_tadirah_activities/add', '/db-webclient/courses_tadirah_activities/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(586, 138, 'view', 'View', '/db-webclient/courses_tadirah_activities/view', '/db-webclient/courses_tadirah_activities/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(587, 138, 'edit', 'Edit', '/db-webclient/courses_tadirah_activities/edit', '/db-webclient/courses_tadirah_activities/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(588, 138, 'delete', 'Delete', '/db-webclient/courses_tadirah_activities/delete', '/db-webclient/courses_tadirah_activities/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(589, 139, 'index', 'List', '/db-webclient/courses_tadirah_objects/index', '/db-webclient/courses_tadirah_objects/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(590, 139, 'add', 'Add', '/db-webclient/courses_tadirah_objects/add', '/db-webclient/courses_tadirah_objects/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(591, 139, 'view', 'View', '/db-webclient/courses_tadirah_objects/view', '/db-webclient/courses_tadirah_objects/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(592, 139, 'edit', 'Edit', '/db-webclient/courses_tadirah_objects/edit', '/db-webclient/courses_tadirah_objects/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(593, 139, 'delete', 'Delete', '/db-webclient/courses_tadirah_objects/delete', '/db-webclient/courses_tadirah_objects/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(594, 140, 'index', 'List', '/db-webclient/courses_tadirah_techniques/index', '/db-webclient/courses_tadirah_techniques/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(595, 140, 'add', 'Add', '/db-webclient/courses_tadirah_techniques/add', '/db-webclient/courses_tadirah_techniques/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(596, 140, 'view', 'View', '/db-webclient/courses_tadirah_techniques/view', '/db-webclient/courses_tadirah_techniques/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(597, 140, 'edit', 'Edit', '/db-webclient/courses_tadirah_techniques/edit', '/db-webclient/courses_tadirah_techniques/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(598, 140, 'delete', 'Delete', '/db-webclient/courses_tadirah_techniques/delete', '/db-webclient/courses_tadirah_techniques/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(599, 141, 'index', 'List', '/db-webclient/disciplines/index', '/db-webclient/disciplines/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(600, 141, 'add', 'Add', '/db-webclient/disciplines/add', '/db-webclient/disciplines/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(601, 141, 'view', 'View', '/db-webclient/disciplines/view', '/db-webclient/disciplines/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(602, 141, 'edit', 'Edit', '/db-webclient/disciplines/edit', '/db-webclient/disciplines/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(603, 141, 'delete', 'Delete', '/db-webclient/disciplines/delete', '/db-webclient/disciplines/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(604, 142, 'index', 'List', '/db-webclient/institutions/index', '/db-webclient/institutions/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(605, 142, 'add', 'Add', '/db-webclient/institutions/add', '/db-webclient/institutions/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(606, 142, 'view', 'View', '/db-webclient/institutions/view', '/db-webclient/institutions/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(607, 142, 'edit', 'Edit', '/db-webclient/institutions/edit', '/db-webclient/institutions/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(608, 142, 'delete', 'Delete', '/db-webclient/institutions/delete', '/db-webclient/institutions/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(609, 143, 'index', 'List', '/db-webclient/languages/index', '/db-webclient/languages/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(610, 143, 'add', 'Add', '/db-webclient/languages/add', '/db-webclient/languages/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(611, 143, 'view', 'View', '/db-webclient/languages/view', '/db-webclient/languages/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(612, 143, 'edit', 'Edit', '/db-webclient/languages/edit', '/db-webclient/languages/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(613, 143, 'delete', 'Delete', '/db-webclient/languages/delete', '/db-webclient/languages/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(619, 145, 'index', 'List', '/db-webclient/tadirah_activities/index', '/db-webclient/tadirah_activities/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(620, 145, 'add', 'Add', '/db-webclient/tadirah_activities/add', '/db-webclient/tadirah_activities/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(621, 145, 'view', 'View', '/db-webclient/tadirah_activities/view', '/db-webclient/tadirah_activities/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(622, 145, 'edit', 'Edit', '/db-webclient/tadirah_activities/edit', '/db-webclient/tadirah_activities/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(623, 145, 'delete', 'Delete', '/db-webclient/tadirah_activities/delete', '/db-webclient/tadirah_activities/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(624, 146, 'index', 'List', '/db-webclient/tadirah_activities_tadirah_techniques/index', '/db-webclient/tadirah_activities_tadirah_techniques/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(625, 146, 'add', 'Add', '/db-webclient/tadirah_activities_tadirah_techniques/add', '/db-webclient/tadirah_activities_tadirah_techniques/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(626, 146, 'view', 'View', '/db-webclient/tadirah_activities_tadirah_techniques/view', '/db-webclient/tadirah_activities_tadirah_techniques/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(627, 146, 'edit', 'Edit', '/db-webclient/tadirah_activities_tadirah_techniques/edit', '/db-webclient/tadirah_activities_tadirah_techniques/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(628, 146, 'delete', 'Delete', '/db-webclient/tadirah_activities_tadirah_techniques/delete', '/db-webclient/tadirah_activities_tadirah_techniques/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(629, 147, 'index', 'List', '/db-webclient/tadirah_objects/index', '/db-webclient/tadirah_objects/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(630, 147, 'add', 'Add', '/db-webclient/tadirah_objects/add', '/db-webclient/tadirah_objects/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(631, 147, 'view', 'View', '/db-webclient/tadirah_objects/view', '/db-webclient/tadirah_objects/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(632, 147, 'edit', 'Edit', '/db-webclient/tadirah_objects/edit', '/db-webclient/tadirah_objects/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(633, 147, 'delete', 'Delete', '/db-webclient/tadirah_objects/delete', '/db-webclient/tadirah_objects/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(634, 148, 'index', 'List', '/db-webclient/tadirah_techniques/index', '/db-webclient/tadirah_techniques/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(635, 148, 'add', 'Add', '/db-webclient/tadirah_techniques/add', '/db-webclient/tadirah_techniques/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(636, 148, 'view', 'View', '/db-webclient/tadirah_techniques/view', '/db-webclient/tadirah_techniques/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(637, 148, 'edit', 'Edit', '/db-webclient/tadirah_techniques/edit', '/db-webclient/tadirah_techniques/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(638, 148, 'delete', 'Delete', '/db-webclient/tadirah_techniques/delete', '/db-webclient/tadirah_techniques/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(639, 149, 'index', 'List', '/db-webclient/user_roles/index', '/db-webclient/user_roles/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(640, 149, 'add', 'Add', '/db-webclient/user_roles/add', '/db-webclient/user_roles/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(641, 149, 'view', 'View', '/db-webclient/user_roles/view', '/db-webclient/user_roles/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(642, 149, 'edit', 'Edit', '/db-webclient/user_roles/edit', '/db-webclient/user_roles/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(643, 149, 'delete', 'Delete', '/db-webclient/user_roles/delete', '/db-webclient/user_roles/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(644, 150, 'index', 'List', '/db-webclient/users/index', '/db-webclient/users/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(645, 150, 'add', 'Add', '/db-webclient/users/add', '/db-webclient/users/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(646, 150, 'view', 'View', '/db-webclient/users/view', '/db-webclient/users/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(647, 150, 'edit', 'Edit', '/db-webclient/users/edit', '/db-webclient/users/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(648, 150, 'delete', 'Delete', '/users/delete', '/users/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'AppUsersController', 'Users', 1),
	(649, 150, 'delete_identity', 'Delete Identity', '/users/delete_identity', '/users/delete_identity', NULL, 0, 0, 1, 0, NULL, 6, 'AppUsersController', '', 0),
	(650, 150, 'approve', 'Approve', '/users/approve', '/users/approve/+', NULL, 1, 0, 1, 0, NULL, 7, 'AppUsersController', 'Users', 1),
	(651, 150, 'profile', 'Profile', '/users/profile', '/users/profile/+', NULL, 1, 0, 1, 0, NULL, 8, 'AppUsersController', 'Users', 1),
	(652, 150, 'dashboard', 'Dashboard', '/users/dashboard', '/users/dashboard/+', NULL, 1, 0, 1, 0, NULL, 9, 'AppUsersController', 'Users', 1),
	(653, 150, 'invite', 'Invite', '/users/invite', '/users/invite', NULL, 0, 0, 1, 0, NULL, 10, 'AppUsersController', '', 0),
	(654, 150, 'resend_email_verification', 'Resend Email Verification', '/users/resend_email_verification', '/users/resend_email_verification', NULL, 0, 0, 1, 0, NULL, 11, 'AppUsersController', 'Users', 1),
	(684, 156, 'index', 'List', '/db-webclient/cities/index', '/db-webclient/cities/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(685, 156, 'add', 'Add', '/db-webclient/cities/add', '/db-webclient/cities/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(686, 156, 'view', 'View', '/db-webclient/cities/view', '/db-webclient/cities/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(687, 156, 'edit', 'Edit', '/db-webclient/cities/edit', '/db-webclient/cities/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(688, 156, 'delete', 'Delete', '/db-webclient/cities/delete', '/db-webclient/cities/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(689, 157, 'index', 'List', '/db-webclient/countries/index', '/db-webclient/countries/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(690, 157, 'add', 'Add', '/db-webclient/countries/add', '/db-webclient/countries/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(691, 157, 'view', 'View', '/db-webclient/countries/view', '/db-webclient/countries/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(692, 157, 'edit', 'Edit', '/db-webclient/countries/edit', '/db-webclient/countries/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(693, 157, 'delete', 'Delete', '/db-webclient/countries/delete', '/db-webclient/countries/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(704, 160, 'index', 'List', '/courses/index', '/courses/index', NULL, 0, 0, 0, 1, NULL, 1, 'CoursesController', '', 0),
	(705, 160, 'add', 'Add', '/courses/add', '/courses/add', NULL, 0, 1, 0, 1, NULL, 2, 'CoursesController', '', 0),
	(706, 160, 'view', 'View', '/courses/view', '/courses/view/+', NULL, 1, 0, 0, 1, NULL, 3, 'CoursesController', '', 0),
	(707, 160, 'edit', 'Edit', '/courses/edit', '/courses/edit/+', NULL, 1, 1, 0, 1, NULL, 4, 'CoursesController', '', 0),
	(708, 160, 'delete', 'Delete', '/courses/delete', '/courses/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'CoursesController', '', 0),
	(709, 160, 'statistic', 'Statistic', '/courses/statistic', '/courses/statistic', NULL, 0, 0, 1, 0, NULL, 6, 'CoursesController', '', 0),
	(710, 160, 'revalidate', 'Revalidate', '/courses/revalidate', '/courses/revalidate/+', NULL, 1, 0, 1, 0, NULL, 7, 'CoursesController', '', 0),
	(741, 167, 'index', 'List', '/db-webclient/institutions/index', '/db-webclient/institutions/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(742, 167, 'add', 'Add', '/db-webclient/institutions/add', '/db-webclient/institutions/add', NULL, 0, 1, 0, 1, NULL, 2, '', 'Cakeclient', 0),
	(743, 167, 'view', 'View', '/db-webclient/institutions/view', '/db-webclient/institutions/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(744, 167, 'edit', 'Edit', '/db-webclient/institutions/edit', '/db-webclient/institutions/edit/+', NULL, 1, 1, 0, 1, NULL, 4, '', 'Cakeclient', 0),
	(745, 167, 'delete', 'Delete', '/db-webclient/institutions/delete', '/db-webclient/institutions/delete/+', NULL, 1, 0, 1, 0, NULL, 5, '', 'Cakeclient', 0),
	(781, 175, 'index', 'List', '/db-webclient/users/index', '/db-webclient/users/index', NULL, 0, 0, 0, 1, NULL, 1, '', 'Cakeclient', 0),
	(783, 175, 'view', 'View', '/db-webclient/users/view', '/db-webclient/users/view/+', NULL, 1, 0, 0, 1, NULL, 3, '', 'Cakeclient', 0),
	(785, 175, 'delete', 'Delete', '/users/delete', '/users/delete/+', NULL, 1, 0, 1, 0, NULL, 5, 'AppUsersController', 'Users', 1),
	(786, 175, 'delete_identity', 'Delete Identity', '/users/delete_identity', '/users/delete_identity', NULL, 0, 0, 1, 0, NULL, 6, 'AppUsersController', '', 0),
	(788, 175, 'profile', 'Profile', '/users/profile', '/users/profile/+', NULL, 1, 0, 1, 0, NULL, 8, 'AppUsersController', 'Users', 1);
/*!40000 ALTER TABLE `cc_config_actions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle dhregistry.cc_config_fielddefinitions
DROP TABLE IF EXISTS `cc_config_fielddefinitions`;
CREATE TABLE IF NOT EXISTS `cc_config_fielddefinitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_config_table_id` int(11) DEFAULT NULL COMMENT 'Define a fieldlist belonging to the table directly as a default fieldlist, applying to any action. Leave empty action_id then.',
  `cc_config_action_id` int(11) DEFAULT NULL COMMENT 'If assigned to a specific action, the fielddefinition overrides the default fielddefinition of the table.',
  `context` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'main' COMMENT 'What this fieldlist''s fielddefinition is for. "main" serves the main purpose of a view, but there might be other areas in the same view requiring an additional fieldlist, possibly one from a foreign model.',
  `position` int(3) DEFAULT NULL,
  `fieldname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_method` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'auto' COMMENT 'value must be one of available methods from DisplayHelper',
  `display_options` text COLLATE utf8_unicode_ci COMMENT 'a list of options, depends from the selected display method',
  PRIMARY KEY (`id`),
  KEY `FK_cc_config_fielddefinitions_cc_config_tables` (`cc_config_table_id`),
  KEY `FK_cc_config_fielddefinitions_cc_config_actions` (`cc_config_action_id`),
  CONSTRAINT `FK_cc_config_fielddefinitions_cc_config_actions` FOREIGN KEY (`cc_config_action_id`) REFERENCES `cc_config_actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_cc_config_fielddefinitions_cc_config_tables` FOREIGN KEY (`cc_config_table_id`) REFERENCES `cc_config_tables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Daten aus Tabelle dhregistry.cc_config_fielddefinitions: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `cc_config_fielddefinitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cc_config_fielddefinitions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle dhregistry.cc_config_menus
DROP TABLE IF EXISTS `cc_config_menus`;
CREATE TABLE IF NOT EXISTS `cc_config_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(3) DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `layout_block` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'cakeclient_navbar' COMMENT 'the layout block',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table holds the menu groups, having many related sub-menu items: individual actions on a table, either CRUD actions as available on convention by the Cakeclient software or as defined in a controller. \r\nBesides that, these objects are the top-level ACOs.';

-- Exportiere Daten aus Tabelle dhregistry.cc_config_menus: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `cc_config_menus` DISABLE KEYS */;
INSERT INTO `cc_config_menus` (`id`, `position`, `label`, `layout_block`, `comment`) VALUES
	(11, 1, 'Config', 'cakeclient_navbar', 'admin menu tree'),
	(12, 2, 'Tables', 'cakeclient_navbar', 'admin menu tree'),
	(14, 2, 'Tables', 'cakeclient_navbar', 'moderator menu');
/*!40000 ALTER TABLE `cc_config_menus` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle dhregistry.cc_config_tables
DROP TABLE IF EXISTS `cc_config_tables`;
CREATE TABLE IF NOT EXISTS `cc_config_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_config_menu_id` int(11) DEFAULT NULL COMMENT 'ACOs need to be created before menus',
  `position` int(3) DEFAULT NULL COMMENT 'sorting for menu generation',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'if not following naming conventions',
  `controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'if not following naming conventions',
  `displayfield` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `displayfield_label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_associations` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'if associated tables are shown AT ALL',
  PRIMARY KEY (`id`),
  KEY `FK_cc_config_tables_cc_config_menus` (`cc_config_menu_id`),
  CONSTRAINT `FK_cc_config_tables_cc_config_menus` FOREIGN KEY (`cc_config_menu_id`) REFERENCES `cc_config_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Daten aus Tabelle dhregistry.cc_config_tables: ~30 rows (ungefähr)
/*!40000 ALTER TABLE `cc_config_tables` DISABLE KEYS */;
INSERT INTO `cc_config_tables` (`id`, `cc_config_menu_id`, `position`, `name`, `label`, `model`, `controller`, `displayfield`, `displayfield_label`, `show_associations`) VALUES
	(126, 11, 1, 'cc_config_acos_aros', 'AcosAros', 'CcConfigAcosAro', 'cc_config_acos_aros', 'id', 'AcosAros', 1),
	(127, 11, 4, 'cc_config_actions', 'Actions', 'CcConfigAction', 'cc_config_actions', 'label', 'Actions', 1),
	(128, 11, 5, 'cc_config_fielddefinitions', 'Fielddefinitions', 'CcConfigFielddefinition', 'cc_config_fielddefinitions', 'label', 'Fielddefinitions', 1),
	(129, 11, 2, 'cc_config_menus', 'Menus', 'CcConfigMenu', 'cc_config_menus', 'label', 'Menus', 1),
	(130, 11, 3, 'cc_config_tables', 'Tables', 'CcConfigTable', 'cc_config_tables', 'name', 'Tables', 1),
	(131, 12, 6, 'cities', 'Cities', 'City', 'cities', 'name', 'Cities', 1),
	(132, 12, 7, 'countries', 'Countries', 'Country', 'countries', 'name', 'Countries', 1),
	(133, 12, 8, 'course_parent_types', 'CourseParentTypes', 'CourseParentType', 'course_parent_types', 'name', 'CourseParentTypes', 1),
	(134, 12, 9, 'course_types', 'CourseTypes', 'CourseType', 'course_types', 'name', 'CourseTypes', 1),
	(135, 12, 10, 'courses', 'Courses', 'Course', 'courses', 'name', 'Courses', 1),
	(136, 12, 11, 'courses_disciplines', 'CoursesDisciplines', 'CoursesDiscipline', 'courses_disciplines', 'id', 'CoursesDisciplines', 1),
	(138, 12, 12, 'courses_tadirah_activities', 'CoursesTadirahActivities', 'CoursesTadirahActivity', 'courses_tadirah_activities', 'id', 'CoursesTadirahActivities', 1),
	(139, 12, 13, 'courses_tadirah_objects', 'CoursesTadirahObjects', 'CoursesTadirahObject', 'courses_tadirah_objects', 'id', 'CoursesTadirahObjects', 1),
	(140, 12, 14, 'courses_tadirah_techniques', 'CoursesTadirahTechniques', 'CoursesTadirahTechnique', 'courses_tadirah_techniques', 'id', 'CoursesTadirahTechniques', 1),
	(141, 12, 15, 'disciplines', 'Disciplines', 'Discipline', 'disciplines', 'name', 'Disciplines', 1),
	(142, 12, 16, 'institutions', 'Institutions', 'Institution', 'institutions', 'name', 'Institutions', 1),
	(143, 12, 17, 'languages', 'Languages', 'Language', 'languages', 'name', 'Languages', 1),
	(145, 12, 18, 'tadirah_activities', 'TadirahActivities', 'TadirahActivity', 'tadirah_activities', 'name', 'TadirahActivities', 1),
	(146, 12, 19, 'tadirah_activities_tadirah_techniques', 'TadirahActivitiesTadirahTechniques', 'TadirahActivitiesTadirahTechnique', 'tadirah_activities_tadirah_techniques', 'id', 'TadirahActivitiesTadirahTechniques', 1),
	(147, 12, 20, 'tadirah_objects', 'TadirahObjects', 'TadirahObject', 'tadirah_objects', 'name', 'TadirahObjects', 1),
	(148, 12, 21, 'tadirah_techniques', 'TadirahTechniques', 'TadirahTechnique', 'tadirah_techniques', 'name', 'TadirahTechniques', 1),
	(149, 12, 22, 'user_roles', 'UserRoles', 'UserRole', 'user_roles', 'name', 'UserRoles', 1),
	(150, 12, 23, 'users', 'Users', 'AppUser', 'users', 'email', 'Users', 1),
	(156, 14, 6, 'cities', 'Cities', 'City', 'cities', 'name', 'Cities', 1),
	(157, 14, 7, 'countries', 'Countries', 'Country', 'countries', 'name', 'Countries', 1),
	(160, 14, 8, 'courses', 'Courses', 'Course', 'courses', 'name', 'Courses', 1),
	(167, 14, 9, 'institutions', 'Institutions', 'Institution', 'institutions', 'name', 'Institutions', 1),
	(175, 14, 10, 'users', 'Users', 'AppUser', 'users', 'email', 'Users', 1);
/*!40000 ALTER TABLE `cc_config_tables` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
