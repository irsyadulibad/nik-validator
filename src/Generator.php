<?php

namespace Irsyadulibad\NIKValidator;

use DateTime;
use Irsyadulibad\NIKValidator\Enum\Gender;

class Generator
{
    private array $regions;

    public function __construct()
    {
        $this->regions = json_decode(
            file_get_contents(__DIR__ . '/assets/regions.json'),
            true
        );
    }

    public function generate(): string
    {
        $subdistrict = array_rand($this->regions['subdistricts']);
        $birthdate = $this->birthdateGen();
        $gender = getRandomEnumValue(Gender::cases());

        $uniqCode = rand(0000, 9999);
        $year = substr($birthdate->year, 2, 2);
        $date = ($gender == Gender::FEMALE)
            ? intval($birthdate->date) + 40 : $birthdate->date;

        return "{$subdistrict}{$date}{$birthdate->month}{$year}{$uniqCode}";
    }

    private function birthdateGen(): object
    {
        $constraintDate = (new DateTime())->format('Y-m-d');
        $randomBirthDateUnix = rand(strtotime('1900-01-01'), strtotime($constraintDate));
        $randomBirthDate = date('Y-m-d', $randomBirthDateUnix);

        return (object) [
            'year' => substr($randomBirthDate, 0, 4),
            'month' => substr($randomBirthDate, 5, 2),
            'date' => substr($randomBirthDate, 8, 2),
        ];
    }
}
