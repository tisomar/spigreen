<script>
    $(function() {
        Number.prototype.formatMoney = function(c, d, t){
            var n = this,
                c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };


        //Date Range Picker
        $('#daterangepicker').daterangepicker(
            {
                locale: {
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'De',
                    toLabel: 'Até',
                    weekLabel: 'W',
                    customRangeLabel: 'Escolher data'
                },
                format: 'DD/MM/YYYY',
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Ontem': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Últimos 7 dias': [moment().subtract('days', 6), moment()],
                    'Últimos 30 dias': [moment().subtract('days', 30), moment()],
                    'Mês atual': [moment().startOf('month'), moment().endOf('month')],
                    'Último mês': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                opens: 'left',
                startDate: moment().subtract('days', 29).format('DD/MM/YYYY'),
                endDate: moment().format('DD/MM/YYYY')
            },
            function(start, end) {
                $('#daterangepicker2 span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        );

        $('#datepicker3').datepicker();

    });
</script>