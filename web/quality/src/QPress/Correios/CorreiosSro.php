<?php
namespace QPress\Correios;

/**
 * Classe base para para a validação e geração de dígito verificador
 * de SRO dos Correios.
 *
 * @author Ivan Wilhelm <ivan.whm@me.com>
 * @version 1.0
 * @abstract
 */
abstract class CorreiosSro
{

    /**
     * Contém as siglas e suas respectivas descrições, adotadas
     * no sistema de identificador de objetos.
     *
     * @see http://www.correios.com.br/servicos/rastreamento/internacional/siglas.cfm
     *
     * @javascript Javascript para obter a lista atualizada. Executar no console dentro o site citado acima:
     * var htmlTeste = ""; $('.conteudo-tabela tr[class!="primeira-linha"]').each(function(a, tr) {  htmlTeste += "'" + $(tr).find('td:first-child').html() + "' => '" + $(tr).find('td:last-child').html() + "',<br>";  }); document.write(htmlTeste);
     *
     * @date Lista atualizada em 10/07/2015 10:58
     * @var array
     */
    protected static $siglasComDescricao = array(
        'AL'	=>	'Agentes de leitura',
        'AR'	=>	'Avisos de recebimento',
        'AS'	=>	'PAC - Ação Social',
        'CA'	=>	'Encomenda Internacional - Colis',
        'CB'	=>	'Encomenda Internacional - Colis',
        'CC'	=>	'Encomenda Internacional - Colis',
        'CD'	=>	'Encomenda Internacional - Colis',
        'CE'	=>	'Encomenda Internacional - Colis',
        'CF'	=>	'Encomenda Internacional - Colis',
        'CG'	=>	'Encomenda Internacional - Colis',
        'CH'	=>	'Encomenda Internacional - Colis',
        'CI'	=>	'Encomenda Internacional - Colis',
        'CJ'	=>	'Encomenda Internacional - Colis',
        'CK'	=>	'Encomenda Internacional - Colis',
        'CL'	=>	'Encomenda Internacional - Colis',
        'CM'	=>	'Encomenda Internacional - Colis',
        'CN'	=>	'Encomenda Internacional - Colis',
        'CO'	=>	'Encomenda Internacional - Colis',
        'CP'	=>	'Encomenda Internacional - Colis',
        'CQ'	=>	'Encomenda Internacional - Colis',
        'CR'	=>	'Carta registrada sem Valor Declarado',
        'CS'	=>	'Encomenda Internacional - Colis',
        'CT'	=>	'Encomenda Internacional - Colis',
        'CU'	=>	'Encomenda internacional - Colis',
        'CV'	=>	'Encomenda Internacional - Colis',
        'CW'	=>	'Encomenda Internacional - Colis',
        'CX'	=>	'Encomenda internacional - Colis ou Selo Lacre para Caixetas',
        'CY'	=>	'Encomenda Internacional - Colis',
        'CZ'	=>	'Encomenda Internacional - Colis',
        'DA'	=>	'SEDEX ou Remessa Expressa com AR Digital',
        'DB'	=>	'SEDEX ou Remessa Expressa com AR Digital (Bradesco)',
        'DC'	=>	'Remessa Expressa CRLV/CRV/CNH e Notificações',
        'DD'	=>	'Devolução de documentos',
        'DE'	=>	'Remessa Expressa Talão/Cartão com AR',
        'DF'	=>	'e-SEDEX',
        'DG'	=>	'SEDEX',
        'DI'	=>	'SEDEX ou Remessa Expressa com AR Digital (Itaú)',
        'DJ'	=>	'SEDEX',
        'DK'	=>	'PAC Extra Grande',
        'DL'	=>	'SEDEX',
        'DM'	=>	'e-SEDEX',
        'DN'	=>	'SEDEX',
        'DO'	=>	'SEDEX ou Remessa Expressa com AR Digital (Itaú)',
        'DP'	=>	'SEDEX Pagamento na Entrega',
        'DQ'	=>	'SEDEX ou Remessa Expressa com AR Digital (Santander)',
        'DR'	=>	'Remessa Expressa com AR Digital (Santander)',
        'DS'	=>	'SEDEX ou Remessa Expressa com AR Digital (Santander)',
        'DT'	=>	'Remessa econômica com AR Digital (DETRAN)',
        'DU'	=>	'e-SEDEX',
        'DV'	=>	'SEDEX c/ AR digital',
        'DX'	=>	'SEDEX 10',
        'EA'	=>	'Encomenda Internacional - EMS',
        'EB'	=>	'Encomenda Internacional - EMS',
        'EC'	=>	'PAC',
        'ED'	=>	'Packet Express',
        'EE'	=>	'Encomenda Internacional - EMS',
        'EF'	=>	'Encomenda Internacional - EMS',
        'EG'	=>	'Encomenda Internacional - EMS',
        'EH'	=>	'Encomenda Internacional - EMS ou Encomenda com AR Digital',
        'EI'	=>	'Encomenda Internacional - EMS',
        'EJ'	=>	'Encomenda Internacional - EMS',
        'EK'	=>	'Encomenda Internacional - EMS',
        'EL'	=>	'Encomenda Internacional - EMS',
        'EM - final "BR"'	=>	'Encomenda Internacional - SEDEX Mundi',
        'EM - final diferente de "BR"'	=>	'Encomenda Internacional - EMS Importação',
        'EN'	=>	'Encomenda Internacional - EMS',
        'EO'	=>	'Encomenda Internacional - EMS',
        'EP'	=>	'Encomenda Internacional - EMS',
        'EQ'	=>	'Encomenda de serviço não expressa (ECT)',
        'ER'	=>	'Objeto registrado',
        'ES'	=>	'e-SEDEX ou EMS',
        'ET'	=>	'Encomenda Internacional - EMS',
        'EU'	=>	'Encomenda Internacional - EMS',
        'EV'	=>	'Encomenda Internacional - EMS',
        'EW'	=>	'Encomenda Internacional - EMS',
        'EX'	=>	'Encomenda Internacional - EMS',
        'EY'	=>	'Encomenda Internacional - EMS',
        'EZ'	=>	'Encomenda Internacional - EMS',
        'FA'	=>	'FAC registrado',
        'FE'	=>	'Encomenda FNDE',
        'FF'	=>	'Objeto registrado (DETRAN)',
        'FH'	=>	'FAC registrado com AR Digital',
        'FM'	=>	'FAC monitorado',
        'FR'	=>	'FAC registrado',
        'IA'	=>	'Logística Integrada (agendado / avulso)',
        'IC'	=>	'Logística Integrada (a cobrar)',
        'ID'	=>	'Logística Integrada (devolução de documento)',
        'IE'	=>	'Logística Integrada (Especial)',
        'IF'	=>	'CPF',
        'II'	=>	'Logística Integrada (ECT)',
        'IK'	=>	'Logística Integrada com Coleta Simultânea',
        'IM'	=>	'Logística Integrada (Medicamentos)',
        'IN'	=>	'Correspondência e EMS recebido do Exterior',
        'IP'	=>	'Logística Integrada (Programada)',
        'IR'	=>	'Impresso Registrado',
        'IS'	=>	'Logística integrada standard (medicamentos)',
        'IT'	=>	'Remessa Expressa Medicamentos / Logística Integrada Termolábil',
        'IU'	=>	'Logística Integrada (urgente)',
        'IX'	=>	'EDEI Expresso',
        'JA'	=>	'Remessa econômica com AR Digital',
        'JB'	=>	'Remessa econômica com AR Digital',
        'JC'	=>	'Remessa econômica com AR Digital',
        'JD'	=>	'Remessa econômica Talão/Cartão',
        'JE'	=>	'Remessa econômica com AR Digital',
        'JF'	=>	'Remessa econômica com AR Digital',
        'JG'	=>	'Objeto registrado urgente/prioritário',
        'JH'	=>	'Objeto registrado urgente / prioritário',
        'JI'	=>	'Remessa econômica Talão/Cartão',
        'JJ'	=>	'Objeto registrado (Justiça)',
        'JK'	=>	'Remessa econômica Talão/Cartão',
        'JL'	=>	'Objeto registrado',
        'JM'	=>	'Mala Direta Postal Especial',
        'JN'	=>	'Objeto registrado econômico',
        'JO'	=>	'Objeto registrado urgente',
        'JP'	=>	'Receita Federal',
        'JQ'	=>	'Remessa econômica com AR Digital',
        'JR'	=>	'Objeto registrado urgente / prioritário',
        'JS'	=>	'Objeto registrado',
        'JT'	=>	'Objeto Registrado Urgente',
        'JV'	=>	'Remessa Econômica (c/ AR DIGITAL)',
        'LA'	=>	'SEDEX com Logística Reversa Simultânea em Agência',
        'LB'	=>	'e-SEDEX com Logística Reversa Simultânea em Agência',
        'LC'	=>	'Objeto Internacional (Prime)',
        'LE'	=>	'Logística Reversa Econômica',
        'LF'	=>	'Objeto Internacional (Prime)',
        'LI'	=>	'Objeto Internacional (Prime)',
        'LJ'	=>	'Objeto Internacional (Prime)',
        'LK'	=>	'Objeto Internacional (Prime)',
        'LM'	=>	'Objeto Internacional (Prime)',
        'LN'	=>	'Objeto Internacional (Prime)',
        'LP'	=>	'PAC com Logística Reversa Simultânea em Agência',
        'LS'	=>	'SEDEX Logística Reversa',
        'LV'	=>	'Logística Reversa Expressa',
        'LX'	=>	'Packet Standard / Econômica',
        'LZ'	=>	'Objeto Internacional (Prime)',
        'MA'	=>	'Serviços adicionais do Telegrama',
        'MB'	=>	'Telegrama (balcão)',
        'MC'	=>	'Telegrama (Fonado)',
        'MD'	=>	'SEDEX Mundi (Documento interno)',
        'ME'	=>	'Telegrama',
        'MF'	=>	'Telegrama (Fonado)',
        'MK'	=>	'Telegrama (corporativo)',
        'ML'	=>	'Fecha Malas (Rabicho)',
        'MM'	=>	'Telegrama (Grandes clientes)',
        'MP'	=>	'Telegrama (Pré-pago)',
        'MR'	=>	'AR digital',
        'MS'	=>	'Encomenda Saúde',
        'MT'	=>	'Telegrama (Telemail)',
        'MY'	=>	'Telegrama internacional (entrante)',
        'MZ'	=>	'Telegrama (Correios Online)',
        'NE'	=>	'Tele Sena resgatada',
        'NX'	=>	'EDEI Econômico (não urgente)',
        'PA'	=>	'Passaporte',
        'PB'	=>	'PAC',
        'PC'	=>	'PAC a Cobrar',
        'PD'	=>	'PAC',
        'PE'	=>	'PAC',
        'PF'	=>	'Passaporte',
        'PG'	=>	'PAC',
        'PH'	=>	'PAC',
        'PI'	=>	'PAC',
        'PJ'	=>	'PAC',
        'PK'	=>	'PAC Extra Grande',
        'PL'	=>	'PAC',
        'PR'	=>	'Reembolso Postal',
        'QQ'	=>	'Objeto de teste (SIGEP Web)',
        'RA'	=>	'Objeto registrado / prioritário',
        'RB'	=>	'Carta registrada',
        'RC'	=>	'Carta registrada com Valor Declarado',
        'RD'	=>	'Remessa econômica ou objeto registrado (DETRAN)',
        'RE'	=>	'Objeto registrado econômico',
        'RF'	=>	'Receita Federal',
        'RG'	=>	'Objeto registrado',
        'RH'	=>	'Objeto registrado com AR Digital',
        'RI'	=>	'Objeto registrado internacional prioritário',
        'RJ'	=>	'Objeto registrado',
        'RK'	=>	'Objeto registrado',
        'RL'	=>	'Objeto registrado',
        'RM'	=>	'Objeto registrado urgente',
        'RN'	=>	'Objeto registrado (SIGEPWEB ou Agência)',
        'RO'	=>	'Objeto registrado',
        'RP'	=>	'Reembolso Postal',
        'RQ'	=>	'Objeto registrado',
        'RR'	=>	'Objeto registrado',
        'RS'	=>	'Objeto registrado',
        'RT'	=>	'Remessa econômica Talão/Cartão',
        'RU'	=>	'Objeto registrado (ECT)',
        'RV'	=>	'Remessa econômica CRLV/CRV/CNH e Notificações com AR Digital',
        'RW'	=>	'Objeto internacional',
        'RX'	=>	'Objeto internacional',
        'RY'	=>	'Remessa econômica Talão/Cartão com AR Digital',
        'RZ'	=>	'Objeto registrado',
        'SA'	=>	'SEDEX',
        'SB'	=>	'SEDEX 10',
        'SC'	=>	'SEDEX a cobrar',
        'SD'	=>	'SEDEX ou Remessa Expressa (DETRAN)',
        'SE'	=>	'SEDEX',
        'SF'	=>	'SEDEX',
        'SG'	=>	'SEDEX',
        'SH'	=>	'SEDEX com AR Digital / SEDEX ou AR Digital',
        'SI'	=>	'SEDEX',
        'SJ'	=>	'SEDEX Hoje',
        'SK'	=>	'SEDEX',
        'SL'	=>	'SEDEX',
        'SM'	=>	'SEDEX 12',
        'SN'	=>	'SEDEX',
        'SO'	=>	'SEDEX',
        'SP'	=>	'SEDEX Pré-franqueado',
        'SQ'	=>	'SEDEX',
        'SR'	=>	'SEDEX',
        'SS'	=>	'SEDEX',
        'ST'	=>	'Remessa Expressa Talão/Cartão',
        'SU'	=>	'Encomenda de serviço expressa (ECT)',
        'SV'	=>	'Remessa Expressa CRLV/CRV/CNH e Notificações com AR Digital',
        'SW'	=>	'e-SEDEX',
        'SX'	=>	'SEDEX 10',
        'SY'	=>	'Remessa Expressa Talão/Cartão com AR Digital',
        'SZ'	=>	'SEDEX',
        'TC'	=>	'Objeto para treinamento',
        'TE'	=>	'Objeto para treinamento',
        'TS'	=>	'Objeto para treinamento',
        'VA'	=>	'Encomendas com valor declarado',
        'VC'	=>	'Encomendas',
        'VD'	=>	'Encomendas com valor declarado',
        'VE'	=>	'Encomendas',
        'VF'	=>	'Encomendas com valor declarado',
        'VV'	=>	'Objeto internacional',
        'XA'	=>	'Aviso de chegada (internacional)',
        'XM'	=>	'SEDEX Mundi',
        'XR'	=>	'Encomenda SUR Postal Expresso',
        'XX'	=>	'Encomenda SUR Postal 24 horas',
    );

