<?php
// 1. Реализовать основные 4 арифметические операции в виде функции с тремя параметрами – два параметра это числа, третий – операция. Обязательно использовать оператор return.

function addition(float $num1, float $num2): float
{
    return $num1 + $num2;
}

function subtraction(float $num1, float $num2): float
{
    return $num1 - $num2;
}

function multiplication(float $num1, float $num2): float
{
    return $num1 * $num2;
}

function division(float $num1, float $num2): float
{
    return $num2 > 0 ? $num1 / $num2 : 0;
}

// 2. Реализовать функцию с тремя параметрами: function mathOperation($arg1, $arg2, $operation), где $arg1, $arg2 – значения аргументов, $operation – строка с названием операции. В зависимости от переданного значения операции выполнить одну из арифметических операций (использовать функции из пункта 3) и вернуть полученное значение (использовать switch).

function mathOperationFunction(float $num1, float $num2, string $operation): float
{
    switch ($operation) {
        case 'addition':
            return addition($num1, $num2);
        case 'subtraction':
            return subtraction($num1, $num2);
        case 'multiplication':
            return multiplication($num1, $num2);
        case 'division':
            return division($num1, $num2);

        default:
            return 0;
    }
}

// 3. Объявить массив, в котором в качестве ключей будут использоваться названия областей, а в качестве значений – массивы с названиями городов из соответствующей области. Вывести в цикле значения массива, чтобы результат был таким: Московская область: Москва, Зеленоград, Клин Ленинградская область: Санкт-Петербург, Всеволожск, Павловск, Кронштадт Рязанская область … (названия городов можно найти на maps.yandex.ru).

$citiesArray = [
    'Московская область' => [
        'Москва',
        'Зеленоград',
        'Клин',
    ],
    'Ленинградская область' => [
        'Санкт-Петербург',
        'Всеволожск',
        'Павловск',
        'Кронштадт',
    ],
    'Рязанская область' => [
        'Касимов',
        'Кораблино',
        'Михайлов',
        'Новомичуринск',
    ]
];

foreach ($citiesArray as $region => $citiesList) {
    echo "- $region" . PHP_EOL;
    foreach ($citiesList as $cityName) {
        echo "--- $cityName" . PHP_EOL;
    }
}

echo  PHP_EOL;

// 4. Объявить массив, индексами которого являются буквы русского языка, а значениями – соответствующие латинские буквосочетания (‘а’=> ’a’, ‘б’ => ‘b’, ‘в’ => ‘v’, ‘г’ => ‘g’, …, ‘э’ => ‘e’, ‘ю’ => ‘yu’, ‘я’ => ‘ya’). Написать функцию транслитерации строк.

function translit(string $russianString): string
{

    $lettersArray = [
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'e',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ь' => '',
        'ы' => 'y',
        'ъ' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
    ];

    $result = '';

    foreach (mb_str_split($russianString) as $char) {
        $result .= $lettersArray[mb_strtolower($char)];
    }

    return $result;
}
echo translit("Привет") . PHP_EOL;

// 5. * С помощью рекурсии организовать функцию возведения числа в степень. Формат: function power($val, $pow), где $val – заданное число, $pow – степень.

function power(float $val, int $pow): float
{
    if ($pow == 1) return $val;
    return $val * power($val, $pow - 1);
}

echo power(2,3) . PHP_EOL;

// 6. * Написать функцию, которая вычисляет текущее время и возвращает его в формате с правильными склонениями, например:
// 22 часа 15 минут
// 21 час 43 минуты.

function getCurrentTimeString(): string
{
    $timeArr = explode(":", date('h:i', time()));
    $hours = $timeArr[0];
    $minutes = $timeArr[1];

    $remainsHours = $hours % 10;
    if ($remainsHours == 0 || $remainsHours >= 5 || $hours >= 11 && $hours <= 19) {
        $hours .= " часов";
    } else if ($remainsHours == 1) {
        $hours .= " час";
    } else if ($remainsHours == 2 || $remainsHours == 3 || $remainsHours == 4) {
        $hours .= " часa";
    }

    $remainsMinutes = $minutes % 10;
    if ($remainsMinutes == 0 || $remainsMinutes >= 5 || $minutes >= 11 && $minutes <= 19) {
        $minutes .= " минут";
    } else if ($remainsMinutes == 1) {
        $minutes .= " минута";
    } else if ($remainsMinutes == 2 || $remainsMinutes == 3 || $remainsMinutes == 4) {
        $minutes .= " минуты";
    }

    return "$hours $minutes";
}

echo getCurrentTimeString() . PHP_EOL;
