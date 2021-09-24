<?php


interface BonificacaoParticipacaoInterface
{
    public function distribuirBonus(ParticipacaoResultado $participacao);
}