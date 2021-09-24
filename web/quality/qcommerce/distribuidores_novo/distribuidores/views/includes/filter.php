<?php
 isset($arrFilter['comprou']) ? $arrFilter['comprou'] : $arrFilter['comprou'] = null;
 isset($arrFilter['nivel']) ? $arrFilter['nivel'] : $arrFilter['nivel'] = null;
 isset($arrFilter['nivel_atingido']) ? $arrFilter['nivel_atingido'] : $arrFilter['nivel_atingido'] = null;
 isset($arrFilter['produto']) ? $arrFilter['produto'] : $arrFilter['produto'] = null;
?>
<form role="form" class="form-horizontal form-inline form-groups-bordered form-search-dist">
    <div class="col-lg-11" style="margin-left: -15px;">

        <input type="text" data-format="dd/mm/yyyy" name="filter[data_inicial]" value="<?php echo isset($arrFilter['data_inicial']) ? escape($arrFilter['data_inicial']) : '' ?>" class="form-control datepicker" id="form-filter-element-1" placeholder="<?php echo escape(_trans('agenda.data_inicial_cadastro')); ?>">
        <input type="text" data-format="dd/mm/yyyy" name="filter[data_final]" value="<?php echo isset($arrFilter['data_final']) ? escape($arrFilter['data_final']) : ''?>" class="form-control datepicker" id="form-filter-element-1" placeholder="<?php echo escape(_trans('agenda.data_final_cadastro')); ?>">

        <?php

        /**
        echo get_form_select(array('' => 'Estado') + $estados, $arrFilter['estado'], array(
            'name' => 'filter[estado]',
            'class' => 'form-control',
            'id' => 'estado',
            'placeholder' => 'Estado'
        ));

        echo get_form_select(array('' => 'Cidade') + $cidades, $arrFilter['cidade'], array(
            'name' => 'filter[cidade]',
            'class' => 'form-control',
            'id' => 'cidade',
            'placeholder' => 'Cidade'
        ));
        */

        echo ' ' . get_form_select(array('' => 'Indiferente de Compras', 'n' => 'Sem compras', 's' => 'Com compras'), $arrFilter['comprou'], array(
            'name' => 'filter[comprou]',
            'class' => 'form-control',
            'id' => 'comprou',
            'placeholder' => 'Comprou'
        ));

        $nivel = array('' => 'Indiferente de Geração');
        for ($i = 1; $i <= 8; $i++) {
            $nivel[$i] = $i . ' ' . 'Geração' . ' ';
        }


        echo ' ' . get_form_select($nivel, $arrFilter['nivel'], array(
            'name' => 'filter[nivel]',
            'class' => 'form-control',
            'id' => 'comprou',
            'placeholder' => 'Geração'
        ));

//        echo ' ' . get_form_select(array('' => 'Indiferente do Nível') + (array) $niveis, $arrFilter['nivel_atingido'], array(
//           'name' => 'filter[nivel_atingido]',
//           'class' => 'form-control',
//           'id' => 'produto',
//           'placeholder' => 'Nivel'
//       ));

        echo '<div style="margin-top: 3px;">' . get_form_select(array('' => 'Qualquer Produto') + $produtos, $arrFilter['produto'], array(
           'name' => 'filter[produto]',
           'class' => 'form-control',
           'id' => 'produto',
           'placeholder' => 'Apenas quem comprou'
        )) . '</div>';

        ?>

    </div>

    <div class="col-lg-1" style=" margin-left: -15px;">
        <button type="submit" class="btn btn-default btn-icon icon-left btn-filtrar">
            Filtrar <i class="fa fa-filter"></i>
        </button>
    </div>
</form>
