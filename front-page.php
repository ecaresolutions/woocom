<?php
/**
 * The front page template file
 *
 * If the user sets a static page as the front page, WordPress will default to page.php.
 * By using front-page.php, we force WordPress to load the homepage layouts defined in index.php.
 *
 * @package Woocom
 */

include get_template_directory() . '/index.php';
