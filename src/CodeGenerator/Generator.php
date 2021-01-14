<?php

namespace Brackets\Verifications\CodeGenerator\Contracts;

use Illuminate\Support\Str;

class SimpleGenerator implements GeneratorInterface
{
    public function generate($type, $length): string
    {
        switch ($type) {
            case 'numeric':
                $nbDigits = $length;
                $max = 10 ** $nbDigits - 1;

                return strval(mt_rand(10 ** ($nbDigits - 1), $max));

            case 'string':
                return Str::random($length);

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }
}
