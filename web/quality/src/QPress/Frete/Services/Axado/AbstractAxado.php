<?php

namespace QPress\Frete\Services\Axado;

use QPress\Frete\FreteInterface;

abstract class AbstractAxado implements FreteInterface
{
    const FORMATO_CAIXA_PACOTE = 1;
    const FORMATO_ROLO_PRISMA = 2;
    const FORMATO_ENVELOPE = 3;

}
