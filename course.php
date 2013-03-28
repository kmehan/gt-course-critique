<?php
/**
  *   Generate a classes page from input ID
**/

//Load Config
include("config.php");

//Check if the ID is set/non-empty
if(empty($_GET['id'])) {
  header("Location: $rootURL"); //Redirect back to searchbar
} else {
  $courseID = $_GET['id'];
}

//Connect to MySQL
  $db = new database;

//Get Data
  $name = $db->pdo->prepare("SELECT DISTINCT Course FROM Data WHERE courseID LIKE :id");
  $raw  = $db->pdo->prepare("SELECT profID, Prof, Size, ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)), ROUND(AVG(W)) FROM Data WHERE courseID Like :id GROUP BY Prof");
  $avg  = $db->pdo->prepare("SELECT ROUND(AVG(GPA),2), ROUND(AVG(A)), ROUND(AVG(B)), ROUND(AVG(C)), ROUND(AVG(D)), ROUND(AVG(F)) FROM Data WHERE courseID Like :id AND GPA !=0 ");

//Check if query executes
  if(!$name->execute(array(":id"=>$courseID)) || !$raw->execute(array(":id"=>$courseID)) || !$avg->execute(array(":id"=>$courseID))) {
    //Query failed TODO Better error handling - TRY - CATCH
    die("Uh-oh the database had an error!");
  }
//Get course name
$name = $name->fetch(PDO::FETCH_NUM);
$name = $name[0];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Course Critique - <?php echo $name; ?></title>
 <!--   <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="description" content="Historical Course GPA information provided by SGA">
    <meta name="author" content="SGA - Georgia Institute of Technology">

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="bootstrap.min.css" rel="stylesheet"> 
    <link href="default/bootstrap-responsive.min.css" rel="stylesheet">  
    <link href="css/bootswatch.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!-- DataTables -->
    <script src="js/dataTables/jquery.dataTables.js"></script>
    <script src="js/dataTables/DT_bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/dataTables/DT_bootstrap.css">
    <style>
      .center-table {
        margin: 0 auto !important;
        float: none !important;
      }
     .dropdown-menu {
	height:325px;
	overflow-y:scroll;
     }
    </style>
  </head>
  
  <body>
  <img src="beta_ribbon.png" class="beta-ribbon" alt="beta" />

    <div class="container">
      <div class="row">
        <h2 style="text-align: center;"><?php echo $name; ?></h2>
        <div class="span6 center-table">
        <table class="table table-striped table-ordered">  
          <thead>
            <tr>
              <th>Avg Marks</th>
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
              <?php
                //Get the avg marks
                $avg = $avg->fetch(PDO::FETCH_NUM);
                foreach($avg as $value) {
                  echo "<td>$value</td>";
                }
              ?>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
    <!-- Table -->
	<table class="table table-striped table-bordered" id="dataTable" style="margin-top: 10px;">
	<thead>
		<tr class="table-head" >
      <th>Professor</th>
      <th>Size</th>
      <th class="avg">GPA</th>
      <th class="avg">A%</th>
      <th class="avg">B%</th>
      <th class="avg">C%</th>
      <th class="avg">D%</th>
      <th class="avg">F%</th>
      <th class="avg">W%</th>
		</tr>
	</thead>
	<tbody>
    <?php
      while($row=$raw->fetch(PDO::FETCH_NUM)) {
        echo "<tr class=\"".$row[0]."\">\n";
        for($i=0; $i<count($row); $i++) {
          echo "<td>";
          if($i==0) { 
            //Dealing w/prof.  Generate link
            echo "<a href=\"prof.php?id=".$row[$i]."#".$_GET['id']."\" >".$row[$i+1]."</a>";
            $i++; //iterate i as we used 2 columns for this link
          } else {
            echo $row[$i];
          }
          echo "</td>\n";
        }
        echo "</tr>\n";
      } 
    ?>
	</tbody>
</table>
			
    <!-- javascript placed at end of the document so the pages load faster -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/application.js"></script>
    <script>
    initTable();
    //Generate dropdown navigation filters
    $(document).ready(function() {
      var profs = new Array();
      //select each prof cell
      $("#dataTable tr:not(tr.table-head) td:first-child").each(function() {
        var profID = $(this).parent().attr("class").split(' ')[0];
        var prof   = $(this).text();
        profs[profID] = prof;
      });
      //Generate the lists
        //Generate nav wrapper
        $("div#dataTable_wrapper > div.row > div.span6:first").append("<ul class=\"nav nav-pills\"></ul>")
        //Generate the buttons
        genList("Professors", profs);
        
      
    });
    

    function genList(outID, elements) {
      var listOptions = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">'+outID+' <b class="caret"></b></a><ul id="'+outID+'" class="dropdown-menu">';
      for(var id in elements) {
        listOptions += '<li><label for="'+id+'"><input id="'+id+'" type="checkbox" onclick="toggleAction(\''+id+'\', \''+outID+'\');" checked="true" />'+elements[id]+'</label></li>';
      }
      listOptions += '</ul></li>';
      $('div#dataTable_wrapper > div.row > div.span6:first > ul').append(listOptions);
    }

    function toggleAction(itemClass, menuName) {
      $("."+itemClass).toggleClass(menuName + "Disabled");
//      $("."+itemClass).toggle('showOrHide');
    }
    //Script to prevent dropdown from closing upon selection
    $(function () {
    //Setup drop down menu
      $('.dropdown input, .dropdown label').click(function(e) {
        e.stopPropagation();
      });
    });
    </script>
    <hr>
    <footer id="footer">
      <p class="pull-right"><a href="#">Back to top</a></p>
      <div class="links">
      </div>
      Made by <a href="http://critique.gatech.edu/author">SGA Course Critique Committee</a>. Contact them at <a href="mailto:sga@gatech.edu">sga@gatech.edu</a>.<br />
    </footer>
    </div>
	</body>
</html>
