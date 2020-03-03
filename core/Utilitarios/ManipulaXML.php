<?php
namespace Core\Utilitarios;

use \DOMDocument;

class ManipulaXML
{
    private $doc_xml;

    public function __construct($xml)
    {
       // $this->doc_xml = \simplexml_load_file($xml);

       $this->doc_xml = new DOMDocument();
       $this->doc_xml->load($xml);
    }

    public function listaEntesFederativos()
    {
        $bases = $this->doc_xml->getElementsByTagName('base');

       foreach($bases as $base)
       {
           print "ID: ".$base->getAttribute('id').'<br/>'.PHP_EOL;
           $names = $base->getElementsByTagName('name');
           $hosts = $base->getElementsByTagName('host');
           $types = $base->getElementsByTagName('type');
           $users = $base->getElementsByTagName('user');
 
           $name = $names->item(0)->nodeValue;
           $host = $hosts->item(0)->nodeValue;
           $type = $types->item(0)->nodeValue;
           $user = $users->item(0)->nodeValue;

           print "Name: ".$name.'<br/>'.PHP_EOL;
           print "Host: ".$host.'<br/>'.PHP_EOL;
           print "Type: ".$type.'<br/>'.PHP_EOL;
           print "user: ".$user.'<br/>'.PHP_EOL;
           print "<br/>".PHP_EOL;

 
            
       } 
    }


    public static function createDocumentXml()
    {
        $dom = new DOMDocument('1.0', "UTF-8");
        $dom->formatOutput = true;

        $bases = $dom->createElement('bases');
        $base = $dom->createElement('base');
        $bases->appendChild($base);
        
        $attribute = $dom->createAttribute('id');
        $attribute->value = 1;
        $base->appendChild($attribute);

        $base->appendChild($dom->createElement('name', 'teste'));
        $base->appendChild($dom->createElement('host', '192.168.0.1'));
        $base->appendChild($dom->createElement('type', 'mysql'));
        $base->appendChild($dom->createElement('user', 'mary'));

        return $dom->saveXML($bases);

    }

    
}