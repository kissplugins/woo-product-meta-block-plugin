# KISS API Guard Plugin

A simple WordPress plugin to restrict access to sensitive data via the WooCommerce REST API.

## Description

This plugin helps to secure your WooCommerce store by restricting access to sensitive product data and user information for unauthenticated REST API requests.

Authenticated requests (using valid WooCommerce API keys) will continue to have full access to the data.

## Features

*   **Product Data Filtering:** Removes sensitive product data like stock quantities, total sales, and cost-of-goods information from product and variation API responses for unauthenticated users.
*   **User Endpoint Restriction:** Blocks access to the `/wp/v2/users` endpoint for unauthenticated requests to prevent user enumeration attacks.
*   **Configurable:** You can enable or disable the restrictions from the WooCommerce settings page (`WooCommerce > Settings > Advanced > API Restrictions`).

## Installation

1.  Download the plugin as a ZIP file.
2.  Go to `Plugins > Add New` in your WordPress admin.
3.  Click `Upload Plugin` and select the ZIP file.
4.  Activate the plugin.
5.  Configure the plugin settings under `WooCommerce > Settings > Advanced > API Restrictions`.

## For Developers

The plugin uses the `kiss_api_guard_sensitive_meta_keys` filter to allow developers to customize the list of meta keys that are removed from the API response.
