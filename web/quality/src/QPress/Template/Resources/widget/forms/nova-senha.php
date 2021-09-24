<form role="form" method="post" class="form-disabled-on-load">

    <?php if(isset($strIncludesKey) && 'minha-conta-nova-senha' == $strIncludesKey): ?>
        <div class="form-group">
            <label for="actual-password">* Senha atual:</label>
            <input type="password" class="form-control" id="actual-password" name="pass[a]" required>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="new-password">* Nova senha:</label>
        <input type="password" class="form-control validity-password" id="new-password" name="pass[n]" autocomplete="off" pattern="<?php echo REG_PASSWORD; ?>" required>
    </div>
    <div class="form-group">
        <label for="new-password-confirm">* Confirmar senha:</label>
        <input type="password" class="form-control validity-password" id="new-password-confirm" name="pass[c]" autocomplete="off" pattern="<?php echo REG_PASSWORD; ?>" required>
    </div>
    <div class="form-group">
        <button type="submit" class="btn <?php echo isset($btnClass) ? $btnClass : 'btn-default'; ?> btn-block">Salvar senha</button>
    </div>
</form>