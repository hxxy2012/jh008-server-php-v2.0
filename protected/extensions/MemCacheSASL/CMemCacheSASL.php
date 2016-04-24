<?php
/**
 
 */
class CMemCacheSASL extends CCache
{
	/**
	 * @var $_cache the MemcacheSASL instance
	 */
	private $_cache=null;
	/**
	 * @var $_server CMemCacheSASLServerConfiguration
	 */
	private $_server=null;

	/**
	 * @var $server array server configuration
	 */
	public $server = array();

	/**
	 * Initializes this application component.
	 * This method is required by the {@link IApplicationComponent} interface.
	 * It creates the memcacheSASL instance and adds memcache servers.
	 */
	public function init()
	{
		include('MemcacheSASL.php');
		parent::init();
		$server = $this->getServer();
		$cache = $this->getMemCache();
		if($server){
			$cache->addServer($server->host, $server->port);
			$cache->setSaslAuthData($server->username, $server->password);
		}
	}

	/**
	 */
	public function getMemCache()
	{
		if($this->_cache===null)
			$this->_cache = new MemcacheSASL;
			
		return $this->_cache;
	}

	/**
	 * @return element {@link CMemCacheServerConfiguration}.
	 */
	public function getServer()
	{
		if($this->_server === null)
			$this->setServer();
		return $this->_server;
	}

	/**
	 * 
	 */
	public function setServer()
	{
		$this->_server = new CMemCacheSASLServerConfiguration($this->server);
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string $key a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key)
	{
		return $this->_cache->get($key);
	}

	/**
	 * Retrieves multiple values from cache with the specified keys.
	 * @param array $keys a list of keys identifying the cached values
	 * @return array a list of cached values indexed by the keys
	 */
	protected function getValues($keys)
	{
		return $this->_cache->getMulti($keys);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string $key the key identifying the value to be cached
	 * @param string $value the value to be cached
	 * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key, $value, $expire)
	{
		if($expire>0)
			$expire+=time();
		else
			$expire=0;

		return $this->_cache->set($key, $value, $expire);
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string $key the key identifying the value to be cached
	 * @param string $value the value to be cached
	 * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key, $value, $expire)
	{
		if($expire>0)
			$expire+=time();
		else
			$expire=0;

		return $this->_cache->add($key, $value, $expire);
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string $key the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key)
	{
		return $this->_cache->delete($key);
	}

	/**
	 * Deletes all values from cache.
	 * This is the implementation of the method declared in the parent class.
	 * @return boolean whether the flush operation was successful.
	 * @since 1.1.5
	 */
	protected function flushValues()
	{
		return $this->_cache->flush();
	}
}

/**
 * CMemCacheServerConfiguration represents the configuration data for a single memcacheSASL server.
 */
class CMemCacheSASLServerConfiguration extends CComponent
{
	/**
	 * @var string memcache server hostname or IP address
	 */
	public $host;
	/**
	 * @var string memcache server username
	 */
	public $username;
	/**
	 * @var string memcache server password
	 */
	public $password;
	/**
	 * @var integer memcache server port
	 */
	public $port=11211;

	/**
	 * Constructor.
	 * @param array $config list of memcache server configurations.
	 * @throws CException if the configuration is not an array
	 */
	public function __construct($config)
	{
		if(is_array($config))
		{
			foreach($config as $key=>$value)
				$this->$key=$value;
			
			if($this->host===null)
				throw new CException(Yii::t('yii','CMemCacheSASL server configuration must have "host" value.'));
			if($this->username===null)
				throw new CException(Yii::t('yii','CMemCacheSASL server configuration must have "username" value.'));
			if($this->password===null)
				throw new CException(Yii::t('yii','CMemCacheSASL server configuration must have "password" value.'));
		}
		else
			throw new CException(Yii::t('yii','CMemCacheSASL server configuration must be an array.'));
	}
}
