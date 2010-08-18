/*
Copyright: 
    Arnout Kazemier ( blog.3rd-Eden.com )
Version:
    1.0.0.0
License:
    GPL/CC
*/
var audiobar_fnQue = [];
var audiobar_domIsReady;
function audiobar_domReady(fn){
    if(audiobar_domIsReady)
        return fn();

    if(!audiobar_fnQue.length){
        function ready(){
            if(audiobar_domIsReady)
                return;
            audiobar_domIsReady = true;

            var i = audiobar_fnQue.length; while(i--)
                audiobar_fnQue[i]();

            audiobar_fnQue = null;
        };
        (function(){
            if(document.body && document.body.lastChild){
                ready();
            } else { 
                return setTimeout(arguments.callee,0);
            }
        })();
        window.onload = ready;
    };
    audiobar_fnQue.unshift(fn);
};

