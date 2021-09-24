<?php

if ($request->getMethod() == 'POST') :
    $con = null;

    try {
        $erros = array();

        $data = $request->request->get('data', []);
        $id = $data['ID'] ?? null;

        if ($data['NIVEL'] != 0) {
            $query = PlanoQuery::create()
                ->filterByNivel($data['NIVEL'])
                ->filterById($data['ID'], Criteria::NOT_EQUAL)
                ->findOne();

            if ($query) :
                $erros[] = 'Já existe um plano com este nível.';
            endif;
        }

        if (!$erros) :
            $con = Propel::getConnection();
            $con->beginTransaction();

            $object = PlanoQuery::create()
                ->filterById($id)
                ->findOneOrCreate();

            $object->setByArray($data);
            $object->save();

            if (!empty($data['PARTICIPA_FIDELIDADE'])) :
                $descontoFidelidade = $data['DESC_FIDELIDADE'] ?? [];
                $descontoFidGraduacao = $data['DESC_FID_GRADUACAO'] ?? [];

                PlanoDescontoFidelidadeQuery::create()
                    ->filterById($data['DESC_FIDELIDADE_EXCLUIR'] ?? [])
                    ->filterByPlanoId($object->getId())
                    ->delete();

                PlanoDescontoFidelidadeGraduacaoQuery::create()
                    ->filterById($data['DESC_FID_GRADUACAO_EXCLUIR'] ?? [])
                    ->filterByPlanoId($object->getId())
                    ->delete();

                foreach ($descontoFidelidade as $desc) :
                    $descFid = PlanoDescontoFidelidadeQuery::create()
                        ->filterById($desc['ID'] ?? null)
                        ->filterByPlanoId($object->getId())
                        ->findOneOrCreate();

                    $descFid->setByArray($desc);
                    $descFid->save();
                endforeach;

                foreach ($descontoFidGraduacao as $descGrad) :
                    $descFidGraduacao = PlanoDescontoFidelidadeGraduacaoQuery::create()
                        ->filterById($descGrad['ID'] ?? null)
                        ->filterByPlanoId($object->getId())
                        ->findOneOrCreate();

                    $descFidGraduacao->setByArray($descGrad);
                    $descFidGraduacao->save();
                endforeach;
            else :
                PlanoDescontoFidelidadeQuery::create()
                    ->filterByPlanoId($object->getId())
                    ->delete();

                PlanoDescontoFidelidadeGraduacaoQuery::create()
                    ->filterByPlanoId($object->getId())
                    ->delete();
            endif;

            if (!empty($data['PARTICIPA_EXPANSAO'])) :
                $percentuaisExpansao = $data['PERC_EXPANSAO'] ?? [];

                foreach ($percentuaisExpansao as $gen => $perc) :
                    $percObj = PlanoPercentualBonusQuery::create()
                        ->filterByPlanoId($object->getId())
                        ->filterByTipo(PlanoPercentualBonus::TIPO_EXPANSAO)
                        ->filterByGeracao($gen)
                        ->findOneOrCreate();

                    $percObj
                        ->setPercentual($perc)
                        ->save();
                endforeach;

                PlanoPercentualBonusQuery::create()
                    ->filterByPlanoId($object->getId())
                    ->filterByTipo(PlanoPercentualBonus::TIPO_EXPANSAO)
                    ->filterByGeracao($gen ?? 0, Criteria::GREATER_THAN)
                    ->delete();
            else :
                PlanoPercentualBonusQuery::create()
                    ->filterByPlanoId($object->getId())
                    ->filterByTipo(PlanoPercentualBonus::TIPO_EXPANSAO)
                    ->delete();
            endif;

            if (empty($data['PARTICIPA_PLANO_CARREIRA'])) :
                $object->setGraduacaoMaxima(null);
                $object->save();
            endif;

            $con->commit();

            $session->getFlashBag()->add(
                'success',
                $object->isNew() ?
                    'Plano criado com sucesso!' :
                    'Plano alterado com sucesso!'
            );
        else :
            foreach ($erros as $erro) :
                $session->getFlashBag()->add('error', $erro);
            endforeach;
        endif;

        redirect('/admin/planos/list');
    } catch (Exception $ex) {
        if (!empty($con) && $con->inTransaction()) :
            $con->rollBack();
        endif;

        $session->getFlashBag()->add('error', $ex->getMessage());
        redirect('/admin/planos/list');
    }
endif;

$object = PlanoQuery::create()
    ->filterById($request->query->get('id'))
    ->findOneOrCreate();

$percsExpansao = $object->getPlanoPercentualBonuss(
    PlanoPercentualBonusQuery::create()
        ->filterByTipo(PlanoPercentualBonus::TIPO_EXPANSAO)
);

$percsFidelidade = $object->getPlanoDescontoFidelidades();

$percsFidGraduacao = $object->getPlanoDescontoFidelidadeGraduacaos();

$planoCarreiras = PlanoCarreiraQuery::create()
    ->select([
        'ID',
        'GRADUACAO'
    ])
    ->orderByNivel()
    ->find();
