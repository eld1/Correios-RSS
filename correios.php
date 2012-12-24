<?php

/*
 * Projeto:     CorreiosRSS: Gerador de RSS para um objeto nos correios
 * File:        rss_parse.inc includes code for parsing
 *                RSS, and returning an RSS object
 * Author:      Augusto de Carvalho Fontes <augusto@tumate.com>
 * Version:        0.2
 * License:        GPL
 *
 * A �ltima vers�o deste c�digo pode ser obtida em:
 * http://www.augustofontes.com
 *
 */

class Eventos {
    var $data;
    var $data_rfc;
    var $local;
    var $detalhe_local;
    var $situacao;
}

class ObjetoRastreado {
    var $identificador;
    var $tipo_objeto;
    var $situacao_atual;
    var $num_eventos;
    var $eventos = array();
    var $URL = "http://websro.correios.com.br/sro_bin/txect01\$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=";
    //http://websro.correios.com.br/sro_bin/txect01\$.QueryList?P_ITEMCODE=&P_LINGUA=001&P_TESTE=&P_TIPO=001&Z_ACTION=&P_COD_UNI=";
/*
    Significados dos dois primeiros caracteres do identificador do objeto.
    Obtidos em: http://www.correios.com.br/servicos/rastreamento/internacional/siglas.cfm
*/
    var $dsc_prefixos = array(
        "AL" => "AGENTES DE LEITURA",
        "AR" => "AVISO DE RECEBIMENTO",
        "AS" => "ENCOMENDA PAC � A��O SOCIAL",
        "CA" => "OBJETO INTERNACIONAL",
        "CB" => "OBJETO INTERNACIONAL",
        "CC" => "COLIS POSTAUX",
        "CD" => "OBJETO INTERNACIONAL",
        "CE" => "CHEQUE CORREIOS",
        "CF" => "OBJETO INTERNACIONAL",
        "CG" => "OBJETO INTERNACIONAL",
        "CH" => "OBJETO INTERNACIONAL",
        "CI" => "OBJETO INTERNACIONAL",
        "CJ" => "REGISTRADO INTERNACIONAL",
        "CK" => "OBJETO INTERNACIONAL",
        "CL" => "OBJETO INTERNACIONAL",
        "CM" => "OBJETO INTERNACIONAL",
        "CN" => "OBJETO INTERNACIONAL",
        "CO" => "OBJETO INTERNACIONAL",
        "CP" => "COLIS POSTAUX",
        "CQ" => "OBJETO INTERNACIONAL",
        "CR" => "CARTA REGISTRADA SEM VALOR DECLARADO",
        "CS" => "OBJETO INTERNACIONAL",
        "CT" => "OBJETO INTERNACIONAL",
        "CU" => "OBJETO INTERNACIONAL",
        "CV" => "REGISTRADO INTERNACIONAL",
        "CW" => "OBJETO INTERNACIONAL",
        "CX" => "OBJETO INTERNACIONAL",
        "CY" => "OBJETO INTERNACIONAL",
        "CZ" => "OBJETO INTERNACIONAL",
        "DA" => "REM EXPRES COM AR DIGITAL",
        "DB" => "REM EXPRES COM AR DIGITAL BRADESCO",
        "DC" => "REM EXPRESSA CRLV/CRV/CNH e NOTIFICA��O",
        "DD" => "DEVOLU��O DE DOCUMENTOS",
        "DE" => "REMESSA EXPRESSA TAL�O E CART�O C/ AR",
        "DF" => "E-SEDEX (L�GICO)",
        "DI" => "REM EXPRES COM AR DIGITAL ITAU",
        "DL" => "SEDEX (CONTRATO - PRE�O/PRAZO DE ENTREGA DEFINIDO PELO REMETENTE)",
        "DP" => "REM EXPRES COM AR DIGITAL PRF",
        "DS" => "REM EXPRES COM AR DIGITAL SANTANDER",
        "DT" => "REMESSA ECON.SEG.TRANSITO C/AR DIGITAL",
        "DX" => "ENCOMENDA SEDEX 10 (L�GICO)",
        "EA" => "OBJETO INTERNACIONAL",
        "EB" => "SEDEX INTERNACIONAL - EMS",
        "EC" => "ENCOMENDA PAC",
        "ED" => "OBJETO INTERNACIONAL",
        "EE" => "SEDEX INTERNACIONAL - EMS",
        "EF" => "OBJETO INTERNACIONAL",
        "EG" => "OBJETO INTERNACIONAL",
        "EH" => "ENCOMENDA NORMAL COM AR DIGITAL",
        "EI" => "OBJETO INTERNACIONAL",
        "EJ" => "ENCOMENDA INTERNACIONAL",
        "EK" => "OBJETO INTERNACIONAL",
        "EL" => "OBJETO INTERNACIONAL",
        "EM" => "OBJETO INTERNACIONAL",
        "EN" => "ENCOMENDA NORMAL NACIONAL",
        "EO" => "OBJETO INTERNACIONAL",
        "EP" => "OBJETO INTERNACIONAL",
        "EQ" => "ENCOMENDA SERVI�O N�O EXPRESSA ECT",
        "ER" => "REGISTRADO",
        "ES" => "e-SEDEX",
        "ET" => "OBJETO INTERNACIONAL",
        "EU" => "OBJETO INTERNACIONAL",
        "EV" => "OBJETO INTERNACIONAL",
        "EW" => "OBJETO INTERNACIONAL",
        "EX" => "OBJETO INTERNACIONAL",
        "EY" => "OBJETO INTERNACIONAL",
        "EZ" => "OBJETO INTERNACIONAL",
        "FA" => "FAC REGISTRATO (L�GICO)",
        "FE" => "ENCOMENDA FNDE",
        "FF" => "REGISTRADO DETRAN",
        "FH" => "REGISTRADO FAC COM AR DIGITAL",
        "FM" => "REGISTRADO - FAC MONITORADO",
        "FR" => "REGISTRADO FAC",
        "IA" => "INTEGRADA AVULSA",
        "IC" => "COLIS POSTAUX - RECEBIDOS DO EXTERIOR",
        "ID" => "INTEGRADA DEVOLUCAO DE DOCUMENTO",
        "IE" => "INTEGRADA ESPECIAL",
        "IF" => "CPF",
        "II" => "INTEGRADA INTERNO",
        "IK" => "INTEGRADA COM COLETA SIMULTANEA",
        "IM" => "INTEGRADA MEDICAMENTOS",
        "IN" => "OBJ DE CORRESP E EMS REC EXTERIOR",
        "IP" => "INTEGRADA PROGRAMADA",
        "IR" => "IMPRESSO REGISTRADO",
        "IS" => "INTEGRADA STANDARD",
        "IT" => "INTEGRADO TERMOL�BIL",
        "IU" => "INTEGRADA URGENTE",
        "JA" => "REMESSA ECONOMICA C/AR DIGITAL",
        "JB" => "REMESSA ECONOMICA C/AR DIGITAL",
        "JC" => "REMESSA ECONOMICA C/AR DIGITAL",
        "JD" => "REMESSA ECONOMICA C/AR DIGITAL",
        "JE" => "REMESSA ECON�MICA C/AR DIGITAL",
        "JG" => "REGISTRATO AG�NCIA (F�SICO)",
        "JJ" => "REGISTRADO JUSTI�A",
        "JL" => "OBJETO REGISTRADO (L�GICO)",
        "JM" => "MALA DIRETA POSTAL ESPECIAL (L�GICO)",
        "LA" => "LOG�STICA REVERSA SIMULT�NEA - ENCOMENDA SEDEX (AG�NCIA)",
        "LB" => "LOG�STICA REVERSA SIMULT�NEA - ENCOMENDA E-SEDEX (AG�NCIA)",
        "LC" => "CARTA EXPRESSA",
        "LE" => "LOG�STICA REVERSA ECONOMICA",
        "LP" => "LOG�STICA REVERSA SIMULT�NEA - ENCOMENDA PAC (AG�NCIA)",
        "LS" => "LOGISTICA REVERSA SEDEX",
        "LV" => "LOGISTICA REVERSA EXPRESSA",
        "LX" => "CARTA EXPRESSA",
        "LY" => "CARTA EXPRESSA",
        "MA" => "SERVI�OS ADICIONAIS",
        "MB" => "TELEGRAMA DE BALC�O",
        "MC" => "MALOTE CORPORATIVO",
        "MD" => "SEDEX MUNDI - DOCUMENTO INTERNO",
        "ME" => "TELEGRAMA",
        "MF" => "TELEGRAMA FONADO",
        "MI" => "TELEGRAMA INTERFACE",
        "MK" => "TELEGRAMA ESCRIT�RIO",
        "MM" => "TELEGRAMA GRANDES CLIENTES",
        "MP" => "TELEGRAMA PR�-PAGO",
        "MS" => "ENCOMENDA SAUDE",
        "MT" => "TELEGRAMA VIA TELEMAIL",
        "MW" => "TELEGRAMA CORPORATIVO",
        "MY" => "TELEGRAMA INTERNACIONAL ENTRANTE",
        "MZ" => "TELEGRAMA VIA CORREIOS ON LINE",
        "NE" => "TELE SENA RESGATADA",
        "PA" => "PASSAPORTE",
        "PB" => "ENCOMENDA PAC - N�O URGENTE",
        "PC" => "ENCOMENDA PAC A COBRAR",
        "PD" => "ENCOMENDA PAC - N�O URGENTE",
        "PF" => "PASSAPORTE",
        "PG" => "ENCOMENDA PAC (ETIQUETA F�SICA)",
        "PH" => "ENCOMENDA PAC (ETIQUETA L�GICA)",
        "PR" => "REEMBOLSO POSTAL - CLIENTE AVULSO",
        "RA" => "REGISTRADO PRIORIT�RIO",
        "RB" => "CARTA REGISTRADA",
        "RC" => "CARTA REGISTRADA COM VALOR DECLARADO",
        "RD" => "REMESSA ECONOMICA DETRAN",
        "RE" => "REGISTRADO ECON�MICO",
        "RF" => "OBJETO DA RECEITA FEDERAL",
        "RG" => "REGISTRADO DO SISTEMA SARA",
        "RH" => "REGISTRADO COM AR DIGITAL",
        "RI" => "REGISTRADO PRIORIT�RIO",
        "RJ" => "REGISTRADO AG�NCIA",
        "RK" => "REGISTRADO AG�NCIA",
        "RL" => "REGISTRADO L�GICO",
        "RM" => "REGISTRADO AG�NCIA",
        "RN" => "REGISTRADO AG�NCIA",
        "RO" => "REGISTRADO AG�NCIA",
        "RP" => "REEMBOLSO POSTAL - CLIENTE INSCRITO",
        "RQ" => "REGISTRADO AG�NCIA",
        "RR" => "CARTA REGISTRADA SEM VALOR DECLARADO",
        "RS" => "REGISTRADO L�GICO",
        "RT" => "REM ECON TALAO/CARTAO SEM AR DIGITA",
        "RU" => "REGISTRADO SERVI�O ECT",
        "RV" => "REM ECON CRLV/CRV/CNH COM AR DIGITAL",
        "RW" => "OBJETO INTERNACIONAL",
        "RX" => "OBJETO INTERNACIONAL",
        "RY" => "REM ECON TALAO/CARTAO COM AR DIGITAL",
        "RZ" => "REGISTRADO URGENTE",
        "SA" => "SEDEX ANOREG",
        "SC" => "SEDEX A COBRAR",
        "SD" => "SEDEX CRV-RS",
        "SE" => "ENCOMENDA SEDEX",
        "SF" => "SEDEX AG�NCIA",
        "SG" => "SEDEX DO SISTEMA SARA",
        "SH" => "SEDEX COM AR DIGITAL",
        "SI" => "SEDEX VIP",
        "SJ" => "SEDEX HOJE",
        "SK" => "SEDEX AG�NCIA",
        "SL" => "SEDEX L�GICO",
        "SM" => "SEDEX MESMO DIA",
        "SN" => "SEDEX COM VALOR DECLARADO",
        "SO" => "SEDEX AG�NCIA",
        "SP" => "SEDEX PR�-FRANQUEADO",
        "SQ" => "SEDEX",
        "SR" => "SEDEX",
        "SS" => "SEDEX F�SICO",
        "SZ" => "SEDEX",
        "ST" => "SEDEX NIT-RS",
        "SU" => "ENCOMENDA SERVI�O EXPRESSA ECT",
        "SV" => "REM EXPRES CRLV/CRV/CNH COM AR DIGITAL",
        "SW" => "e-SEDEX",
        "SX" => "SEDEX 10",
        "SY" => "REM EXPRES TALAO/CARTAO COM AR DIGITAL",
        "SZ" => "SEDEX AG�NCIA",
        "TE" => "TESTE (OBJETO PARA TREINAMENTO)",
        "TS" => "TESTE (OBJETO PARA TREINAMENTO)",
        "VA" => "ENCOMENDAS COM VALOR DECLARADO",
        "VC" => "ENCOMENDA NACIONAL - N�O-URGENTE",
        "VD" => "ENCOMENDAS COM VALOR DECLARADO",
        "VE" => "ENCOMENDAS",
        "VF" => "ENCOMENDAS COM VALOR DECLARADO",
        "VV" => "OBJETO INTERNACIONAL",
        "XM" => "SEDEX MUNDI",
        "XR" => "ENCOMENDA SUR POSTAL EXPRESSO",
        "XX" => "ENCOMENDA SUR POSTAL 24 HORAS"
    );

