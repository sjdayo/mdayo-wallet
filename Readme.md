# Wallet Laravel Package

A Laravel package to manage wallets, including configurations, migrations, and routes. The package works out-of-the-box but allows customization via publishing.

---

## 1. Installation

Install the package via Composer:

```bash
composer require mdayo/wallet
```

If your Laravel version does not support auto-discovery, add the service provider to config/app.php:

```php
'providers' => [
    Mdayo\Wallet\WalletServiceProvider::class,
]
```
## 2. Configuration
The package provides default configuration files. You can publish them to customize:

```bash
php artisan vendor:publish --tag=wallet-config
php artisan vendor:publish --tag=wallet-queue-config
```

### After publishing, edit:

* config/wallet.php
* config/queue.php 

Default values are still available even if you don’t publish, thanks to mergeConfigFrom().

## 3. Migrations
The package comes with default migrations.

Run directly from the package:


```bash
php artisan migrate
```
Or publish migrations to customize:

```bash
php artisan vendor:publish --tag=wallet-migrations
php artisan migrate
```
Ensure published migrations have unique timestamps to avoid duplicates.

## 4. Routes
Default API routes are automatically loaded from the package:

```php
$this->loadRoutesFrom(__DIR__.'/../routes/wallet.php');
```

## 5. Publish Routes for Customization
To override package routes, publish them:

```bash
php artisan vendor:publish --tag=wallet-routes
```
The published file will be available at:

routes/wallet.php
Edit this file to customize routes. You can also configure the package to load the published routes if they exist.