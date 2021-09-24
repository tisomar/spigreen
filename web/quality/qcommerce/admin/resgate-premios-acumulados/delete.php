<?php 


if ($request->query->has('id')) {

   $object = ResgatePremiosAcumuladosPeer::retrieveByPK($request->query->get('id'));
   
   if($object->getSelecionado() == 'DINHEIRO') {
      $con = Propel::getConnection();
      $gerenciador = new GerenciadorPontosAcumulados($con = Propel::getConnection(), $logger);
      $resposta = $gerenciador->doExtornaResgateAndExtrato($object, $object->getPremio());
   }

   $object->delete();

   $session->getFlashBag()->add('success', 'Registro alterado com sucesso!');
   redirectTo('/admin/resgate-premios-acumulados/list');
}