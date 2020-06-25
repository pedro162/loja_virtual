<?php

namespace Core\Utilitarios;

use \Exception;

/**
 * Busca o edereco na web
 * E retorna em formato xml
 */
class LoadEnderecoApi
{
	const URL_CEP = 'http://viacep.com.br/ws/{cep}/xml';
	
	function __construct(String $cep)
	{
		$this->setCep($cep);
	}

	public function setCep(String $cep):bool
	{
		if(!isset($cep)){
			throw new Exception("Parâmetro inválido\n");
			
		}

		//remove os caracteres não numéricos
		$cep = preg_replace("/[^0-9]/", '', trim($cep));

		if(strlen($cep) == 0){
			throw new Exception("Parâmetro inválido\n");
			
		}

		$this->cep = $cep;
		return true;

	}

	public function getEndereco()
	{
		if(isset($this->cep) && (!empty($this->cep))){
			$url = str_replace('{cep}', $this->cep, self::URL_CEP);

			$xml = simplexml_load_file($url);
			return $xml;
		}

		throw new Exception("Propriedade não definida\n");
		
	}
}