<?php /** @var $object Plano */  ?>

<form action="/admin/planos/registration" id="registration" method="post" class="row-border form-horizontal">
    <?php if (!$object->isNew()) : ?>
        <input type="hidden" name="data[ID]" value="<?= $object->getId() ?>">
    <?php endif ?>

    <div id="graduacoes" hidden>
        <?= $planoCarreiras->toJSON(false) ?>
    </div>

    <fieldset>
        <div class="form-group">
            <label for="nome" class="col-sm-3 control-label">
                <span class="required"> *</span>
                Nome
            </label>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="nome" name="data[NOME]" required value="<?= $object->getNome() ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="descricao" class="col-sm-3 control-label">
                Descrição
            </label>
            <div class="col-sm-6">
                <textarea rows="5" type="text" name="data[DESCRICAO]" class="form-control" id="descricao"><?= $object->getDescricao() ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label for="nivel" class="col-sm-3 control-label">
                Nivel
            </label>
            <div class="col-sm-6">
                <input
                    type="number"
                    min="0"
                    max="127"
                    name="data[NIVEL]"
                    class="form-control"
                    id="nivel"
                    value="<?= $object->getNivel() ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="plano-cliente-preferencial">
                Plano de Cliente Preferencial
            </label>
            <div class="col-sm-6">
                <select name="data[PLANO_CLIENTE_PREFERENCIAL]" class="form-control" id="plano-cliente-preferencial">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PLANO_CLIENTE_PREFERENCIAL) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getPlanoClientePreferencial() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="row">
                    <label class="col-sm-3 control-label" for="participa-fidelidade">
                        Participa do Desconto de Fidelidade
                    </label>
                    <div class="col-sm-6">
                        <select name="data[PARTICIPA_FIDELIDADE]" class="form-control" id="participa-fidelidade">
                            <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_FIDELIDADE) as $value => $text) : ?>
                                <option value="<?= $value ?>" <?= $object->getParticipaFidelidade() == $value ? 'selected' : '' ?>><?= $text ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-sm-offset-3 box-desconto-fidelidade">
                <table class="table">
                    <thead>
                    <tr>
                        <th>De <small>(meses)</small></th>
                        <th>Até <small>(meses)</small></th>
                        <th>Percentual</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($percsFidelidade as $index => $percFidelidade) : /* @var $percFidelidade PlanoDescontoFidelidade */ ?>
                        <tr>
                            <td>
                                <input
                                    type="hidden"
                                    name="data[DESC_FIDELIDADE][<?= $index ?>][ID]"
                                    value="<?= $percFidelidade->getId() ?>">
                                <input
                                    type="number"
                                    class="form-control input-sm text-center"
                                    name="data[DESC_FIDELIDADE][<?= $index ?>][MES_INICIAL]"
                                    value="<?= $percFidelidade->getMesInicial() ?>"
                                    min="0"
                                    required>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control input-sm text-center"
                                    name="data[DESC_FIDELIDADE][<?= $index ?>][MES_FINAL]"
                                    value="<?= $percFidelidade->getMesFinal() ?>"
                                    min="0"
                                    <?= ($index + 1) < count($percsFidelidade) ? 'required' : '' ?>>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control input-sm"
                                    name="data[DESC_FIDELIDADE][<?= $index ?>][PERCENTUAL]"
                                    value="<?= $percFidelidade->getPercentual() ?>"
                                    min="0"
                                    max="100"
                                    required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary btn-add">+</button>
                <button type="button" class="btn btn-sm btn-danger btn-del">-</button>
            </div>

            <div class="col-xs-12 col-sm-6 col-sm-offset-3 box-desconto-fidelidade-graduacao">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Gradução <small>(e acima)</small></th>
                        <th>Percentual</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($percsFidGraduacao as $index => $percFidGraduacao) : /* @var $percFidGraduacao PlanoDescontoFidelidadeGraduacao */ ?>
                        <tr>
                            <td>
                                <input
                                    type="hidden"
                                    name="data[DESC_FID_GRADUACAO][<?= $index ?>][ID]"
                                    value="<?= $percFidGraduacao->getId() ?>">
                                <select name="data[DESC_FID_GRADUACAO][<?= $index ?>][PLANO_CARREIRA_ID]]" class="form-control input-sm" required>
                                    <?php foreach ($planoCarreiras as $planoCarreira) : /* @var $planoCarreira PlanoCarreira */ ?>
                                        <option value="<?= $planoCarreira['ID'] ?>"
                                                <?= $percFidGraduacao->getPlanoCarreiraId() == $planoCarreira['ID'] ? 'selected' : '' ?>>
                                            <?= $planoCarreira['GRADUACAO'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control input-sm"
                                    name="data[DESC_FID_GRADUACAO][<?= $index ?>][PERCENTUAL]"
                                    value="<?= $percFidGraduacao->getPercentual() ?>"
                                    min="0"
                                    max="100"
                                    required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary btn-add">+</button>
                <button type="button" class="btn btn-sm btn-danger btn-del">-</button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="row">
                    <label class="col-sm-3 control-label" for="participa-expansao">
                        Participa do Bônus de Expansão
                    </label>
                    <div class="col-sm-6">
                        <select name="data[PARTICIPA_EXPANSAO]" class="form-control" id="participa-expansao">
                            <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_EXPANSAO) as $value => $text) : ?>
                                <option value="<?= $value ?>" <?= $object->getParticipaExpansao() == $value ? 'selected' : '' ?>><?= $text ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-sm-offset-3 box-bonus-expansao">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Geração</th>
                        <th>Percentual</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($percsExpansao as $percExpansao) : /* @var $percExpansao PlanoPercentualBonus */ ?>
                        <tr>
                            <td>
                                <label for="geracao<?= $percExpansao->getGeracao() ?>">
                                    <?= $percExpansao->getGeracao() ?>
                                </label>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    step="any"
                                    class="form-control input-sm"
                                    id="geracao<?= $percExpansao->getGeracao() ?>"
                                    name="data[PERC_EXPANSAO][<?= $percExpansao->getGeracao() ?>]"
                                    value="<?= $percExpansao->getPercentual() ?>"
                                    min="0"
                                    required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary btn-add">+</button>
                <button type="button" class="btn btn-sm btn-danger btn-del">-</button>
            </div>

        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-produtividade">
                Participa do Bônus de Produtividade
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_PRODUTIVIDADE]" class="form-control" id="participa-produtividade">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_PRODUTIVIDADE) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaProdutividade() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-cliente-preferencial">
                Participa do Bônus de Cliente Preferencial
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_CLIENTE_PREFERENCIAL]" class="form-control" id="participa-cliente-preferencial">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_CLIENTE_PREFERENCIAL) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaClientePreferencial() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">

            <div class="col-xs-12">
                <div class="row">
                    <label class="col-sm-3 control-label" for="participa-plano-carreira">
                        Participa do Plano de Carreira
                    </label>
                    <div class="col-sm-6">
                        <select name="data[PARTICIPA_PLANO_CARREIRA]" class="form-control" id="participa-plano-carreira">
                            <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_PLANO_CARREIRA) as $value => $text) : ?>
                                <option value="<?= $value ?>" <?= $object->getParticipaPlanoCarreira() == $value ? 'selected' : '' ?>><?= $text ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 box-graduacao-maxima">
                <div class="row">
                    <label class="col-sm-3 control-label" for="graduacao-maxima">
                        Graduação Máxima
                    </label>
                    <div class="col-sm-6">
                        <select name="data[GRADUACAO_MAXIMA]" class="form-control" id="graduacao-maxima">
                            <option value="">Sem graduação máxima</option>
                            <?php foreach ($planoCarreiras as $planoCarreira) : /* @var $planoCarreira PlanoCarreira */ ?>
                                <option value="<?= $planoCarreira['ID'] ?>"
                                        <?= $object->getGraduacaoMaxima() == $planoCarreira['ID'] ? 'selected' : '' ?>>
                                    <?= $planoCarreira['GRADUACAO'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-participacao-lucros">
                Participa da Participação de Lucros
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_PARTICIPACAO_LUCROS]" class="form-control" id="participa-participacao-lucros">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_PARTICIPACAO_LUCROS) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaParticipacaoLucros() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-lideranca">
                Participa do Bônus de Liderança
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_LIDERANCA]" class="form-control" id="participa-lideranca">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_LIDERANCA) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaLideranca() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-desempenho">
                Participa do Bônus de Desempenho
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_DESEMPENHO]" class="form-control" id="participa-desempenho">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_DESEMPENHO) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaDesempenho() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-destaque">
                Participa do Bônus de Destaque
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_DESTAQUE]" class="form-control" id="participa-destaque">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_DESTAQUE) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaDestaque() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label" for="participa-incentivo">
                Participa de Incentivos
            </label>
            <div class="col-sm-6">
                <select name="data[PARTICIPA_INCENTIVO]" class="form-control" id="participa-incentivo">
                    <?php foreach (PlanoPeer::getValueSet(PlanoPeer::PARTICIPA_INCENTIVO) as $value => $text) : ?>
                        <option value="<?= $value ?>" <?= $object->getParticipaIncentivo() == $value ? 'selected' : '' ?>><?= $text ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="perc_desconto_hotsite" class="col-sm-3 control-label">
                Percentual do Bônus de Ecommerce
            </label>
            <div class="col-sm-6">
                <input
                    type="number"
                    min="0"
                    max="100"
                    name="data[PERC_DESCONTO_HOTSITE]"
                    class="form-control"
                    id="perc_desconto_hotsite"
                    value="<?= $object->getPercDescontoHotsite() ?>">
            </div>
        </div>

        <div class="form-group form-actions">
            <div class="col-xs-12 col-sm-9 col-sm-offset-3">
                <button type="submit" title="Salvar" class="btn btn-primary btn-label btn btn-primary" id="registrer-element-8">
                    <span class="icon-ok"></span> SALVAR
                </button>
                <a title="Cancelar" class="btn btn-default btn" href="/admin/planos/list">
                    <i class="icon-remove"></i> Cancelar
                </a>
            </div>
        </div>
    </fieldset>
