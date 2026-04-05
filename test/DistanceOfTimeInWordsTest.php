<?php

namespace php_rutils\test;

use php_rutils\RUtils;

class FixedDt extends \php_rutils\Dt
{
    private $fixedNow;

    public function __construct()
    {
        $this->fixedNow = new \DateTimeImmutable('2026-04-01 00:00:00', new \DateTimeZone('UTC'));
    }

    protected function now(?\DateTimeZone $timeZone = null): \DateTime
    {
        $now = \DateTime::createFromImmutable($this->fixedNow);
        if ($timeZone) {
            $now->setTimezone($timeZone);
        }

        return $now;
    }
}

class DistanceOfTimeInWordsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \php_rutils\Dt
     */
    private $_object;

    /**
     * @var string
     */
    private $_previousTimezone;

    protected function setUp(): void
    {
        $this->_previousTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $this->_object = new FixedDt();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->_previousTimezone);
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testAccuracyYear()
    {
        $nowTime = strtotime('2026-04-01 00:00:00 UTC');
        $tomorrow = strtotime('2026-04-02 00:00:00 UTC');
        $afterTomorrow = strtotime('2026-04-03 00:00:00 UTC');
        $dNowTomorrow = $tomorrow - $nowTime;

        $testData = [
            //past
            date('Y-m-d H:i:s', $nowTime - 1) => 'менее минуты назад',
            date('Y-m-d H:i:s', $nowTime - 60) => "минуту\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60) => "2 минуты\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60) => "5 минут\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 60 * 60) => "час\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60 * 60) => "2 часа\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60 * 60) => "5 часов\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 24 * 60 * 60) => 'вчера',
            date('Y-m-d H:i:s', $nowTime - 2 * 24 * 60 * 60) => 'позавчера',
            date('Y-m-d H:i:s', $nowTime - 3 * 24 * 60 * 60) => "3 дня\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 8 * 24 * 60 * 60 - 1 * 60 * 60) => "8 дней\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 32 * 24 * 60 * 60) => "месяц\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 32 * 24 * 60 * 60) => "2 месяца\xC2\xA0назад",
            ($nowTime - 366 * 24 * 60 * 60) => "год\xC2\xA0назад",
            ($nowTime - 2 * 370 * 24 * 60 * 60) => "2 года\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60) => "10 лет\xC2\xA0назад",
            //future
            date('Y-m-d H:i:s', $nowTime + 1) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 2 * 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 5 * 60) => ($dNowTomorrow >= 300 ? "через\xC2\xA05 минут" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 60 * 60) => ($dNowTomorrow >= 3600 ? "через\xC2\xA0час" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 2 * 60 * 60) => ($dNowTomorrow >= 7200 ? "через\xC2\xA02 часа" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 5 * 60 * 60) => ($dNowTomorrow >= 18000 ? "через\xC2\xA05 часов" : 'завтра'),
            date('Y-m-d H:i:s', $tomorrow) => "через\xC2\xA01 день",
            date('Y-m-d H:i:s', $afterTomorrow) => "через\xC2\xA02 дня",
            date('Y-m-d H:i:s', $nowTime + 3 * 24 * 60 * 60) => "через\xC2\xA03 дня",
            date('Y-m-d H:i:s', $nowTime + 8 * 24 * 60 * 60) => "через\xC2\xA08 дней",
            date('Y-m-d H:i:s', $nowTime + 32 * 24 * 60 * 60) => "через\xC2\xA0месяц",
            date('Y-m-d H:i:s', $nowTime + 2 * 32 * 24 * 60 * 60) => "через\xC2\xA02 месяца",
            ($nowTime + 366 * 24 * 60 * 60) => "через\xC2\xA0год",
            ($nowTime + 2 * 370 * 24 * 60 * 60) => "через\xC2\xA02 года",
            ($nowTime + 10 * 370 * 24 * 60 * 60) => "через\xC2\xA010 лет",
        ];

        foreach ($testData as $toTime => $expected) {
            $actual = $this->_object->distanceOfTimeInWords($toTime);
            if (str_starts_with($expected, '/^')) {
                $this->assertMatchesRegularExpression($expected, $actual);
                continue;
            }

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testAccuracyMonth()
    {
        $nowTime = strtotime('2026-04-01 00:00:00 UTC');
        $tomorrow = strtotime('2026-04-02 00:00:00 UTC');
        $afterTomorrow = strtotime('2026-04-03 00:00:00 UTC');
        $dNowTomorrow = $tomorrow - $nowTime;

        $testData = [
            //past
            date('Y-m-d H:i:s', $nowTime - 1) => 'менее минуты назад',
            date('Y-m-d H:i:s', $nowTime - 60) => "минуту\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60) => "2 минуты\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60) => "5 минут\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 60 * 60) => "час\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60 * 60) => "2 часа\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60 * 60) => "5 часов\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 24 * 60 * 60) => 'вчера',
            date('Y-m-d H:i:s', $nowTime - 2 * 24 * 60 * 60) => 'позавчера',
            date('Y-m-d H:i:s', $nowTime - 3 * 24 * 60 * 60) => "3 дня\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 8 * 24 * 60 * 60 - 1 * 60 * 60) => "8 дней\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 32 * 24 * 60 * 60) => "месяц\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 32 * 24 * 60 * 60) => "2 месяца\xC2\xA0назад",
            ($nowTime - 366 * 24 * 60 * 60) => "год\xC2\xA0назад",
            ($nowTime - 2 * 370 * 24 * 60 * 60) => "2 года\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 + 2 * 24 * 60 * 60 + 12) => "10 лет, %d месяц%S\xC2\xA0назад",
            //future
            date('Y-m-d H:i:s', $nowTime + 1) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 2 * 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 5 * 60) => ($dNowTomorrow >= 300 ? "через\xC2\xA05 минут" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 60 * 60) => ($dNowTomorrow >= 3600 ? "через\xC2\xA0час" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 2 * 60 * 60) => ($dNowTomorrow >= 7200 ? "через\xC2\xA02 часа" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 5 * 60 * 60) => ($dNowTomorrow >= 18000 ? "через\xC2\xA05 часов" : 'завтра'),
            date('Y-m-d H:i:s', $tomorrow) => "через\xC2\xA01 день",
            date('Y-m-d H:i:s', $afterTomorrow) => "через\xC2\xA02 дня",
            date('Y-m-d H:i:s', $nowTime + 3 * 24 * 60 * 60) => "через\xC2\xA03 дня",
            date('Y-m-d H:i:s', $nowTime + 8 * 24 * 60 * 60) => "через\xC2\xA08 дней",
            date('Y-m-d H:i:s', $nowTime + 32 * 24 * 60 * 60) => "через\xC2\xA0месяц",
            date('Y-m-d H:i:s', $nowTime + 2 * 32 * 24 * 60 * 60) => "через\xC2\xA02 месяца",
            ($nowTime + 366 * 24 * 60 * 60) => "через\xC2\xA0год",
            ($nowTime + 2 * 370 * 24 * 60 * 60) => "через\xC2\xA02 года",
            ($nowTime + 10 * 370 * 24 * 60 * 60 + 2 * 24 * 60 * 60 + 12) => "через\xC2\xA010 лет, %d месяц%S",
        ];

        foreach ($testData as $toTime => $format) {
            $actual = $this->_object->distanceOfTimeInWords($toTime, null, RUtils::ACCURACY_MONTH);
            if (str_starts_with($format, '/^')) {
                $this->assertMatchesRegularExpression($format, $actual);
                continue;
            }

            $this->assertStringMatchesFormat($format, $actual);
        }
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testAccuracyDay()
    {
        $nowTime = strtotime('2026-04-01 00:00:00 UTC');
        $tomorrow = strtotime('2026-04-02 00:00:00 UTC');
        $afterTomorrow = strtotime('2026-04-03 00:00:00 UTC');
        $dNowTomorrow = $tomorrow - $nowTime;

        $testData = [
            //past
            date('Y-m-d H:i:s', $nowTime - 1) => 'менее минуты назад',
            date('Y-m-d H:i:s', $nowTime - 60) => "минуту\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60) => "2 минуты\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60) => "5 минут\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 60 * 60) => "час\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60 * 60) => "2 часа\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60 * 60) => "5 часов\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 24 * 60 * 60) => 'вчера',
            date('Y-m-d H:i:s', $nowTime - 2 * 24 * 60 * 60) => 'позавчера',
            date('Y-m-d H:i:s', $nowTime - 3 * 24 * 60 * 60) => "3 дня\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 8 * 24 * 60 * 60 - 1 * 60 * 60) => "8 дней\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 32 * 24 * 60 * 60) => "1 месяц, %d д%s\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 32 * 24 * 60 * 60) => "2 месяца, %d д%s\xC2\xA0назад",
            ($nowTime - 366 * 24 * 60 * 60) => "1 год, %d д%s\xC2\xA0назад",
            ($nowTime - 2 * 370 * 24 * 60 * 60 - 72 * 24 * 60 * 60 - 12 * 60) => "2 года, 2 месяца, %d д%s\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 + 2 * 24 * 60 * 60 + 12) => "10 лет, %d месяц%S\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 - 62 * 24 * 60 * 60) => "10 лет, %d месяц%s, %d д%s\xC2\xA0назад",
            //future
            date('Y-m-d H:i:s', $nowTime + 1) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 2 * 60) => '/^(?:менее чем через минуту|через\xC2\xA0минуту|через\xC2\xA02 минуты)$/',
            date('Y-m-d H:i:s', $nowTime + 5 * 60) => ($dNowTomorrow >= 300 ? "через\xC2\xA05 минут" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 60 * 60) => ($dNowTomorrow >= 3600 ? "через\xC2\xA0час" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 2 * 60 * 60) => ($dNowTomorrow >= 7200 ? "через\xC2\xA02 часа" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 5 * 60 * 60) => ($dNowTomorrow >= 18000 ? "через\xC2\xA05 часов" : 'завтра'),
            date('Y-m-d H:i:s', $tomorrow) => "через\xC2\xA01 день",
            date('Y-m-d H:i:s', $afterTomorrow) => "через\xC2\xA02 дня",
            date('Y-m-d H:i:s', $nowTime + 3 * 24 * 60 * 60) => "через\xC2\xA03 дня",
            date('Y-m-d H:i:s', $nowTime + 8 * 24 * 60 * 60) => "через\xC2\xA08 дней",
            date('Y-m-d H:i:s', $nowTime + 32 * 24 * 60 * 60) => "через\xC2\xA01 месяц, %d д%s",
            date('Y-m-d H:i:s', $nowTime + 2 * 32 * 24 * 60 * 60) => "через\xC2\xA02 месяца, %d д%s",
            ($nowTime + 367 * 24 * 60 * 60) => "через\xC2\xA01 год, %d д%s",
            ($nowTime + 2 * 370 * 24 * 60 * 60) => "через\xC2\xA02 года, %d д%s",
            ($nowTime + 10 * 370 * 24 * 60 * 60 + 65 * 24 * 60 * 60 + 12) => "через\xC2\xA010 лет, %d месяц%S, %d д%s",
        ];

        foreach ($testData as $toTime => $format) {
            $actual = $this->_object->distanceOfTimeInWords($toTime, null, RUtils::ACCURACY_DAY);
            if (str_starts_with($format, '/^')) {
                $this->assertMatchesRegularExpression($format, $actual);
                continue;
            }

            $this->assertStringMatchesFormat($format, $actual);
        }
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testAccuracyMinute()
    {
        $nowTime = strtotime('2026-04-01 00:00:00 UTC');
        $tomorrow = strtotime('2026-04-02 00:00:00 UTC');
        $dNowTomorrow = $tomorrow - $nowTime;

        $testData = [
            //past
            date('Y-m-d H:i:s', $nowTime - 1) => 'менее минуты назад',
            date('Y-m-d H:i:s', $nowTime - 60) => "минуту\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60) => "2 минуты\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60) => "5 минут\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 60 * 60) => "час\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 60 * 60) => "2 часа\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 5 * 60 * 60) => "5 часов\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 24 * 60 * 60) => 'вчера',
            date('Y-m-d H:i:s', $nowTime - 2 * 24 * 60 * 60) => 'позавчера',
            date('Y-m-d H:i:s', $nowTime - 3 * 24 * 60 * 60) => "3 дня\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 8 * 24 * 60 * 60 - 1 * 60 * 60) => "8 дней, 1 час\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 32 * 24 * 60 * 60) => "1 месяц, %d д%s\xC2\xA0назад",
            date('Y-m-d H:i:s', $nowTime - 2 * 32 * 24 * 60 * 60) => "2 месяца, %d д%s\xC2\xA0назад",
            ($nowTime - 366 * 24 * 60 * 60) => "1 год, %d д%s\xC2\xA0назад",
            ($nowTime - 2 * 370 * 24 * 60 * 60 - 72 * 24 * 60 * 60 - 12 * 60) => "2 года, 2 месяца, %d д%s\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 + 2 * 24 * 60 * 60 + 12) => "10 лет, %d месяц%S\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 - 62 * 24 * 60 * 60) => "10 лет, %d месяц%s, %d д%s\xC2\xA0назад",
            ($nowTime - 10 * 370 * 24 * 60 * 60 - 65 * 24 * 60 * 60 - 90 * 60) => "10 лет, %d месяц%S, %d д%s, %d ч%s, %d минут%S\xC2\xA0назад",
            //future
            date('Y-m-d H:i:s', $nowTime + 1) => ($dNowTomorrow >= 1 ? 'менее чем через минуту' : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 60) => ($dNowTomorrow >= 60 ? "через\xC2\xA0минуту" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 2 * 60) => ($dNowTomorrow >= 120 ? "через\xC2\xA02 минуты" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 5 * 60) => ($dNowTomorrow >= 300 ? "через\xC2\xA05 минут" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 60 * 60) => ($dNowTomorrow >= 3600 ? "через\xC2\xA0час" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 2 * 60 * 60) => ($dNowTomorrow >= 7200 ? "через\xC2\xA02 часа" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 5 * 60 * 60) => ($dNowTomorrow >= 18000 ? "через\xC2\xA05 часов" : 'завтра'),
            date('Y-m-d H:i:s', $nowTime + 3 * 24 * 60 * 60) => "через\xC2\xA03 дня",
            date('Y-m-d H:i:s', $nowTime + 8 * 24 * 60 * 60) => "через\xC2\xA08 дней",
            date('Y-m-d H:i:s', $nowTime + 32 * 24 * 60 * 60) => "через\xC2\xA01 месяц, %d д%s",
            date('Y-m-d H:i:s', $nowTime + 2 * 32 * 24 * 60 * 60) => "через\xC2\xA02 месяца, %d д%s",
            ($nowTime + 367 * 24 * 60 * 60) => "через\xC2\xA01 год, %d д%s",
            ($nowTime + 2 * 370 * 24 * 60 * 60) => "через\xC2\xA02 года, %d д%s",
            ($nowTime + 10 * 370 * 24 * 60 * 60 + 65 * 24 * 60 * 60 + 90 * 60) => "через\xC2\xA010 лет, %d месяц%S, %d д%s, %d ч%s, %d минут%S",
        ];

        foreach ($testData as $toTime => $format) {
            $this->assertStringMatchesFormat(
                $format,
                $this->_object->distanceOfTimeInWords(
                    $toTime,
                    null,
                    RUtils::ACCURACY_MINUTE
                )
            );
        }
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testFromTimePast()
    {
        $fromTime = new \DateTime('2000-01-01 00:00:00', new \DateTimeZone('UTC'));
        $toTime = new \DateTime('2067-04-15 04:02:00', new \DateTimeZone('UTC'));

        $this->assertEquals(
            "через\xC2\xA067 лет, 3 месяца, 14 дней, 4 часа, 2 минуты",
            $this->_object->distanceOfTimeInWords($toTime, $fromTime, RUtils::ACCURACY_MINUTE)
        );
    }

    /**
     * @covers \php_rutils\Dt::distanceOfTimeInWords
     */
    public function testFromTimeFuture()
    {
        $fromTime = new \DateTime('2067-04-15 04:02:00', new \DateTimeZone('UTC'));
        $toTime = new \DateTime('2000-01-01 00:00:00', new \DateTimeZone('UTC'));

        $this->assertEquals(
            "67 лет, 3 месяца, 14 дней, 4 часа, 2 минуты\xC2\xA0назад",
            $this->_object->distanceOfTimeInWords($toTime, $fromTime, RUtils::ACCURACY_MINUTE)
        );
    }
}
