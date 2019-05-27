<?php
/**
 * This file contains ApcCache class
 *
 * @author KoolPHP Inc (support@koolphp.net)
 * @link https://www.koolphp.net
 * @copyright KoolPHP Inc
 * @license https://www.koolreport.com/license
 */

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
                apc_add($this->_apcKeyCache,$data,$ttl);
            });
        }
    }
}