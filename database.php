<?php
	class Database
	{
		private static $dbName = 'u347279731_verano_guzmadb' ;
		private static $dbHost = 'localhost' ;
		private static $dbUsername = 'u347279731_verano_guzma';  // Default Laragon MySQL username
		private static $dbUserPassword = 'Project1';  // Default Laragon MySQL password (empty)
		 
		private static $cont  = null;
		 
		public function __construct() {
			die('Init function is not allowed');
		}
		 
		public static function connect()
		{
		   // One connection through whole application
		   if ( null == self::$cont )
		   {     
			try
			{
			  self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); 
			}
			catch(PDOException $e)
			{
			  die($e->getMessage()); 
			}
		   }
		   return self::$cont;
		}
		 
		public static function disconnect()
		{
			self::$cont = null;
		}
	}
?>
