(function($) {
    $.fn.initMaskMoney = function() {
        return this.maskMoney({
            thousands:'.', decimal:',', allowZero:true, prefix: 'R$ '
        });
    };
})(jQuery);