<?php

$address = 'birthdays.txt';

$name = readline("Введите имя: ");
$date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");

if (validate($date)) {
    $data = $name . ", " . $date . "\r\n";

    $fileHandler = fopen($address, 'a');

    if (fwrite($fileHandler, $data)) {
        echo "Запись $data добавлена в файл $address";
    } else {
        echo "Произошла ошибка записи. Данные не сохранены";
    }

    fclose($fileHandler);
} else {
    echo "Введена некорректная информация" . PHP_EOL;
}

function validate(string $date): bool
{
    $dateBlocks = explode("-", $date);

    if (count($dateBlocks) < 3) {
        return false;
    }

    if (isset($dateBlocks[0]) && $dateBlocks[0] > 31) {
        return false;
    }

    // if (isset($dateBlocks[1]) && $dateBlocks[0] > 12) { - было так с ошибкой
    if (isset($dateBlocks[1]) && $dateBlocks[1] > 12) {
        return false;
    }

    if (isset($dateBlocks[2]) && $dateBlocks[2] > date('Y')) {
        return false;
    }

    return true;
}
