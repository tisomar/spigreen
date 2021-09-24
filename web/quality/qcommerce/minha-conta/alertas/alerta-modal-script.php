<script>
    $(document).ready(function () {
        var readed = false;
        initModal();

        function initModal() {
            $.post(
                "<?php echo $root_path; ?>/ajax/alertas-obter-alertas-cliente-ajax",
                {
                    "view": 0
                },
                function (response, status) {
                    if (status == 'success' && response['alerta'] != null) {
                        if (response['pendentes'] == 0) {
                            $('#toRead').css('visibility', 'hidden');
                        } else {
                            $('#toRead').css('visibility', 'visible');
                        }
                        $('#toRead').html(response['pendentes']);

                        $('#id_alerta').val(response['alerta']['ID']);
                        for (var i in response['alerta']) {
                            if (i === 'SOMENTE_LEITURA') {
                                if (response['alerta'][i] == 0) {
                                    $('#' + i).prop('checked', false);
                                    $('.modal-campo.' + i).show();
                                    $('#btn-accept').css('display', 'block');
                                    $('#btn-accept').prop('disabled', true);
                                    $('#btn-close').css('display', 'none');
                                    $('#btn-close-window').css('visibility', 'hidden')
                                } else {
                                    $('#btn-accept').css('display', 'none');
                                    $('#btn-close').css('display', 'block');
                                    $('#btn-close-window').css('visibility', 'visible');
                                    $('.modal-campo.' + i).hide();
                                }
                            }
                            if (i === 'CORPO' || i === 'TITULO' || i === 'TIPO_MENSAGEM') {
                                $('.modal-campo.' + i).html(response['alerta'][i]);
                            }
                        }

                        var basePathPdf = "<?php echo ROOT_PATH ?>";
                        if (response['pdfs'] != null && response['pdfs'].length > 0) {
                            $('#adjuntos').html(
                                '<h5 style="padding-top: 10px; padding-bottom: 5px"><?php echo _trans("alertas.adjuntos") ?></h5>'
                            );
                            for (var j in response['pdfs']) {
                                var pathPdf = basePathPdf + response['pdfs'][j]['strPathAlertaPdf'] + response['pdfs'][j]['nomeArquivo'];
                                $('#adjuntos').append(
                                    '<div><a target="_blank" href= "' + pathPdf + '"><i class="fa fa-file-pdf-o"></i> ' + response['pdfs'][j]['nomeOriginal'] + '</a></div>'
                                );
                            }
                        }
                        else {
                            $('#adjuntos').hide();
                        }
                        $('.modalAlertas').modal('show');
                    } else if (response['alerta'] == null) {
                        if (response['pendentes'] == 0) {
                            $('#toRead').css('visibility', 'hidden');
                        } else {
                            $('#toRead').css('visibility', 'visible');
                        }
                        $('#toRead').html(response['pendentes']);
                        if(typeof initVideoVip == 'function'){
                            initVideoVip();
                        }
                    }
                },
                'json'
            );

            $('.modalAlertas').on('shown.bs.modal', function () {
                $('#modal-alertas').scrollTop(0);
            });

            if ($('#modal-alertas').get(0).scrollHeight !== $('#modal-alertas').get(0).clientHeight || $('.SOMENTE_LEITURA').is(':visible')) {
                $('#btn-accept').prop('disabled', true);
            } else {
                $('#btn-accept').prop('disabled', false);
            }

            if ($('#modal-alertas').get(0).scrollHeight === $('#modal-alertas').get(0).clientHeight) {
                readed = true;
            }
        }

        $('#SOMENTE_LEITURA').on('click', function () {
            if (readed === true && $(this).is(':checked')) {
                $('#btn-accept').prop('disabled', false);
            }
            else {
                $('#btn-accept').prop('disabled', true);
            }
        });

        $('#modal-alertas').on("scroll", function () {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                readed = true;
            }

            if (readed == true && $('#SOMENTE_LEITURA').is(':checked')) {
                $('#btn-accept').prop('disabled', false);
            }
        });

        $('#btn-close-window').on('click', function () {
            if ($('#data_lido').val() === '') {
                $.post(
                    "<?php echo $root_path; ?>/ajax/alertas-fechar-alerta.php",
                    null,
                    function () {
                        initVideoVip();
                    },
                    null);
            }
        });

        $('.continue').on('click', function (event) {
            if ($('#data_lido').val() === '') {
                $.post(
                    "<?php echo $root_path; ?>/ajax/alertas-aceitar-alerta",
                    {
                        'idAlerta': $('#id_alerta').val(),
                        'idDocumento': $('#id_documento').val()
                    },
                    function (response) {
                        //Atualizar o badge vermelho de mensagens
                        if (response['pendentes'] == 0) {
                            $('#toRead').css('visibility', 'hidden');
                        } else {
                            $('#toRead').css('visibility', 'visible');
                        }
                        $('#toRead').html(response['pendentes']);
                        $('#' + response['idDocumentoAlertaClientes']).closest('tr').css('font-weight', 'normal');
                        $('#' + response['idDocumentoAlertaClientes']).closest('tr').find('td:eq(2)').html(response['dataAceptacao']);

                        // Load up a new modal...
                        $.get(
                            "<?php echo $root_path; ?>/central/actions/alertas-obter-alertas-cliente-ajax",
                            {
                                "view": 0
                            },
                            function (response, status) {
                                if (status === 'success' && response['alerta'] != null) {
                                    $('#id_alerta').val(response['alerta']['ID']);
                                    for (var i in response['alerta']) {
                                        if (i === 'SOMENTE_LEITURA') {
                                            if (response['alerta'][i] == 0) {
                                                $('#' + i).prop('checked', false);
                                                $('.modal-campo.' + i).show();
                                                $('#btn-accept').prop('disabled', true);
                                                $('#btn-accept').css('display', 'block');
                                                $('#btn-close').css('display', 'none');
                                                $('#btn-close-window-window').css('visibility', 'hidden');
                                            } else {
                                                $('#btn-accept').css('display', 'none');
                                                $('#btn-close').css('display', 'block');
                                                $('#btn-close-window').css('visibility', 'visible');
                                                $('.modal-campo.' + i).hide();
                                            }
                                        }
                                        if (i === 'CORPO' || i === 'TITULO' || i === 'TIPO_MENSAGEM') {
                                            $('.modal-campo.' + i).html(response['alerta'][i]);
                                        }
                                    }

                                    var basePathPdf = "<?php echo ROOT_PATH ?>";
                                    if (response['pdfs'] != null && response['pdfs'].length > 0) {
                                        $('#adjuntos').html(
                                            '<h5 style="padding-top: 10px; padding-bottom: 5px"><?php echo _trans("alertas.adjuntos") ?></h5>'
                                        );
                                        for (var j in response['pdfs']) {
                                            var pathPdf = basePathPdf + response['pdfs'][j]['strPathAlertaPdf'] + response['pdfs'][j]['nomeArquivo'];
                                            $('#adjuntos').append(
                                                '<div><a target="_blank" href= "' + pathPdf + '"><i class="fa fa-file-pdf-o"></i> ' + response['pdfs'][j]['nomeOriginal'] + '</a></div>'
                                            );
                                        }
                                    }
                                    else {
                                        $('#adjuntos').hide();
                                    }
                                    $('.modalAlertas').modal('show');
                                } else if (response['alerta'] == null) {
                                    if (response['pendentes'] == 0) {
                                        $('#toRead').css('visibility', 'hidden');
                                    } else {
                                        $('#toRead').css('visibility', 'visible');
                                    }
                                    $('#toRead').html(response['pendentes']);
                                    if(typeof initVideoVip == 'function'){
                                        initVideoVip();
                                    }
                                }
                            },
                            'json'
                        )
                    },
                    'json'
                );
            }
        });

        $('.visualizar-alerta').on('click', function (event) {
            var id_documento = $(this).get(0).id;
            $.post(
                "<?php echo $root_path; ?>/ajax/alertas-obter-alertas-cliente-ajax",
                {
                    "view": 1,
                    "idDocumento": id_documento
                },
                function (response, status) {
                    if (status === 'success' && response) {
                        $('#id_alerta').val(response['alerta']['ID']);
                        $('#id_documento').val(id_documento);
                        $('#data_lido').val(response['alerta']['DATA_LIDO']);
                        for (var i in response['alerta']) {
                            if (i === 'SOMENTE_LEITURA') {
                                if (response['alerta'][i] == 0) {
                                    if (response['alerta']['DATA_LIDO'] != '') {
                                        $('#' + i).prop('checked', true);
                                        $('#' + i).prop('disabled', true);
                                        $('#btn-accept').css('display', 'none');
                                        $('#btn-close').css('display', 'block');
                                    } else {
                                        $('#' + i).prop('checked', false);
                                        $('#' + i).prop('disabled', false);
                                        $('#btn-accept').css('display', 'block');
                                        $('#btn-close').css('display', 'none');
                                        $('#btn-accept').prop('disabled', true);
                                    }
                                    $('.modal-campo.' + i).show();
                                    $('#btn-close-window').css('visibility', 'hidden');
                                } else {
                                    if (response['alerta']['DATA_LIDO'] != '') {
                                        $('#' + i).prop('checked', true);
                                        $('#' + i).prop('disabled', true);
                                        $('#btn-accept').css('display', 'none');
                                        $('#btn-close').css('display', 'block');
                                    } else {
                                        $('#' + i).prop('checked', false);
                                        $('#' + i).prop('disabled', false);
                                        $('#btn-accept').css('display', 'block');
                                        $('#btn-close').css('display', 'none');
                                        $('#btn-accept').prop('disabled', true);
                                    }
                                    $('#btn-close-window').css('visibility', 'visible');
                                    $('.modal-campo.' + i).hide();
                                }
                            }
                            if (i === 'CORPO' || i === 'TITULO' || i === 'TIPO_MENSAGEM') {
                                $('.modal-campo.' + i).html(response['alerta'][i]);
                            }
                        }

                        var basePathPdf = "<?php echo ROOT_PATH ?>";
                        if (response['pdfs'] != null && response['pdfs'].length > 0) {
                            $('#adjuntos').html(
                                '<h5 style="padding-top: 10px; padding-bottom: 5px"><?php echo _trans("alertas.adjuntos") ?></h5>'
                            );
                            for (var j in response['pdfs']) {
                                var pathPdf = basePathPdf + response['pdfs'][j]['strPathAlertaPdf'] + response['pdfs'][j]['nomeArquivo'];
                                $('#adjuntos').append(
                                    '<div><a target="_blank" href= "' + pathPdf + '"><i class="fa fa-file-pdf-o"></i> ' + response['pdfs'][j]['nomeOriginal'] + '</a></div>'
                                );
                            }
                        }
                        else {
                            $('#adjuntos').hide();
                        }
                        $('.modalAlertas').modal('show');
                    }
                },
                'json'
            );
        });
    });
</script>