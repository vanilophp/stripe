# Stripe Module for Vanilo Changelog

## 3.x

### 3.0.0
#### 2025-11-19

- Upgrade to Vanilo 5
- Dropped PHP 8.2 support
- Added PHP 8.5 support
- Added Laravel 12 support
- Changed the minimum Laravel requirements to 10.48.3, 11.44.1 and 12.2, respectively

## 2.x

### 2.2.0
#### 2024-12-21

- Added PHP 8.4 support
- Added the create customer option
  - Added the `STRIPE_CREATE_CUSTOMER` env var (defaults to false) which enables Stripe customer creation
- Improved the default HTML snippet for a better UX for shoppers 

### 2.1.0
#### 2024-07-26

- Added the `return_url` configuration (passing it in the `$options` array is no longer needed)
- Fixed the SVG logo viewBox markup

### 2.0.0
#### 2024-07-26

- Added Vanilo 4 Support
- Changed PHP requirements to PHP 8.2 or PHP 8.3
- Changed Laravel requirements to 10.43+ or 11.0+
- Dropped Vanilo 2.x support (3.x was never supported)
- Fixed the Stripe API communication and workflow (Thanks @xujiongze)
- Improved the HTML snippet

## 1.x

- No final release ever made, was available as `1.0.x-dev`
- Worked with Vanilo 2.1