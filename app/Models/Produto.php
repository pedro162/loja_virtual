<?php

namespace App\Models;

use App\Models\Models;
use \Exception;
use \InvalidArgumentException;
use App\Models\Departamento;

class Produto extends BaseModel
{
    private $nomeProduto;
    private $textoPromorcional;
    private $preco;
    private $fabricante;
    private $caracteristicas;
    private $idProduto;
    private $nomeDepartamento;
    private $idDepartamento;
    private $estoque;

    protected $table = 'Produto';

    public function __construct()
    {
        self::open();
    }



    public function addProduto()
    {
        # code...
    }


    public function salvarProduto($resquest)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        # code...
    }


    public function getFiltros():array
    {
        return[
            'Departamento'=>['Games', 'Celulares'],
            'Preco'=>['1200', '12'],
            'Mais procurados'=>['Notboocks', 'Maquiagem'],
            'Condições'=> ['1x', '4x', '5x']
        ];

    }

    public function detalheProduto(Int $id)
    {
        $result = $this->select(['nomeProduto','textoPromorcional', 'idProduto', 'preco', 'idDepartamento'], ['idProduto'=>$id], '=');
        $array[] = $result[0]->getNomeProduto();
        $array[] = $result[0]->getPreco();
        return json_encode($array);
    }


    public function listarProdutos(array $campos):array
    {
        $resultSelect = $this->select($campos);

        $gridProdutos = [];

        if((count($resultSelect) % 2) ==0)
        {
           for ($i=0; !($i == count($resultSelect)); $i+=6) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 6)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        else{
           for ($i=0; !($i == count($resultSelect)); $i+=3) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 3)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        
        return $gridProdutos;
    }


    public function getDepartamento():array
    {
        $departamento = new Departamento();
        $restult = $departamento->select(['nomeDepartamento', 'idDepartamento']);

        return $restult;
        
    }


    public function setNomeProduto(string $nomeProduto):bool
    {
        if(!((is_string($nomeProduto)) && (strlen($nomeProduto) >= 4)))
        {
            throw new Exception("Descricao inválida<br/>\n");
        }

        $this->nomeProduto = $nomeProduto;

        return true;
    }



    public function getNomeProduto():string
    {
        if(empty($this->nomeProduto))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->nomeProduto;
    }


    public function setTextoPromorcional(String $texto):bool
    {
       if(strlen($texto < 6))
       {
            throw new Exception("Texto promorcional muto curto<br/>\n");
       }

       $this->textoPromorcional = $texto;
       return true;
    }


    public function getTextoPromorcional():string
    {
        if(empty($this->textoPromorcional))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->textoPromorcional;
    }


    public function setEstoque(int $estoque):bool
    {
        if(!(is_integer($estoque) && ($estoque > 0)))
        {
            throw new Exception("Estoque inválido<br/>\n");
        }

        $this->estoque = $estoque;
        return true;
    }

    public function getEstoque():int
    {
        if(empty($this->estoque))
        {
            throw new InvalidArgumentException("Estoque indefinido<br/>");
        }

        return $this->estoque;
    }   


    public function setPreco(float $preco):bool
    {
        if(is_float($preco) && ($preco > 0))
        {
            $this->preco = $preco;
            return true;
        }

        throw new Exception("Preço inválido<br/>\n");

    }

    public function getPreco():float
    {
        if(empty($this->preco))
        {
            throw new Exception("Preço indefinido<br/>\n");
        }

        return $this->preco;
    }


    public function setFabricante(Fabricante $newFabricante)
    {
        $this->fabricante = $newFabricante;
    }

    public function getFabricante():Fabricante
    {
        if(empty($this->fabricante))
        {
            throw new InvalidArgumentException("Fabricante indefinido<br/>\n");
        }

        return $this->fabricante;
    }

    public function addCaracteristica(String $nome, String $valor):bool
    {
        $this->caracteristicas[] = new Caracteristica($nome, $valor);
        
        return true;
    }

    public function getCaracteristicas():array
    {
        if(empty($this->caracteristicas))
        {
            throw new Exception("Caracteristicas indefinidas<br/>\n");
        }

        return $this->caracteristicas;

    }

    public function getIdProduto():int
    {
        if(empty($this->idProduto))
        {
            throw new \Exception("Propriedade indefinida<br/>");
        }
        return $this->idProduto;
    }


    public function listarConsultaPersonalizada(array $parametros):array
    {   
        $newParams = [];

        for ($i=0; !($i == count($parametros)); $i++) { 
           $subArray = explode('-', $parametros[$i]);

           $newParams[] = [$subArray[0] => $subArray[1]];
        }

        for ($i=0; !($i == count($newParams)); $i++) { 
           $newParams[$i] = $this->satinizar($newParams[$i]);
        }

       $sqlPersonalizada = "SELECT P.idProduto, P.nomeProduto, P.preco, P.textoPromorcional, D.idDepartamento DepartProd, D.nomeDepartamento,".
                             "D.idDepartamento DepartDepart FROM Produto P,".
                             "Departamento D WHERE P.idDepartamento = D.idDepartamento AND ";

        for ($i=0; !($i == count($newParams)); $i++) { 
            
            foreach ($newParams[$i] as $key => $value) {

                switch ($key) {
                case 'Departamento':
                   $sqlPersonalizada .= "D.nomeDepartamento = {$value} AND ";
                    break;
                case 'Condicoes':
                   $sqlPersonalizada .= "P.Condicoes = {$value} AND ";
                    break;
                case 'Mais procurados':
                    $sqlPersonalizada .= "P.Maisprocurados = {$value} AND ";
                    break;
                case 'Preco':
                    $sqlPersonalizada .= "P.Preco <= {$value} AND ";
                    break;
                }
               
            }

            
        }

        $sqlPersonalizada  = substr($sqlPersonalizada, 0, -4);

        
        return $this->persolizaConsulta($sqlPersonalizada);
    }


}

