<?php

namespace App\Models;

use App\Models\BaseModel;
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
    private $idCategoria;
    private $estoque;
    private $codigo;

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
            'Categoria'=>['Games', 'Celulares'],
            'Preco'=>['1200', '1000', '155.50'],
            'Condições'=> ['2x', '4x', '6x', '10x']
        ];

    }

    public function detalheProduto(Int $id)
    {
        $result = $this->select(['nomeProduto','textoPromorcional', 'idProduto', 'preco'], ['idProduto'=>$id], '=');
        $array[] = $result[0]->getNomeProduto();
        $array[] = $result[0]->getPreco();
        return json_encode($array);
    }


    public function listarProdutos(array $campos):array
    {
        $resultSelect = $campos;

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


    public function getCodigoProduto():string
    {
        if(empty($this->codigo))
        {
            throw new InvalidArgumentException("Propriedade não definida<br/>");
        }

        return $this->codigo;
    }

    public function setCodigoProduto(String $codigo):bool
    {
        if(isset($codigo) && (!empty($codigo))){
            $this->codigo = $codigo;
            return true;
        }
        
        throw new InvalidArgumentException("Valor inváldio<br/>");
    }

    public function getDepartamento():array
    {
        $departamento = new Categoria();
        $restult = $departamento->select(['nomeCategoria', 'idCategoria'], ['idCategoria' => $this->idCategoria]);

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



    private function parseFiltroAjax(array $request):array
    {
        if(!((isset($request['post'])) && (count($request['post']) > 0))){

            throw new \Exception("Consulta inválida<br/>\n");
            //return false;
        }

        if(!((isset($request['post']['produtos'])) && (count($request['post']['produtos']) > 0))){

            throw new \Exception("Consulta inválida<br/>\n");
            //return false;
        }

        /*
        $superArray = [];

        for ($i=0; !($i == count($request['post']['produtos'])); $i++) { 
            $resultado = null;

            $chave = null;

            for ($j=0; !($j == count($request['post']['produtos'][$i])); $j++) {

                $chave = $request['post']['produtos'][$i][0];
                if($j != 0){
                    $resultado[] = $request['post']['produtos'][$i][$j];
                }
                
            }

            $superArray[$chave] = $resultado;
            
        }

        return $superArray;
        */
        return $this->parseRequestAjax($request['post']['produtos']);
    }


    public function listarConsultaPersonalizada(array $request):String
    {   
        $parametros = $this->parseFiltroAjax($request);

        if((is_array($parametros))&&(count($parametros)==0)){

            throw new Exception("Consulta inválida<br/>\n");
            //return json_encode(['msg','Consulta inválida']);
        }
        
        /*
        if($parametros == false){
            return json_encode(['msg','Consulta inválida']);
        }*/

        $sentinelaSubarray = false;

        foreach ($parametros as $key => $value) {
            if(count($value)>0){

                $sentinelaSubarray = true;
            }
            
        }

        if($sentinelaSubarray == false)
            throw new Exception("Consulta inválida<br/>\n");


        $sqlPersonalizada = "SELECT distinct P.idProduto,P.nomeProduto, P.textoPromorcional,";
        $sqlPersonalizada .= " P.Condicoes, P.preco ";
        $sqlPersonalizada .= " FROM  ProdutoCategoria PC inner join Produto P on P.idProduto = PC.ProdutoIdProduto";
        $sqlPersonalizada .= " inner join Categoria C on C.idCategoria = PC.CategoriaIdCategoria";


       $codicoes = '';

       $preco = '';

       $categoria = '';

       foreach ($parametros as $key => $value) {

            for ($i=0; !($i == count($value)); $i++) { 

                switch ($key) {
                case 'Categoria':
                   $categoria .= " C.nomeCategoria = ".$this->satinizar($value[$i])." or ";
                    break;

                case 'Condicoes':
                   $codicoes .= " P.Condicoes = ".$this->satinizar($value[$i])." AND ";
                    break;

                case 'Preco':
                    $preco .= " P.preco <= ".$this->satinizar($value[$i])." AND ";
                    break;
                }
            }
        }
        

        $were = ' where ';
        if(strlen($categoria) > 0){
            $categoria  = substr($categoria, 0, -3);
            $were .= '('.$categoria.') AND ';
        }

        if(strlen($preco) > 0){
            $preco  = substr($preco, 0, -4);
            $were .= '('.$preco.') AND ';

        }

        if(strlen($codicoes) > 0){
            $codicoes  = substr($codicoes, 0, -4);
            $were .= '('.$codicoes.') AND ';

        }

        if(strlen($were) > 7){
            $were = substr($were, 0, -4);
            $sqlPersonalizada .= $were;
        }

        $result = $this->persolizaConsulta($sqlPersonalizada);
        if($result == false){
            return json_encode(['msg', 'Nenhum resultado encontrado!']);
        }

        //return $sqlPersonalizada;
        return json_encode ($result);
    }


}

