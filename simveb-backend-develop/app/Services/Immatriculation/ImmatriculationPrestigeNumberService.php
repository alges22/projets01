<?php

namespace App\Services\Immatriculation;

use App\Models\Immatriculation\Immatriculation;

class ImmatriculationPrestigeNumberService
{
    /**
     * @param string $template
     * @return mixed
     */
    public function getSuggestions(string $template): mixed
    {
        $template = strtoupper($template);

        $templateArgs = sortedArrayUnique(str_split($template));

        $numbers = [];
        do {
            $generatedNumber = $this->generateNumber($template, $templateArgs, $numbers ? $numbers[array_key_last($numbers)] : '0000');

            if (is_string($generatedNumber)) {
                $numbers[] = $generatedNumber;
            }
        } while (is_string($generatedNumber));

        return $numbers;
    }

    /**
     * @param string $template
     * @param string $lastNumber
     * @param array $templateArgs
     * @return string|bool
     */
    public function generateNumber(string $template, array $templateArgs, string $lastNumber): string|bool
    {
        $template = strtoupper($template);

        foreach (str_split($template) as $index => $value) {
            if ($value == $templateArgs[0]) {
                $arg1Positions[] = $index;
            } else {
                $arg2Positions[] = $index;
            }
        }

        $replaced = str_replace($templateArgs[0], str_split($lastNumber)[0], str_replace($templateArgs[1], '0', $template));
        $replacedArr = str_split($replaced);

        $uniqValues = sortedArrayUnique($replacedArr);

        foreach ([$templateArgs[1], $templateArgs[0]] as $parameter) {
            for ($i = 0; $i <= 9; $i++) {
                if ($parameter == $templateArgs[0]) {
                    $arg1 = $i;
                    $arg2 = count($uniqValues) == 2 ? $uniqValues[1] : $uniqValues[0];
                } else {
                    $arg2 = $i;
                    $arg1 = $uniqValues[0];
                }

                $prestige = '';
                foreach ($replacedArr as $index => $value) {
                    if ($parameter == $templateArgs[0] && in_array($index, $arg1Positions)) {
                        $prestige .= $arg1;
                    } elseif ($parameter == $templateArgs[1] && in_array($index, $arg2Positions)) {
                        $prestige .= $arg2;
                    } else {
                        $prestige .= $value;
                    }
                }

                if ($this->checkNumber($prestige, $lastNumber, sortedArrayUnique(str_split($prestige)))) {
                    return $prestige;
                }
            }
        }

        return false; // no available number in this serie
    }

    /**
     * @param string $number
     * @param string $lastImNumber
     * @param string $array $args
     * @return bool
     */
    public function checkNumber(string $number, string $lastImNumber, array $args): bool
    {
        // TODO: check only on current serie
        // TODO: exclude reserved numbers

        return (
            (int) $number > (int) $lastImNumber
            && count($args) == 2
            && (int) $args[0] != (int) $args[1]
            && Immatriculation::where('number', $number)->doesntExist()
        );
    }
}
