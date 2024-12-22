<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Helper;

class Helper
{
    public function interpretString(string $input): string
    {
        return preg_replace_callback('/\$(\{([^}]+)\}|([a-zA-Z_][a-zA-Z0-9_.]*))/', static fn ($matches) => $_SERVER[$matches[1]] ?: '', $input);
    }

    /**
     * @param string[] $strings
     *
     * @return string[]
     */
    public function interpretArray(array $strings): array
    {
        $result = [];
        foreach ($strings as $key => $string) {
            $interpretedKey = $this->interpretString($key);
            if (is_array($string)) {
                $result[$interpretedKey] = $this->interpretArray($string);
            } else {
                $result[$interpretedKey] = $this->interpretString($string);
            }
        }

        return $result;
    }
}
