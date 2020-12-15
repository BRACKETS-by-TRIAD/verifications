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

    public function getPhoneAttribute(): string
    {
        return $this->morphMany(VerifiableAttribute::class, 'verifiable')
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

`@verify(Verifiable $verifiable, String $channel, String $redirectTo = '/')` <br/>
**Method params:** <br/>
`$verifiable` - morphable entity to verifiable  <br/>
`$channel` - channel used to send verification code 'sms'/'email' <br/>
`$redirectTo` - route name to redirect if the verification passed <br/>

```.
    public function foo()
    {
        // ...
        return (new Verification())->verify($user, 'sms', '/home')    
    }
```
