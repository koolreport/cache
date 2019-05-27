<?php

namespace koolreport\cache;
use \koolreport\core\Utility;

trait ApcCache
{
    protected $_apcKeyCache;
    public function __constructApcCache()
    {
        $this->_apcKeyCache = md5(Utility::getClassPath($this).json_encode($this->params));
        if(apc_exists($this->_apcKeyCache))
        {
            $this->registerEvent("OnBeforeRun",function(){
                $data = apc_fetch($this->_apcKeyCache);
                foreach($this->dataStores as $name=>&$dataStore)
                {
                    if(isset($data[$name]))
                    {
                        $dataStore->meta($data[$name]["meta"]);
                        $dataStore->data($data[$name]["data"]);
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
                apc_add($this->_apcKeyCache,$data,$ttl);
            });
        }
    }
}