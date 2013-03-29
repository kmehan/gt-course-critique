
function search(query) {
  if(query=="George P. Burdell" || query=="Colin Bookman") {
    clippy();
  }
  if($.trim(query)==0) {
    return false;
  }
  $.ajax({
        type: "GET",
        url:  "/search.php",
        data: 'query='+query, //the * will hit performance quite a bit, may need to be removed
        dataType: "JSON",
        success: function(msg) { showSearch(msg); }
  });
  return true;
}
var temp ="";
function showSearch(msg) {
  if(msg.error) {  //No results
    $("#searchResults").html("<h3>Error with query syntax</h3>");
    return false;
  } else if(msg.hits.total==0) {
 //   $("#searchResults").html("<h5>0 documents in: " + msg.took + "ms</h5>");
 //   return true;
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
function clippy() {
  //Load Clippy CSS
  $('head').append('<link rel="stylesheet" href="js/clippy/build/clippy.css" type="text/css" />');
  //Load clippy js
  $.getScript("js/clippy/build/clippy.min.js").done(function() {
    //Random clippy
    var agents = ["Bonzi", "Genius", "Clippy", "F1", "Genie", "Links", "Merlin", "Rover"];
    agents = agents[Math.floor(Math.random()*agents.length)];
    clippy.load(agents, function(agent) {
      agent.show();
      agent.speak("You Found Me! How about we sing a song?   ");
      setTimeout(function() {
        if(window.HTMLAudioElement) {
          var snd = new Audio('');
          if(snd.canPlayType('audio/mp3')) {
            snd = new Audio('js/clippy/R-Wreck-vocal.mp3');
          } else if(snd.canPlayType('audio/ogg')) {
            snd = new Audio('js/clippy/R-Wreck-vocal.ogg');
          }
          snd.play();
        } else {
          agent.speak("Seems your browser doesn't support HTML5, maybe next time");
        }
        var random = setInterval(function() { agent.animate(); }, 1000);
      }, 3000);
    });
  })
}

