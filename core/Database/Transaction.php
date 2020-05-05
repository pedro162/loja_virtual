<?php

namespace Core\Database;

use Core\Database\Connection;

abstract class Transaction
{
	protected static $conexao;

	public static function startTransaction(String $database):bool
	{
		if(empty(self::$conexao))
		{
			self::$conexao = Connection::open($database);

			self::$conexao->beginTransaction(); // inicia a transação

		}
		
		return true;
	}

	public static function get()
	{
		if(!empty(self::$conexao))
		{
			return self::$conexao;
		}
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