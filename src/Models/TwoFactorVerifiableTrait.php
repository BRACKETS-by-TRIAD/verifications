<?php


namespace Brackets\Verifications\Models;


use Illuminate\Http\Request;

trait TwoFactorVerifiableTrait
{
    public function saveVerifiableAttributes(Request $request): Void
    {
        $attributes = $request->get('attributes[]');

        foreach ($attributes as $attribute => $value) {
            $verifiableAttribute = $this->findOrCreateVerifiableAttribute($attribute);
            $verifiableAttribute->attribute_value = $value;
            $verifiableAttribute->save();
        }
    }

    private function findOrCreateVerifiableAttribute($attribute)
    {
        return VerifiableAttribute::where('verifiable_type', get_class($this))
                                  ->where('verifiable_id', $this->id)
                                  ->where('attribute_name', $attribute)
                                  ->first()
            ?? (new VerifiableAttribute())->fill($this->getFilledAttributes($attribute))->save();
    }

    private function getFilledAttributes($attribute)
    {
        return [
            'verifiable_type' => get_class($this),
            'verifiable_id' => $this->id,
            'attribute_name' => $attribute
        ];
    }
}
