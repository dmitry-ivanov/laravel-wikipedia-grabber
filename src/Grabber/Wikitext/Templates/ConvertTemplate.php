<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

/**
 * @see https://en.wikipedia.org/wiki/Template:Convert
 * @see https://ru.wikipedia.org/wiki/Шаблон:Convert
 * @see https://en.wikipedia.org/wiki/Help:Convert_units
 */
class ConvertTemplate
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function extract()
    {
        $result = collect();

        $body = $this->body;
        $body = str_replace_first('{{', '', $body);
        $body = str_replace_last('}}', '', $body);

        $parts = array_map('trim', explode('|', $body));
        array_shift($parts);

        foreach ($parts as $part) {
            if ($unit = $this->toUnit($part)) {
                $result->push(str_plural($unit));
                return $result->implode(' ');
            }

            $result->push($part);
        }

        return $this->handleUnknownUnit($parts);
    }

    protected function handleUnknownUnit(array $parts)
    {
        $value = $parts[0];
        $unit = str_plural($parts[1]);

        return "{$value} {$unit}";
    }

    protected function toUnit($unit)
    {
        return array_get($this->units(), $unit);
    }

    /**
     * @see https://en.wikipedia.org/wiki/Help:Convert_units
     */
    protected function units()
    {
        return [
            // SI prefixes
            'a' => 'are',
            // 'm2' => 'square meter',
            'coulomb' => 'coulomb',
            'J' => 'joule',
            'N' => 'newton',
            // 'm' => 'meter',
            'T' => 'tesla',
            // 'g' => 'gram',
            'W' => 'watt',
            // 'Pa' => 'pascal',
            'Bq' => 'becquerel',
            'Ci' => 'curie',
            's' => 'second',
            // 'L' => 'liter',
            // 'l' => 'liter',
            // 'm3' => 'cubic meter',

            // Area
            'acre' => 'acre',
            'ha' => 'hectare',
            'm2' => 'square meter',
            'cm2' => 'square centimeter',
            'km2' => 'square kilometer',
            'sqin' => 'square inch',
            'sqft' => 'square foot',
            'sqyd' => 'square yard',
            'sqmi' => 'square mile',

            // Fuel efficiency
            'km/L' => 'kilometer per liter',
            'mpgimp' => 'mile per imperial gallon',
            'mpgus' => 'mile per U.S. gallon',
            'L/km' => 'liter per kilometer',
            'L/100 km' => 'liter per 100 kilometers',

            // Length
            'uin' => 'microinch',
            'in' => 'inch',
            'ft' => 'foot',
            'yd' => 'yard',
            'mi' => 'mile',
            'nmi' => 'nautical mile',
            'm' => 'meter',
            'cm' => 'centimeter',
            'mm' => 'millimeter',
            'km' => 'kilometer',
            'angstrom' => 'angstrom',

            // Mass
            'g' => 'gram',
            'kg' => 'kilogram',
            'oz' => 'ounce',
            'lb' => 'pound',
            'st' => 'stone',
            'LT' => 'long ton',
            'MT' => 'metric ton',
            'ST' => 'short ton',

            // Pressure
            'atm' => 'standard atmosphere',
            'mbar' => 'millibar',
            'psi' => 'pound per square inch',
            'Pa' => 'pascal',

            // Speed
            'km/h' => 'kilometer per hour',
            'km/s' => 'kilometer per second',
            'kn' => 'knot',
            'mph' => 'mile per hour',

            // Temperature
            'C' => 'degree Celsius',
            'F' => 'degree Fahrenheit',
            'K' => 'kelvin',
            'C-change' => 'degree Celsius change',
            'F-change' => 'degree Fahrenheit change',
            'K-change' => 'kelvin change',

            // Torque
            'lb.in' => 'pound force-inch',
            'lb.ft' => 'pound force-foot',
            'Nm' => 'newton meter',

            // Volume
            'cuin' => 'cubic inch',
            'cuft' => 'cubic foot',
            'cuyd' => 'cubic yard',
            'cumi' => 'cubic mile',
            'impgal' => 'imperial gallon',
            'impoz' => 'imperial fluid ounce',
            'usgal' => 'U.S. gallon',
            'usoz' => 'U.S. fluid ounce',
            'L' => 'liter',
            'l' => 'liter',
            'm3' => 'cubic meter',
            'cc' => 'cubic centimeter',
            'mm3' => 'cubic millimeter',

            // Hand
            'hand' => 'hand',
        ];
    }
}
