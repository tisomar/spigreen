<?php
if ($request->getMethod() == 'POST') {
    $importFrom = TransportadoraFaixaPesoQuery::create()
        ->filterByTransportadoraRegiaoId($request->request->get('importFrom'))
        ->filterByTransportadoraRegiaoId($request->request->get('reference'), Criteria::NOT_EQUAL)
        ->find();

    foreach ($importFrom as $from) { /* @var TransportadoraFaixaPeso $from */
        $importTo = $from->copy();
        $importTo->setTransportadoraRegiaoId($request->request->get('importTo'));
        $importTo->save();
    }

    $session->getFlashBag()->add('success', 'Registros importados com sucesso!');
    ?>
    <script>
        window.parent.location = window.parent.location;
    </script>
    <?php
    die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
<head>
    <?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/head.php'; ?>
</head>
<body>
<div id="page-container" class="modal-container">
    <div id="page-content">
        <div id='wrap'>

            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <?php include QCOMMERCE_DIR . '/admin/_2015/layout/flash-messages.php'; ?>
                                <h4>
                                    Selecione a região que deseja importar as faixas:
                                </h4>
                                <?php
                                $collTransportadoraRegiao = TransportadoraRegiaoQuery::create()
                                    ->join('TransportadoraRegiao.TransportadoraFaixaPeso')
                                    ->orderByNome()
                                    ->find();
                                ?>
                                <?php echo get_form_select_object($collTransportadoraRegiao, null, 'getId', 'getNome', array('class' => 'form-control', 'id' => 'faixa-peso'), array('' => 'Selecione')); ?>

                                <div class="container">
                                    <div id="faixas">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once QCOMMERCE_DIR . '/admin/_2015/layout/javascripts.php'; ?>
<script>
    $(function() {
        $('body').on('change', 'select#faixa-peso', function() {
            $('#faixas').load('<?php echo get_url_admin() ?>/faixa-peso/ajax/load-faixas-peso?importFrom=' + $(this).val() + '&importTo=<?php echo $request->query->get("reference") ?>');
        });
    });
</script>
</body>
</html>
