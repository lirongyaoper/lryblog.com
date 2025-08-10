<?php
class cache_file{
    protected $config = array();

    public function __construct($config){
        $this->config = array(
            'cache_dir' => RYPHP_ROOT.'cache' . DIRECTORY_SEPARATOR.'cache_file' . DIRECTORY_SEPARATOR,
            'suffix' => '.cache.php',
            'mode' => '1',
        );
        if(!empty($config)){
            $this->config = array_merge($this-> config,$config);
        }
    }

    protected function _filenametoid($filename){
        return str_replace($this->config['suffix'],'',$filename);
    }

    protected function _idtofilename($id){
        return $id.$this->config['suffix'];
    }

    protected function _file($id){
        $filename = $this->_idtofilename($id);
        return $this->config['cache_dir'] . $filename;
    }

    public function has($id){
        $file = $this ->_file($id);
        if(!is_file($file)){
            return false;
        }
        return true;
    }

    public function get($id){
         if(!$this->has($id)){
            return false;
         }
         $file = $this->_file($id);
         $data = $this -> _filegetcontents($file);
         if($data['expire'] == 0 || SYS_TIME < $data['expire']){
            return $data['contents'];
         }
         return false;
    }

    public function set($id, $data,$cachelife = 0){
        $cache = array();
        $cache['contents'] = $data;
        $cache['expire']  = $cachelife === 0 ? 0 : SYS_TIME +  $cachelife;
        $cache['mtime'] = SYS_TIME;
        if(!is_dir($this ->config['cache_dir'])){
            @mkdir($this->config['cache_dir'],0777,true);
        }
        $file = $this-> _file($id);
        return $this ->_fileputcontents($file, $cache);
    }

    protected function _filegetcontents($file){
        if(!file_exists($file)){
            return false;
        }

        if($this->config['mode'] == 1){
            $handle = @fopen($file, 'r');
            fgets($handle);
            return unserialize(fgets($handle));
        }else{
            return @require $file;
        }
    }

    protected function _fileputcontents($file, $contents){
        if(!is_file($file)) touch($file) && @chmod($file, 0777);
        if($this ->config['mode'] ==1){
            $contents = "<?php  exit('NO.'); ?>".serialize($contents);
        }else{
            $contents = "<? php \n return ".var_export($contents,true). "\n?>";
        }
        $filesize = file_put_contents($file, $contents,LOCK_EX);
        return $filesize ? $filesize : false;
    }
    public function delete($id){
        if($this -> has($id)){
            return false;
        }
        $file = $this -> _file($id);
        return unlink($file);
    }




    public function flush(){
        $glob = glob($this->config['cache_dir'].'*'.$this->config['suffix']);
        if(empty($glob)) return false;
        foreach ($glob as $v){
            $id = $this -> _filenametoid(basename($v));
            $this ->delete($id);
            
        }
        return true;
    }
}