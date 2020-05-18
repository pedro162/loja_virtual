<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use App\Models\ProdutoCategoria;
use \Exception;
use \InvalidArgumentException;
use App\Models\Departamento;
use App\Models\Marca;
use App\Models\Categoria;

class Produto extends BaseModel
{
    private $nomeProduto;
    private $textoPromorcional;
    private $preco;
    private $fabricante;
    private $caracteristicas;
    private $idProduto;
    private $nomeDepartamento;
    private $idCategoria = [];
    private $estoque;
    private $codigo;
    private $marca;
    private $nf;
    private $fornecedor;
    private $idMarca;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    protected $table = 'Produto';

    public function addProduto()
    {
        # code...
    }


    protected function clear(array $dados):bool//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case 'nome':
                   $this->setNomeProduto($subArray[1]);
                   break;
                case 'texto':
                   $this->setTextoPromorcional($subArray[1]);
                   break;

                case 'marca':
                    $idMarca = (int) $subArray[1];
                   $this->setIdMarca($idMarca);
                   break;

                case 'categoria':

                    $subArrCateg = explode(',' , $subArray[1]);

                    for ($j=0; !($j == count($subArrCateg)) ; $j++) { 
                        
                        $idCategoria = (int) $subArrCateg[$j];
                        $this->setIdCategoria($idCategoria);
                    }
                break;
                case 'prod':
                    $this->setIdProduto($subArray[1]);
                break;
            }

        }

        return true;
    }

    protected function parseCommit()
    {   

        $this->data['nomeProduto']          = $this->getNomeProduto();
        $this->data['IdMarca']              = $this->getMarca()->getIdMarca();
        $this->data['textoPromorcional']    = $this->getTextoPromorcional();

        return $this->data;
    }


    public function save(array $dados):array
    {

        $this->clear($dados);

        $result = $this->parseCommit();

        $resultSelect = $this->select(['nomeProduto'], ['nomeProduto' => $this->getNomeProduto()], '=','asc', null, null, true);

        if($resultSelect != false){
            return ['msg','warning','Atenção: Este produto já existe!'];
        }

        $this->insert($result);

        $idProdutoInserido = $this->maxId();

        $produtoCategoria = new ProdutoCategoria();

        for ($i = 0; !($i == count($this->getIdCategoria())); $i++) {
            $subArraiComm = [];
            $subArraiCommit['idProduto'] = $idProdutoInserido;
            $subArraiCommit['idCategoria'] = $this->getIdCategoria()[$i];
            

            $res = $produtoCategoria->save($subArraiCommit);
            if($res === false){
                throw new Exception("Falha ao cadastrar produto");
            }
        }

        return ['msg','success','Produto cadastrado com sucesso!'];

    }


    public function modify(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultUpdate = $this->update($result, $this->getIdProduto());

        $produtoCategoria = new ProdutoCategoria();

        $produtoCategoria->delete('ProdutoIdProduto', '=', $this->getIdProduto());

        for ($i = 0; !($i == count($this->getIdCategoria())); $i++) {
            $subArraiComm = [];
            $subArraiCommit['idProduto'] = $this->getIdProduto();
            $subArraiCommit['idCategoria'] = $this->getIdCategoria()[$i];
            

            $res = $produtoCategoria->save($subArraiCommit);
            if($res == false){
                throw new Exception("Falha ao atualizar produto");
            }
        }

        return ['msg','success','Produto atualizado com sucesso!'];
    }

    public function getFiltros():array
    {
        return[
            'Categoria'=>['Games', 'Celulares'],
            'Preco'=>['1200', '1000', '155.50'],
            'Condições'=> ['2x', '4x', '6x', '10x']
        ];

    }

    public function setFornecedor(Int $id)
    {
        $fornecedor = new Fornecedor();
        $result = $fornecedor->select(['idFornecedor','nomeFornecedor'], ['idFornecedor'=>$id], '=','asc', null, null,true);

        $this->fornecedor = $result[0];

    }

    public function getFornecedor()
    {
        if(!isset($this->fornecedor)){
            throw new Exception("Propriedade indefinida");
        }

        if(!empty($this->fornecedor)){
            return $this->fornecedor;
        }else{
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }
        
    }

    public function produtoCategoria()
    {
        $produtoCategoria = new ProdutoCategoria();
        $result = $produtoCategoria->getCategoria($this->idProduto);
        return $result;
    }


    public function detalheProduto(Int $id)
    {
        $result = $this->select(['nomeProduto','textoPromorcional', 'idProduto', 'preco'], ['idProduto'=>$id], '=','asc', null, null,true);
        $array[] = $result[0]->getNomeProduto();
        $array[] = $result[0]->getPreco();
        return json_encode($array);
    }

    public function setNf($nf):bool
    {   
        $nf = (string) $nf;

        $nf = trim($nf);

        if((!is_string($nf)) || (!isset($nf)) || (strlen($nf) < 13) || (strlen($nf) > 13)){
            throw new Exception("Nota Fiscal com formato inválido<br/>\n");
        }
        $this->nf = $nf;
        return true;
    }

    public function getNf():String
    {
        if(isset($this->nf) && (!empty($this->nf))){
            return $this->nf;
        }
        throw new Exception("Propriedade indefinida<br/>\n");
    }

    public function getMarca()
    {
        $marca = new Marca();
        $result = $marca->select(['idMarca','nomeMarca'], ['idMarca'=>$this->getIdMarca()], '=','asc', null, null,true);

        return $result[0];
    }

    public function setIdMarca(int $idMarca)
    {   
        if($idMarca > 0)
        {
            $this->idMarca = $idMarca;

            return true;
        }
        throw new Exception('Propriedade inválida<br/>'.PHP_EOL);
        
        
    }

    public function getIdMarca()
    {   
        if(isset($this->idMarca) && (!empty($this->idMarca)))
        {
            return $this->idMarca;
        }

        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        
        
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
        if(empty($this->codigo) || (!isset($this->codigo)))
        {
            throw new InvalidArgumentException("Propriedade não definida<br/>");
        }

        return $this->codigo;
    }

    public function setCodigoProduto(String $codigo):bool
    {   
        $codigo = trim($codigo);

        if(isset($codigo) && (strlen($codigo) >= 4) && (strlen($codigo) <= 8)){
            $this->codigo = $codigo;
            return true;
        }
        
        throw new InvalidArgumentException("Propriedade inváldia<br/>");
    }

    public function getCategoria()
    {   
        $prodCateg = new ProdutoCategoria();

        $result = $prodCateg->getCategoria($this->idProduto);
        return $result;
    }


    public function setIdCategoria(Int $id):bool
    {
        if(($id > 0) && (!in_array($id, $this->idCategoria))){

            $categoria = new Categoria();

            $result = $categoria->select(['idCategoria','nomeCategoria'], ['idCategoria'=>$id], '=','asc', null, null,true);

            if(count($result) > 0)
            {
                $this->idCategoria[] = $result[0]->getIdCategoria();
            }
            return true;
        }
        throw new Exception("Parâmetro inválido<br/>\n");
    }


    public function getIdCategoria()
    {   
        if(count($this->idCategoria) > 0){
            return $this->idCategoria;
        }
        throw new Exception('Propriedade indefinida<br/>');
    }

    
    public function setNomeProduto(string $nomeProduto):bool
    {
        $nomeProduto = trim($nomeProduto);

        if(!(strlen($nomeProduto) >= 4))
        {
            throw new Exception("Descricao inválida<br/>\n");
        }

        $this->nomeProduto = $nomeProduto;

        return true;
    }



    public function getNomeProduto():string
    {
        if(empty($this->nomeProduto) || (!isset($this->nomeProduto)))
        {
            throw new InvalidArgumentException("Propriedade não definida<br/>");
        }

        return $this->nomeProduto;
    }


    public function setTextoPromorcional(String $texto):bool
    {
        $texto = trim($texto);

        if(!(isset($texto) && (strlen($texto) >= 6 && strlen($texto) <= 30))){
            throw new Exception("Texto promorcional com formato inválido<br/>\n");
        }

        $this->textoPromorcional = $texto;
        return true;
    }


    public function getTextoPromorcional():string
    {
        if(empty($this->textoPromorcional) || (!isset($this->textoPromorcional)))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->textoPromorcional;
    }


   public function setEstoque(int $estoque):bool
    {   

        if(!(is_integer($estoque) && ($estoque > 0) && isset($estoque)))
        {
            throw new Exception("Estoque inválido<br/>\n");
        }

        $this->estoque = $estoque;
        return true;
    }

    public function getEstoque():int
    {
        if(empty($this->estoque) || (!isset($this->estoque)) || ($this->estoque < 0))
        {
            throw new InvalidArgumentException("Estoque indefinido<br/>");
        }

        return $this->estoque;
    }   


    public function setPreco(float $preco):bool
    {   
        $preco = trim($preco);

        if($preco > 0)
        {
            $this->preco = $preco;
            return true;
        }

        throw new Exception("Preço inválido<br/>\n");

    }

    public function getPreco():float
    {
        if(empty($this->preco) || (!isset($this->preco)) || ($this->preco <= 0))
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
        if(empty($this->idProduto) || (!isset($this->idProduto)))
        {
            throw new \Exception("Propriedade indefinida<br/>");
        }
        return $this->idProduto;
    }

    public function setIdProduto(Int $id)
    {
        if($id <= 0)
        {
            throw new \Exception("Propriedade indefinida<br/>");
        }

        $result = $this->select(['idProduto'], ['idProduto'=>$id], '=','asc', null, null,true);

        if($result != false){
            $this->idProduto = $result[0]->getIdProduto();
            return true;
        }
        return false;
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


        //Transaction::startTransaction(self::getDatabase());

        $sqlPersonalizada = "SELECT distinct P.idProduto,P.nomeProduto, P.textoPromorcional ";
       // $sqlPersonalizada .= ", P.Condicoes, P.preco ";
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
                    //comentados por mudanda na table do banco
               /* case 'Condicoes':
                   $codicoes .= " P.Condicoes = ".$this->satinizar($value[$i])." AND ";
                    break;

                case 'Preco':
                    $preco .= " P.preco <= ".$this->satinizar($value[$i])." AND ";
                    break;*/
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

        //Transaction::close();

        if($result == false){
            return json_encode(['msg', 'Nenhum resultado encontrado!']);
        }

        //return $sqlPersonalizada;
        return json_encode ($result);
    }


    


}

