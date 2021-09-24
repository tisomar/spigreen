<?php

?>
<style>
    @media (max-width: 768px) {
        .modal-dialog { width: 100%; position: absolute; top: 0px; left: 0px}
    }

    #adjuntos a {
        padding-right: 15px;
    }
</style>
<div class="container">
    <!-- Modal -->
    <div class="modal fade modalAlertas" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="btn-close-window" type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title modal-campo TIPO_MENSAGEM"></h4>
                </div>
                <div class="modal-body" id="modal-alertas"
                     style="max-height: calc(100vh - 212px); overflow-y: scroll;">
                    <h5 class="modal-campo TITULO" style="padding-bottom: 10px; font-weight: bold"></h5>
                    <div class="modal-campo CORPO" style="text-align: justify"></div>

                    <div id="adjuntos">
                        <h5 style="padding-top: 10px; padding-bottom: 5px"><?php echo _trans('alertas.adjuntos') ?></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-check pull-left modal-campo SOMENTE_LEITURA">
                        <input id="SOMENTE_LEITURA" type="checkbox" class="form'check'input">
                        <label class="form-check-label"
                               for="SOMENTE_LEITURA"><?php echo _trans('alertas.aceitar') ?></label>
                    </div>
                    <input type="hidden" id="id_alerta">
                    <input type="hidden" id="id_documento">
                    <input type="hidden" id="data_lido">
                    <button id="btn-accept" type="button"
                            class="btn btn-danger pull-right continue" data-dismiss="modal" style="display: none"><i
                                class="fa fa-check"></i> <?php echo _trans('button.aceito') ?>
                    </button>
                    <button id="btn-close" type="button"
                            class="btn btn-danger pull-right continue" data-dismiss="modal" style="display: none"><i
                                class="fa fa-close"></i> <?php echo _trans('button.fechar') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('alerta-modal-script.php');
?>
