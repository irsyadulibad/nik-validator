<?php

namespace Irsyadulibad\NIKValidator;

use DateTime;
use Irsyadulibad\NIKValidator\Enum\Gender;

class Validator
{
    private string $nik;
    private object $regions;

    public function __construct($nik)
    {
        $this->nik = $nik;
        $this->regions = json_decode(
            file_get_contents(__DIR__ . '/assets/regions.json')
        );
    }

    public function parse(): object
    {
        if(!$this->isValid())
            return (object) ['valid' => false];

        $bornDate = $this->getBornDate();

        return (object) [
            'nik' => $this->nik,
            'address' => $this->getAddress(),
            'age' => $this->getAge(),
            'borndate' => $bornDate,
            'gender' => $this->getGender(),
            'nextBirthday' => $this->getNextBirthday(),
            'uniqCode' => substr($this->nik, 12, 4),
            'zodiac' => zodiac($bornDate->month, $bornDate->date),
            'valid' => true,
        ];
    }

    private function getAddress(): object
    {
        $provCode = substr($this->nik, 0, 2);
        $regCode = substr($this->nik, 0, 4);
        $subCode = substr($this->nik, 0, 6);

        return (object) [
            'province' => $this->regions->provinces->$provCode ?? null,
            'regency' => $this->regions->regencies->$regCode ?? null,
            'subdistrict' => $this->regions->subdistricts->$subCode ?? null,
        ];
    }

    private function getAge(): object
    {
        $bornDate = $this->getBornDate();

        $bornDate = new DateTime("{$bornDate->year}-{$bornDate->month}-{$bornDate->date}");
        $interval = (new DateTime())->diff($bornDate);

        return (object) [
            'year' => $interval->y,
            'month' => $interval->m,
            'day' => $interval->d,
        ];
    }

    private function getBornDate(): object
    {
        $bornDate = intval(substr($this->nik, 6, 2));
        $bornYear = intval(substr($this->nik, 10, 2));
        $currYear = intval(substr(date('Y'), -2));
        $gender = $this->getGender();

        return (object) [
            'date' => ($gender == Gender::FEMALE) ?
                $bornDate - 40 : $bornDate,
            'month' => intval(substr($this->nik, 8, 2)),
            'year' => ($bornYear < $currYear) ?
                $bornYear + 2000 : $bornYear + 1900,
        ];
    }

    private function getGender(): Gender
    {
        $bornDate = intval(substr($this->nik, 6, 2));

        return ($bornDate > 40) ?
            Gender::FEMALE :
            Gender::MALE;
    }

    private function getNextBirthday(): object
    {
        $bornDate = $this->getBornDate();
        $diff = (
            strtotime("{$bornDate->year}-{$bornDate->month}-{$bornDate->date}") -
            time()
        );

        return (object) [
            'month' => abs(gmdate('m', $diff)),
            'day' => abs(gmdate('d', $diff) - 1),
        ];
    }

    private function isValid(): bool
    {
        $isValidLen = strlen($this->nik) == 16;
        $address = $this->getAddress();

        return (
            $isValidLen &&
            $address->province &&
            $address->regency &&
            $address->subdistrict
        );
    }
}
