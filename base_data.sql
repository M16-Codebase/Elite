--
-- Главный каталог, родитель всех каталогов
--
REPLACE INTO `item_types` 
(`id`, `title`, `parent_id`, `position`, `auto_mult`, `status`, `counters`, `parents`, `allow_children`, `post_id`, `annotation`, `fixed`) 
VALUES
(1, 'Каталог', NULL, 0, 0, 'visible', NULL, '.', 1, 0, '', 1);
--
-- Коллекции картинок основных сущностей
--
REPLACE INTO `image_collection` 
(`id`, `cover`, `data`, `default`, `type`) 
VALUES 
(1, 0, '', NULL, 'Default'),
(2, 0, '', NULL, 'TypeCover'),
(3, 0, '', NULL, 'ItemsDefault'),
(4, 0, '', NULL, 'Files'),
(5, 0, '', NULL, 'Manuf'),
(6, 0, '', NULL, 'Property');
--
-- Картинки по умолчанию
--
REPLACE INTO `images` 
(`id`, `collection_id`, `width`, `height`, `hidden`, `num`, `gravity`) 
VALUES 
(1, 1, 0, 0, 0, 1, 'C'),
(2, 1, 0, 0, 0, 2, 'C'),
(3, 1, 0, 0, 0, 3, 'C'),
(4, 1, 0, 0, 0, 4, 'C'),
(5, 1, 0, 0, 0, 5, 'C'),
(6, 1, 0, 0, 0, 6, 'C');
--
-- Основные роли пользователей
--
REPLACE INTO `user_roles` 
(`id`, `key`, `title`, `default_permission`, `after_login_redirect`, `position`)
VALUES
(1, 'SuperAdmin', 'Разработчик', 'enable', NULL, 1),
(2, 'Admin', 'Админ', 'enable', NULL, 2),
(3, 'Guest', 'Гость', 'disable', NULL, 4),
(4, 'Broker', 'Брокер', 'enable', NULL, 3);
--
-- Основные действия чтобы начать работать
--
REPLACE INTO `actions` 
(`id`, `module_class`, `module_url`, `action`, `title`, `admin`) 
VALUES
(1, 'Modules\\Main\\View', 'main', 'index', 'Главная', '0'),
(2, 'Modules\\Welcome\\Guest', 'welcome', 'login', 'Аутентификация', '0'),
(3, 'Modules\\Welcome\\Guest', 'welcome', 'registration', 'Регистрация', '0'),
(4, 'Modules\\Welcome\\Guest', 'welcome', 'logout', 'Разлогиниться', '0'),
(5, 'Modules\\Site\\SharedMemory', 'shared-memory', 'check', 'Проверка разделяемой памяти', '1');
--
-- Разрешения для гостя на некоторые действия
--
REPLACE INTO `user_permissions` 
(`action_id`, `role_id`, `permission`) 
VALUES
(1, 3, 'enable'),
(2, 3, 'enable'),
(3, 3, 'enable'),
(4, 3, 'enable'),
(5, 3, 'enable');