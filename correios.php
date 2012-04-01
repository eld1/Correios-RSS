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
        "AR" => "AVISO DE RECEBIMENTO",
        "CC" => "COLIS POSTAUX",
        "CE" => "CHEQUE CORREIOS",
        "CR" => "CARTA REGISTRADA SEM VALOR DECLARADO",
        "EB" => "SEDEX INTERNACIONAL - EMS",
        "EC" => "ENCOMENDA PAC",
        "EE" => "SEDEX INTERNACIONAL - EMS",
        "EH" => "ENCOMENDA NORMAL COM AR DIGITAL",
        "EN" => "ENCOMENDA NORMAL NACIONAL",
        "ER" => "REGISTRADO",
        "ES" => "e-SEDEX",
        "FE" => "ENCOMENDA FNDE",
        "FF" => "REGISTRADO DETRAN",
        "FH" => "REGISTRADO FAC COM AR DIGITAL",
        "FR" => "REGISTRADO FAC",
        "IC" => "COLIS POSTAUX - RECEBIDOS DO EXTERIOR",
        "IF" => "CPF",
        "IN" => "OBJ DE CORRESP E EMS REC EXTERIOR",
        "IR" => "IMPRESSO REGISTRADO",
        "LE" => "LOG�STICA REVERSA ECONOMICA",
        "LS" => "LOGISTICA REVERSA SEDEX",
        "LV" => "LOGISTICA REVERSA EXPRESSA",
        "MA" => "SERVI�OS ADICIONAIS",
        "MB" => "TELEGRAMA DE BALC�O",
        "MC" => "MALOTE CORPORATIVO",
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
        "PA" => "PASSAPORTE",
        "PB" => "ENCOMENDA PAC",
        "PR" => "REEMBOLSO POSTAL - CLIENTE AVULSO",
        "RA" => "REGISTRADO PRIORIT�RIO",
        "RB" => "CARTA REGISTRADA",
        "RC" => "CARTA REGISTRADA COM VALOR DECLARADO",
        "RE" => "REGISTRADO ECON�MICO",
        "RF" => "OBJETO DA RECEITA FEDERAL",
        "RH" => "REGISTRADO COM AR DIGITAL",
        "RI" => "REGISTRADO PRIORIT�RIO",
        "RL" => "REGISTRADO L�GICO",
        "RP" => "REEMBOLSO POSTAL - CLIENTE INSCRITO",
        "RR" => "CARTA REGISTRADA SEM VALOR DECLARADO",
        "RZ" => "REGISTRADO URGENTE",
        "SA" => "SEDEX ANOREG",
        "SC" => "SEDEX A COBRAR",
        "SD" => "SEDEX CRV-RS",
        "SE" => "ENCOMENDA SEDEX",
        "SH" => "SEDEX COM AR DIGITAL",
        "SI" => "SEDEX VIP",
        "SJ" => "SEDEX HOJE",
        "SL" => "SEDEX L�GICO",
        "SM" => "SEDEX MESMO DIA",
        "SN" => "SEDEX COM VALOR DECLARADO",
        "SP" => "SEDEX PR�-FRANQUEADO",
        "SQ" => "SEDEX",
        "SR" => "SEDEX",
        "SS" => "SEDEX",
        "SZ" => "SEDEX",
        "ST" => "SEDEX NIT-RS",
        "SW" => "e-SEDEX",
        "SX" => "SEDEX 10",
        "TE" => "TESTE (OBJETO PARA TREINAMENTO)",
        "VC" => "ENCOMENDA NACIONAL - N�O-URGENTE",
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