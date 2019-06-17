<?php
class menu extends api{
    /**
    * 创建菜单
     */
    public function create_menu($data){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        if(gettype($data)=="array"){
            $this->data = json_encode(array("button"=>$data),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }else{
            $this->data = $data;
        }
        return $this->post();
    }

    /**
    * 删除菜单
     */
    public function remenu(){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=$access_token";
        return $this->get();
    }

}
?>