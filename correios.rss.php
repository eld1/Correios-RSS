<?php
date_default_timezone_set('America/Sao_Paulo');

    // Error handler 
    function erro_correios($type, $msg, $file, $line, $context) {
        $title = "Correios.rss Erro";
        //include("../header.php");
        print "<h1>Correios RSS</h1>";
        print "<h2>Erro ao obter informações da Encomenda</h2>";
        print "<p>$msg</p>";
        //include("../footer.php");
        die();
    }
    set_error_handler('erro_correios');
    include("correios.php");
    $obj = (new ObjetoRastreado($_GET["id"]));
    header("Content-type: text/xml;charset=iso-8859-1;application/rss+xml");
    print "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
    <title><?=$obj->identificador ?> - Rastreamento de Objeto</title>
    <description><?=$obj->identificador ?> - <?=$obj->tipo_objeto ?> - Historico do Objeto</description>
    <link>http://www.correios.com.br</link>
    <pubDate><?=date("r")?></pubDate>
    <generator>http://www.augustofontes.com/</generator>
    <image>
        <title>Acompanhamento de Objeto</title>
        <url>http://websro.correios.com.br/correios/Img/correios.gif</url>
        <link>http://www.correios.com.br</link>
    </image>

<?php
// Se não tiver evento nenhum
if ( $obj->num_eventos == 0 ) {
?>
    <item>
        <title>O sistema não possui dados sobre o objeto.</title>
        <link>http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_ITEMCODE=&amp;P_LINGUA=001&amp;P_TESTE=&amp;P_TIPO=001&amp;P_COD_UNI=<?=$obj->identificador?>&amp;Z_ACTION=</link>
        <guid>http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_ITEMCODE=&amp;P_LINGUA=001&amp;P_TESTE=&amp;P_TIPO=001&amp;P_COD_UNI=<?=$obj->identificador?>&amp;Z_ACTION=</guid>
        <description>O nosso sistema não possui dados sobre o objeto informado. Se o objeto foi postado recentemente, é natural que seus rastros não tenham ingressado no sistema, nesse caso, por favor, tente novamente mais tarde. Adicionalmente, verifique se o código digitado está correto: <?=$obj->identificador?>.</description>
    </item>
<?php
} else {
    // Para cada evento...
    for ($i=0; $i < $obj->num_eventos; $i++ ) {
        $evento = $obj->eventos[$i];
?>
    <item>
        <title><?=$evento->situacao?> (<?=$evento->local?>)</title>
        <pubDate><?=$evento->data_rfc?></pubDate>
        <link>http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_ITEMCODE=&amp;P_LINGUA=001&amp;P_TESTE=&amp;P_TIPO=001&amp;P_COD_UNI=<?=$obj->identificador?>&amp;Z_ACTION=</link>
        <guid>http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_ITEMCODE=&amp;P_LINGUA=001&amp;P_TESTE=&amp;P_TIPO=001&amp;P_COD_UNI=<?=$obj->identificador?>&amp;Z_ACTION=#<?=($obj->num_eventos-$i)?></guid>
        <description>A encomenda recebeu o status de <?=$evento->situacao?> em <?=$evento->local?>.
<?=$evento->detalhe_local?></description>
        <content:encoded><![CDATA[  A encomenda recebeu o status de <i><?=$evento->situacao?></i> (<?=$evento->data?>) em <b><?=$evento->local?></b>. <br />
            <?=$evento->detalhe_local?>
        ]]></content:encoded>
    </item>
<?php
    } // for
} // if
?>

</channel>
</rss>