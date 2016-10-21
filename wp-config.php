<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'sdela');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '!t2hA%}wVdY7|h|NKS%I8Stvr!24x^~:aDK{xf*`gd]g9>^v%<y:0`r*gtofeqmc');
define('SECURE_AUTH_KEY',  '3BUUD1rey}&_bW`vP!OK[s&`v*>@y.|$s&0WTpFBkH|40FfQSh4Vg6yeSU$&>| 6');
define('LOGGED_IN_KEY',    'M<1`xva+}[|f&e7y6>6c&[m].E}h9/mP(Cvlwf%>*/e.zJyZCZF+.{nEZw&dPiAJ');
define('NONCE_KEY',        '/a|E98<=y}zehS_H@W2o.OQb(H3Xt@j9[AusKs$Dbbv?O(W>4]QhpGruL|RwG45~');
define('AUTH_SALT',        '76tKr$^rb^{N[R8n938v*5D[Z|+Vp9Po98l#0_$~C}C<!TT0g0AMbFS$F/sLh}X,');
define('SECURE_AUTH_SALT', 'E}!1-Ad>,k;f %0TM2Qe}KS*9~W2b)/nFO,&tfq72eA>&H:]/-^60AN8I~L=^i]/');
define('LOGGED_IN_SALT',   '&@]=+}PdGw.CAL{6 3WtAwq1W[X;OOWs0JW@B-m!a.hL]ylg?bj3c Lvs@: I{LN');
define('NONCE_SALT',       'l(>)d3|mQ;L.YA=x}n@iWL#},Ji3L3kkrd3VY_& E8y;tuu4F.4!gJIlR2wY.r50');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'sdl_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
