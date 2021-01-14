<?php

namespace Brackets\Verifications\CodeGenerator\Contracts;

interface GeneratorInterface
{
    /**
     * Generates the code
     *
     * @param string $type could be one of the following: numeric, string
     * @param $length int lenght of the code
     * @return string generated code
     *
     * @throws \Exception
     */
    public function generate(string $type, int $length): string;
}
