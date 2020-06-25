<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Produto;
use App\Models\Usuario;
use App\Models\DetalhesPedido;
use \Core\Utilitarios\Utils;

/**
 * Classe fornecimento tambem acessa
 * via getter os atributos do produto
 * para melhor desempenho nas querys
 */
class Fornecimento extends BaseModel
{	
	private $idFornecimento;
    private $FornecedorIdFornecedor;
    private $dtFornecimento;
    private $dtRecebimento;
    private $dtValidade;
    private $qtdFornecida;
    private $qtdVendida;
    private $vlCompra;
    private $vlVenda;
    private $ativo;
    private $quantidade;
    private $UsuarioIdUsuario; 
    private $nf;
    private $estoque;
    private $altura;
    private $largura;
    private $comprimento;
    private $cor;

    private $idProduto;
    private $texto;
    private $produtoNome;

    private $idCategoria;
    private $nomeCategoria;

    private $url;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    const TABLENAME = 'Fornecimento';
    const ESTOQUEBAIXO = 10;

	protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
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
                case 'produto':

                    $this->setProdutoIdProduto($subArray[1]);
                    break;

                case 'fornecedor':

                    $this->setFornecedorIdFornecedor((int)$subArray[1]);
                    break;

                case 'dtValidade':

                    $this->setDtValidade($subArray[1]);
                    break;

                case 'dtFornecimento':

                    $this->setDtFornecimento($subArray[1]);
                    break;

                case 'dtRecebimento':

                    $this->setDtRecebimento($subArray[1]);
                    break;

                case 'qtd':

                    $this->setQtdFornecida($subArray[1]);
                    break;
                case 'qtdVend':
                    $qtd = (int)$subArray[1];
                    $this->setQtdVendida($qtd);
                    break;

                case 'vlCompra':

                    $this->setVlCompra($subArray[1]);
                    break;

                case 'vlVenda':

                    $this->setVlVenda($subArray[1]);
                    break;

                case 'margem':

                    $this->setMargem($subArray[1]);
                    break;

                case 'fornecimento': // falta criar o metodo ideal
                    $this->setIdFornecimento($subArray[1]);
                    break;

                case 'nf': // falta criar o metodo ideal
                    $this->setNf($subArray[1]);
                    break;

