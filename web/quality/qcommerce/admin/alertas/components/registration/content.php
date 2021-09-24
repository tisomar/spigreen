<?php
/* @var $object DocumentoAlerta */
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Título :", "data[TITULO]", array(
    "value" => $object->getTitulo(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Data de Envio", "data[DATA_ENVIO]", array(
    "value" => $request->query->get('data[DATA_ENVIO]', null, true),
    "title" => "Data de envio",
    "class" => "_datepicker-today-init mask-date",
)));


$form->addElement(new Element\Select("Tipo da Mensagem:", "data[TIPO_MENSAGEM]", $object->getAllTipoDesc(), array(
    "value" => $object->getTipoMensagem(),
)));

$form->addElement(new Element\Textbox("Ordem:", "data[ORDEM]", array(
    "value" => $object->getOrdem(),
    "required" => true,
)));

$form->addElement(new Element\Select("Destinatário:", "data[TIPO_DEST]", $object->getAllDestinatariosDesc(), array(
    "value" => $object->getTipoDest(),
    "id"    => 'dest-opt'
)));

//$form->addElement(new Element\HTML('
//        <div id="nivel-options" style="display: none;">
//
//'));
//
//$form->addElement(new Element\Select("Selecione o(s) Nível(is)", "NIVEIS[]", array(), array(
//    "value" => '',
//    "required" => false,
//)));
//$form->addElement(new Element\HTML('
//        </div>
//
//'));

$form->addElement(new Element\HTML('
        <div id="cliente-options" style="display: none;">
    
'));

$form->addElement(new Element\Select("Selecione o(s) Cliente(s)", "CLIENTES_ID[]", ClientePeer::getAllClientesList(), array(
    "value" => '',
    "required" => false,
    "class" => 'select2',
    'multiple' => 'multiple'
)));
$form->addElement(new Element\HTML('
        </div>
    
'));

$form->addElement(new Element\Textarea("Corpo da Mensagem", "data[CORPO]", array(
    "value" => $object->getCorpo(),
    'class' => 'mceEditor',
)));

//$form->addElement(new Element\File("Arquivo:", "ARQUIVO", array(
//    "required" => false
//)));


$form->addElement(new Element\Select("Somente Leitura?", "data[SOMENTE_LEITURA]", array('1' => 'Sim', '0' => 'Não'), array(
    "value" => $object->getSomenteLeitura(),
    "required" => true
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->addElement(new Element\HTML('
<script>
    $( document ).ready(function() {
        
        function pontosShowHide(val) {
            if(val == "nivel_mensal"){
                $("#nivel-options").show();
                $("#cliente-options").hide();
            } else if(val == "cliente") {
                $("#nivel-options").hide();
                $("#cliente-options").show();
            } else {
                $("#nivel-options").hide();
                $("#cliente-options").hide();
            }
        }
        
        $("body").on("change", "#dest-opt", function(e) {
            pontosShowHide($(this).val());
        });
        
        window.onload = function() {
            var destinario = $("#dest-opt");
            
            pontosShowHide(destinario.val());
            
            if (destinario.val() == "cliente") {
                var clientesNomes = "' . implode(',', $clientesNomes) . '";
                clientesNomes = clientesNomes.split(",");
                
                var clientesIds = "' . implode(',', $clientedIds) . '";
                clientesIds = clientesIds.split(",");
                
                for (var cont = 0; cont < clientesNomes.length; cont++) {
                    addClienteSelectContainer(clientesNomes[cont], clientesIds[cont]);
                }
                
                clientesIds.forEach(adicionaClienteSelect);
            }
        }
        
        function adicionaClienteSelect(clienteId) {
            var select = document.getElementById("registrer-element-6");
            
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value == clienteId) {
                    select.options[i].selected = true;
                }
            }
        }
        
        function removerClienteSelect(clienteId) {
            var select = document.getElementById("registrer-element-6");
            
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value == clienteId) {
                    select.options[i].selected = false;
                }
            }
        }
        
        function addClienteSelectContainer(clienteNome, clienteId) {
            var searchChoice = document.createElement("li");
                searchChoice.className = "select2-search-choice";
                
            var divSearchChoice = document.createElement("div");
            divSearchChoice.append(clienteNome);
            
            var aSearchChoice = document.createElement("a");
            aSearchChoice.href = "#";
            aSearchChoice.onclick = function() {
                aSearchChoice.parentNode.remove();
                removerClienteSelect(clienteId);
            };
            aSearchChoice.className = "select2-search-choice-close";
            aSearchChoice.tabIndex = "-1";
            
            searchChoice.append(divSearchChoice);
            searchChoice.append(aSearchChoice);
            
            $(".select2-choices").prepend(searchChoice);
        }
        
    });        
</script>
    
'));

$form->render();
