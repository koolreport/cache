<?php

namespace koolreport\cache;
use \koolreport\core\Utility;
trait FileCache
{
    private $_fileKeyCache;
    public function __constructFileCache()
    {
        $this->registerEvent("OnInitDone",function(){
            $this->_fileKeyCache = md5(Utility::getClassPath($this).json_encode($this->params));

            $settings = array();
            if(method_exists($this,"cacheSettings"))
            {
                $settings = $this->cacheSettings();
            }
            $ttl = Utility::get($settings,"ttl",5*60);

            $filepath = sys_get_temp_dir()."/".$this->_fileKeyCache;
            if(file_exists($filepath))
            {
                if(time()-filemtime($filepath)<$ttl)
                {
                    
                    $this->registerEvent("OnBeforeRun",function(){
                        // Load data here
                        $filepath = sys_get_temp_dir()."/".$this->_fileKeyCache;
                        
                        $data = json_decode(gzuncompress(file_get_contents($filepath)),true);
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
                    return;
                }
            }
            $this->registerEvent("OnRunEnd",function(){
                //Save data here
                $filepath = sys_get_temp_dir()."/".$this->_fileKeyCache;
                $data = array();
                foreach($this->dataStores as $name=>$dataStore)
                {
                    $data[$name] = array(
                        "meta"=>$dataStore->meta(),
                        "data"=>$dataStore->data(),
                    );
                }
                file_put_contents($filepath,gzcompress(json_encode($data)));                
            });
        });
    }
}