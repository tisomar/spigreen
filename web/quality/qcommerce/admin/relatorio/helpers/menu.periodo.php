<div class="col-xs-12">
    <div>
        <div id="nav-widget" class="btn-group btn-group-justified">
            <div class="btn-group">
                <a class="btn <?php echo 'custom' == $range ? 'active' : '' ?>" data-toggle="collapse" href="#custom-range">
                    Personalizado <i class="icon-chevron-down"></i>
                </a>
            </div>
            <?php foreach ($filters as $_range => $_label) : ?>
                <div class="btn-group">
                    <a class="btn <?php echo $_range == $range ? 'active' : '' ?>" href="?range=<?php echo $_range ?>">
                        <?php echo $_label ?>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<div class="col-xs-12">
    <div id="custom-range" class="collapse <?php echo 'custom' == $range ? 'in' : '' ?>">
        <div style="margin-bottom: 20px;">
            <form action="">
                <input name="range" value="custom" type="hidden" />
                <div class="input-daterange input-group pull-left" id="datepicker3">
                    <input type="text" class="input-small form-control" name="startDate" required="" placeholder="data inicial" value="<?php echo $container->getRequest()->query->get('startDate') ?>">
                    <span class="input-group-addon">até</span>
                    <input type="text" class="input-small form-control" name="endDate" required="" placeholder="data final" value="<?php echo $container->getRequest()->query->get('endDate') ?>">
                </div>
                <input type="submit" value="Enviar" class="btn pull-left"/>
            </form>
            <div class="clearfix"></div>
        </div>
        <hr/>
    </div>
</div>
