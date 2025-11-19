# WooCommerce Recently Viewed Products

A lightweight, object-oriented PHP solution to track and display "Recently Viewed Products" in WooCommerce. This codebase is designed to be included within a theme or a custom plugin structure without external dependencies.

## Features

-   **Cookie-Based Tracking:** Uses lightweight cookies to store product IDs (server-side rendering friendly).
-   **Duplicate Handling:** Automatically moves the most recently viewed item to the top and removes duplicates.
-   **Shortcode Support:** Provides a flexible shortcode to display products anywhere.
-   **Performance:** Optimized queries using `post__in` and clean memory management.
-   **Standard:** Adheres to WordPress Coding Standards (WPCS).

## Installation

1.  Copy the `wc-recently-viewed-products` folder into your theme directory (e.g., `wp-content/themes/your-theme/inc/`).
2.  Include the loader file in your theme's `functions.php`:

```php
// functions.php

$hussainas_tracker_path = get_template_directory() . '/inc/wc-recently-viewed-products/loader.php';

if ( file_exists( $hussainas_tracker_path ) ) {
    require_once $hussainas_tracker_path;
}

```

## Usage

**Basic Usage:** Add the following shortcode to any page, post, or widget:

```php
[hussainas_recently_viewed]

```

**Advanced Usage with Attributes:** You can customize the number of products, columns, and the section title:

```php
[hussainas_recently_viewed limit="5" columns="5" title="You Checked These Out"]

```

## Requirements

  - PHP 7.4 or higher
  - WordPress 5.8+
  - WooCommerce 5.0+





