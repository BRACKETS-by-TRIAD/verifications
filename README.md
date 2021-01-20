# Verifications
This package should be used for a code-based verification of actions (typically routes).

Currently packages supports two channels for sending verification codes:

- sms
- email

but it's easy to extend the package to support custom channels.

Packages ships also with a simple frontend (screen where user can input the code + default email/sms template) that could be easily overridden to meet your UX.

Package can help you also with a special case of a verification - the [Two-factor authentication](#markdown-header-two-factor-authentication). 

## Installation

1. `composer require brackets/verifications`

2. `php artisan verifications:install`

## Usage

### Implementing Verifiable
First of all, you need to have an authenticated user (i.e. User model) that implements **Verifiable** interface and use **VerifiableTrait**. Interface requires you to define two methods that are - in a typical scenario - just the accessors methods for the `email` and `phone` own attributes on your user User (but you have the option if i.e. these attributes exists on some related model like `UserProfileData` etc.):

```php
class User extends Authenticatable implements Verifiable
{
    use VerifiableTrait;

    public function getPhoneAttribute(): string
    {
        return $this->phone;
    }

    public function getEmailAttribute(): string
    {
        return $this->email;
    }
    
    // ...
}
```

Note: If you need to insert **phone_number**/**email** attributes to your `user` table, you can use artisan commands `verifications:add-email {table-name}` and/or `verifications:add-phone {table-name}`. 

### Configuration
Then you need to define an action that would require verification.

This can be achieved in the configuration file `/config/verifications.php`. 
 
```php
    'enabled' => env('VERIFICATION_ENABLED', true), // you can enable/disable globally (i.e. disabled for tests/dev env)
    'actions' => [
        'my-action' => [
            'enabled' => true,                              // you can enable/disable single action
            'channel' => 'sms',                             // currently: sms, email
            'keep_verified_during_session' => false,        // if true, keeps verification valid while session exists
            'verified_action_valid_minutes' => 15,          // if keep_verified_during_session == false, then this config specifies how many minutes does it take to require another code verification for the same action
            'code' => [
                'type' => 'numeric',                        // specifies the type of verification code, can be one of: 'numeric' or 'string'
                'length' => 6,                              // specifies the verification code length, defaults to 6
                'validity_length_minutes' => 10,            // specifies the length in minutes how long the code will be valid for use
            ],
        ],
    ]
```

### GET request verification
Typically you use this package to protect the entrance to some specific area of the application.
This can be done by protecting all the routes using **VerificationMiddleware** middleware:

```php
Route::middleware('verifications.verify:{my-action}')
```

**Example:**  
Let's say we want to verify the secret **money-balance** screen.

Define the action in your config:
```php
    'enabled' => env('VERIFICATION_ENABLED', true), // you can enable/disable globally (i.e. disabled for tests/dev env)
    'actions' => [
        'money-balance' => [
            'enabled' => true,                              // you can enable/disable single action
            'channel' => 'sms',                             // currently: sms, email
            'keep_verified_during_session' => false,        // if true, keeps verification valid while session exists
            'verified_action_valid_minutes' => 15,          // if keep_verified_during_session == false, then this config specifies how many minutes does it take to require another code verification for the same action
            'code' => [
                'type' => 'numeric',                        // specifies the type of verification code, can be one of: 'numeric' or 'string'
                'length' => 6,                              // specifies the verification code length, defaults to 6
                'validity_length_minutes' => 10,            // specifies the length in minutes how long the code will be valid for use
            ],
        ],
    ]
```

And protect the route: 

```php
Route::get('/{account}/money-balance', 'MoneyController@moneyBalance')
    ->name('money-balance')
    ->middleware('verifications.verify:money-balance');
```

When User tries to go to the `/{account}/money-balance` URL, he will be redirected to the verification screen where he is required to provide a code that was sent to him. 

### POST request verification

Verifying POST actions is a bit more tricky because user cannot be redirected back to the POST request (this is technically impossible).

Of course you can block the access to some POST action until user verifies it. Once he does verify it, everything works for him smoothly.

You should always create a GET route displaying a screen where User can perform the action and protect this GET route too (meaning protecting the entrance into the area, where he can perform the POST action). In that case, User never experience weird behaviour of needing to click to the same action twice.   

You have two options here:

1. either make sure User is always verified on some GET route *before* he performs the POST action (so limit the entrance to some area, where he can perform the POST actions),
2. or crete a pseudo-screen with with some handy JavaScript, that will auto-run the POST request on User's behalf on load, so he doesn't have to click twice

Which option to use depends on your exact use case. But typically, if the action requires User to input some data to the form, the _showForm_ GET route should be always protected, etc.

**Example:**  
Let's continue with our MoneyApp example, but now we want to protect the **money-withdraw** action.

The protection of a POST route is very similar:
```php
Route::post('/{account}/money-withdraw', 'MoneyController@moneyWithdraw')
    ->name('money-withdraw')
    ->middleware('verifications.verify:money-withdraw');
```

This will definitely prevent withdrawing the money for the unverified user. But it doesn't solve the redirect problem. Let's do it.

If we think about it, we actually want to protect the GET route for money withdraw feature, not only the final submit button. 

So let's add a GET route:
```php
Route::get('/{account}/money-withdraw', 'MoneyController@moneyWithdrawForm')
    ->name('money-withdraw-form')
    ->middleware('verifications.verify:money-withdraw');

Route::post('/{account}/money-withdraw', 'MoneyController@moneyWithdraw')
    ->name('money-withdraw')
    ->middleware('verifications.verify:money-withdraw');
```

Method `moneyWithdrawForm` will display the blade view with the form that will perform the POST to `/{account}/money-withdraw` on submit. But User is verified ahead, in the GET route, so his UX will be smooth.

Tip: you can of course add middleware to the whole group of routes:
```php
Route::middleware(['verifications.verify:money-balance'])->group(static function () {
    Route::get('/{account}/money-balance', 'MoneyController@moneyBalance')
        ->name('money-balance');
        
    Route::get('/{account}/money-withdraw', 'MoneyController@moneyWithdrawAutoConfirmator')
        ->name('money-withdraw-auto-confirmator');
        
    Route::post('/{account}/money-withdraw', 'MoneyController@moneyWithdraw')
        ->name('money-withdraw');
    // ...
});
```

### Advanced use case

In some scenarios you may want to use the verification on some action and needs further customization (i.e. only verify if some other conditions are met or provide a custom redirectTo method for POST actions verifications). You may use the Verification facade to manually run the verification in your controller providing the closure that will be run only after successful verification:

```php
public function postDownloadInvoice(Invoice $invoice)
{
    // this code will run on the attempt before the verification and then again, after the successful verification
    if (!$invoice->isPublished()) {
        throw new InvoiceNotPublishedException();  
    }  

    return Verification::verify('download-invoice',             // name of the action
                                '/invoices',                    // URL user will be redirect after verification (he must click to download the invoice again manually :( 
                                function () use ($invoice) {
                                    // on the other hand this code will run only once, after the verification
                                    return $invoice->download();
                                });
}
``` 

### Customizing the views

To customized the default blade views you just need to publish them using:

```php
php artisan vendor:publish --provider="Brackets\Verifications\VerificationServiceProvider" --tag="views"
```

### Conditional verification

In some cases, you may want to provide an option for your users to choose if they should verify some specific action.
Or maybe you want to allow users with some specific role/permission to skip the verification for some specific action.
In these cases you just need to define the strictly named method **isVerificationRequired(string $action)**.

**Example:**
```php
class User extends Authenticatable implements Verifiable
{
    use VerifiableTrait;
    // ...

    public function isVerificationRequired($action) {
    
        // allow super admin to all actions
        if ($this->hasRole('Admin') {
            return false;
        }
    
        if ($action == 'withdraw-money') {
            // allow withdraw-money action to be optional (i.e. user can set it in their profile)
            if (!$this->withdraw_monoey_requires_verification) {
                return false;
            }        
        }
        
        return true; 
    }
```

### Two factor authentication

Special case for the use of this package is Two-Factor Authentication.

Imagine simple scenario when 2FA is required for all users.

First, add new 2FA action to the config:

```php
    'actions' => [
        '2FA' => [
            'enabled' => true,
            'channel' => 'sms',
            'keep_verified_during_session' => true, 
            'code' => [
                // ...
            ],
        ],
    ]
```

And then protect all your routes:

```php
Route::group([Brackets\Verifications\Middleware\VerifyMiddleware::class, '2FA'], function(){

    // all your routes goes here
    
})
``` 

## Channels

The packages ships with two default channels - email and sms.

### Email

The package uses the default Laravel's [Mail](https://laravel.com/docs/mail). facade to send emails, so be sure to configure it properly.

### SMS

The package ships with the one SMS provider - Twilio.

To use Twilio, you just need to provide these variables in your `.env` file:

```.
TWILIO_SID:"INSERT YOUR TWILIO SID HERE"
TWILIO_AUTH_TOKEN:"INSERT YOUR TWILIO TOKEN HERE"
TWILIO_NUMBER:"INSERT YOUR TWILIO NUMBER IN [E.164] FORMAT"
```

Check out [this blogpost](https://www.twilio.com/blog/create-sms-portal-laravel-php-twilio) to find out more info about the Twilio integration.


## Security

If you discover any security related issues, please email [pavol.perdik@brackets.sk](mailto:pavol.perdik@brackets.sk) instead of using the issue tracker.

## Credits

- [Miroslav Trnavsky](https://github.com/miroslavtrnavsky)
- [Pavol Perdik](https://github.com/palypster)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
