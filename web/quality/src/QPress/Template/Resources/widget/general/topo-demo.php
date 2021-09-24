<?php
if ($container->getRequest()->getMethod() == 'POST') {
    if ($container->getRequest()->request->has('accept-terms-demo')) {
        $container->getSession()->set('accepted-demo-popup', true);
    }
}
if (!$container->getSession()->get('accepted-demo-popup', false)) {
    ?>
    <form id="demo-alert-popup" class="mfp-hide white-popup-block" method="post">
        <h3>Leia com atenção!</h3>
        <br>
        <p>Este site é uma versão demonstrativa utilizada exclusivamente para apresentações dos recursos da plataforma.</p>
        <p>As informações contidas aqui são meramente ilustativas e não possuem valor real.</p>
        <p>Compras realizadas neste site são apenas simulações não tendo vínculo com pagamento real.</p>
        <br>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="accept-terms-demo" required> Li e estou de acordo com os termos apresentados.
            </label>
        </div>
        <button type="submit" class="btn btn-success">Continuar...</button>
    </form>
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $.magnificPopup.open({
                    items: {
                        src: '#demo-alert-popup',
                        type: 'inline'
                    }
                });
            }, 600);
        });
    </script>
    <?php
}
?>