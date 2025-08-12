<?php
$example_persons_array = [
    [
        'fullname' => 'ИваНов ИВан ИВановиЧ',
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
    
];


$array = array_column($example_persons_array, 'fullname');

$i = 0;

$fullNameString = $array[$i];

$partsOfFullName = (explode(' ', $fullNameString));
$surname = $partsOfFullName[0];
$name = $partsOfFullName[1];
$patronymic = $partsOfFullName[2];


# Разбиение и объединение ФИО

function getFullnameFromParts($surnamePart, $namePart, $patronymicPart) {
    
    $nameFromParts = implode(' ', [$surnamePart, $namePart, $patronymicPart]);
    return $nameFromParts;
    
}

getFullnameFromParts($surname, $name, $patronymic);


function getPartsFromFullname($glued) {

    $partsOfFullName = (explode(' ', $glued));
    $resultParts = ['surname' => $partsOfFullName[0], 'name' => $partsOfFullName[1], 'patronymic' => $partsOfFullName[2]];
    return $resultParts;

}

getPartsFromFullname($fullNameString);


# Сокращение ФИО

function getShortName($short) {

    $shortName = getPartsFromFullname($short);
    $name = $shortName['name'];
    $surname = $shortName['surname'];
    $surnameShort = mb_substr($surname, 0, mb_strpos($surname, 1) + 1) . '.';
    $resultShort = $name . ' ' . $surnameShort;
    return $resultShort;

}

getShortName($fullNameString);


# Функция определения пола по ФИО

function getGenderFromName($gender) {

    $shortName = getPartsFromFullname($gender);
    $patronymic = $shortName['patronymic'];
    $name = $shortName['name'];
    $surname = $shortName['surname'];
    
    // Признаки мужского пола:
    $patronymicMale = mb_strrpos($patronymic, 'ич');
    $nameMale = mb_strrpos($name, 'н');
    $nameMale2 = mb_strrpos($name, 'й');
    $surnameMale = mb_strrpos($surname, 'в');

    // Признаки женского пола:
    $patronymicFemale = mb_strrpos($patronymic, 'вна');
    $nameFemale = mb_strrpos($name, 'а');
    $surnameFemale = mb_strrpos($surname, 'ва');

    $genderMale = 0;

    if ($patronymicMale > 0) {
        $genderMale++;
        
        if ($nameMale > 0 || $nameMale2 > 0) {
            $genderMale++;
        }
        if ($surnameMale > 0) {
            $genderMale++;
        }
             
    }

    $genderFemale = 0;

    if ($patronymicFemale > 0) {
        $genderFemale--;
        
        if ($nameFemale > 0) {
            $genderFemale--;
        }
        if ($surnameFemale > 0) {
            $genderFemale--;
        }       
        
    }
   
   $gender = 0 + $genderMale <=> 0 - $genderFemale;
   
   return $gender;

}

getGenderFromName($fullNameString);


# Определение возрастно-полового состава

function getGenderDescription($arrayGender) {

    $filterMale = array_filter($arrayGender, function($arrayGender) {
        return (getGenderFromName($arrayGender['fullname']) === 1);
    });

    $filterFemale = array_filter($arrayGender, function($arrayGender) {
        return (getGenderFromName($arrayGender['fullname']) === -1);
    });

    $filterUnknown = array_filter($arrayGender, function($arrayGender) {
        return (getGenderFromName($arrayGender['fullname']) === 0);
    });

    $sum = count($filterMale) + count($filterFemale) + count($filterUnknown);
    $percentMale =  round(count($filterMale) / $sum * 100, 1);
    $percentFemale = round(count($filterFemale) / $sum * 100, 1);
    $percentUnknown = round(count($filterUnknown) / $sum  * 100, 1);

    $message = <<<MSG
    Гендерный состав аудитории:
    ---------------------------
    Мужчины - $percentMale%
    Женщины - $percentFemale%
    Не удалось определить - $percentUnknown%
    \n
    MSG;
    
    return $message;

}

getGenderDescription($example_persons_array);


# Подбор идеальной пары

function getPerfectPartner(
    $surnamePartner,
    $namePartner,
    $patronymicPartner,
    $arrayPartner
) {
  
    $surnamePartner = mb_convert_case(($surnamePartner), MB_CASE_TITLE, "UTF-8");
    $namePartner = mb_convert_case(($namePartner), MB_CASE_TITLE, "UTF-8");
    $patronymicPartner = mb_convert_case(($patronymicPartner), MB_CASE_TITLE, "UTF-8");

    $firstPartner = getFullnameFromParts($surnamePartner, $namePartner, $patronymicPartner);
    
    $genderPartner = getGenderFromName($firstPartner);
    
    $secondPartner = $arrayPartner[array_rand($arrayPartner)]['fullname'];
    $secondPartner = mb_convert_case(($secondPartner), MB_CASE_TITLE, "UTF-8");
    $secondPartnerGender = getGenderFromName($secondPartner);
        
    while ($genderPartner === $secondPartnerGender || $secondPartnerGender === 0) {
        $secondPartner = $arrayPartner[array_rand($arrayPartner)]['fullname'];
        $secondPartner = mb_convert_case(($secondPartner), MB_CASE_TITLE, "UTF-8");
        $secondPartnerGender = getGenderFromName($secondPartner);
    }
     
    $firstPartner = getShortName($firstPartner);
    $secondPartner = getShortName($secondPartner);
    
    $randomPercentSum = round(rand(50, 100) + rand(0, 99) * 0.01, 2);

    $randomPercent = number_format($randomPercentSum, 2, '.', '');

    if ($genderPartner === 0) {
        $message2 = '⚣ Не удалось подобрать подходящую пару. ⚢';
    }

    else {
        $message2 = <<<MSG2
        $firstPartner + $secondPartner =
        ♡ Идеально на $randomPercent% ♡
        \n
        MSG2;
    }

    return $message2;

}

getPerfectPartner($surname, $name, $patronymic, $example_persons_array);

?>