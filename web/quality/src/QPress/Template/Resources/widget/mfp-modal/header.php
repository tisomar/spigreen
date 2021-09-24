<style>
    .mfp-modal-header {
        padding: 10px;
        background: #F5F5F5;
        margin-bottom: 15px;
    }
    .mfp-modal-header h2 {
        float: left;
    }
    .mfp-modal-header .mfp-close {
        float: right;
        color: #757575;
        font-size: 60px;
        margin-top: 13px;
        margin-right: 25px;;
    }
</style>
<script>
    $(function() {
        $('.mfp-modal-header').on('click', '.mfp-close', function() {
            parent.$('.mfp-close').trigger('click');
        })
    })
</script>
<header class="mfp-modal-header">
    <div class="container">
        <h2><?php echo $title ?></h2>
        <button class="mfp-close" type="button">×</button>
        <div class="clearfix"></div>
    </div>
</header>