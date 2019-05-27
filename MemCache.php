<?php
/**
 * This file contains MemCache class
 *
 * @author KoolPHP Inc (support@koolphp.net)
 * @link https://www.koolphp.net
 * @copyright KoolPHP Inc
 * @license https://www.koolreport.com/license
 */
namespace koolreport\cache;
use \koolreport\core\Utility;

trait MemCache
{
    private $_memKeyCache;
    private $_memCache;
    public function __constructMemCache()
    {
        $this->_memKeyCache = md5(Utility::getClassPath($this).json_encode($this->params));
        $settings = array();
        if(method_exists($this,"cacheSettings"))
        {
            $settings = $this->cacheSettings();
        }
        $ttl = Utility::get($settings,"ttl",5*60);
        $servers = Utility::get($servers,"servers");
        if($servers==null)
        {
            throw new \Exception("Please added servers");
        }
        
        $this->_memCache = new \Memcached();
        foreach($servers as $host=>$post)
        {
            $this->_memCache->addServer($host,$port);
        }
        if($this->_memCache->get($this->_memKeyCache))
        {
            $this->registerEvent("OnBeforeRun",function(){
                $data = $this->_memCache->get($this->_memKeyCache);
                foreach($this->dataStores as $name=>&$dataStore)
                {
                    if(isset($data[$name]))
                    {
                        $dataStore->meta($data[$name]["meta"]);
                        $dataStore->data($data[$name]["data"]);
                        // This to avoid widget to initiate requestDataSending()
                        $dataStore->setEnded(true);

                    }
                }
                return false;
            });
        }
        else
        {
            $this->registerEvent("OnRunEnd",function(){
                $settings = array();
                if(method_exists($this,"cacheSettings"))
                {
                    $settings = $this->cacheSettings();
                }
                $ttl = Utility::get($settings,"ttl",5*60);
                $data = array();
                foreach($this->dataStores as $name=>$dataStore)
                {
                    $data[$name] = array(
                        "meta"=>$dataStore->meta(),
                        "data"=>$dataStore->data(),
                    );
                }
                $this->_memCache->add($this->_memKeyCache,$data,$ttl);
            });
        }
    }
}