<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER["REQUEST_URI"]) === true) {
    return false;
} else {
    include __DIR__ . '/index.php';
}