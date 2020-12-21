# Verifications
Package for verifying sms/email generated codes. Should be used for two factor authentication or verifying any simple action in your app.
## Installation

1. `composer require brackets/verifications`

2. `php artisan verifications:install`

## Configuration

### Base
Required configuration you need to setup is defined in `/config/verifications.php`. 
As you can see, there is commented-out some example cases for it's usage. Please keep strict key-names and values. 
```.
    'enabled' => true, // true, false                      // global package enable/disable for test purposes @ localhost
    'actions' => [
//        'invoices' => [
//            'enabled' => 'forced,                         // forced, optional, false
//            'model' => \App\Core\Models\Invoice::class,   // implements Verifiable
//            'channel' => 'sms'                            // sms, email
//        ]
    ],
    '2fa' => [
//        'users' => [
//            'enabled' => 'forced',                          // forced, optional, false
//            'model' => \App\Core\Models\User::class,        // implements Verifiable
//            'channel' => 'sms',                             // sms, email
//        ],
    ],

    'code' => [
        'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
        'length' => 6,                      // specifies verification code length, set to 6 by default
        'validity_length_minutes' => 10     // specifies length of code validity
    ],
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

Your entity should implement **Verifiable** interface, with generated method stubs.

**E.g.:**
```.
class User extends Authenticatable implements Verifiable
{
    // ...

    public function getPhoneAttribute(): string
    {
        return $this->owner->contact->phone;    // suitable usage
    }

    public function getEmailAttribute(): string
    {
        return $this->email;
    }
    
    // ...
}
```

Then you just neeed to change return value in the method, where you want to insert verification middle step.

`@verify(Verifiable $verifiable, String $channel, String $redirectTo = '/')` 

**Method params:** 

`$verifiable` - morphable entity to verifiable  

`$channel` - channel used to send verification code 'sms'/'email' 

`$redirectTo` - route name to redirect if the verification passed 

```.
    public function foo()
    {
        // ...
        return (new Verification())->verify($user, 'sms', '/home')    
    }
```
### Two factor authentication

If you have setup for optional 2FA (set from user's profile), 
you need to insert new database attribute **login_verify_enabled** to your entity.
It would be done manually (by making own migration,..) or call
command, which generates migration and call migrate:


**php artisan verifications:add-2fa {table_name}** => e.g.: `php artisan verifications:add-2fa users`

### Views
TBD - craftable admin-auth generating..
