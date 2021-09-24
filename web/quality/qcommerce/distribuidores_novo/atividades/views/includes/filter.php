<div class="group-filters">
    <ul class="filters-modal list-unstyled" style="padding-left: 0;">
        <li class="active">
            <label class="control control--checkbox filtro-assunto" data-assunto="all">
                <strong><?php echo escape(_trans('agenda.todas')) ?></strong>
                <input type="radio" name="opt-filter" checked/>
                <div class="control__indicator"></div>
            </label>
        </li><?php

            $subjects = DistribuidorEventoPeer::getSubjects();

        foreach ($subjects as $subject) {
            ?><li>
                    <label class="control control--checkbox filtro-assunto" data-assunto="<?php echo $subject['category']; ?>">
                        <i class="<?php echo $subject['icon']; ?>"></i>
                    <?php echo escape(_trans('agenda.' . $subject['text'])) ?>
                        <input type="radio" name="opt-filter"/>
                        <div class="control__indicator"></div>
                    </label>
                </li><?php
        }

        ?></ul>
</div>
<hr>
<div class="group-filters">
    <ul class="filters list-unstyled">
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'todas' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=todas"><?php echo escape(_trans('agenda.todas')) ?></a>
        </li>
        <li<?php echo !isset($_GET['filter']) ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/"><?php echo escape(_trans('agenda.andamento')) ?></a>
        </li>
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'finalizadas' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=finalizadas"><?php echo escape(_trans('agenda.finalizadas')) ?></a>
        </li>
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'atrasadas' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=atrasadas"><?php echo escape(_trans('agenda.atrasadas')) ?></a>
        </li>
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'hoje' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=hoje"><?php echo escape(_trans('agenda.hoje')) ?></a>
        </li>
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'esta-semana' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=esta-semana"><?php echo escape(_trans('agenda.esta_semana')) ?></a>
        </li>
        <li<?php echo isset($_GET['filter']) && $_GET['filter'] == 'proxima-semana' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?filter=proxima-semana"><?php echo escape(_trans('agenda.proxima_semana')) ?></a>
        </li>
    </ul>
</div>