    /**
     * Realiza a validação completa do SRO.
     * @param string $sro
     * @return boolean
     */
    public static function validaSro($sro)
    {

        //Valida a estrutura do SRO
        if (preg_match('/[A-Z]{2}[0-9]{9}[A-Z]{2}/', $sro))
        {
            //Valida a sigla do SRO
            if (isset(self::$siglasComDescricao[substr($sro, 0, 2)]))
            {
                //Valida o dígito verificador
                if (self::calculaDigitoVerificador(substr($sro, 2, 8)) == substr($sro, 10, 1))
                {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
     * Calcula o dígito verificador do SRO.
     * Retorna -1 se o cálculo for incorreto.
     * @param string $sro SRO
     * @return int
     */
    public static function calculaDigitoVerificador($sro)
    {
        //Inicializa o retorno
        $retorno = -1;
        //Valida a quantidade de caracteres
        if (strlen(trim($sro)) === 8)
        {
            //Valida
            $soma = 0;
            for ($i = 0; $i <= 8; $i++)
            {
                $soma = $soma + (int) substr($sro, $i, 1) * (int) substr('86423597', $i, 1);
            }
            //Calcula o dígito validador
            switch ($soma % 11)
            {
                case 0:
                    $retorno = 5;
                    break;
                case 1:
                    $retorno = 0;
                    break;
                default:
                    $retorno = 11 - ($soma % 11);
                    break;
            }
        }
        //Retorna
        return $retorno;
    }

}
  