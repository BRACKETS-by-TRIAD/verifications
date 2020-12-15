# Verifications
Package for verifying sms/email generated codes.
## Installation

`harbor artisan verifications:instal`

## Configuration
In your project `.env` file define:

```.
TWILIO_SID:
TWILIO_AUTH_TOKEN:
TWILIO_NUMBER:
```

Basic configuration defined in `/config/verifications.php`:
```.
    'simple_verifications_enabled' => true, // enable if you need to use this package as middle step to verify some custom type of action
    '2fa' => [
        'required_for_all_users' => true,   // enables 2fa for all system users
        'set_per_user_available' => false,  // enables user's personal setup for 2fa in profile
        'generated_attributes' => [         // fill only if u want generate attributes to profile
            [
                'label' => 'Phone',
                'name' => 'phone'
            ]
        ]
    ],
    'code' => [
        'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
        'length' => 6,                      // specifies verification code length, set to 6 by default
        'validity_length_minutes' => 10     // specifies length of code validity
    ],
```
## Usage

Your entity should implement **Verifiable** interface, with generated method stubs.

**E.g.:**
```.
class User extends Authenticatable implements Verifiable
{
    // ...
    
    public function getModelInstance(): Model
    {
        return $this;
    }

    public function verifiableAttributes(): MorphMany
    {
        return $this->morphMany(VerifiableAttribute::class, 'verifiable');
    }

    public function getPhoneAttribute(): string
    {
        return $this->verifiableAttributes()
                    ->where('attribute_name', '=', 'phone')
                    ->first()
                    ->attribute_value;
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

If u need to implement this package for two factor authentication, there is one more thing
you need to do - add `use TwoFactorVerifiableTrait` in your **User** model.
