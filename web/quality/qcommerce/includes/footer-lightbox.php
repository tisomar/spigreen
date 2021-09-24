<script type="text/javascript" src="<?php echo asset('/js/min/footer.js'); ?>"></script>

<?php // Verifica se o arquivo .js existe. Caso sim, adiciona-o à página ?>
<?php if (is_file($request->server->get('DOCUMENT_ROOT') . $request->getBasePath() . '/js/min/' . $strIncludesKey . '.js')) : ?>
    <script type="text/javascript" src="<?php echo asset('/js/min/' . $strIncludesKey . '.js') ?>"></script>
<?php endif; ?>

<?php include QCOMMERCE_DIR . '/includes/livereload.php' ?>
