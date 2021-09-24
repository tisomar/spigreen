// Verifica o tamanho da tela
function identifyScreen() {
    var screen = $(window).innerWidth();

    $('body').attr('data-screen', 'xs');

    if(screen >= 768) {
        $('body').attr('data-screen', 'sm');
    }

    if(screen >= 992) {
        $('body').attr('data-screen', 'md');
    }

    if(screen >= 1200) {
        $('body').attr('data-screen', 'lg');
    }
}

// Ajusta o padding do body para ficar alinhado com o topo que está fixo
function controlPaddingBody(){
    /*padding = $('#main-header').innerHeight() + 'px';

     $('body').css('padding-top', padding);*/
}

$(document).ready(function(){
    // Define dataScreen para pegar o tamanho da tela
    dataScreen = $('body').data('screen');

    // Fontes
    //$.getScript('//use.typekit.net/gav5mpg.js',function(){
    //    try{Typekit.load();}catch(e){}
    //})
    /*
    (function(d) {
        var config = {
                kitId: 'cto6lgx',
                scriptTimeout: 3000
            },
            h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
    })(document);
    */

    identifyScreen();
})

$(window).on('load', function(){
    controlPaddingBody();
})

$(window).resize(function(){
    controlPaddingBody();
    identifyScreen();
});



