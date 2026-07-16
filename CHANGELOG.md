# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-07-16

### Added
- Complete Customer storefront (Home, Cart, Checkout, Wishlist).
- Midtrans payment gateway integration (Snap API & Webhooks).
- Cloudinary media management integration.
- Full Admin Dashboard with Real-time statistics.
- Order Pipeline (Pending -> Paid -> Processing -> Shipped -> Delivered -> Completed).
- Robust Stock Movement logging system.
- Customer Review system with duplicate prevention.
- Security Headers Middleware enforcing Strict Content Security Policy (CSP).
- HTTPS forcing on production environments.
- Comprehensive Deployment Documentation (Nginx, Supervisor, Cron, Backup).
- Automated PostgreSQL backup script with rotation.

### Fixed
- Stabilized SQLite testing environment via `RefreshDatabase` trait adjustments.
- Resolved `UserFactory` constraint violation for the `phone` attribute.
- Wrapped Dashboard aggregate queries in `Cache::remember` to alleviate N+1 DB loads.

### Security
- Created `.env.production.example` to enforce `APP_DEBUG=false` in production.
- Validated all Midtrans Webhook signatures using SHA512.
- Ensured IDOR protection on all customer-facing routes.