                case 'ativo': // falta criar o metodo ideal
                    $id = (int)$subArray[1];
                    $this->setAtivo($id);
                    break;
            }

        }

        $this->setUsuarioIdUsuario(1);//aqui será inseido o usuario que fez a orepacao
    }

    protected function parseCommit()
    {	
        if(isset($this->data['dtRecebimento']) && (isset($this->data['dtFornecimento']))){

            $dtRece = new \DateTime($this->getDtRecebimento());
            $dtForne = new \DateTime($this->getDtFornecimento());
            
            if($dtRece < $dtForne){
                throw new Exception('Falha ao no cadastro de fornecimento!<br/>');
            }
        }
        

        return $this->data;
    }


    public function save(array $dados)
    {
        
        $this->clear($dados);

        //vefica se existe um estoque com qtd >  0 
        $where = 'F.ativo=1 and (F.qtdFornecida - F.qtdVendida > 0) and P.idProduto = '.$this->getProdutoIdProduto();
        $stqZero = $this->listarConsultaPersonalizada($where, null, null, true);

        //e desativa o novo estoque
        if($stqZero){
            $this->setAtivo(0);
        }


        $resultParse = $this->parseCommit();
        
        $result = $this->insert($resultParse);

        //busca o estoque  zero e ativo
        $where = 'F.ativo=1 and (F.qtdFornecida - F.qtdVendida = 0) and P.idProduto = '.$this->getProdutoIdProduto();
        $stqZero = $this->listarConsultaPersonalizada($where, null, null, true);

        if($stqZero != false){
           //após lançar o novo estoque e desativa o que estiver ativo e com quantidade zerada.
            $dados = ['ativo=0'];

            $resultModify = $stqZero[0]->modify($dados);

            if(($result == true) && ($resultModify)){
                return ['msg', 'success', 'Estoque lançado com sucesso'];
            }
            
        }else{

            if($result == true){
                return ['msg', 'success', 'Estoque lançado com sucesso'];
            }
        }

        
        throw new Exception("Erro ao lançar estoque");
        
    }


    public function modify(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultUpdate = $this->update($result, $this->getIdFornecimento());

        if($resultUpdate){
            return ['msg','success','Estoque atualizado com sucesso!'];
        }
        throw new Exception("Estoque não pôde ser atualizado");
        
    }

    public function getCor():String
    {
        if((!isset($this->cor)) || (strlen($this->cor) == 0)){

            throw new Exception('Propriedade inválida');
        }

        return $this->cor;
    }

    public function getComprimento():float
    {
        if((!isset($this->comprimento)) || ($this->comprimento < 0)){

            throw new Exception('Propriedade inválida dd');
        }

        return $this->comprimento;
    }

    public function getLargura():float
    {
        if((!isset($this->largura)) || ($this->largura < 0)){

            throw new Exception('Propriedade inválida');
        }

        return $this->largura;
    }

    public function getAltura():float
    {
        if((!isset($this->altura)) || ($this->altura < 0)){

            throw new Exception('Propriedade inválida');
        }

        return $this->altura;
    }

    public function getProdutoIdProduto():int
    {

        if((!isset($this->idProduto)) || (empty($this->idProduto))){

            if(isset($this->data['ProdutoIdProduto']) && ($this->data['ProdutoIdProduto'] > 0)){
                return $this->data['ProdutoIdProduto'];
            }
            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->idProduto;
        
    }

    public function getTexto():String
    {

        if((!isset($this->texto)) || (strlen($this->texto) == 0)){

            if(isset($this->data['textoPromorcional']) && (strlen($this->data['textoPromorcional']) > 0)){
                return $this->data['textoPromorcional'];
            }
            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->texto;

    }

    public function getProdutoNome():String
    {
        if((!isset($this->produtoNome)) || (strlen($this->produtoNome) == 0)){

            if(isset($this->data['produtoNome']) && (strlen($this->data['produtoNome']) > 0)){
                return $this->data['produtoNome'];
            }
            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->produtoNome;

    }

    public function getNomeCategoria():String
    {
        if((!isset($this->nomeCategoria)) || (strlen($this->nomeCategoria) == 0)){

            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->nomeCategoria;

    }

    public function getIdCategoria():int
    {
        if((!isset($this->idCategoria)) || ($this->idCategoria <= 0)){

            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->idCategoria;

    }


    public function findFornecimentoForId(Int $id)
    {
        if($id <= 0){
            throw new Exception("Parametro inválido");
            
        }

        $result = $this->select(['idFornecimento','qtdVendida', 'qtdFornecida', 'ProdutoIdproduto as nomeProduto'], ['idFornecimento'=>$id], '=','asc', null, null,true);
        if($result == false){
            throw new Exception("Elemento não encontrado");
            
        }

        return $result[0];
    }


    public function setProdutoIdProduto(Int $id):bool
    {
        if($id >0){

            $produto = new Produto();

            $result = $produto->select(['idProduto','nomeProduto'], ['idProduto'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->data['ProdutoIdProduto'] = $result[0]->getIdProduto();
                return true;
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);

    }


    public function setUsuarioIdUsuario(int $id):bool
    {
        $this->data['UsuarioIdUsuario'] = $id;//apenas para teste, deve ser removida
        return true;//apenas para teste, deve ser removida

        if($id > 0){

            $user = new User();

            $result = $user->select(['idUsuario','nomeUsuario'], ['idUsuario'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->data['UsuarioIdUsuario'] = $result[0]->getIdUsuario();
                return true;
            }else{
                throw new Exception('Parametro invalido<br/>'.PHP_EOL);
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);
    }

    public function getUsuarioIdUsuario():int
    {
        if((!isset($this->UsuarioIdUsuario)) || ($this->UsuarioIdUsuario <= 0)){

            if(isset($this->data['UsuarioIdUsuario']) && ($this->data['UsuarioIdUsuario'] > 0)){
                return $this->data['UsuarioIdUsuario'];
            }
            throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);
        }
        return $this->UsuarioIdUsuario;
    }

    public function setFornecedorIdFornecedor(int $id):bool
    {   
         $this->data['FornecedorIdFornecedor'] = $id;return true;
        //apenas para test falta implementar classe fornecelor
        if($id > 0){

            $fornecedor = new Fornecedor();

            $result = $fornecedor->select(['idFornecedor','nomeFornecedor'], ['idFornecedor'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->data['FornecedorIdFornecedor'] = $result[0]->getIdFornecedor();
                return true;
            }else{
                throw new Exception('Parametro invalido<br/>'.PHP_EOL);
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);
    }

    public function getFornecedorIdFornecedor():int
    {
        if((!isset($this->FornecedorIdFornecedor)) || ($this->FornecedorIdFornecedor <= 0)){

            if(isset($this->data['FornecedorIdFornecedor']) && ($this->data['FornecedorIdFornecedor'] > 0)){
                return $this->data['FornecedorIdFornecedor'];
            }

            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }
        return $this->FornecedorIdFornecedor;
        
    }


    public function setNf(String $nota)
    {
        if(isset($nota) && (strlen($nota) >=4) && (strlen($nota) <= 40)){
            $this->data['nf'] = $nota;
            return true;
        }
        throw new Exception('Parãmetro inválido<br/>'.PHP_EOL);
    }


    public function getNf()
    {
        if((!isset($this->nf)) || (strlen($this->nf) == 0)){

            if(isset($this->data['nf']) && (strlen($this->data['nf']) > 0)){
                return $this->data['nf'];
            }

            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
            
        }

        return $this->nf;
       
    }


    public function getIdFornecimento():int
    {
        if((!isset($this->idFornecimento)) || (empty($this->idFornecimento))){

            if(isset($this->data['idFornecimento']) && ($this->data['idFornecimento'] > 0)){
                return $this->data['idFornecimento'];
            }

            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->idFornecimento;
        
    }

    public function setIdFornecimento(Int $id):bool
    {
        if(isset($id) && ($id > 0)){
            $fornecimento = $this->select(['idFornecimento'], ['idFornecimento'=>$id], '=','asc', null, null,true);
            if($fornecimento != false){
                $this->data['idFornecimento'] = $id;
                return true;
            }else{
                throw new Exception('Propriedade inválida<br/>'.PHP_EOL);
            }
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function getDtFornecimento()
    {
        if((!isset($this->dtFornecimento)) || (empty($this->dtFornecimento))){

            if(isset($this->data['dtFornecimento']) && (!empty($this->data['dtFornecimento']))){
                return $this->data['dtFornecimento'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->dtFornecimento;

    }


    public function setDtFornecimento(String $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $fornecimento = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($fornecimento <= $today){

                    $this->data['dtFornecimento'] = $fornecimento->format('Y-m-d H:i:s');

                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Data inválida<br/>');
    }


    public function getDtRecebimento()
    {

        if((!isset($this->dtRecebimento)) || (empty($this->dtRecebimento))){

            if(isset($this->data['dtRecebimento']) && (!empty($this->data['dtRecebimento']))){
                return $this->data['dtRecebimento'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->dtRecebimento;

    }

    public function setDtRecebimento(string $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $receb = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($receb <= $today){
                    $this->data['dtRecebimento'] = $receb->format('Y-m-d H:i:s');
                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Parametro inváldio<br/>');
    }


    public function getDtValidade()
    { 
        if((!isset($this->dtValidade)) || (empty($this->dtValidade))){

            if(isset($this->data['dtValidade']) && (!empty($this->data['dtValidade']))){
                return $this->data['dtValidade'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->dtValidade;
    }


    public function setDtValidade(String $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $validade = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($validade >= $today){

                    $this->data['dtValidade'] = $validade->format('Y-m-d H:i:s');

                    return true;
                }
               
               throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Parãmetro inválido<br/>');
    }

    public function getQtdVendida():int
    {
        if((!isset($this->qtdVendida)) || ($this->qtdVendida < 0)){

            if(isset($this->data['qtdVendida']) && ($this->data['qtdVendida'] >= 0)){
                return $this->data['qtdVendida'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->qtdVendida;
    }

    public function setQtdVendida(Int $qtd):bool
    {
        if((isset($qtd)) && ($qtd > 0)){

            $this->data['qtdVendida'] = $qtd;
            return true;
        }
       throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function getQtdFornecida():int
    {
        if((!isset($this->qtdFornecida)) || ($this->qtdFornecida <= 0)){

            if(isset($this->data['qtdFornecida']) && ($this->data['qtdFornecida'] > 0)){
                return $this->data['qtdFornecida'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->qtdFornecida;
    }


    public function setQtdFornecida(Int $qtd)
    {
        if($qtd > 0)
        {
            $this->data['qtdFornecida'] = $qtd;
            return true;
        }

        throw new Exception('Parametro inválido<br/>');

    }


    public function getValCompra()
    {
        if((!isset($this->vlCompra)) || ($this->vlCompra <= 0)){

            if(isset($this->data['vlCompra']) && ($this->data['vlCompra'] > 0)){
                return $this->data['vlCompra'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->vlCompra;
    }

    public function setVlCompra(float $val)
    {
        
        if(isset($val) && ($val > 0)){
            
            $this->data['vlCompra'] = $val;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }


    public function getVlVenda()
    {
        if((!isset($this->vlVenda)) || ($this->vlVenda <= 0)){

            if(isset($this->data['vlVenda']) && ($this->data['vlVenda'] > 0)){
                return $this->data['vlVenda'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->vlVenda;
    }


    public function setVlVenda(float $val)
    {
        if(isset($val) && ($val > 0)){
            
            $this->data['vlVenda'] = $val;
            
            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }

    public function getAtivo()
    {

        if((!isset($this->ativo)) || ($this->ativo < 0) || ($this->ativo > 1) ){

            if(isset($this->data['ativo']) && ($this->data['ativo'] >= 0)  && ($this->data['ativo'] <= 1)){
                return $this->data['ativo'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->ativo;
    }


    public function setAtivo(int $ativo = 1)
    {
        
        if(isset($ativo) && ($ativo >=0) && ($ativo <= 1)){

            $this->data['ativo'] = $ativo;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }

    public function setMargem(float $margem)
    {
        if(isset($margem) && ($margem >= 0)){

            $this->margem = $margem;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }


    public function getMargem():float
    {
        if((!isset($this->margem)) || ($this->margem < 0)){

            if(isset($this->data['margem']) && ($this->data['margem'] > 0)){
                return $this->data['margem'];
            }
            
            throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
        }

        return $this->margem;
    }

    public function getUrl()
    {
        if(isset($this->url)){
            return $this->url;
        }
    }

    public function getProduto()
    {
        $prod = new Produto();
        $result = $prod->select(['idProduto','nomeProduto'],
         ['idProduto'=>$this->ProdutoIdProduto], '=','asc', null, null,true);

        if($result == false){
            throw new Exception("Erro ao carregar elemento\n");
            
        }

        return $result;
    }


    public function listarConsultaPersonalizada(String $where = null, Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false)
    {

        $sqlPersonalizada = "SELECT distinct P.nomeProduto As produtoNome, P.textoPromorcional As texto, P.idProduto AS idProduto, F.vlVenda as vlVenda, F.dtValidade, F.dtRecebimento, F.dtRecebimento, F.dtFornecimento , F.nf, F.qtdVendida, F.qtdFornecida, F.idFornecimento";
        $sqlPersonalizada .= " FROM  Fornecimento F inner join Produto P on P.idProduto = F.ProdutoIdProduto";

        if($where != null)
        {
            $sqlPersonalizada .= ' WHERE '.$where;
        }

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sqlPersonalizada .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        } 
        $result = $this->persolizaConsulta($sqlPersonalizada, $clasRetorno);

        return $result;
    }

    public function getProdutoEndCategoria(Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false,Int $idProduto = null)
    {
        $sql = 'select P.nomeProduto As produtoNome ,P.idProduto, P.textoPromorcional As texto, F.vlVenda, F.idFornecimento, Img.url,Img.tipo,F.qtdFornecida, F.qtdVendida, C.nomeCategoria, C.idCategoria from Fornecimento as F inner join Produto as P on F.ProdutoIdProduto = P.idProduto inner join ProdutoCategoria as PG on PG.ProdutoIdproduto = P.idProduto inner join Categoria as C on PG.CategoriaIdCategoria = C.idCategoria inner join Imagem as Img on Img.ProdutoIdProduto = P.idProduto ';
        $sql .=' WHERE F.ativo = 1 and Img.tipo = \'primaria\' and PG.classificCateg = \'primaria\'';

        if(isset($idProduto) && ($idProduto > 0)){
            $sql .=' and PG.ProdutoIdproduto = '.$idProduto;
        }
        
        $sql .=' GROUP by P.idProduto ';

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sql .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        }

        $result = $this->persolizaConsulta($sql, $clasRetorno);

        return $result;
    }


    public function loadFornecimentoForIdProduto(Int $idProduto, $clasRetorno = false)
    {
        if($idProduto > 0){
            
             $sql = 'select PC.CategoriaIdCategoria as idCategoria, P.nomeProduto As produtoNome ,P.idProduto, ';
             $sql .= ' P.altura ,P.largura, P.comprimento, P.textoPromorcional As texto, Img.url, ';
             $sql .= ' F.vlVenda from Fornecimento as F inner join Produto as P on ';
             $sql .= ' F.ProdutoIdProduto = P.idProduto inner join Imagem as Img on Img.ProdutoIdProduto = P.idProduto ';
             $sql .= ' inner join ProdutoCategoria as PC on PC.ProdutoIdProduto = P.idProduto ';
             $sql .=' WHERE F.ativo = 1 and (F.qtdFornecida - F.qtdVendida) > 0 and Img.tipo = \'primaria\' and PC.classificCateg = \'primaria\' and F.ProdutoIdProduto='.$idProduto;

             $result = $this->persolizaConsulta($sql, $clasRetorno);
             if($result != false){
                return $result[0];
             }
             throw new Exception("Erro ao carregar produto.\n");
             
        }
        
    }

    public function loadFornecimentoForIdCategoria(array $inIdCatec, $clasRetorno = false, Int $notProd = null, Int $limInit = null, Int $limEnd = null)
    {
        if(count($inIdCatec) > 0){
             
             $sql = 'SELECT distinct P.nomeProduto As produtoNome, Img.url ,P.idProduto, P.textoPromorcional As texto, F.vlVenda FROM Fornecimento as F inner join Produto as P on F.ProdutoIdProduto = P.idProduto';
             $sql .= ' inner join ProdutoCategoria as PC on PC.ProdutoIdproduto = P.idProduto';
             $sql .= ' inner join Imagem as Img on Img.ProdutoIdProduto = P.idProduto';
             $sql .= ' WHERE (F.ativo = 1) and ((F.qtdFornecida - F.qtdVendida) > 0) and (Img.tipo = \'primaria\')';
             $sql .= ' and (PC.classificCateg <> \'secundaria\') ';

             $in = implode(',', $inIdCatec);

             $sql .=' and PC.CategoriaIdCategoria in ('.$in.')';

             if($notProd != null){
                $sql .= ' and F.ProdutoIdproduto <> '.$notProd;
             }
             
             if(($limInit != null) && ($limEnd != null)){
                $sql .= ' LIMIT '.$limInit.', '.$limEnd;
             }

             $result = $this->persolizaConsulta($sql, $clasRetorno);
             if($result != false){
                return $result;
             }
             return false;
             
        }
        
    }

    public function countPersonalizado(array $inIdCatec, $clasRetorno = false, Int $notProd = null, Int $limInit = null, Int $limEnd = null)
    {
        if(count($inIdCatec) > 0){
             
             $sql = 'SELECT distinct count(F.idFornecimento) total FROM Fornecimento as F inner join Produto as P on F.ProdutoIdProduto = P.idProduto';
             $sql .= ' inner join ProdutoCategoria as PC on PC.ProdutoIdproduto = P.idProduto';
             $sql .= ' inner join Imagem as Img on Img.ProdutoIdProduto = P.idProduto';
             $sql .= ' WHERE (F.ativo = 1) and ((F.qtdFornecida - F.qtdVendida) > 0) and (Img.tipo = \'primaria\')';
             $sql .= ' and (PC.classificCateg <> \'secundaria\') ';

             $in = implode(',', $inIdCatec);

             $sql .=' and PC.CategoriaIdCategoria in ('.$in.')';

             if($notProd != null){
                $sql .= ' and F.ProdutoIdproduto <> '.$notProd;
             }
             
             if(($limInit != null) && ($limEnd != null)){
                $sql .= ' LIMIT '.$limInit.', '.$limEnd;
             }

             $result = $this->persolizaConsulta($sql, $clasRetorno);
             if($result != false){
                return $result[0]->total;
             }
             throw new Exception("Erro ao carregar produto.\n");
             
        }
        
    }


    public function listarCategoriaFornecimento(Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false)
    {
        $sql = 'SELECT C.nomeCategoria, C.idCategoria
                FROM Fornecimento as F inner join Produto as P on F.ProdutoIdProduto = P.idProduto
                inner join ProdutoCategoria as PG on PG.ProdutoIdproduto = P.idProduto
                inner join Categoria as C on PG.CategoriaIdCategoria = C.idCategoria
                WHERE (F.ativo = 1) and (F.qtdFornecida - F.qtdVendida) > 0
                GROUP by C.idCategoria';

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sql .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        }

        $result = $this->persolizaConsulta($sql, $clasRetorno);

        return $result;
    }



    public function loadFornecimento($dados, $like = true)
    {   
        if(is_array($dados)){
           if($dados[0]=='cod'){
                $dados = $dados[1];
            }else{
                $dados = $dados[0];//falta terminar de implementar
            }
        }

        $length =(int) strlen($dados);


        if($length > 0){

            $result =false;

            if($like){
                $dados = '%'.$dados.'%';
                $dados = $this->satinizar($dados, true);
                $result = $this->listarConsultaPersonalizada('F.ativo = 1 and P.nomeProduto LIKE '.$dados, NULL, NULL, true);
            }else{
                $result = $this->listarConsultaPersonalizada();
            }

            return $result;
        }
        throw new Exception('Parâmetro inválido<br/>'.PHP_EOL);
    }
    
    public function monitoraEstoque()
    {
            $where = 'F.ativo=1 and (F.qtdFornecida - F.qtdVendida <= '.self::ESTOQUEBAIXO.')';

            $result = $this->listarConsultaPersonalizada($where,NULL, NULL, true);

            $arrItens = ['qtdItens' =>count($result)];

            $arrItens['itens'] = [];
            for ($i=0; !($i == count($result)) ; $i++) { 
                $subArr = [
                    'idFornecimento'    =>  $result[$i]->getIdFornecimento(),
                    'qtdFornecida'      =>  $result[$i]->getQtdFornecida(),
                    'qtdVendida'        =>  $result[$i]->getQtdVendida(),
                    'idProduto'         =>  $result[$i]->getProdutoIdProduto(),
                    'produtoNome'       =>  $result[$i]->getProdutoNome()

                        ];
                $arrItens['itens'][] = $subArr;
            }

            return $arrItens;
    }

    public function getDetalhesPedido()
    {
        $detalhesPedido = new DetalhesPedido();

        $restult = $detalhesPedido->select(
            ['idDetalhesPedido','PedidoIdPedido', 'qtd', 'precoUnitPratic','vlDescontoUnit',
            'dataHoraPedido', 'UsuarioIdUsuario', 'FornecimentoIdFornecimento']
            , [$this->idFornecimento =>'FornecimentoIdFornecimento']
            , '=', 'asc', null, null, true, false);

        if($restult == false){
            throw new Exception("Erro ao carregar elemento");
            
        }
        return $restult;
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