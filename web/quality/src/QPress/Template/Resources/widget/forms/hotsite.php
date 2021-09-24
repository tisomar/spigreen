<form role="form" method="post" class="form-contact form-disabled-on-load" enctype="multipart/form-data">
	<h2>Informe os dados do seu hotsite</h2>
    <div class="form-group">
        <label for="nome">* Nome:</label>
        <input type="text" class="form-control" id="nome" required name="hotsite[NOME]" value="<?php echo $objHotsite->getNome() ?>">
    </div>
    <div class="form-group">
        <label for="email">* Email:</label>
        <input type="email" id="email" class="form-control validity-email" name="hotsite[EMAIL]" value="<?php echo $objHotsite->getEmail() ?>" required>
    </div>
	<div class="form-group">
		<label for="url">* Slug:</label>
        <?php
            $urlExemplo = str_replace('https','http',get_url_site().'/franqueado/');
        ?>
		<input type="text" class="form-control slug" id="url" required name="hotsite[URL]"  value="<?php echo $objHotsite->getUrl() ?>">
        <?php
            $slug = $objHotsite->getSlug();
            if(empty($slug)):?>
                <p class="text-muted"> Sua url será: <span class="text-muted slug-result"> <?php echo $urlExemplo.'(slug)'  ?> </span> </p>
        <?php else: ?>
                <p>Sua url: <a href="<?php echo $urlExemplo.$slug ?>"> <?php echo $urlExemplo.$slug ?> </a> </p>
        <?php endif; ?>
         
	</div>
	<div class="form-group">
		<label for="descricao">* Descrição:</label>
		<textarea id="descricao" class="form-control" required name="hotsite[DESCRICAO]"><?php echo $objHotsite->getDescricao() ?></textarea>
	</div>
    <?php
    if ($objHotsite->isImagemExists()): ?>
        <div class="form-group">
            <label for="registrer-element-1">
            Foto atual:</label>
                <?php echo $objHotsite->getThumb('width=220&height=180&cropratio=1.222:1', array(
			    'class' => 'thumbnail',
			    'style' => 'background: #555',
		    )) ?>
        </div>
        <input type="file" id="foto" name="hotsite[FOTO]" value="Alterar Foto"> <br>
	<?php else: ?>
        <div class="form-group">
            <label for="foto">Foto:</label>
            <input type="file" class="form-control" id="foto" name="hotsite[FOTO]" value="<?php  ?>">
        </div>
    <?php endif; ?>
	<div class="form-group">
		<button type="submit" class="btn btn-theme btn-block">Enviar</button>
	</div>
</form>
