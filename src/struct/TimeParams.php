<?php

namespace php_rutils\struct;

class TimeParams
{
    /**
     * Date format, use PHP date() function specification:
     * http://www.php.net/manual/en/function.date.php
     */
    public string $format = 'd.m.Y';

    /**
     * Date value, default=null translates to 'now'.
     * For string values use matched PHP rules:
     * http://www.php.net/manual/en/datetime.formats.php
     * Int value as Unix timestamp
     */
    public int|string|\DateTime|null $date = null;

    /**
     * Timezone value, default=null translates to default PHP timezone.
     * For string values use matched PHP rules:
     * http://www.php.net/manual/en/timezones.php
     */
    public string|\DateTimeZone|null $timezone = null;

    /**
     * Is month inflected (января, февраля), default false
     */
    public bool $monthInflected = false;

    /**
     * Is day inflected (среду, пятницу) default false
     */
    public bool $dayInflected = false;

    /**
     * Is preposition used (во вторник), default false
     * $preposition=true automatically implies $dayInflected=true
     */
    public bool $preposition = false;

    /** Create params from array or with default values */
    public static function create(?array $aParams = null): self
    {
        $params = new self();
        if ($aParams === null) {
            return $params;
        }

        foreach ($aParams as $name => $value) {
            $params->$name = $value;
        }

        return $params;
    }

    public function __set(string $name, mixed $value): never
    {
        throw new \InvalidArgumentException("Wrong parameter name: $name");
    }
}
