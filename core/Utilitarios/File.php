<?php

namespace Core\Utilitarios;

class File
{
	private $name;
	private $extensao;
	private $size;
	private $nameTemporario;
	const EXTENSOES = [
		'imagem' =>[
			'extsoes'=>['.jpg', '.jpeg', '.png', '.gig', '.bmp'],
			'tamanho' => '200000'
		],
		'texto' => [
			'extsoes'=>['.txt', '.pdf'],
			'tamanho' => '200000'
		],
		'video' => [
			'extsoes'=>['.mp4', '.mp3'],
			'tamanho' => '200000'
		],
		'audio' => [
			'extsoes'=>[],
			'tamanho' => '200000'
		]

	]; 

	public function __construct(String $nome, int $size, String $nomeTemporario)
	{
		$this->setName($nome);
		$this->setSize($size);
		$this->setNameTemporario($nomeTemporario);
	}



	public function salvar($path, $refresh = false):bool
	{
		$path = __DIR__.'/../../public/files/'.$path;
		if(is_dir($path) == true)
		{	
			$result = true;

			$file = $path.'/'.$this->getName();

			if(file_exists($file)){

				if($refresh != false){
					$result = move_uploaded_file($this->getNameTemporario(), $path.'/'.$this->getName());
				}else{
					$result = move_uploaded_file($this->getNameTemporario(), $path.'/'.'copy_'.$this->getName());
				}
				

			}else{
				
				$result = move_uploaded_file($this->getNameTemporario(), $path.'/'.$this->getName());
			}

			if($result === false){
				throw new \Exception("Falha ao salvar imgem<br/>\n");
			}

			return true;
		}else{
			throw new \Exception("Diretorio inválido<br/>\n");
		}
		
	}

	public function setName(String $name):bool
	{
		$this->name = $name;
		return true;
	}

	public function setExtensao(String $extensao):bool
	{
		$this->extensao = $extensao;
		return true;
	}

	public function setSize(int $size):bool
	{
		$this->size = $size;
		return true;
	}

	public function setNameTemporario(String $nameTemporario):bool
	{
		$this->nameTemporario = $nameTemporario;
		return true;
	}


	public function getNameTemporario():string
	{
		if(!empty($this->nameTemporario)){
			return $this->nameTemporario;
		}
		throw new \Exception("Propriedade indefinida<br/>\n");
		
	}

	public function getName():string
	{
		if(!empty($this->name)){
			return $this->name;
		}
		throw new \Exception("Propriedade indefinida<br/>\n");
		
	}

}