<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>____ Практика</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>Домашнее задание 12.6.1 Практика по PHP. "Идеальный подбор пары" (HW-02)</h1>
    <main>
        
<?php
// Исходный массив для задания
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
    // [
    //     'fullname' => 'Петросян Евгений Вагановна',
    //     'job' => 'Комик',
    // ],
    // [
    //     'fullname' => 'Лебедев Артемий Татаяновна',
    //     'job' => 'Блогер',
    // ],
];


/*  Принимает как аргумент три строки — фамилию, имя и отчество.
Возвращает как результат их же, но склеенные через пробел. */

function getFullnameFromParts($surName, $firstName, $partonymycName) {
    return $surName.' '.$firstName.' '.$partonymycName;
}

/*  Принимает как аргумент одну строку — склеенное ФИО
    Возвращает как результат массив из трёх элементов
    с ключами 'name', 'surname' и 'patronomyc'. */

function getPartsFromFullname($fullName) {

    $fullNameArr = explode(' ', $fullName);
    
    // защита от пустых значений фамилии или отчества, чтобы не ловить вылеты от неполных данных
    (isset($fullNameArr[0])) ? $surName = $fullNameArr[0] : $surName = '';
    (isset($fullNameArr[1])) ? $firstName = $fullNameArr[1] : $firstName = '';
    (isset($fullNameArr[2])) ? $patronName = $fullNameArr[2] : $patronName = '';

    return ['surname' => $surName,
            'name' => $firstName,
            'patronomyc' => $patronName,];
}

/*  Принимает как аргумент строку, содержащую ФИО вида «Иванов Иван Иванович»
    и возврает строку вида «Иван И.», где сокращается фамилия и отбрасывается отчество. */

function getShortName($fullName) {
    $person = getPartsFromFullname($fullName);
    $surLetter = mb_substr($person['surname'], 0, 1);
    return $person['name'].' '.$surLetter.'.';
}

/* Принимает строку с полным ФИО, возвращает вероятный признак пола:
    -1 : пол женский
    +1 : пол мужской
    0 : пол не определен
 */

function getGenderFromName ($fullName) {
    $person = getPartsFromFullname($fullName); 
    // print_r($person);
    $genderSpeculation = 0; // стартовое значение предположения, + мужской, - женский

    // определяем на весах пол персонажа по признакам в его имени
    if (mb_substr($person['surname'],-1) == 'в') $genderSpeculation++; // м
    if (mb_substr($person['surname'],-2) == 'ва') $genderSpeculation--; // ж

    if (mb_substr($person['name'],-1) == 'й') $genderSpeculation++; // м
    if (mb_substr($person['name'],-1) == 'н') $genderSpeculation++; // м
    if (mb_substr($person['name'],-1) == 'а') $genderSpeculation--; // ж
    
    if (mb_substr($person['patronomyc'],-2) == 'ич') $genderSpeculation++; // м
    if (mb_substr($person['patronomyc'],-3) == 'вна') $genderSpeculation--; // ж

    return $genderSpeculation <=> 0;
}

/*  принимает массив где каждый элемент - асс.массив, в котором есть пара 'fullname' => 'ФИО'
    возвращает текстовый блок с процентной разбивкой гендерного состава аудитории */

