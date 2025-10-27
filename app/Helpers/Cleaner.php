<?php

namespace App\Helpers;

use Purifier;

class Cleaner
{
    /**
     * Membersihkan semua input string dalam array (rekursif).
     */
    public static function cleanAll(array $inputs): array
    {
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $inputs[$key] = Purifier::clean($value);
            } elseif (is_array($value)) {
                $inputs[$key] = self::cleanAll($value);
            }
        }
        return $inputs;
    }

    /**
     * Membersihkan satu string saja.
     */
    public static function clean(string $input): string
    {
        return Purifier::clean($input);
    }
}