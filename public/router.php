<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/*
 * This file implements rewrite rules for PHP built-in web server.
 *
 * See: https://php.net/features.commandline.webserver
 *
 * If you have custom directory layout, then you have to write your own router
 * and pass it as a value to 'router' option of server:run command.
 *
 * @author Michał Pipa <michal.pipa.xsolve@gmail.com>
 * @author Albert Jessurum <ajessu@gmail.com>
 */

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(\in_array(@$_SERVER['REMOTE_ADDR'], array('169.254.0.1', '127.0.0.1', 'fe80::1', '::1')) || \php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

// Workaround https://bugs.php.net/64566
if (ini_get('auto_prepend_file') && !in_array(realpath(ini_get('auto_prepend_file')), get_included_files(), true)) {
    require ini_get('auto_prepend_file');
}
if (is_file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_NAME'])) {
    return false;
}

$script = $_SERVER['APP_FRONT_CONTROLLER'] ?? $_ENV['APP_FRONT_CONTROLLER'] ?? 'app_dev.php';
//$_SERVER = array_merge($_SERVER, $_ENV);
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$script;
// Since we are rewriting to app_dev.php, adjust SCRIPT_NAME and PHP_SELF accordingly
$_SERVER['SCRIPT_NAME'] = DIRECTORY_SEPARATOR.$script;
$_SERVER['PHP_SELF'] = DIRECTORY_SEPARATOR.$script;
require $script;
error_log(sprintf('%s:%d [%d]: %s', $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'], http_response_code(), $_SERVER['REQUEST_URI']), 4);
