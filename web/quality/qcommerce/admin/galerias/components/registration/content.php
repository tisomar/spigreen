<?php

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI'),
    'view' => new PFBC\View\Vertical(),
));

$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$form->addElement(new Element\Textarea("Descrição:", "data[DESCRICAO]", array(
    "value" => $object->getDescricao(),
)));

//$form->addElement(new Element\File("Imagens (zip)", "IMAGENS", array(
//    "shortDesc" => 'Você pode inserir um arquivo .zip com as imagens que deseja cadastrar para esta galeria.',
//)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

if ($object->isNew() == false) {
    ob_start();
    ?>
    <div>
        <p>
            <i class="icon-info-sign"></i> Você pode enviar todas as fotos deste produto de uma vez só.
            <!--        A ordem das páginas serão atribuídas automaticamente pela ordem de envio da lista, mas não se preocupe, você poderá alterá-la depois.-->
        </p>
    </div>

    <form id="fileupload" action="" method="POST" enctype="multipart/form-data">

        <div class="row fileupload-buttonbar">

            <div class="col-xs-12">
                <div class="btn-toolbar">
                <span class="btn btn-primary fileinput-button">
                    <i class="icon-plus-sign"></i>
                    <span>Adicionar arquivos...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                    <button type="submit" class="btn btn-green start">
                        <i class="icon-upload"></i>
                        <span>Iniciar upload</span>
                    </button>
                    <button type="reset" class="btn btn-midnightblue cancel">
                        <i class="icon-ban-circle"></i>
                        <span>Cancelar upload</span>
                    </button>
                    <button type="button" class="btn btn-brown delete">
                        <i class="icon-trash"></i>
                        <span>Deletar</span>
                    </button>
                    <input type="checkbox" class="toggle">

                    <span class="fileupload-loading"></span>
                </div>
            </div>

            <div class="col-xs-12 fileupload-progress fade">
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <div class="progress-extended">&nbsp;</div>
            </div>

        </div>
        <div class="table-responsive">
            <table role="presentation" class="table table-condensed">
                <tbody class="files"></tbody>
            </table>
        </div>
    </form>


    <script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">

            <td>
                <span class="preview"></span>
            </td>

            <td>
                <p>Arquivo: {%=file.name%}</p>

                <p>Tamanho: <b><span class="size">{%=o.formatFileSize(file.size)%}</span></b></p>

                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
                {% if (!o.files.error) { %}
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                    </div>
                {% } %}

            </td>

            <td class="text-right">
                {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                    <button class="btn btn-green start">
                        <i class="icon-upload"></i>
                        <span>Enviar</span>
                    </button>
                {% } %}
                {% if (!i) { %}
                    <button class="btn btn-midnightblue cancel">
                        <i class="icon-ban-circle"></i>
                        <span>Cancelar</span>
                    </button>
                {% } %}
            </td>

        </tr>
    {% } %}

    </script>

    <script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">

            <td data-title="Foto">
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" class="open-in-modal">
                            <img src="{%=file.thumbnailUrl%}">
                        </a>
                    {% } %}
                </span>
            </td>

            <td data-title="Opções"">
                <p> Legenda:
                    <a href="#" class="editable" data-placement="top" data-pk="{%=file.id%}"
                        data-type="text" data-url="{%=file.urlLegenda%}">{%=file.legenda%}</a>
                </p>

                <p> Ordem:
                    #<a href="#" class="editable" data-placement="top" data-pk="{%=file.id%}"
                        data-type="number" data-url="{%=file.urlOrdem%}">{%=file.ordem%}</a>
                </p>

                <p>Tamanho: <b><span class="size">{%=o.formatFileSize(file.size)%}</span></b></p>

                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>

            <td data-title="Ações" class="text-right">
                <a class="btn btn-default" href="{%=file.url%}" download="{%=file.url%}" title="{%=file.name%}">
                        <i class="icon-download"></i> <span>Download</span>
                    </a>

                {% if (file.deleteUrl) { %}
                    <button class="btn btn-brown delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}">
                        <i class="icon-trash "></i>
                        <span>Deletar</span>
                    </button>
                    <input type="checkbox" name="delete" value="1" class="toggle">
                {% } else { %}
                    <button class="btn btn-midnightblue cancel">
                        <i class="icon-ban-circle"></i>
                        <span>Cancelar</span>
                    </button>
                {% } %}
            </td>

        </tr>
    {% } %}

    </script>
    <script>

        var urlUpload = '<?php echo get_url_admin() ?>/galerias/upload?id=<?php echo $request->query->get('id') ?>';

        $(function () {

            'use strict';

            // Inicializa o plugin de upload
            $('#fileupload').fileupload({
                url: urlUpload,
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
                maxFileSize: 500000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                previewMaxWidth: 100,
                previewMaxHeight: 100,
                sequentialUploads: true
            }).bind('fileuploadalways', function (e, data) {
                setTimeout(function () {
                    initEditableInline();
                    initModal();
                }, 200);
            });

            // Carrega os arquivos existentes
            $('#fileupload').addClass('fileupload-processing');

            $.ajax({
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');

                setTimeout(function () {
                    initEditableInline();
                    initModal();
                }, 200);

            }).done(function (result) {
                $(this).fileupload('option', 'done')
                    .call(this, null, {result: result});
            });

        });

    </script>

    <?php
    $galeriaRender = ob_get_clean();
} else {
    $galeriaRender = '<h4>Cadastre a galeria para inserir as imagens.</h4>';
}

// --------------------------------------------------------------------------------------------------------------------
?>

<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Galeria</h4>
            </div>
            <div class="panel-body">
                <?php $form->render(); ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-8">
        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <h4>Imagens</h4>
            </div>
            <div class="panel-body">
                <?php echo $galeriaRender ?>
            </div>
        </div>
    </div>
</div>

