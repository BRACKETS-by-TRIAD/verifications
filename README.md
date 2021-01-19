# Verifications
This package should be used for code-based verification of of actions (routes)

Currently packages supports two channels out of the box:
- sms
- email

but it's easy to extend package to support custom channels.

Packages ships also with a simple frontend that could be easily overridden to meet your UX.

One special case of a verification is a Two-factor authentication, but the package is intentionally designed to be versatile. 

## Installation

1. `composer require brackets/verifications`

2. `php artisan verifications:install`

## Usage

### Implementing Verifiable
First of all, you need to have an authenticated user (i.e. User model) that implements **Verifiable** interface and use **VerifiableTrait**. Interface requires you to define two methods that are - in a typical scenario - just the accessors methods for the `email` and `phone` own attributes on your user User (but you have the option if i.e. these attributes exists on some related model like `UserProfileData` etc.):

```
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
 
```
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

```
...->middleware('verifications.verify:{my-action}');
```

**Example:**  
Let's say we want to verify the secret **money-balance** screen.

Define the action in your config:
```
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

```
Route::get('/{account}/money-balance', 'MoneyController@moneyBalance')
    ->name('money-balance')
    ->middleware('verifications.verify:money-balance');
```

Every time users tries to go to the `/{account}/money-balance` URL, he will be redirected to the verification screen where he is required to provide a code that was sent to him. 

### POST request verification

Verifying POST actions is a bit more tricky because user cannot be redirected back to the POST request (this is technically impossible).

But of course you can block the access to some POST action until user verifies it. Once he does verify it, everything works for him smoothly. But until he does and hit the POST route, he will be redirected back to the previous GET URL.

You have two options here:
1. either make sure User is always verified on some GET route *before* he performs the action.
2. you can create a JavaScript script that will perform the POST request on Users behalf on a GET route, if he is verified  

**Example:**  
Let's continue with our MoneyApp example. But now we want to protect the **money-withdraw** action.

The protection of a POST route is very similar:
```
Route::post('/{account}/money-withdraw', 'MoneyController@moneyWithdraw')
    ->name('money-withdraw')
    ->middleware('verifications.verify:money-withdraw');
```

Tip: you can of course add middleware to the whole group of routes:
```
Route::middleware(['verifications.verify:money-balance'])->group(static function () {
    Route::get('/{account}/money-balance', 'MoneyController@moneyBalance')
        ->name('money-balance');
        
    Route::post('/{account}/money-withdraw', 'MoneyController@moneyWithdraw')
        ->name('money-withdraw');
    // ...
});
```

### Other use

In some scenarios you may want to use the verification on some action and needs further customization (i.e. only verify if some other conditions are met or provide a custom redirectTo method for POST actions verifications). You may use the Verification facade to manually run the verification in your controller providing the closure that will be run only after successful verification:

```
public function postDownloadInvoice(Invoice $invoice)
{
    // this code will run on the attempt before and verification and then again after the verification
    if (!$invoice->isPublished()) {
        throw InvoiceNotPublishedException();  
    }  

    return Verification::verify('download-invoice', // name of the action
                                '/invoices',        // URL user will be redirect after verification (he must click to download the invoice againa manually :( 
                                function () use ($invoice) {
                                    return $invoice->download();
                                });
}
``` 

### Customizing view

Base view of insert code form is stored in `verifications/resources/views/verification.blade.php`.
You can easily override it by creating the file `resources/views/vendor/brackets/verifications/verification.blade.php`.

### Conditional verification

In some cases, you may want to provide an option for your users to choose if they should verify some specific action.
Or maybe you want to allow users with some specific role/permission to skip the verification for some specific action.
In these cases you just need to define the strictly named method **isVerificationRequired(string $action)**.

**Example:**
```.
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

1. add 2FA to the config

```
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

2. protect all your routes

```
Route::group([Brackets\Verifications\Middleware\VerifyMiddleware::class, '2FA'], function(){

    // all your routes goes here
    
})
``` 

## Channels

## Email
If you want to send verification codes via email, you need to set up
mailer variables your project `.env` file:

```.
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

Note: If you need to override email view, you can override it, by creating
the same named view in your project folder `resources/views/vendor/brackets/verifications/email/verification-email.blade.php`.


## Twilio - SMS
If you need to use Twilio client for SMS notifications, you are 
supposed to define common variables your project `.env` file:

```.
TWILIO_SID:"INSERT YOUR TWILIO SID HERE"
TWILIO_AUTH_TOKEN:"INSERT YOUR TWILIO TOKEN HERE"
TWILIO_NUMBER:"INSERT YOUR TWILIO NUMBER IN [E.164] FORMAT"
```

More info here: https://www.twilio.com/blog/create-sms-portal-laravel-php-twilio