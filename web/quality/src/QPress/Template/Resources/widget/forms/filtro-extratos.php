<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="register-cpf">Início:</label>
            <input type="text" class="form-control datepicker" id="periodo-inicio" name="inicio" value="<?php echo ($dtInicio) ? $dtInicio->format('d/m/Y') : '' ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="register-phone">Fim:</label>
            <input type="text" class="form-control datepicker" id="periodo-fim" name="fim" value="<?php echo ($dtFim) ? $dtFim->format('d/m/Y') : '' ?>">
        </div>
    </div>
    
</div>