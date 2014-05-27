/* Easter Egg */
var clippy_easter_hold = false;
function search_easter(query) {
    if (query === "George P. Burdell") {
        do_easter();
    }
}
function do_easter() {
    if (clippy_easter_hold) {
        return false;
    }
    clippy_easter_hold = true;

    //Load Clippy CSS
    $('head').append('<link rel="stylesheet" href="js/clippy/build/clippy.css" type="text/css" />');
    //Load clippy js
    $.getScript("js/clippy/build/clippy.min.js").done(function() {
        //Random clippy
        var agents = ["Bonzi", "Genius", "Clippy", "F1", "Genie", "Links", "Merlin", "Rover"];
        agents = agents[Math.floor(Math.random() * agents.length)];
        clippy.load(agents, function(agent) {
            agent.show();
            agent.speak("You found me! How about we sing a song?");
            setTimeout(function() {
                if (!window.HTMLAudioElement) {
                    agent.speak("Seems your browser doesn't support HTML5, maybe next time");
                }
                else {
                    var snd = new Audio('');
                    if (snd.canPlayType('audio/mp3')) {
                        snd = new Audio('js/clippy/R-Wreck-vocal.mp3');
                    } else if (snd.canPlayType('audio/ogg')) {
                        snd = new Audio('js/clippy/R-Wreck-vocal.ogg');
                    }
                    snd.play();
                }
                var random = setInterval(function() {
                    agent.animate();
                }, 1000);
            }, 3000);
        });
    });
}
