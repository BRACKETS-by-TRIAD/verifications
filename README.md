# Verifications
Package used for verification of any kind of actions by sms/email generated codes. Although, would be used for Two-Factor authentication.

## Installation

1. `composer require brackets/verifications`

2. `php artisan verifications:install`

## Usage

### Configuration
First, you need to define the routes/actions you would like to be verified.

This can be achieved in the configuration file `/config/verifications.php`. 

As you can see, there are some example cases for it's usage. Please, keep strict key-names and values. 
```.
    'enabled' => true,                                      // global package enable/disable for test purposes @ localhost
    'actions' => [
        'withdraw-money' => [
            'enabled' => true,                              // truue/false
            'channel' => 'sms',                             // sms, email
            'keep_verified_during_session' => false,        // if true, keeps verification valid while session exists
            'verified_action_valid_minutes' => 15,          // if keep_verified_during_session == false, then this config specifies how many minutes does it take to require another code verification for the same action 
            'code' => [
                'type' => 'numeric',                        // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                              // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10,            // specifies length of code validity
            ],
        ],
    ]
```

### GET request verification
If you would like to protect some GET routes:

1. your authenticated entity (typically User) should implement **Verifiable** interface and use **VerifiableTrait**

2. your protected route should be guarded by **VerificationMiddleware** 
* `...->middleware('verifications.verify:{action-name}');` - action name must be the same as a name in config

Note: If you need to insert **phone_number**/**email** attributes to your table, you can use artisan commands `verifications:add-email {table-name}` and/or `verifications:add-phone {table-name}`.

**Example:**  
Let's say, you want to verify the **withdraw-money** operation in your system. 

1. Implement **Verifiable** interface with generated method stubs and use **VerifiableTrait** in your authenticated model entity.

```.
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


2. Lastly, add **VerificationMiddleware** to your route 

```.
Route::get('/{account}/withdraw-money', 'MoneyController@withdrawMoney')
            ->name('withdraw-money')
            ->middleware('verifications.verify:withdraw-money');
```

Every time users tries to call the withdraw-money action, he will be redirected to the verification screen where he is required to provide a code that was sent to him. 

### POST request verification

TODO finish this

### Customizing view

Base view of insert code form is stored in `verifications/resources/views/verification.blade.php`. If you want to modify this base template, you can override it, by creating 
the same named view in your project folder `resources/views/brackets/verifications/verification.blade.php`.

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

1. add 2FA in the config

```
```.
    'enabled' => true,
    'actions' => [
        '2FA' => [
            'enabled' => true,                              // true/false
            'channel' => 'sms',                             // sms, email
            'keep_verified_during_session' => true,         // if true, keeps verification valid while session exists 
            'code' => [
                'type' => 'numeric',                        // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                              // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10,            // specifies length of code validity
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

Note: If you need to override email view, you can override it... TBD after testing

## Twilio - SMS
If you need to use Twilio client for SMS notifications, you are 
supposed to define common variables your project `.env` file:

```.
TWILIO_SID:"INSERT YOUR TWILIO SID HERE"
TWILIO_AUTH_TOKEN:"INSERT YOUR TWILIO TOKEN HERE"
TWILIO_NUMBER:"INSERT YOUR TWILIO NUMBER IN [E.164] FORMAT"
```

More info here: https://www.twilio.com/blog/create-sms-portal-laravel-php-twilio

## Custom channel providers

If you need to use some other channel for verification, you need follow common steps:

1. create contract for your channel, which extends `ChannelProviderInterface` 

2. create provider for your channel, which implements your contract from the 1st step

3. add binding

4. override Verification@getProvider($action)

TBD after testing