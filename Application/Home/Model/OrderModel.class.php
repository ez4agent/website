<?php


function getExchangeRate($from_Currency,$to_Currency)
{
    $amount = urlencode($amount);
    $from_Currency = urlencode($from_Currency);
    $to_Currency = urlencode($to_Currency);
    $url = "download.finance.yahoo.com/d/quotes.html?s=".$from_Currency.$to_Currency."=X&f=sl1d1t1ba&e=.html";
    $ch = curl_init();
    $timeout = 0;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $rawdata = curl_exec($ch);
    curl_close($ch);
    $data = explode(',', $rawdata);
    return $data[1];
}
​//调用方法
echo getExchangeRate("CNY","USD");