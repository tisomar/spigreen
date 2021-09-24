<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 29/01/2019
 * Time: 15:41
 */
?>

<style>
    .modal-dialog {
        width: 95%;
        height: 90%;
        padding: 0;
        margin: 2%;
    }

    .modal-content {
        height: 100%;
        border-radius: 0;
    }

    .video-container {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: absolute;
        top: 0;
        left: 0;
    }

    .modal-body {
        height: 100%;
        width: 100%;
    }

    #video-ajuda .modal-body {
        background-color: dimgrey;
    }

    .modal-body .close {
        position: absolute;
        z-index: 300000;
        right: 15px;
        top: 8px;
        opacity: 0.8;
    }
</style>

<div class="modal fade" id="video-ajuda" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: whitesmoke">x</span>
                </button>
                <iframe width="100%" height="100%" src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
            </div>
        </div>
    </div>
</div>

