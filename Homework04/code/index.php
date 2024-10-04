<?php
// 1. Придумайте класс, который описывает любую сущность из предметной области библиотеки: книга, шкаф, комната и т.п.
// 2. Опишите свойства классов из п.1 (состояние).
// 3. Опишите поведение классов из п.1 (методы).

class Book
{
    public string $title;
    public string $author;
    public int $pages;
    public int $isOpen;

    public function __construct(string $title, string $author, int $pages) {
        $this->title = $title;
        $this->author = $author;
        $this->pages = $pages;
        $this->isOpen = false;
    }

    public function open() : void {
        if ($this->isOpen == true) 
            throw new Exception("Book is already open!");
        
        $this->isOpen = true;
    }

    public function close() : void {
        if ($this->isOpen == false) 
            throw new Exception("Book is already closed!");

        $this->isOpen = false;
    }

}

$book = new Book("Название", "Автор", 100);
$book->open();
$book->close();

// 4. Придумайте наследников классов из п.1. Чем они будут отличаться?

class HistoricalBook extends Book {
    public string $genre;

    public function __construct(string $title, string $author, int $pages) {
        parent::__construct($title, $author, $pages);
        $this->genre = 'history';
    }
}

$historicalBook = new HistoricalBook("Название 2", "Автор 2", 100);

// 5. Создайте структуру классов ведения книжной номенклатуры.
// — Есть абстрактная книга.
// — Есть цифровая книга, бумажная книга.
// — У каждой книги есть метод получения на руки.
// У цифровой книги надо вернуть ссылку на скачивание, а у физической – адрес библиотеки, где ее можно получить. 
// У всех книг формируется в конечном итоге статистика по кол-ву прочтений.
// Что можно вынести в абстрактный класс, а что надо унаследовать?

interface getableOnHand {
    function getOnHand(): string;
}

abstract class Book2 implements getableOnHand
{
    public string $title;
    public int $readingsCounter;

    public function __construct(string $title) {
        $this->title = $title;
        $this->readingsCounter = 0;
    }
}

class DigitalBook extends Book2 
{
    public string $link;

    public function __construct(string $title, string $link) {
        parent::__construct($title);
        $this->link = $link;
    }

    function getOnHand(): string {
        $this->readingsCounter++;
        return $this->link;
    }

}

class PaperBook extends Book2 
{
    public string $libraryAddress;

    public function __construct(string $title, string $libraryAddress) {
        parent::__construct($title);
        $this->libraryAddress = $libraryAddress;
    }

    function getOnHand(): string {
        $this->readingsCounter++;
        return $this->libraryAddress;
    }
}

$digitalBook = new DigitalBook('Название 3', 'link');
$digitalBook->getOnHand();
var_dump($digitalBook);

// 6. Дан код:

class A1
{
    public function foo()
    {
        static $x = 0;
        echo ++$x;
    }
}
$a1 = new A1();
$a2 = new A1();
$a1->foo();
$a2->foo();
$a1->foo();
$a2->foo();

// Что он выведет на каждом шаге? Почему?
// ОТВЕТ: Выведутся последовательно числа от 1 до 4 потому что используется прекремент для статического поля, а значит значение будет распространяться на все экземпляры класса.

// Немного изменим п.5

class A2
{
    public function foo()
    {
        static $x = 0;
        echo ++$x;
    }
}
class B extends A2 {}
$a1 = new A2();
$b1 = new B();
$a1->foo();
$b1->foo();
$a1->foo();
$b1->foo();

// Что он выведет теперь?
// ОТВЕТ: Также выведутся последовательно числа от 1 до 4 потому что используется прекремент для статического поля, а значит значение будет распространяться на все экземпляры класса. Классы B наследник класса A2 поэтому статическое поле распространяется на них обоих.