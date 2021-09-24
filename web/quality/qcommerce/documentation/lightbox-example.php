<?php
$strIncludesKey = 'lightbox-example';
include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox-page">
   <header class="container container-primary align-center text-group-small header-lightbox">
        <h1 class="clear-margin title-default title-detail">Lightbox Example</h1>
    </header>
    <main role="main" class="main-lightbox">
        <div class="wrapper-grids-small">
            <div class="col-12 push-6 pull-6">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, veniam illum iste natus sapiente provident quos sint nihil. 
                    Similique, voluptatibus, eveniet illo pariatur perferendis quis voluptatum numquam odio porro minus.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, veniam illum iste natus sapiente provident quos sint nihil. 
                    Similique, voluptatibus, eveniet illo pariatur perferendis quis voluptatum numquam odio porro minus.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, veniam illum iste natus sapiente provident quos sint nihil. 
                    Similique, voluptatibus, eveniet illo pariatur perferendis quis voluptatum numquam odio porro minus.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, veniam illum iste natus sapiente provident quos sint nihil. 
                    Similique, voluptatibus, eveniet illo pariatur perferendis quis voluptatum numquam odio porro minus.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, veniam illum iste natus sapiente provident quos sint nihil. 
                    Similique, voluptatibus, eveniet illo pariatur perferendis quis voluptatum numquam odio porro minus.
                </p>
            </div>
        </div>
        
    </main>
        
        <?php
        // Exibindo mensagens de erro (se for sucesso só será exibido na página em será redirecionada
        if (FlashMsg::hasErros()) {
            FlashMsg::display('erro');
        }
        ?>
    <?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>
    
</body>
</html>
