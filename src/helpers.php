<?php

function zodiac(int $month, int $date): string
{
    $zodiacs = [
        ["name" => "Capricorn", "start_date" => "12-22", "end_date" => "01-19"],
        ["name" => "Aquarius", "start_date" => "01-20", "end_date" => "02-18"],
        ["name" => "Pisces", "start_date" => "02-19", "end_date" => "03-20"],
        ["name" => "Aries", "start_date" => "03-21", "end_date" => "04-19"],
        ["name" => "Taurus", "start_date" => "04-20", "end_date" => "05-20"],
        ["name" => "Gemini", "start_date" => "05-21", "end_date" => "06-20"],
        ["name" => "Cancer", "start_date" => "06-21", "end_date" => "07-22"],
        ["name" => "Leo", "start_date" => "07-23", "end_date" => "08-22"],
        ["name" => "Virgo", "start_date" => "08-23", "end_date" => "09-22"],
        ["name" => "Libra", "start_date" => "09-23", "end_date" => "10-22"],
        ["name" => "Scorpio", "start_date" => "10-23", "end_date" => "11-21"],
        ["name" => "Sagitarius", "start_date" => "11-22", "end_date" => "12-21"]
    ];

    $year = date("Y") . '-';
    $birthDate = strtotime("{$year}{$month}-{$date}");

    foreach ($zodiacs as $zodiac) {
        $startDate = strtotime($year . $zodiac['start_date']);
        $endDate = strtotime($year . $zodiac['end_date']);

        if($birthDate >= $startDate && $birthDate <= $endDate)
            return $zodiac['name'];
    }

    return 'Unknown';
}

function getRandomEnumValue(array $enum) {
    $keys = array_keys($enum);
    $randomKey = $keys[array_rand($keys)];
    return $enum[$randomKey];
}
