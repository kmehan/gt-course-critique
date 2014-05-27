/* searchResults.js */
var temp = ""; /* Set in showSearch */

function search(query) {
  if(typeof search_easter === 'function') {
    search_easter(query);
  }
  else if($.trim(query)==0) {
    return false;
  }
  //TODO: the * will hit performance quite a bit, may need to be removed
  $.ajax({
        type: "GET",
        url:  "/search.php",
        data: 'query='+query,
        dataType: "JSON",
        success: function(msg) { showSearch(msg); }
  });
  return true;
}
function showSearch(msg) {
  if(msg.error) {  //No results
    $("#searchResults").html("<h3>Error with query syntax</h3>");
    return false;
  }
  //Output number of hits...etc
  temp = msg;
  var output = "<h5>" + msg.hits.total + " results in: " + msg.took + "ms</h5>";
  for(var i = 0; i < msg.hits.hits.length; i++) {
    //type
      var type =  msg.hits.hits[i]._index
    //Get title for each respective type
    var titleTag="";
    var action = "#Error";
    switch(type) {
      case('class') : titleTag = "course"; action="course.php"; break;
      case('prof')  : titleTag = "prof"; action = "prof.php"; break;
    }
    var id = msg.hits.hits[i]._id;
    var title = msg.hits.hits[i]._source[titleTag].replace(",",", ");
    //Heading
      output += "<div><h4><a href='"+action+"?id="+id.toUpperCase()+"'>"+title+"</a><p>";
    //End tags
      output += "</p></div>";
  }
  $("#searchResults").html(output);
  return true;
}
