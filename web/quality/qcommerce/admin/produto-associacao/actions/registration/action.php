<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$erros = array();
$_classPeer = $_class::PEER;
if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

if (!$object) {
    redirect_404admin();
}

if ($request->getMethod() == 'POST') {
    $data = ($request->request->get('data'));
    /** @var $object \QualityPress\QCommerce\Component\Association\Propel\AssociacaoProduto  */
    $object->fromArray($data);
    if ($object->validate()) {
        $object->save();
        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');

        $redirectUrl = $config['routes']['list'];
        if ($request->request->get('redirectToOnSuccess') != '') {
            $redirectUrl = $request->request->get('redirectToOnSuccess');
        }

        redirectTo($redirectUrl);
    } else {
        foreach ($object->getValidationFailures() as $objValidationFailed) {
            $erros[] = $objValidationFailed->getMessage();
        }
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}
