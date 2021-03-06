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
use App\Models\Imagem;
use App\Models\Estrela;
use App\Models\Cometario;
use \App\Models\Usuario;

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
    private $IdMarca;
    private $imagemProduto;
    private $gostei;
    private $votos;
    private $UsuarioIdUsuario;

    private $altura;
    private $largura;
    private $comprimento;
    private $cor;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    const TABLENAME = 'Produto';


    protected function clear(array $dados):bool//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        foreach ($dados as $key => $value) {
           
            switch ($key) {
                case 'nome':
                        $this->setNomeProduto($value);
                    break;
                case 'texto':
                        $this->setTextoPromorcional($value);
                    break;

                case 'marca':
                        $idMarca = (int) $value;
                        $this->setIdMarca($value);
                    break;

                case 'categoria':

                        $idCategoria = (int) $value;
                        $this->setIdCategoria('categ',$idCategoria);
                    break;
                case 'subCategoria':

                        $idSubCategoria = (int) $value;
                        $this->setIdCategoria('subCateg',$idSubCategoria);
                    break;
                case 'prod':
                        $this->setIdProduto($value);
                    break;
                case 'img':
                        $this->setImagemProduto($value);
                    break;
                case 'cor':
                        $this->setCor($value);
                    break;
                case 'comprimento':
                        $value = (float) $value;
                        $this->setComprimento($value);
                    break;
                case 'largura':
                        $value = (float) $value;
                        $this->setLargura($value);
                    break;
                case 'altura':
                        $value = (float) $value;
                        $this->setAltura($value);
                    break;
            }

        }

        return true;
    }

    protected function parseCommit()
    {   
        $arrayPase = [];
        for ($i=0; !($i == count($this->columns())) ; $i++) { 
            $chave = $this->columns()[$i]->Field;
            if(array_key_exists($chave, $this->data)){
                $arrayPase[$chave] = $this->data[$chave];
            }
        }
        return $arrayPase;
    }

    public function save(array $dados):array
    {

        $this->clear($dados); //atribui os dados aos gets e sets

        $result = $this->parseCommit(); //retorna os dados já filtrados 

        //verifica se o produto já existe
        $resultSelect = $this->select(['nomeProduto'], ['nomeProduto' => $this->getNomeProduto()], '=','asc', null, null, true);

        if($resultSelect != false){
            throw new Exception("Este produto já existe");
        }

        $resultInsertProd = $this->insert($result);//salva o produto

        if($resultInsertProd == false){
            throw new Exception("Falha ao cadastrar produto");
        }

        $idProdutoInserido = $this->maxId();


        foreach ($this->getImagemProduto() as $key => $value) {

            $imagem = new Imagem();
            $dataImg = ['url'=>$value[1], 'produto'=>$idProdutoInserido, 'tipo' => $value[0], 'usuario'=>$this->getUsuarioIdUsuario()];//prepara o array com dados para a classe de imagem

            $resultImg = $imagem->save($dataImg);

            if($resultImg == false){
                throw new Exception("Falha ao cadastrar produto");
            }
        }
        
        //prepara os dados para salvar a categoria do produto
        $produtoCategoria = new ProdutoCategoria();

        foreach ($this->getIdCategoria() as $key => $value) {
            $subArraiComm = [];
            $subArraiCommit['idProduto'] = $idProdutoInserido;
            $subArraiCommit['idCategoria'] = $value;
            
            if($key == 'subCateg'){
                $subArraiCommit['classific'] = 'secundaria';
            }

            $res = $produtoCategoria->save($subArraiCommit);
            if($res === false){
                throw new Exception("Falha ao cadastrar produto");
            }

        }

        return ['msg','success','Produto cadastrado com sucesso!'];

    }

    //ajustar method de update
    public function modify(array $dados)
    {


        $this->clear($dados); //atribui os dados aos gets e sets

        $result = $this->parseCommit(); //retorna os dados já filtrados 

        $resultInsertProd = $this->update($result, $this->getIdProduto());//salva o produto

        $arrTipoImgUpdated = [];

        for ($i=0, $imagem = $this->getImagem(); !($i == count($imagem)) ; $i++) { 

            foreach ($this->getImagemProduto() as $key => $value) {

                $dataImg = ['url'=>$value[1], 'tipo' => $value[0]];//prepara o array

                if($imagem[$i]->getTipo() == $value[0]){

                    $imagem[$i]->modify($dataImg);
                    $arrTipoImgUpdated[] = $value[0];

                }
            }
            
        }


        //adiciona as imagen que por acaso não haviam no banco.
        foreach ($this->getImagemProduto() as $key => $value) {

            if(! in_array($value[0], $arrTipoImgUpdated)){

                $imagem = new Imagem();
                $dataImg = ['url'=>$value[1], 'produto'=>$this->getIdProduto(), 'tipo' => $value[0], 'usuario'=>$this->getUsuarioIdUsuario()];//prepara o array com dados para a classe de imagem

                $resultImg = $imagem->save($dataImg);

                if($resultImg == false){
                    throw new Exception("Falha ao cadastrar produto");
                }

            }
        }

        //remove o relacionamento anterior entre a categoria e o produto
        $produtoCategoria = $this->getProdCateg();

        if(is_array($produtoCategoria)){
            for ($i=0; !($i == count($produtoCategoria)) ; $i++) { 

                $produtoCategoria[$i]->delete('idProdutoCategoria' , '=' ,(int)$produtoCategoria[$i]->getIdProdutoCategoria(), 1);
            }
        }

        //estabelece a nova relação
        $produtoCategoria = new ProdutoCategoria();

        foreach ($this->getIdCategoria() as $key => $value) {
            $subArraiCommit = [];
            $subArraiCommit['idProduto'] = $this->getIdProduto();
            $subArraiCommit['idCategoria'] = $value;
            
            if($key == 'subCateg'){
                $subArraiCommit['classific'] = 'secundaria';
            }elseif($key == 'categ'){
                $subArraiCommit['classific'] = 'primaria';
            }
            //var_dump($subArraiCommit);
            $res = $produtoCategoria->save($subArraiCommit);
            if($res === false){
                throw new Exception("Falha ao atualizar produto");
            }


            //var_dump($res);
        }

        return ['msg','success','Produto atualizado com sucesso!'];
    }


    public function getProdCateg()
    {

        $prodCateg = new ProdutoCategoria();
        $result = $prodCateg->selectNew(['*'],
         [
            ['key'=>'ProdutoIdProduto','val'=>$this->getIdProduto(),
            'comparator'=>'=']
        ], null,  null, null, true, false);

        return $result;

    }

    public function getFiltros():array
    {
        return[
            'Categoria'=>['Games', 'Celulares'],
            'Preco'=>['1200', '1000', '155.50'],
            'Condições'=> ['2x', '4x', '6x', '10x']
        ];

    }

    public function setUsuarioIdUsuario(Int $id)
    {
        if(isset($id) && ($id > 0)){
            $this->data['UsuarioIdUsuario'] = $id;
            return true;
        }
    }

    public function getUsuarioIdUsuario()
    {
        if((!isset($this->UsuarioIdUsuario)) || ($this->UsuarioIdUsuario <= 0)){

            if(isset($this->data['UsuarioIdUsuario']) && ($this->data['UsuarioIdUsuario'] > 0)){
                return $this->data['UsuarioIdUsuario'];
            }

            throw new Exception('Propriedade inválida');
        }

        return $this->UsuarioIdUsuario;
    }


    public function getCor():String
    {
        if((!isset($this->cor)) || (strlen($this->cor) == 0)){

            if(isset($this->data['cor']) && (strlen($this->data['cor']) > 0)){
                return $this->data['cor'];
            }

            throw new Exception('Propriedade inválida');
        }

        return $this->cor;
    }

    public function setCor(String $cor):bool
    {
        if((!isset($cor)) || (strlen($cor) == 0)){

            throw new Exception('Parâmetro invalido\n');
        }

        $this->data['cor'] = $cor;
        return true;
    }

    public function getComprimento():float
    {
        if((!isset($this->comprimento)) || ($this->comprimento < 0)){

            if(isset($this->data['comprimento']) && ($this->data['comprimento'] > 0)){
                return $this->data['comprimento'];
            }
            throw new Exception('Propriedade inválida dd');
        }

        return $this->comprimento;
    }

    public function setComprimento(float $compr):bool
    {
        if((!isset($compr)) || ($compr <= 0)){

            throw new Exception('Parâmetro invalido\n');
        }

        $this->data['comprimento'] = $compr;

        return true;
    }


    public function getLargura():float
    {
        if((!isset($this->largura)) || ($this->largura < 0)){

            if(isset($this->data['largura']) && ($this->data['largura'] > 0)){
                return $this->data['largura'];
            }
            throw new Exception('Propriedade inválida');
        }

        return $this->largura;
    }

    public function setLargura(float $larg):bool
    {
        if((!isset($larg)) || ($larg <= 0)){

             throw new Exception('Parâmetro invalido\n');
        }

        $this->data['largura'] = $larg;

        return true;
    }


    public function getAltura():float
    {
        if((!isset($this->altura)) || ($this->altura < 0)){

            if(isset($this->data['altura']) && ($this->data['altura'] > 0)){
                return $this->data['altura'];
            }
            throw new Exception('Propriedade inválida');
        }

        return $this->altura;
    }

    public function setAltura(float $alt):bool
    {
        if((!isset($alt)) || ($alt <= 0)){

            throw new Exception('Propriedade inválida');
        }

        $this->data['altura'] = $alt;
        return true;
    }


    public function setFornecedor(Int $id):bool
    {
        $fornecedor = new Fornecedor();
        $result = $fornecedor->select(['idFornecedor','nomeFornecedor'], ['idFornecedor'=>$id], '=','asc', null, null,true);

        $this->data['fornecedor'] = $result[0];
        return true;
    }


    public function loadProdutoForId(Int $id)
    {   
        if($id > 0){
            $result = $this->select(['nomeProduto, idProduto, textoPromorcional'],
             ['idProduto'=>$id], '=','asc', null, null,true);
            if($result != false){
                return $result[0];
            }
        }
        throw new Exception("Ṕroduto não encontrado\n");
        
        
    }

    public function getFornecedor()
    {
        if((!isset($this->fornecedor)) || (empty($this->fornecedor))){

            if((!isset($this->data['fornecedor'])) || (empty($this->data['fornecedor']))){
                throw new Exception("Propriedade indefinida");
            }
            return $this->data['fornecedor'];
        }
        
        return $this->fornecedor;
    }

    public function produtoCategoria()
    {
        $produtoCategoria = new ProdutoCategoria();
        $result = $produtoCategoria->getCategoria($this->idProduto);
        return $result;
    }


    public function Estrela()
    {
        $estrela = new Estrela();
        $result = $estrela->select(['idEstrela', 'dtEstrela', 'ProdutoIdProduto'
                                , 'UsuarioIdUsuario', 'numEstrela'],
                                 ['ProdutoIdProduto'=>$this->idProduto], '=','asc', null, null,true);
        if($result == false){
            return false;
        }

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

        $this->data['nf'];
        return true;
    }

    public function getNf():String
    {
        if((!isset($this->nf)) || (empty($this->nf))){

            if(isset($this->data['nf']) && (!empty($this->data['nf']))){
                return $this->data['nf'];
            }
            
            throw new Exception("Propriedade indefinida<br/>\n");
        }
        return $this->nf;
    }

    public function getMarca()
    {
        $marca = new Marca();
        $result = $marca->select(['idMarca','nomeMarca'], ['idMarca'=>$this->getIdMarca()], '=','asc', null, null,true);

        return $result[0];
    }

    public function getImagem()
    {
        $img = new Imagem();
        $result = $img->selectNew(['*'],
         [
            [
                'key'=>'ProdutoIdProduto','val'=>$this->getIdProduto(),'comparator'=>'='
            ]
        ], null, null, null, true, false);
        
        if($result != false){
            return $result;
        }
        throw new Exception("Imgem não encontrada\n");
        

    }

    public function setImagemProduto(array $img)
    {   
        if (count($img) < 4) {
            throw new Exception("Parâmetro inválido\n");
            
        }

        foreach ($img as $key => $value) {

            if(strlen($value) == 0){
                throw new Exception("Parâmetro inválido\n");
                
            }

            $tipo = null;
            switch (trim($key)) {
                case 'imgProduto-2':
                   $tipo = 'secundaria';
                    break;
                case 'imgProduto-3':
                    $tipo = 'ternaria';
                    break;
                case 'imgProduto-4':
                    $tipo = 'quartenaria';
                    break;
                default:
                     $tipo = 'primaria';
                    break;
            }

            $nameImg = $tipo.'-'.$value;
            $this->imagemProduto[] = [$tipo, $nameImg];

        }

        

        
    }

    public function getImagemProduto()
    {
        return $this->imagemProduto;
    }

    public function setIdMarca(int $idMarca)
    {   
        if($idMarca > 0)
        {
            $this->data['IdMarca'] = $idMarca;

            return true;
        }
        throw new Exception('Propriedade inválida<br/>'.PHP_EOL);
        
        
    }

    public function getIdMarca()
    {   
        if((!isset($this->IdMarca)) || (empty($this->IdMarca))){

            if(isset($this->data['IdMarca']) && (!empty($this->data['IdMarca'])))
            {
                return $this->data['IdMarca'];
            }

            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }
        return $this->IdMarca;
        
    }

    public function findForId(Int $id)
    {
        $result = $this->selectNew(['*'],
         [
            ['key'=>'idProduto','val'=>$id,'comparator'=>'=', 'operator'=>'and'],
            ['key'=>'ativo','val'=>'1','comparator'=>'=']
        ], null, 1, null, true, false);
        if($result == false){
            throw new Exception('Produto não encontrado');
            
        }

        return $result[0];
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
            if(empty($this->data['codigo']) || (!isset($this->data['codigo'])))
            {
                throw new InvalidArgumentException("Propriedade não definida<br/>");
            }

            return $this->data['codigo'];
        }

        $this->codigo;
    }

    public function setCodigoProduto(String $codigo):bool
    {   
        $codigo = trim($codigo);

        if(isset($codigo) && (strlen($codigo) >= 4) && (strlen($codigo) <= 8)){
            
            $this->data['codigo'] = $codigo;

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


    public function setIdCategoria(String $key, Int $id):bool
    {   
        $key = trim($key);

        if((strlen($key) == 0 )|| ($id <= 0)){
            throw new Exception("Parâmetro inválido<br/>\n");
        }


        $categoria = new Categoria();

        $result = $categoria->select(['idCategoria','nomeCategoria'], ['idCategoria'=>$id], '=','asc', null, null,true);

        if(count($result) > 0)
        {
            $this->data['categorias'][$key]= $result[0]->getIdCategoria();
        }
        return true;
        
        throw new Exception("Parâmetro inválido<br/>\n");
    }




    public function getIdCategoria()
    {   
        if(isset($this->data['categorias'])){
            return $this->data['categorias'];
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

        $this->data['nomeProduto'] = $nomeProduto;

        return true;
    }



    public function getNomeProduto():string
    {
        if(empty($this->nomeProduto) || (!isset($this->nomeProduto)))
        {
            if(isset($this->data['nomeProduto']) && (!empty($this->data['nomeProduto']))){
                return $this->data['nomeProduto'];
            }

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

        $this->data['textoPromorcional'] = $texto;
        return true;
    }


    public function getTextoPromorcional():string
    {
        if(empty($this->textoPromorcional) || (!isset($this->textoPromorcional)))
        {
            if(empty($this->data['textoPromorcional']) || (!isset($this->data['textoPromorcional'])))
            {
                throw new InvalidArgumentException("Descrição não definida<br/>");
            }

            return $this->data['textoPromorcional'];
        }

        return $this->textoPromorcional;
    }


    public function getIdProduto():int
    {
        if(empty($this->idProduto) || (!isset($this->idProduto)))
        {
            if(isset($this->data['idProduto']) && (!empty($this->data['idProduto']))){
                return $this->data['idProduto'];
            }
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

            $this->data['idProduto'] = $result[0]->getIdProduto();
            return true;
        }
        return false;
    }

    public function Cometario()
    {
        $comentario = new Cometario();
        $comentarios = $comentario->select(
            ['idComentario','ProdutoIdProduto', 'textoComentario', 'dtComentario'],
            ['ProdutoIdProduto'=>$this->idProduto], '=','asc', null, null,true
        );

        return $comentarios;
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

       
        return $this->parseRequestAjax($request['post']['produtos']);
    }


    public function listarConsultaPersonalizada(array $request):String
    {   
        $parametros = $this->parseFiltroAjax($request);

        if((is_array($parametros))&&(count($parametros)==0)){

            throw new Exception("Parametro inválido<br/>\n");
        }
        

        $sentinelaSubarray = false;

        foreach ($parametros as $key => $value) {
            if(count($value)>0){

                $sentinelaSubarray = true;
            }
            
        }

        if($sentinelaSubarray == false)
            throw new Exception("Consulta inválida<br/>\n");


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


    public function __get($prop)
    {
        if(method_exists($this, 'get'.ucfirst($prop))){

            return call_user_func([$this,'get'.ucfirst($prop)]);
        }
    }

    public function __set($prop, $value)
    {   
        if(method_exists($this, 'set'.ucfirst($prop))){ 
            return call_user_func([$this,'set'.ucfirst($prop)], $value);
        }
    }
    


}

