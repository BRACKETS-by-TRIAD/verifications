# Verifications
Package used for verification of any kind of actions by sms/email generated codes. Although, would be used for two factor authentication.

## Installation

1. `composer require brackets/verifications`

2. Register `Brackets\Verifications\VerificationServiceProvider::class,` in your `config/app.php`

3. `php artisan verifications:install`

## Configuration

### Base
Required configuration you need to setup is defined in `/config/verifications.php`. 
As you can see, there are some example cases for it's usage. Please, keep strict key-names and values. 
```.
    'enabled' => true, // true, false                       // global package enable/disable for test purposes @ localhost
    'actions' => [
        'withdraw-money' => [
            'enabled' => 'optional',                        // forced, optional, false
            'channel' => 'sms',                             // sms, email
            'verified_action_valid_minutes' => 15,          // specifies how many minutes does it take to require another code verification
            'code' => [
                'type' => 'numeric',                        // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                              // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10,            // specifies length of code validity
            ],
        ],
        '2fa_users' => [
            'enabled' => 'optional',                        // forced, optional, false
            'channel' => 'sms',                             // sms, email
            'action_verified_in_minutes' => 15,             // specifies how many minutes does it take to require another code verification
            'code' => [
                'type' => 'numeric',                        // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                              // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10,            // specifies length of code validity
            ],
        ],
    ]
```

### Twilio - SMS
If you need to use Twilio client for SMS notifications, you are 
supposed to define common variables your project `.env` file:

```.
TWILIO_SID:"INSERT YOUR TWILIO SID HERE"
TWILIO_AUTH_TOKEN:"INSERT YOUR TWILIO TOKEN HERE"
TWILIO_NUMBER:"INSERT YOUR TWILIO NUMBER IN [E.164] FORMAT"
```

More info here: https://www.twilio.com/blog/create-sms-portal-laravel-php-twilio

## Usage

### GET request verification
If your config is correctly setup, then all you need to do, is to follow common steps:

1. Your authenticated entity should implement **Verifiable** interface, with generated method stubs and use **VerifiableTrait**

2. Your protected route should be guarded by **VerificationMiddleware** 
* `...->middleware('verifications.verify:{action-name}');` - action name must be the same as a name in config

**E.g.:**  
Let's say, you want to verify the **withdraw-money** operation in your system. 

1. Implement **Verifiable** interface with generated method stubs and use **VerifiableTrait** in your authenticated model entity.

```.
class User extends Authenticatable implements Verifiable
{
    use VerifiableTrait;
    // ...

    public function getPhoneAttribute(): string
    {
        return $this->owner->contact->phone;    // you can use eager loading
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

### POST request verification
Your entity should implement **Verifiable** interface, with generated method stubs.

**E.g.:**
```.
class User extends Authenticatable implements Verifiable
{
    // ...

    public function getPhoneAttribute(): string
    {
        return $this->owner->contact->phone;    // you can use eager loading
    }

    public function getEmailAttribute(): string
    {
        return $this->email;
    }
    
    // ...
}
```

### Generating attributes
If you need to insert **phone_number**/**email** to your table, you can use these commands, to generate migration and insert attributes:

1. Adding email - `php artisan verifications:add-email {table_name}`

2. Adding phone_number - `php artisan verifications:add-phone {table_name}`

Then you just neeed to change return value in the method, where you want to insert verification middle step.

`@verify(Verifiable $verifiable, String $redirectTo = '/')` 

**Method params:** 

`$verifiable` - Entity which implements **Verifiable** interface

`$redirectTo` - route name to redirect if the verification passed 

```.
    public function foo()
    {
        // ...
        return (new Verification())->verify($user, '/home')    
    }
```
### Two factor authentication

If you have setup for optional 2FA (set from user's profile), 
you need to insert new database attribute **login_verify_enabled** to your entity.
It would be done manually (by making own migration,..) or call
command, which generates migration and call migrate:


**php artisan verifications:add-2fa {table_name}** => e.g.: `php artisan verifications:add-2fa users`

### Adding other notification channel
If you need to send verification codes with different channel, you need to implement it.

...TBD...

### Views
TBD - craftable admin-auth generating..


`php artisan admin:generate:user admin_users`
