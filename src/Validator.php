<?php

namespace Irsyadulibad\NIKValidator;

class Validator
{
	private $location, $nik;
	
	public function __construct($nik)
	{
		$this->nik = $nik;
		// Get location from assets and convert it to array
		$wilayahPath = realpath('./assets/wilayah.json');
		$this->location = json_decode(file_get_contents($wilayahPath), true);
	}
	
	public static function set($nik)
	{
		return new Self(strval($nik));
	}
	
	public function parse()
	{
		if($this->validate()) {
			$born = $this->getBornDate();
			
			return (object) [
				'nik' => $this->nik,
				'uniqueCode' => $this->getUniqueCode(),
				'gender' => $this->getGender(),
				'born' => $this->getBornDate(),
				'age' => $this->getAge(),
				'nextBirthday' => $this->getNextBirthday(),
				'zodiac' => $this->getZodiac(),
				'address' => [
					'province' => $this->getProvince(),
					'city' => $this->getCity(),
					'subDistrict' => $this->getSubDistrict(),
				],
				'postalCode' => $this->getPostalCode(),
				'valid' => true
			];
		} else {
			return (object) [
				'valid' => false
			];
		}
	}
	
	// Get last 2 digits number at the current year
	private function getCurrentYear()
	{
		return intval(substr(date('Y'), -2));
	}
	
	// Get year in NIK
	private function getNIKYear()
	{
		return intval(substr($this->nik, 10, 2));
	}
	
	// Get date in nik
	private function getNIKDate()
	{
		return intval(substr($this->nik, 6, 2));
	}
	
	// Get born date from NIK
	private function getBornDate()
	{
		$NIKdate = $this->getNIKDate();
		$NIKyear = $this->getNIKYear();
		$currYear = $this->getCurrentYear();
		$isFemale = ($this->getGender() == 'PEREMPUAN');
		
		// Get born date
		if($isFemale) $NIKdate -= 40;
		$date = ($NIKdate > 10) ? strval($NIKdate) : "0$NIKdate";
		// Get born month
		$month = substr($this->nik, 8, 2);
		// Get born year
		$year = strval(($NIKyear < $currYear) ? 2000 + $NIKyear : 1900 + $NIKyear);
		// Generate to full date format (d-m-Y)
		$full = "$date-$month-$year";
						
		// return as object
		return (object) compact('date', 'month', 'year', 'full');
	}
	
	// Get age data from born date
	private function getAge()
	{
		$bornDate = $this->getBornDate()->full;
		$ageDate = time() - strtotime($bornDate);
		
		$year = abs(gmdate('Y', $ageDate) - 1970);
		$month = abs(gmdate('m', $ageDate));
		$day = abs(gmdate('d', $ageDate));
		
		return (object) compact('year', 'month', 'day');
	}
	
	// Get next birthday from born date
	private function getNextBirthday()
	{
		$bornDate = $this->getBornDate()->full;
		$diff = strtotime($bornDate) - time();
		
		$month = abs(gmdate('m', $diff));
		$day = abs(gmdate('d', $diff));
		
		return (object) compact('month', 'day');
	}
	
	// Get zodiac from born date
	private function getZodiac()
	{
		$bornDate = $this->getBornDate();
		$month = intval($bornDate->month);
		$date = intval($bornDate->date);
		
		if (($month == 1 && $date >= 20) || ($month == 2 && $date < 19))
			return "Aquarius";
			
		if (($month == 2 && $date >= 19) || ($month == 3 && $date < 21))
			return "Pisces";
		
		if (($month == 3 && $date >= 21) || ($month == 4 && $date < 20))
			return "Aries";
		
		if (($month == 4 && $date >= 20) || ($month == 5 && $date < 21))
			return "Taurus";
		
		if (($month == 5 && $date >= 21) || ($month == 6 && $date < 22))
			return "Gemini";
		
		if (($month == 6 && $date >= 21) || ($month == 7 && $date < 23))
			return "Cancer";
		
		if (($month == 7 && $date >= 23) || ($month == 8 && $date < 23))
			return "Leo";
		
		if (($month == 8 && $date >= 23) || ($month == 9 && $date < 23))
			return "Virgo";
		
		if (($month == 9 && $date >= 23) || ($month == 10 && $date < 24))
			return "Libra";
		
		if (($month == 10 && $date >= 24) || ($month == 11 && $date < 23))
			return "Scorpio";
		
		if (($month == 11 && $date >= 23) || ($month == 12 && $date < 22))
			return "Sagitarius";
		
		if (($month == 12 && $date >= 22) || ($month == 1 && $date < 20))
			return "Capricorn";
		
		return "N/A";
	}
	
	// Get the province from NIK
	private function getProvince()
	{
		return $this->location['provinsi'][substr($this->nik, 0, 2)] ?? null;
	}
	
	//Get the city from NIK
	private function getCity()
	{
		return $this->location['kabkot'][substr($this->nik, 0, 4)] ?? null;
	}
	
	// Get the sub-district from NIK
	private function getSubDistrict()
	{
		$result = $this->location['kecamatan'][substr($this->nik, 0, 6)];
		return trim(explode('--', $result)[0]) ?? null;
	}
	
	// Get postal code
	private function getPostalCode()
	{
		$result = $this->location['kecamatan'][substr($this->nik, 0, 6)];
		return trim(explode('--', $result)[1]) ?? null;
	}
	
	// Get unique code from NIK
	private function getUniqueCode()
	{
		return substr($this->nik, 12, 4) ?? null;
	}
	
	// Get gender from NIK Date
	private function getGender()
	{
		$date = $this->getNIKDate();
		
		return ($date > 40) ? "PEREMPUAN" : "LAKI-LAKI";
	}
	
	// Make sure NIK is valid
	private function validate()
	{
		$length = (strlen($this->nik) == 16);
		
		return (
			$length &&
			$this->getProvince() &&
			$this->getCity() &&
			$this->getSubDistrict()
		) ? true : false;
	}
}