</form>

<script>
    $('select#participa-fidelidade').on('change', function (e) {
        var value = this.value;

        $('.box-desconto-fidelidade')[value === '1' ? 'show' : 'hide']();
        $('.box-desconto-fidelidade input:not([type="hidden"])').attr('disabled', value !== '1');

        $('.box-desconto-fidelidade-graduacao')[value === '1' ? 'show' : 'hide']();
        $('.box-desconto-fidelidade-graduacao input:not([type="hidden"])').attr('disabled', value !== '1');
    });

    $('select#participa-expansao').on('change', function (e) {
        var value = this.value;

        $('.box-bonus-expansao')[value === '1' ? 'show' : 'hide']();
        $('.box-bonus-expansao input:not([type="hidden"])').attr('disabled', value !== '1');
    });

    $('select#participa-plano-carreira').on('change', function (e) {
        var value = this.value;

        $('.box-graduacao-maxima')[value === '1' ? 'show' : 'hide']();
    });

    $('.box-desconto-fidelidade .btn-add').on('click', function () {
        var $table = $('.box-desconto-fidelidade .table tbody');
        var index = $table.children().length + 1;

        $table
            .find('tr:last-child input[name*="[MES_FINAL]"]')
            .attr('required', true);

        $table.append(
            $('<tr>').append(
                $('<td>').append(
                    $('<input>').addClass('form-control input-sm text-center').attr({
                        type: 'number',
                        name: 'data[DESC_FIDELIDADE][' + index + '][MES_INICIAL]',
                        required: true
                    })
                ),
                $('<td>').append(
                    $('<input>').addClass('form-control input-sm text-center').attr({
                        type: 'number',
                        name: 'data[DESC_FIDELIDADE][' + index + '][MES_FINAL]'
                    })
                ),
                $('<td>').append(
                    $('<input>').addClass('form-control input-sm').attr({
                        type: 'number',
                        name: 'data[DESC_FIDELIDADE][' + index + '][PERCENTUAL]',
                        required: true,
                        max: 100
                    })
                )
            )
        );
    });

    $('.box-desconto-fidelidade-graduacao .btn-add').on('click', function () {
        var $table = $('.box-desconto-fidelidade-graduacao .table tbody');
        var index = $table.children().length + 1;

        var planosCarreira = JSON.parse($('#graduacoes').text());

        $table.append(
            $('<tr>').append(
                $('<td>').append(
                    $('<select>')
                        .addClass('form-control input-sm')
                        .attr({
                            name: 'data[DESC_FID_GRADUACAO][' + index + '][PLANO_CARREIRA_ID]',
                            required: true
                        })
                        .append(
                            planosCarreira.map(function(planoCarreira) {
                                return $('<option>')
                                    .val(planoCarreira.ID)
                                    .text(planoCarreira.GRADUACAO);
                            })
                        )
                ),
                $('<td>').append(
                    $('<input>').addClass('form-control input-sm').attr({
                        type: 'number',
                        name: 'data[DESC_FID_GRADUACAO][' + index + '][PERCENTUAL]',
                        required: true,
                        min: 0,
                        max: 100
                    })
                )
            )
        );
    });

    $('.box-desconto-fidelidade .btn-del').on('click', function () {
        var $table = $('.box-desconto-fidelidade .table tbody');

        var $linhaRemovida = $table.find('tr:last-child').remove();

        var $idInput = $linhaRemovida.find('input[name*="[DESC_FIDELIDADE]"][name*="[ID]"]');

        if ($idInput.length > 0) {
            $('.box-desconto-fidelidade').append(
                $('<input>')
                    .attr({
                        type: 'hidden',
                        name: 'data[DESC_FIDELIDADE_EXCLUIR][]'
                    })
                    .val($idInput.val())
            )
        }

        $table
            .find('tr:last-child input[name*="[MES_FINAL]"]')
            .attr('required', false);
    });

    $('.box-desconto-fidelidade-graduacao .btn-del').on('click', function () {
        var $table = $('.box-desconto-fidelidade-graduacao .table tbody');

        var $linhaRemovida = $table.find('tr:last-child').remove();

        var $idInput = $linhaRemovida.find('input[name*="[DESC_FID_GRADUACAO]"][name*="[ID]"]');

        if ($idInput.length > 0) {
            $('.box-desconto-fidelidade-graduacao').append(
                $('<input>')
                    .attr({
                        type: 'hidden',
                        name: 'data[DESC_FID_GRADUACAO_EXCLUIR][]'
                    })
                    .val($idInput.val())
            )
        }
    });

    $('.box-bonus-expansao .btn-add').on('click', function() {
        var $table = $('.box-bonus-expansao .table tbody');
        var geracao = $table.children().length + 1;

        $table.append(
            $('<tr>').append(
                $('<td>').addClass('text-center').append(
                    $('<label>')
                        .attr({
                            for: 'geracao' + geracao
                        })
                        .text(geracao)
                ),
                $('<td>').append(
                    $('<input>').addClass('form-control').attr({
                        id: 'geracao' + geracao,
                        type: 'number',
                        name: 'data[PERC_EXPANSAO][' + geracao + ']',
                        required: true,
                        max: 100
                    })
                )
            )
        );
    });

    $('.box-bonus-expansao .btn-del').on('click', function() {
        var $table = $('.box-bonus-expansao .table tbody');

        $table.find('tr:last-child').remove();
    });

    $(function () {
        var participaFidelidade = $('select#participa-fidelidade').val();
        $('.box-desconto-fidelidade')[participaFidelidade === '1' ? 'show' : 'hide']();
        $('.box-desconto-fidelidade-graduacao')[participaFidelidade === '1' ? 'show' : 'hide']();

        var participaExpansao = $('select#participa-expansao').val();
        $('.box-bonus-expansao')[participaExpansao === '1' ? 'show' : 'hide']();

        var participaPlanoCarreira = $('select#participa-plano-carreira').val();
        $('.box-graduacao-maxima')[participaPlanoCarreira === '1' ? 'show' : 'hide']();
    })
