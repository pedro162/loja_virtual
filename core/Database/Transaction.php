<?php

namespace Core\Database;

abstract class Transaction
{
	protected static $conexao;

	public static function startTransaction(\PDO $conn):bool
	{
		var_dump($conn);
		if(empty(self::$conexao))
		{
			self::$conexao = $conn;
		}
		
		self::$conexao->beginTransaction(); // inicia a transação
		return true;
	}


	public static function rollback()
	{
		if(empty(self::$conexao))
		{
			throw new \Exception("Não existe conexão aberta<br/>\n");
		}

		self::$conexao->rollback();
		self::$conexao = null;
	}

	public static function close()
	{
		if(empty(self::$conexao))
		{
			throw new \Exception("Não existe conexão aberta<br/>\n");
		}

		self::$conexao->commit();
		self::$conexao = null;
	}
}