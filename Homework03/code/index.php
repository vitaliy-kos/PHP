<?php
// 1. Обработка ошибок. Посмотрите на реализацию функции в файле fwrite-cli.php в исходниках. Может ли пользователь ввести некорректную информацию (например, дату в виде 12-50-1548)? Какие еще некорректные данные могут быть введены? Исправьте это, добавив соответствующие обработки ошибок.

$answer = 'Пользователь МОЖЕТ ввести дату 12-50-1548, потому что в условии проверки месяца - isset($dateBlocks[1]) && $dateBlocks[0] > 12 - проверка наличия первого индекса корректа, а проверка на превышение 12 почему то для нулевого индекса, хотя должен быть тоже первый индекс. Так же не сделана проверка на нулевые значения. Ну и можно добавить проверку на дату рождения до 100 лет последних, в данном контексте это логически имеет смысл.';

// 2. Поиск по файлу. Когда мы научились сохранять в файле данные, нам может быть интересно не только чтение, но и поиск по нему. Например, нам надо проверить, кого нужно поздравить сегодня с днем рождения среди пользователей, хранящихся в формате:
// -- Василий Васильев, 05-06-1992
// И здесь нам на помощь снова приходят циклы. Понадобится цикл, который будет построчно читать файл и искать совпадения в дате. Для обработки строки пригодится функция explode, а для получения текущей даты – date.

$fileName = 'birthdays.txt';
function getFileContent(string $route) : string {
    $content = '';
    $file = fopen($route, 'rb');

    if ($file === false) {
        throw new Exception('Невозможно открыть и прочитать файл.');
    } else {
        while (!feof($file)) {
            $content .= fread($file, 100);
        }
    }

    fclose($file);

    return $content;
}

$fileContent = getFileContent($fileName);

$strings = explode(PHP_EOL, $fileContent);
$needle = [];
$todayDate = date('d-m-Y', time());

foreach ($strings as $string) {
    $strArr = explode(', ', $string);
    
    if (count($strArr) == 2 && trim($strArr[1]) === trim($todayDate)) {
        $needle[] = $strArr[0];
        
    }
}

if (count($needle) > 0) {
    echo "Нужно поздравить: " . implode(', ', $needle) . PHP_EOL;
}

// 3. Удаление строки. Когда мы научились искать, надо научиться удалять конкретную строку. Запросите у пользователя имя или дату для удаляемой строки. После ввода либо удалите строку, оповестив пользователя, либо сообщите о том, что строка не найдена.
// 4. Добавьте новые функции в итоговое приложение работы с файловым хранилищем.

function writeFileContent(string $route, string $content) : bool 
{
    $fileHandler = fopen($route, 'w');

    if (!fwrite($fileHandler, $content)) {
        throw new Exception('Не удалось записать файл.');
    }

    fclose($fileHandler);

    return true;
}

function mb_str_replace($search, $replace, $string): array|string
{
    $charset = mb_detect_encoding($string);

    $unicodeString = iconv($charset, "UTF-8", $string);
    
    return str_replace($search, $replace, $unicodeString);
}

$name = readline("Введите имя: ");
$date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");

if (mb_strpos($fileContent, "$name, $date") === false) {
    echo 'Строка не найдена!' . PHP_EOL;
} else {
    writeFileContent($fileName, mb_str_replace("$name, $date", '', $fileContent));
    echo "Строка - $name, $date - успешно удалена!" . PHP_EOL;
}