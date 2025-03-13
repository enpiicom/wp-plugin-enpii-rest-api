<?php
/**
 * Plugin Name: Enpii REST API - User Authentication & Access Control
 * Plugin URI:  https://enpii.com/wp-plugin-enpii-rest-api/
 * Description: This WordPress plugin provides a secure and efficient way to handle REST API authentication and user access control
 * Author:      dev@enpii.com, nptrac@yahoo.com
 * Author URI:  https://enpii.com/enpii-team/
 * Version:     0.0.2
 * License:     MIT
 * License URI: https://mit-license.org/
 * Text Domain: enpii-rest-api
 */

// We want to split all the bootstrapping code to a separate file
//  for putting into composer autoload and
//  for easier including on other section e.g. unit test
require_once __DIR__ . DIRECTORY_SEPARATOR . 'enpii-rest-api-bootstrap.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'enpii-rest-api-init.php';
