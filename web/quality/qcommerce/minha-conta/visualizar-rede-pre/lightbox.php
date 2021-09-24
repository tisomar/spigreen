<?php
use QPress\Template\Widget;
require __DIR__ . '/actions/lightbox.actions.php';
include_once __DIR__ . '/../../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" style="overflow: hidden;" data-page="minha-conta-visualizar-rede">

<?php
Widget::render('mfp-modal/header', array(
    'title' => 'Visualização Rede'
));
?>

<div class="row">
    <div class="col-xs-9" style="border: 1px solid black">
        <?php   ?>
        <div style="display: none;">
            <?php echo $htmlRede;  ?>
        </div>
        <div class="panel-body" id="rede-container" style="overflow: hidden; overflow: auto;">
        </div>
    </div>
    <div class="col-xs-3" style="position: relative">
        <div class="col-xs-3" style="position: fixed; padding-left: 0px;padding-right: 30px;">
            <label>Zoom In/Out:</label>
            <input type="range" class="range" min="20" max="200" value="100" oninput="zoomed(this.value)"><br>
            <label>Rolagem Vertical:</label>
            <input type="range" class="range" min="0" max="100" value="0" oninput="positionx(this.value);"><br>
            <label>Rolagem Horizontal:</label>
            <input type="range" class="range" min="0" max="100" value="0" oninput="positiony(this.value);">
        </div>
    </div>

</div>

<?php include_once __DIR__ . '/../../includes/footer-lightbox.php' ?>
<script>
    function zoomed(v) {
        $('#rede-container').css('zoom', v+'%');

    }
    function positionx(v) {

        var panel = $("body");

        var height = $(document).height();
        var point = (height / 100) * v;

        panel.scrollTop(point);



    }
    function positiony(v) {

        var panel = document.getElementById("rede-container");

        var total = panel.scrollWidth - panel.offsetWidth;
        var percentage = total*(v/100);

        panel.scrollLeft = percentage;


    }
</script>
</body>
</html>