    /*
     * Verifica se um identificador de um objeto � v�lido.
     * Um exemplo de identificador �: EN508213800BR
     */
    function identificadorValido($id) {
        // Verifica o tamanho do identificador
        if (strlen($id) !== 13) {
            return false;
        }
        // Verifica se o identificador possui um prefixo v�lido - DISABLED
        //if (@$this->dsc_prefixos[substr($id, 0, 2)] === NULL) {
        //    return false;
        //}
        return true;
    }

    /**
     * Interpreta a p�gina HTML e extrai as informa��es sobre os eventos
     */
    function parseHtml($conteudo) {

        $this->num_eventos = 0;
        // Quebra conte�do em linhas
        $linhas_conteudo = explode("\n", $conteudo);
        for ($i=0; $i<count($linhas_conteudo); $i++) {
            $linha = $linhas_conteudo[$i];
            // Se for uma linha que cont�m um evento do rastreamento...
            $pos = strpos($linha, "<tr><td rowspan=");
            if (is_int($pos) && ($pos >= 0)) {
                // Monta a data
                $data = substr($linha, 18, 16);
                $ano  = substr($data, 6, 4);
                $mes = substr($data, 3, 2);
                $dia   = substr($data, 0, 2);
                $hora  = substr($data, 11, 2);
                $min   = substr($data, 14, 2);
                $str_time = "$ano-$mes-$dia $hora:$min:00";
                $data_rfc = date("r", strtotime($str_time));   //$hora, $min, 0, $mes, $dia, $ano
                $pos = strpos($linha, "</td><td>", 43);
                $local = substr($linha, 43, $pos-43);
                // Utiliza uma express�o regular para retirar m�ltiplos espa�os em branco na string
                $local = preg_replace('/\s+/', ' ', $local);
                $local = trim($local);
                $pos = strpos($linha, "<FONT COLOR=\"") + 21;
                $pos1 = strpos($linha, "</font>");
                $situacao = substr($linha, $pos, $pos1-$pos);
                $situacao = preg_replace('/\s+/', ' ', $situacao);
                $detalhe_local = "";
                // Se a pr�xima linha incluir detalhes do local
                $linha = $linhas_conteudo[$i+1];
                if (!(strpos($linha, "<tr><td colspan=") === false)) {
                    $detalhe_local = substr($linha, 18, strlen($linha)-28);
                }
                // Cadastra uma nova evento
                $this->eventos[$this->num_eventos] = new Eventos();
                $this->eventos[$this->num_eventos]->data = $data;
                $this->eventos[$this->num_eventos]->data_rfc = $data_rfc;
                $this->eventos[$this->num_eventos]->local = $local;
                $this->eventos[$this->num_eventos]->detalhe_local = $detalhe_local;
                $this->eventos[$this->num_eventos]->situacao = $situacao;
                $this->num_eventos++;
            }
        }
    }

