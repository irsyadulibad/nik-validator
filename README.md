# nik-validator
NIK Validator is a package to convert Indonesian citizenship identity number into usefull information. You just call the **parse** method and input NIK number in the parameter, then you will get the informations (without internet connection).

## Usage
* Installation
```
composer require irsyadulibad/nik-validator
```

* Example
```php
<?php
use Irsyadulibad\NIKValidator\Validator;

$parsed = Validator::set('35090xxxxxxxxxx')->parse();

if($parsed->valid) {
    var_dump($parsed);
}
```
