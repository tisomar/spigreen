<?php
include(__DIR__ . '/../includes/config.inc.php');
include(__DIR__ . '/../includes/security.inc.php');

$errors = array();

// Obtém a classe do registro
$class = filter_var($request->query->get('class'), FILTER_SANITIZE_STRING);

// Obtém o ID do registro
$id = $request->query->get('id');

// Valida se o id não é nulo e se a classe existe
if (is_null($id) || $class == '' || !class_exists($class)) {
    $session->getFlashBag()->add('error', 'Registro não disponível.');
    redirectTo($request->server->get('HTTP_REFERER'));
    exit;
}

$con = Propel::getConnection();
$con->beginTransaction();

try {
    $classQuery = $class . 'Query';

    $buildQuery = $classQuery::create($con);
    $buildQuery->filterById($id);

    $objects = $buildQuery->find();

    foreach ($objects as $object) {
        if (!$object instanceof $class) {
            $session->getFlashBag()->add('error', 'Registro não disponível.');
            redirectTo($request->server->get('HTTP_REFERER'));
            exit;
        }

        if (method_exists($object, 'validateOnDelete')) {
            $errors = $object->validateOnDelete();
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $session->getFlashBag()->add('error', $error);
            }
        }

        $object->delete($con);
        $con->commit();
    }

    if (count($errors) == 0) {
        $session->getFlashBag()->add('success', 'Registro removido com sucesso!');
    }

    redirectTo($request->server->get('HTTP_REFERER'));
    exit;
} catch (Exception $e) {
    $con->rollBack();

    $session->getFlashBag()->add('error', $e->getMessage());
    redirectTo($request->server->get('HTTP_REFERER'));
    exit;
}
