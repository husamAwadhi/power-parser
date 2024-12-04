# ![PowerParser](/storage/img/logo_size_invert.jpg)

[![Actions](https://img.shields.io/github/actions/workflow/status/husamAwadhi/power-parser/main.yaml?branch=master&label=Tests&style=round-square)](https://github.com/husamAwadhi/power-parser/actions)
[![Latest Stable Version](https://poser.pugx.org/husam-awadhi/power-parser/v)](https://packagist.org/packages/husam-awadhi/power-parser) 
[![PHP Version Require](https://poser.pugx.org/husam-awadhi/power-parser/require/php)](https://packagist.org/packages/husam-awadhi/power-parser)
[![License](https://poser.pugx.org/husam-awadhi/power-parser/license)](https://packagist.org/packages/husam-awadhi/power-parser) 
[![Total Downloads](https://poser.pugx.org/husam-awadhi/power-parser/downloads)](https://packagist.org/packages/husam-awadhi/power-parser) 

File parsing tool with a configured blueprint design with support for `ods`, `xlsx`, `xls`, `xml`, `html`, `sylk`, `csv` file types out-of-the-box.

## Installation
Require PowerParser using [Composer](https://getcomposer.org):

```bash
composer require husam-awadhi/power-parser
```

## Usage
To parse a file you'll need to a `Parser` instance which can be done by using these 3 lines. which will return the parsed file data, ready for your magic! :sparkler: 
```PHP
try {
    $pp = new PowerParser();
    $parser = $pp->getParserBuilder(
                stream: 'path-to-blueprint.yml',
                file: 'path-to-file.ext'
        )->build();
    $parsedData = $parser->parse()->getAsArray();
} catch (Exception $e) {
    // hmm
}
```

## Blueprint
### Blueprint Basics

refer to example, [Valid Blueprint](/storage/tests/blueprints/valid.yaml)

#### Version `string`

blueprint version. not yet utilized but will be used for backward compatibility. 

---
#### Meta `object`

object contains meta data.

##### File `object`

object contains parsed file meta data.

###### Extension `string`

parsed file extension.

###### Name `string`

parsed file name. only used in the returned parsed file data.

---
#### Blueprint `object array`

array of main parameters to capture data in parsed file.

##### Name `string`

code of captured data, when returning the parsed as array this will be used as array key for the matched data.

##### Mandatory `boolean`

when true, parsing will throw an exception if data not matched in the parsed file.

##### Conditions `objects array`

rules and clauses for finding an element.

###### Column `integers array`

expected location, a column number in Excel or CSV file

###### is, isNot, anyOf, noneOf `string` [one only]

Used to match the condition with the given value. If you want to use null as a value, use `"{null}"`, which will be converted in the `BlueprintInterpreter`.


##### Fields `objects array`

once a match has been found, any data defined here will be captured. 

###### Name `string`

The name of the data found in the field. When returning the parsed data as an array, this will be used as the array key for the matched data.

###### Position `integer`

cell number in Excel or CSV files

###### Format `format` [optional]

formats applied to matched data, refer to [Processors](#processors)

---
### Processors

processors has 2 types,
#### Casting
1. bool-strict: `true` 
   1. if value = `true`, `1`, `"true"`
2. bool: `false`,
   1. if value has a value and is not equal to `true`.
   2. If value is `true`, `1`, or `"true"`.
3. int: PHP casting
4. float: PHP casting

Example:
```yml
blueprint:
- fields:
      - name: cash
        position: 1
        type: int
```

#### Formatting
1. money
2. string length limits

Example:
```yml
blueprint:
- fields:
      - name: cash
        position: 1
        format: f%2 # input: 23.441 output: 23.44
      - name: cash
        position: 1
        format: s%5 # input: 'sweets' output: 'sweet'
```

## License

PowerParser is an open-sourced software licensed under the [MIT license](LICENSE).
