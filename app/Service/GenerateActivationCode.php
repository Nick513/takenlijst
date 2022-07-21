<?php

namespace App\Service;

class GenerateActivationCode
{

    /**
     * Generate activation code
     *
     * @param string $email
     * @return string
     */
    public static function generate(string $email): string
    {

        // Return code
        return sha1(mt_rand(10000,99999).time().$email);

    }

}
