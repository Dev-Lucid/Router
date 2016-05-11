<?php
# to use this file,
# php -S 127.0.0.1:9000 php_inbuilt_server.php
#
#
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER["REQUEST_URI"]) === true) {
    return false;
} else {
    include __DIR__ . '/index.php';
}