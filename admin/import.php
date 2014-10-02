<?php
/*
Fields:
0 : Term
1 : Course
2 : Section
3 : CRN
4 : Name
5 : A
6 : B
7 : C
8 : D
9 : F
10: I
11: NR
12: S
13: U
14: V
15: W
16: Total
17: Class Size Group
18: Calculated GPA
19: Level
*/

$_class_size = [
    'Very Small (Fewer than 10 students)',
    'Small (10-20 Students)',
    'Mid-size (21-30 Students)',
    'Large (31-49 Students)',
    'Very Large (50 Students or More)',
];

$_term = [
    '02' => 'Spring',
    '05' => 'Summer',
    '08' => 'Fall',
];

$_input = [
    '201305.csv' => 0,
    '201308_201402_201405.csv' => 0
];

$destination = __DIR__ . '/insert.sql';
$insert = "INSERT INTO `Data` (`profID`,`courseID`,`Course`,`Section`,`Year`,`Prof`,`A`,`B`,`C`,`D`,`F`,`W`,`Size`,`Level`,`GPA`) VALUES ";
$_counter = [];
echo "\nCount    | Filename\n------------------------------";
foreach($_input as $file => $count) {
    $csv = array_map('str_getcsv', file(__DIR__ . '/' . $file));
    array_shift($csv);

    $csvlen = count($csv);
    echo "\n" . str_pad($csvlen, 8) . ' | ' . $file;
    $sql = '';
    $counter = 0;
    foreach($csv as $row) {
        $name = explode(',', $row[4]); // Now [lastname, firstname]
        $course = explode(' ', $row[1]); // Now [department, number]
        $term = $_term[substr($row[0], 4, 2)] . ' ' . substr($row[0], 0, 4);
        
        $in = [
            '\'' . preg_replace('/[^A-Z]/', '', strtoupper(implode('', $name))) . '\'',
            '\'' . strtoupper(implode('', $course)) . '\'',
            '\'' . "{$course[0]} {$course[1]}" . '\'',
            '\'' . $row[2] . '\'',
            '\'' . $term . '\'',
            '\'' . implode(', ', $name) . '\'',
            preg_replace('/[^0-9]/', '', $row[5]) ?: 0,
            preg_replace('/[^0-9]/', '', $row[6]) ?: 0,
            preg_replace('/[^0-9]/', '', $row[7]) ?: 0,
            preg_replace('/[^0-9]/', '', $row[8]) ?: 0,
            preg_replace('/[^0-9]/', '', $row[9]) ?: 0,
            preg_replace('/[^0-9]/', '', $row[15]) ?: 0,
            '\'' . $row[17] . '\'',
            '\'' . $row[19] . '\'',
            $row[18] != 'N/A' ? $row[18] : 0,
        ];

        $sql .= "\n (" . implode(', ', $in) . ")";
        if(!(++$counter % 100) || $counter == $csvlen) {
            file_put_contents($destination, $insert . $sql . ";\n\n", FILE_APPEND);
            $sql = '';
        }
        else {
            $sql .= ',';
        }
    }
}
echo "\nDone! \n";