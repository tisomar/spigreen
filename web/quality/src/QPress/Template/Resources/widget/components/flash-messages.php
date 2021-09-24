<?php if (FlashMsg::hasMessages()): ?>
    <div class="container">
        <?php FlashMsg::display(); // Exibindo mensagens ?>
    </div>
<?php endif; ?>