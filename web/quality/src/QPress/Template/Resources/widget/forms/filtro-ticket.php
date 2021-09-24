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

    <div class="form-group col-md-4">
        <label for="inputEmail4">Topico</label>
        <select id="inputAssunto" class="form-control" name="topico">
            <option value="">Selecione...</option>
            <option value="Comercial">COMERCIAL</option>
            <option value="Financeiro">FINANCEIRO</option>
            <option value="Jurídico">JURÍDICO</option>
            <option value="Logistica">LOGISTICA</option>
            <option value="Sugestões">SUGESTÕES</option>
            <option value="Ti">TI</option>
        </select>                                  
    </div>
</div>