function getGenderDescription ($person_array) {
    
    // 3 раза фильтровать массив на отдельные массивы с женскими/мужскими/неопределенными
    // признаками, чтобы их потом каждый отдельно считать, - показалось неоптимальной идеей,
    // поэтому array_filter, требуемый в решении задачи, применен альтернативным способом:
    // Для надежности отфильтровываем из входящего массива только записи, где есть непустое поле 'fullname'
    // Для теста можно закомментить поле 'fullname' в любой записи $example_persons_array
    
    $filtered_person_array = array_filter($person_array, function($element) {
            return isset($element['fullname']);
        });

    $cntTotal = count($filtered_person_array);

    $cntMan = 0;
    $cntWoman = 0;
    $cntUndef = 0;
    
    foreach ($filtered_person_array as $key=>$person) {
        switch (getGenderFromName($person['fullname'])) {
            case  1: $cntMan++; break;
            case -1: $cntWoman++; break;
            case  0: $cntUndef++; break;
        };
    };

    $percentMan = round($cntMan/$cntTotal*100,1);
    $percentWoman = round($cntWoman/$cntTotal*100,1);
    $percentUndef = round($cntUndef/$cntTotal*100,1);

    $result = <<<GENDERDOCTEXT
<b>Гендерный состав аудитории:</b><br>
* Мужчины \t\t\t- $cntMan ($percentMan %)<br>
* Женщины \t\t\t- $cntWoman ($percentWoman %)<br>
* Пол не определен \t- $cntUndef ($percentUndef %)<br>
Всего в базе: $cntTotal человек<br>

GENDERDOCTEXT;
    
    return $result;
}

/*  Функция определения "идеальной" пары */

function getPerfectPartner ($surName, $firstName, $patronymName, $person_array) {
    // конверсия регистра входных данных в единый стандарт
    $surName        = mb_convert_case($surName, MB_CASE_TITLE);
    $firstName      = mb_convert_case($firstName, MB_CASE_TITLE);
    $patronymName   = mb_convert_case($patronymName, MB_CASE_TITLE);

    $fullName = getFullnameFromParts($surName, $firstName, $patronymName);
    $gender = getGenderFromName($fullName);
    $userShortname = getShortName($fullName);

    // switch ($gender) {
    //     case '0':   $genderLetter = 'пол не определен'; break;
    //     case '1':   $genderLetter = 'М'; break;
    //     case '-1':  $genderLetter = 'Ж'; break;
    // }
    // $result = "\nИщем пару для: $fullName ($genderLetter)... \n\n";
   
    $result = "\n";
    if ($gender == 0) {
            $result .= "$userShortname, к сожалению, мы не смогли найти для вас пару... ;-( \n<br>";
        } else {
            $cntPersons = count($person_array);
            $keepSearch = true;
            while ($keepSearch) {
                $rnd = rand(0, $cntPersons-1);
                $pair = $person_array[$rnd];
                $pairGender = getGenderFromName($pair['fullname']);
                
                // 0 уже исключен ранее, а идеальная пара это +1-1=0
                if ($gender + $pairGender == 0) {
                    $pairShortName = getShortName($pair['fullname']);
                    $percentIdeal = round(rand(5000,10000)/100,2);
                    $keepSearch = false; // выходим из цикла поиска
                }
            };
            $result .= "$userShortname + $pairShortName = <span class='pair'>♡ Идеально на $percentIdeal % ♡</span><br>\n";
        };
    return $result;
}

// MAIN
echo (getGenderDescription($example_persons_array));

echo "<p><i>Начинаем подбор идеальной пары</i></p>\n";

echo "\n<h4>Попытка №1: Агузарова Жанна Петровна</h4>\n";
echo (getPerfectPartner('Агузарова', 'Жанна', 'Петровна', $example_persons_array));
echo "\n<h4>Попытка №2: Антонидас</h4>\n";
echo (getPerfectPartner('Антонидас', '', '', $example_persons_array));
echo "\n<h4>Попытка №3: Крупица Анатолий Федорович</h4>\n";
echo (getPerfectPartner('Крупица', 'Анатолий', 'Федорович', $example_persons_array));
echo "\n<h4>Попытка №4: Обло Чудище Стозевно</h4>\n";
echo (getPerfectPartner('Обло', 'Чудище', 'Стозевно', $example_persons_array));


?>
    </>
    </main>
    <footer>
        Страница проекта на github: 
        Выполнил: Александр Климок (<a href="https://github.com/kydechuk01/">kydechuk01</a>)<br>
        Курс: PHPPRO_19<br>
        Дата: 08.04.2024
    </footer>
</body>
<script>
</script>

</html>