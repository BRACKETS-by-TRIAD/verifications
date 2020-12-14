<?php


namespace Brackets\Verifications\Helpers;


use Illuminate\Support\Str;

abstract class CodeGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function generateCode(): string
    {
        //TODO: check if generated code doesn't exist ?

        switch(config('verifications.code.type'))
        {
            case 'numeric':
                $code = '';

                for($i = 0; $i < config('verifications.code.length', 6); $i++) {
                    $code = $code . rand(0, 9);
                }

                return $code;

            case 'string':
                return Str::random(config('verifications.code.length', 6));

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }
}
