=== Enpii REST API ===
Contributors: npbtrac, kimloile
Tags: enpii, enpii rest api, enpii-rest-api, enpii base, enpii-base, laravel, wordpress laravel
Requires at least: 6.0
Tested up to: 6.7.1
Requires PHP: 7.3
Stable tag: 0.0.2
License: MIT
License URI: https://mit-license.org/

Enpii REST API WordPress plugin provides a secure and efficient way to handle REST API authentication and user access control. 

== Description ==

## Base concepts
This WordPress plugin provides a secure and efficient way to handle REST API authentication and user access control. It generates unique access tokens for authenticated users, which can be used to authorize actions such as editing posts, pages, and other restricted operations within the WordPress environment.

## Key Features
### User Authentication via Enpii REST API
- Implements an API endpoint for user login.
- Authenticates users based on their WordPress credentials (username & password).
- Returns a unique token upon successful authentication.

### Token-Based Authentication
- Generates a secure, time-limited access token for each user.
- Stores tokens in the database with expiration timestamps.
- Supports token refresh and revocation for security.

### Role-Based Access Control (RBAC)
- Maps WordPress user roles to different levels of API permissions.
- Ensures only authorized users can perform specific actions (e.g., only Editors and Admins can edit posts).

### API Endpoints for Authentication & Authorization
- /wp-json/enpii-auth/v1/login → Authenticates users and returns an access token.
- /wp-json/enpii-auth/v1/validate → Validates tokens for API requests.
- /wp-json/enpii-auth/v1/logout → Invalidates tokens upon user logout.

### Integration with WordPress Core
- Compatible with WordPress’s built-in user management system.
- Supports default WordPress user roles and capabilities.

### Security Measures
- Uses hashed tokens for storage.
- Implements token expiration and refresh mechanisms.
- Protects against brute force attacks with rate limiting.

## Use Case Scenarios
- Allowing external applications to authenticate users securely via REST API.
- Restricting access to specific WordPress REST API routes based on user roles.
- Enabling frontend applications (e.g., React, Vue.js) to interact with WordPress securely.
- Enhancing API security without relying on basic authentication or cookies.

== Installation ==

= Minimum Requirements =

* Wordpress 6.0 or greater
* PHP 7.3 or greater

### Install and Enable/Disable the plugin
* The Enpii REST API plugin is now available for WordPress. After installing it, simply click on **Activate** the plugin.

* You can find out more how to install and use the plugin with our user guides documentation.

== Frequently Asked Questions ==

= What plugins are required for Enpii REST API to function? =

- The Enpii REST API requires the Enpii Base plugin for proper functionality.

= How can I download and activate the required plugins? =

- You can use the Enpii Plugins Installer page, which lists all necessary plugins. From there, you can download and activate them directly.

== Changelog ==

= 0.0.2 - 2025-03-13 =

- Update extract folders permission.

= 0.0.1 - 2025-03-03 =

- Release version
