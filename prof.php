<?php
/* Generate a prof's page from input ID (profs name, no commas and no spaces) */

// Load config
include("config.php");

// Check if the ID is set/non-empty.
if (empty($_GET['id'])) {
    header("Location: $rootURL"); //Redirect back to searchbar
} else {
    $profID = $_GET['id'];
}

//Connect to MySQL
$db = new database;
//Get data
$profName = $db->pdo->prepare("SELECT DISTINCT Prof FROM Data WHERE profID = :id");
$raw = $db->pdo->prepare("SELECT courseID, Course, Section, Year, Size, GPA, A, B, C, D, F, W FROM Data WHERE profID = :id");
$avg = $db->pdo->prepare("SELECT ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)) FROM Data WHERE profID = :id AND GPA != 0 GROUP BY Prof");

//execute query and handle error
if (!$raw->execute(array(":id" => $profID)) || !$avg->execute(array(":id" => $profID)) || !$profName->execute(array(":id" => $profID))) {
    $error = file_get_contents("404.html");
    die($error);
}

// Get the professor's name
$profName = $profName->fetch(PDO::FETCH_NUM);
$profName = $profName[0];

//Get the avg
$avg = $avg->fetch(PDO::FETCH_NUM);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Course Critique - <?= $profName ?></title>
        <meta name="description" content="Historical Course GPA information provided by SGA">
        <meta name="author" content="SGA - Georgia Institute of Technology">

        <!--[if lt IE 9]>
          <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link href="bootstrap.min.css" rel="stylesheet">
        <link href="default/bootstrap-responsive.min.css" rel="stylesheet"> 
        <link href="css/bootswatch.css" rel="stylesheet">
        <link href="css/critique.css" rel="stylesheet">
        <script src="js/jquery.min.js"></script>
        
        <!-- DataTables -->
        <script src="js/dataTables/jquery.dataTables.js"></script>
        <script src="js/dataTables/DT_bootstrap.js"></script>
        <link href="css/dataTables/DT_bootstrap.css" rel="stylesheet" >
    </head>

    <body>
        <img src="beta_ribbon.png" class="beta-ribbon" alt="beta" />
        <div class="container">
            <div class="row">
                <h2 style="text-align: center;"><?= $profName ?></h2>
                <div class="span6 center-table">
                    <table class="table table-striped table-ordered">  
                        <thead>
                            <tr>
                                <th>Average Marks</th>
                                <th>GPA</th>
                                <th>A%</th>
                                <th>B%</th>
                                <th>C%</th>
                                <th>D%</th>
                                <th>F%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <?php foreach ($avg as $value): ?>
                                    <td><?= $value ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Table -->
            <table class="table table-striped table-bordered" id="dataTable" style="margin-top: 10px;" >
                <thead>
                    <tr class="table-head">
                        <th>Course</th>
                        <th>Section</th>
                        <th>Year</th>
                        <th>Size</th>
                        <th>GPA</th>
                        <th>A%</th>
                        <th>B%</th>
                        <th>C%</th>
                        <th>D%</th>
                        <th>F%</th>
                        <th>W%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $raw->fetch(PDO::FETCH_NUM)): ?>
                        <tr class="<?= $row[0] . " " . str_replace(" ", "", $row[3]) ?>">
                            <td><a class="courseName" href="course.php?id=<?= $row[0] ?>"><?= $row[1] ?></a></td>
                            <?php for ($i = 2; $i < count($row); $i++): ?>
                                <td><?= $row[$i] ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- javascript placed at end of the document so the pages load faster -->
            <script src="js/bootstrap.min.js"></script>
            <script src="js/application.js"></script>
            <script>
                initTable();
                //Generate dropdown navigation filters
                $(document).ready(function() {
                    /* init our dataTable */
                    var courses = new Array();
                    var terms = new Array();

                    //Select each non table-head (remove duplicates as well)
                    $("#dataTable tr:not(tr.table-head) td:first-child").each(function() {
                        //Get course data
                        var courseID = $(this).parent().attr("class").split(' ')[0].toUpperCase();
                        var course = $(this).text();
                        //Get term data
                        var termID = $(this).parent().attr("class").split(' ')[1];
                        var term = $(this).next().next().text();

                        if (termID !== undefined && course !== 'No data available in table') {
                            courses[courseID] = course;
                            terms[termID] = term;
                        }
                    });
                    //Generate dropdown list
                    //Generate nav wrapper
                    if (terms.length > 0) {
                        $("div#dataTable_wrapper > div.row > div.span6:first").append("<ul class=\"nav nav-pills\"></ul>")
                        //Generate the course/term buttons
                        genList("Courses", courses);
                        genList("Terms", terms);
                        //Filter out all but requested hash (if there is one)\
                        urlhashFilter();
                    }
                });

                function genList(outID, elements) {
                    var listOptions = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">' + outID + ' <b class="caret"></b></a><ul id="' + outID + '" class="dropdown-menu">';
                    //Sort keys of hashtable
                    var keys = [];
                    for (var key in elements) {
                        if (elements.hasOwnProperty(key)) {
                            keys.push(key);
                        }
                    }
                    keys.sort();
                    for (var i in keys) {
                        listOptions += '<li><label for=\'' + keys[i] + '\'><input id=\'' + keys[i] + '\' type="checkbox" onclick="toggleAction(\'' + keys[i] + '\', \'' + outID + '\');" checked="true" />' + elements[keys[i]] + '</label></li>';
                    }
                    listOptions += "</ul></li>";
                    $("div#dataTable_wrapper > div.row > div.span6:first > ul").append(listOptions);
                }

                function toggleAction(itemClass, menuName) {
                    $("." + itemClass).toggleClass(menuName + "Disabled");
                }
                function urlhashFilter() {
                    /* Get first string in URL hash */
                    var url = location.hash.substring(1).split(' ')[0];
                    if (url.length === 0) {
                        return false;
                    }

                    /* Change to gold */
                    $("a.dropdown-toggle:first").toggleClass("prof-flash-gold");

                    /* Find the request prof in our filter list */
                    if ($("body").find("ul#Courses li input#" + url + ":first").length === 1) {
                        $("ul#Courses li input").each(function() {
                            /* Hide everything but the requested course */
                            if ($(this).attr('id') !== url) {
                                $(this).trigger('click');
                            }
                        });
                        dataTable.fnSort([[2, 'desc']]); //Sort Year asc
                    }
                    setTimeout(function() {
                        $("a.dropdown-toggle:first").toggleClass("prof-flash-gold");
                    }, 250);
                }

                //prevent dropdown from closing upon selection
                $(function() {
                    $('.dropdown input, .dropdown label').click(function(e) {
                        e.stopPropagation();
                    });
                });
            </script>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>
