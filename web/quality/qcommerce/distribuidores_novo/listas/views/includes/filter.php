<form role="form" class="form-horizontal form-inline form-groups-bordered form-search-cli">
    <div class="col-lg-11" style="margin-left: -15px;">
        <div class="input-group">
            <span class="input-group-addon"><?php echo escape(_trans('agenda.de')); ?></span>
            <input type="text" name="filter[DataIni]" class="form-control datepicker" data-format="dd/mm/yyyy" placeholder="<?php echo escape(_trans('agenda.periodo_inicial_compra')); ?>" value="<?php echo (isset($arrFilter['DataIni']) ? $arrFilter['DataIni'] : ''); ?>">
        </div>

        <div class="input-group">
            <span class="input-group-addon"><?php echo escape(_trans('agenda.ate')); ?></span>
            <input type="text" name="filter[DataFim]" class="form-control datepicker" data-format="dd/mm/yyyy" placeholder="<?php echo escape(_trans('agenda.periodo_final_compra')); ?>" value="<?php echo (isset($arrFilter['DataFim']) ? $arrFilter['DataFim'] : ''); ?>">
        </div>

        <input type="text" name="filter[Nome]" value="<?php echo escape($arrFilter['Nome']) ?>" class="form-control input-xs" id="form-filter-nome" placeholder="<?php echo escape(_trans('agenda.nome')); ?>">

        <div class="div-select" style="width: 150px">
            <select class="selectboxit" name="filter[Comprou]">
                <option value="-1"><?php echo escape(_trans('agenda.indiferente')); ?></option>
                <optgroup label="<?php echo escape(_trans('agenda.compras')); ?>">
                    <option value="1"<?php echo $arrFilter['Comprou'] == 1 ? ' selected' : ''; ?>><?php echo escape(_trans('agenda.sim_comprou')); ?></option>
                    <option value="2"<?php echo $arrFilter['Comprou'] == 2 ? ' selected' : ''; ?>><?php echo escape(_trans('agenda.nao_comprou')); ?></option>
                </optgroup>
                <optgroup label="<?php echo escape(_trans('agenda.agendamentos')); ?>">
                    <option value="3"<?php echo $arrFilter['Comprou'] == 3 ? ' selected' : ''; ?>><?php echo escape(_trans('agenda.com_agendamento')); ?></option>
                    <option value="4"<?php echo $arrFilter['Comprou'] == 4 ? ' selected' : ''; ?>><?php echo escape(_trans('agenda.sem_agendamento')); ?></option>
                </optgroup>
            </select>
        </div>
        
    </div>
    <div class="col-lg-1" style=" margin-left: -15px;">
        <button type="submit" class="btn btn-default btn-icon icon-right">
            <?php echo escape(_trans('agenda.filtrar')); ?>
            <i class="fa fa-filter"></i>
        </button>
    </div>
</form>