    /*
     * Constructor.
     */
    function ObjetoRastreado($id_objeto) {
        // Deixa o identificador em MAI�SCULAS
        $id_objeto = strtoupper($id_objeto);
        
        // Se o identificador foi inv�lido, morra.
        if (!$this->identificadorValido($id_objeto)) {
            trigger_error("Identificador de objeto inv�lido.", E_USER_ERROR);
            return;
        }

        $this->identificador = $id_objeto;
        if (substr($id_objeto, -2) === "BR")
        {
              $this->tipo_objeto = (!empty($this->dsc_prefixos[substr($id_objeto, 0, 2)])) ? $this->dsc_prefixos[substr($id_objeto, 0, 2)] : "Tipo de objeto desconhecido";
        }
        else
        {
              $this->tipo_objeto = "Internacional";
        }
        $this->num_eventos = 0;

        // Monta a URL para o pedido GET
        $url_get = $this->URL . urlencode($this->identificador);

        // Apesar de originalmente, o formul�rio da p�gina dos correios faz o pedido por HTTP POST,
        // o GET tamb�m � aceito e isso facilita nosso trabalho ;)
        $handle = fopen($url_get, "r");
        $conteudo = "";
        while (!feof($handle)) {
          $conteudo .= fread($handle, 8192);
        }
        // Interpreta o HTML para extrair as informa��es.
        $this->parseHtml($conteudo);
        fclose ($handle);
    }
}

?>