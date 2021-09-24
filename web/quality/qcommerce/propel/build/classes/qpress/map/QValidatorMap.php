<?php

class QValidatorMap extends ValidatorMap
{

	public function getMessage()
	{
		return 'O campo ' . $this->getColumn()->getPhpName() . ' ' . utf8_decode(parent::getMessage()); // usar função para traduzir as mensagens
	}
  
}
