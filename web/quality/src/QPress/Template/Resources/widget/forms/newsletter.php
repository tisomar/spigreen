<form role="form" class="form-newsletter form-disabled-on-load" name="form-newsletter" method="post" action="#">
    <div class="row">
        <div class="col-xs-12">
            <h4 class="h3 tit">Assine nossa newsletter:</h4>
            <div class="input-group">
                <input class="form-control validity-email" type="email" placeholder="Seu e-mail" name="newsletter[email]" value="<?php echo isset($objNewsletter) ? $objNewsletter->getEmail() : '' ?>" required>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-theme">Enviar</button>
                </span>
            </div>
        </div>
    </div>
</form>