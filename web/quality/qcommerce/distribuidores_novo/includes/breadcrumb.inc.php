<ol class="breadcrumb">
    
    <li class='<?php echo empty($breadcrumb) ? 'active' : '' ?>'>
        <?php if (!empty($breadcrumb)) :  ?>
            <a href="<?php echo $root_path ?>/distribuidores">Início</a>
        <?php else : ?>
            Início
        <?php endif ?>
    </li>
    
<?php if (!empty($breadcrumb)) :  ?>
    <?php end($breadcrumb)  ?>
    <?php $ultimo = key($breadcrumb)  ?>
    <?php foreach ($breadcrumb as $area => $path) :  ?>
        <li class="<?php echo ($area == $ultimo) ? 'active' : '' ?>">
            <?php if ($path) :  ?>
                <a href="<?php echo escape($path) ?>"><?php echo escape($area) ?></a>
            <?php else : ?>
                <?php echo escape($area) ?>
            <?php endif ?>
        </li>
    <?php endforeach ?>
<?php endif ?>
        
</ol>
