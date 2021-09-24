<?php

if (UsuarioPeer::getUsuarioLogado()->isMaster()) {
    $add = new \PFBC\Element\AddNewButton($config['routes']['registration']);
    $add->render();
}
