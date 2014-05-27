<?php

/**
 * Gets and parses oscar data as JSON
 */
if (empty($_GET['course_number']) || empty($_GET['department'])) {
    die("ERROR: Missing Parameters");
}

//Get correctly formatted params
$department = strtoupper($_GET['department']);
$course_number = $_GET['course_number'];


//Generate termNo
$found = false;
$cur_sem = 8; //Starting semester Number TODO - START WITH THE CURRENT SEMESTER!!
$year = date("Y"); //Starting year
//Loop through each year/semester till we find the last occurance of the course
while (!$found) {
    if ($cur_sem < 2) { //Finished the current year, look at past years
        $cur_sem = 8;
        $year--;
    }
    $termNum = $year . "0" . $cur_sem;
    //Get list of all courses
    $ch = curl_init("https://oscar.gatech.edu/pls/bprod/bwckctlg.p_disp_course_detail?cat_term_in=201308&subj_code_in=$department&crse_numb_in=$course_number");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    echo $html;
    curl_close();

    //See if we've found the course, and parse course name
    $course_name = parser("$department $course_number - ", "<");
    if ($course_name != "") {
        $found = true;
    } else {
        $cur_sem-=3;
    }
}

//Course Name output
echo "DEPT - NUM: $department $course_number<br />\n";
echo "Course Name: $course_name\n";

// echo "<br />Description: $description";
echo "<br /><br /> Description: " . parser("<TD CLASS=\"ntdefault\">", "<");

function parser($start, $end) {
    global $html;
    $position = strpos($html, $start);
    $position += strlen($start);
    $parsed = false;
    $text = "";
    while (!$parsed) {
        if ($html[$position] != $end) {
            $text .= $html[$position];
            $position++;
        } else {
;
            $parsed = true;
        }
    }
    return $text;
}