</script>

<style>
    .box-bonus-expansao,
    .box-desconto-fidelidade,
    .box-desconto-fidelidade-graduacao {
        padding-top: 20px;
        display: flex;
        align-items: flex-end;
    }

    .box-bonus-expansao .table,
    .box-desconto-fidelidade .table,
    .box-desconto-fidelidade-graduacao .table {
        margin-bottom: 0;
        table-layout: fixed;
    }

    .box-bonus-expansao .table thead tr th:nth-child(1),
    .box-bonus-expansao .table tbody tr td:nth-child(1) {
        width: 30%;
        vertical-align: middle;
        text-align: center;
    }

    .box-bonus-expansao .table tbody tr td:nth-child(1) label {
        display: inline;
        padding: 10px 20px 10px 10px;
    }

    .box-bonus-expansao .table thead tr th:nth-child(2),
    .box-bonus-expansao .table tbody tr td:nth-child(2) {
        width: 70%;
    }

    .box-desconto-fidelidade .table thead tr th:nth-child(1),
    .box-desconto-fidelidade .table tbody tr td:nth-child(1) {
        width: 25%;
    }

    .box-desconto-fidelidade .table thead tr th:nth-child(2),
    .box-desconto-fidelidade .table tbody tr td:nth-child(2) {
        width: 25%;
    }

    .box-desconto-fidelidade .table thead tr th:nth-child(3),
    .box-desconto-fidelidade .table tbody tr td:nth-child(3) {
        width: 50%;
    }

    .box-desconto-fidelidade-graduacao .table thead tr th:nth-child(1),
    .box-desconto-fidelidade-graduacao .table tbody tr td:nth-child(1) {
        width: 60%;
    }

    .box-desconto-fidelidade-graduacao .table thead tr th:nth-child(2),
    .box-desconto-fidelidade-graduacao .table tbody tr td:nth-child(2) {
        width: 40%;
    }

    .box-bonus-expansao .btn-add,
    .box-bonus-expansao .btn-del,
    .box-desconto-fidelidade .btn-add,
    .box-desconto-fidelidade .btn-del,
    .box-desconto-fidelidade-graduacao .btn-add,
    .box-desconto-fidelidade-graduacao .btn-del {
        margin-bottom: 11px;
        margin-left: 10px;
    }

    .box-graduacao-maxima {
        padding-top: 15px;
    }
</style>
