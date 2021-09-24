var itau = (function() {

    var outputData = function() {
        $.ajax({
            async: false,
            dataType: "json",
            url: $('#itau-shopline').find('[name="itau-data-url"]').val(),
            success: function(data) {
                var token   = data.token;

                $('#data-itau-token').remove();
                $('#itau-shopline').attr('action', data.destination);
                $('#itau-shopline').append('<input type="hidden" name="DC" value="'+ token +'" id="data-itau-token" />')
            }
        });

        window.open('', 'SHOPLINE', 'toolbar=yes,menubar=yes,resizable=yes,status=no,scrollbars=yes,width=815,height=575');
    };

    return {
        output: outputData
    };
})();