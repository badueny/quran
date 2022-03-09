<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController {    
  
	function __construct(){
        parent:: __construct();        
        date_default_timezone_set("Asia/Jakarta"); 
    }

    function surat_get()
    {        
        $verify = parent::HTTP_OK;
        $no = cleanInputGet('no');
        if($no!=null ){
            $surat = Query::getDataSelectWhere('surat','*',array('noSurat' => $no))->row();
        }else{
            $surat = Query::getDataSelect('surat','*')->result();
        }
        $respon['results']=$surat;
        $respon['status']=$verify;              
        $this->response($respon);
    }
    
    //=========        
}