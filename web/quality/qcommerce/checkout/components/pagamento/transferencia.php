<?php
use QPress\Template\Widget;

if (empty($valorTranferencia)) {
    ?>
    <form role="form" class="form-payment form-validate form-disabled-on-load" method="post" action="#"  id="" enctype="multipart/form-data">
        <input
            type="hidden"
            name="forma_pagamento"
            value="<?= PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA ?>"
        >

        <?php renderPagamentoDivididoFields() ?>
        
        <div class="panel">
            <div class="panel-body">
                <?php
                Widget::render('checkout/pagamento/title-payment-type', [
                    'paymentTypeId'     => PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA,
                    'paymentTypeName'   => 'Transferência',
                    'paymentTypeValue'  => $valorRestanteDividido,
                    'isOpenedPanel'     => $isOpenedPanel,
                ]);
                ?>

                <div
                    id="<?= PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA ?>"
                    class="panel-collapse collapse <?= ($isOpenedPanel ? 'in' : '') ?>"
                >
                    <div class="panel-body">
                        <?php
                        Widget::render('checkout/pagamento/header-payment-type', [
                            'valorTotal' => $valorRestanteDividido,
                        ]);
                        ?>

                        <br>

                        <!-- Ag 4408  C/C 130025616 -->
                        <!-- <table style="margin-bottom: 30px;">
                            <tbody>
                                <tr>
                                    <td style="width: 120px;" colspan="2">
                                        <strong>Banco Santander - 033</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>Agência</strong>
                                    </td>
                                    <td class="text-right">4408</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>Conta Corrente</strong>
                                    </td>
                                    <td class="text-right">130025616</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>CNPJ</strong>
                                    </td>
                                    <td class="text-right">31.716.218/0001-70</td>
                                </tr>
                            </tbody>
                        </table> -->

                        <!-- Ag 4205-6  C/C 21837-5 -->
                        <table style="margin-bottom: 30px;">
                            <tbody>
                                <tr>
                                    <td style="width: 120px;" colspan="2">
                                        <strong>Banco do Brasil - 001</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>Agência</strong>
                                    </td>
                                    <td class="text-right">4205-6</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>Conta Corrente</strong>
                                    </td>
                                    <td class="text-right">21837-5</td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <strong>CNPJ</strong>
                                    </td>
                                    <td class="text-right">31.716.218/0001-70</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Ag 4408  C/C 130025616 -->
                        <table style="margin-bottom: 30px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>PIX</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>pix.bb@spigreen.com.br</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-group container-comprovante">
                            <label class="label-comprovante" for="comprovante">
                                <input id="comprovante" name="comprovante" type="file"/>
                                <span class="fa fa-upload"></span>
                                Anexar comprovante
                            </label>
                            <div class="preview-comprovante-container" style="display: none">
                                <button type="button" class="btn btn-sm btn-danger excluir-comprovante">
                                    Remover
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <small style="color: #d35400;">
                                O pagamento é aprovado a partir do próximo dia útil.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-success btn-block confirm-payment" data-payment-type="transferencia" disabled>
                            <span class="<?php icon('lock') ?>"></span> Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .label-comprovante {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            color: #56af50;
            border: currentColor 1px solid;
            border-radius: 5px;
            transition: background-color 300ms ease-in-out;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        .label-comprovante:hover {
            background-color: #f0f0f0;
        }
        .label-comprovante span {
            margin-right: 10px;
        }
        .label-comprovante input[type="file"] {
            display: none;
        }

        img.preview-comprovante {
            display: block;
            max-width: 300px;
            max-height: 300px;
            margin: 10px 0;
        }
    </style>

    <script>
        $('#comprovante').off('change').on('change', e => {
            const $this = $(e.target),
                $submitButton = $this.closest('form').find('button[type="submit"]'),
                files = e.target.files
            
            $this
                .closest('.container-comprovante')
                .find('.preview-comprovante-container')
                .hide()
                .find('.preview-comprovante')
                .remove()

            if (!!files && files.length > 0) {
                const file = files.item(0)

                if (['image/png', 'image/jpg', 'image/jpeg'].indexOf(file.type) !== -1) {
                    const reader = new FileReader()

                    reader.onload = e => {
                        const base64 = e.target.result

                        $this
                            .parent()
                            .next('.preview-comprovante-container')
                            .show()
                            .prepend(
                                $('<img>')
                                    .prop('src', base64)
                                    .addClass('preview-comprovante')
                            )
                        
                        $submitButton.prop('disabled', false)
                        return;
                    }

                    reader.readAsDataURL(file)
                } else {
                    $this.val(null)

                    alert('Somente permitido arquivos PNG e JPG')
                }
            }

            $submitButton.prop('disabled', true)
        })

        $('.preview-comprovante-container .excluir-comprovante').on('click', e => {
            const $this = $(e.target),
                $container = $this.closest('.container-comprovante')
            
            $this.closest('form').find('input#comprovante').val(null).trigger('change')
        })
    </script>
    <?php
}
