# Laravel WaboxApp

This package provides integration with the WaboxApp API. It currently only supports sending a chat message.

## Installation

You can install this package via Composer using:

```bash
composer require kylewlawrence/laravel-waboxapp
```

The facade is automatically installed.

```php
WaboxApp::sendChat(['to' => 1234567890, 'text' => 'This is the message']);
```

## Configuration

To publish the config file to `app/config/waboxapp-laravel.php` run:

```bash
php artisan vendor:publish --provider="KyleWLawrence\WaboxApp\Providers\WaboxAppServiceProvider"
```

Set your configuration using **environment variables**, either in your `.env` file or on your server's control panel:

- `WABOXAPP_TOKEN`

The API access token. You can create one at: `https://app.startwaboxapp.com/profile/developer/tokens`

- `WABOXAPP_UID`

Set this to the UID number registered at waboxapp.com in order to not have to include it in every request.

- `WABOXAPP_DRIVER` _(Optional)_

Set this to `null` or `log` to prevent calling the WaboxApp API directly from your environment.

## Contributing

Pull Requests are always welcome here. I'll catch-up and develop the contribution guidelines soon. For the meantime, just open and issue or create a pull request.

## Usage

### Facade

The `WaboxApp` facade acts as a wrapper for an instance of the `WaboxApp\Http\HttpClient` class.

### Dependency injection

If you'd prefer not to use the facade, you can instead inject `KyleWLawrence\WaboxApp\Services\WaboxAppService` into your class. You can then use all of the same methods on this object as you would on the facade.

```php
<?php

use KyleWLawrence\WaboxApp\Services\WaboxAppService;

class MyClass {

    public function __construct(WaboxAppService $waboxapp_service) {
        $this->waboxapp_service = $waboxapp_service;
    }

    public function getBoards() {
        $this->waboxapp_service->sendChat();
    }

}
```

This package is available under the [MIT license](http://opensource.org/licenses/MIT).
