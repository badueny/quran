<?php

defined('BASEPATH') or exit('No direct script access allowed');

class GeneralModel extends CI_Model
{
    
    
    //Migration===
    function createDatabase($dbName)
    {
        $this->load->dbforge();        
        return $this->dbforge->create_database($dbName,TRUE);
    }

    function dropDatabase($dbName)
    {
        $this->load->dbforge();
        return $this->dbforge->drop_database($dbName);
    }

    function createTable($tableName,$arrayField,$keyId)
    {
        $this->load->dbforge();
        return $this->dbforge->add_field($arrayField)->add_key($keyId, true)->create_table($tableName,TRUE);
    }

    function renameTable($oldTable,$newTable)
    {
        $this->load->dbforge();
        return $this->dbforge->rename_table($oldTable,$newTable);
    }

    function dropTable($tableName)
    {
        $this->load->dbforge();
        return $this->dbforge->drop_table($tableName,TRUE);
    }
    //==========
    
    function dbInfo()
    {
        //$version = $this->db->version();
        //$platform = $this->db->platform();
        return $this->db->call_function('get_client_info');//'Flatform: '.$flatform.'<br>'.'Version: '.$version;
    }
    //QuerySql
    function querySql($query)
    {
        return $this->db->query($query);
    }
    //======

    //Datatable
    private function searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {
        $this->db->from($tableName);
        $i = 0;
        foreach ($searchPola as $item) 
        {
            if($this->input->post('search')['value'])
            {
                if($i===0) 
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $this->input->post('search')['value']);
                }
                else
                {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }
                if(count($searchPola) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
        
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($orderTable))
        {
            $order = $orderTable;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        $customFilter!='' ? $this->db->where($customFilter) : '';
    }    
    
    function getTableData($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($this->input->post('length') != -1)
        $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query;
    }
    
    function countFilterTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function countAllTableData($tableName,$customFilter)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $customFilter!='' ? $this->db->where($customFilter) : '';
        $this->db->from($tableName);
        return $this->db->count_all_results();
    }
    
    //Datatable Select
    function getTableDataSelect($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($this->input->post('length') != -1)
        $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query;
    }
    
    function countFilterTableSelect($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function countAllTableDataSelect($tableName,$customFilter,$select)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $customFilter!='' ? $this->db->where($customFilter) : '';
        $this->db->from($tableName);
        return $this->db->count_all_results();
    }
    //=============
           
    //Datatable Join
    function getTableDataSelectJoin($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select,$joinTabel)
    {
        $jumJoint = count($joinTabel);
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($joinTabel!='' AND $jumJoint==1){
                $this->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
            foreach ($joinTabel as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        $maxLimit= ($this->input->post('length')>100 OR $this->input->post('length')=='')  ? 100 : $this->input->post('length');
        if($this->input->post('length') != -1)
        $this->db->limit($maxLimit, $this->input->post('start'));
        $query = $this->db->get();
        return $query;
    }
    
    function countFilterTableSelectJoin($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select,$joinTabel)
    {
        $jumJoint = count($joinTabel);
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $this->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($joinTabel!='' AND $jumJoint==1){
                $this->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
             foreach ($joinTabel as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function countAllTableDataSelectJoin($tableName,$customFilter,$select,$joinTabel)
    {
        $jumJoint = count($joinTabel);
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select);
        $customFilter!='' ? $this->db->where($customFilter) : '';
        if($joinTabel!='' AND $jumJoint==1){
                $this->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
             foreach ($joinTabel as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        $this->db->from($tableName);
        return $this->db->count_all_results();
    }
    //============

      
        
    //CountData
    function countAllData($tableName,$select)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->select($select)->group_by($select)->get($tableName)->num_rows();
    }

    function countWhereData($tableName,$query,$select)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->select($select)->where($query)->get($tableName)->num_rows();
    }
    //=============

    //CRUD
    function saveData($tableName,$data)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->insert($tableName, $data);
    }
    
    function saveDataDelete($tableName,$query,$data)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->where($query)->delete($tableName);
        return $this->db->insert($tableName, $data);
    }

    function updateDataById($tableName,$where,$update)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->where($where)->update($tableName, $update);
    }

    function updateBatch($tableName,$data,$id)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->update_batch($tableName, $data, $id);
    }
    
    function delDataById($tableName,$where)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->where($where)->delete($tableName);
    }

    function insertBatchData($tableName,$data)
    {
        $this->db->protect_identifiers($tableName, TRUE);       
        return $this->db->insert_batch($tableName,$data); 
    }  
    
           
    function insertBatchDeleteData($tableName,$query,$data)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $cek = $this->db->where($query)->get($tableName)->num_rows(); 
        if($cek>0){
            return false;
        }else{
           return $this->db->insert_batch($tableName,$data); 
        }
        
    }  

    function truncateTable($tableName)
    {
        return $this->db->truncate($tableName);
    }
    //======

    //Get Data Select
    function getDataSelect($tableName,$select)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         return $this->db->select($select)->get($tableName); 
    }
    
    //Get Data Join Limit
    function getLastDataWhereSelectJoin($tableName,$select,$where,$order,$join,$limit)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         $this->db->select($select)->where($where);
         if($join!='' AND count($join)==1){
                $this->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        if($order!=''){ $this->db->order_by(key($order), $order[key($order)]); }
         return $this->db->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where
    function getDataSelectWhere($tableName,$select,$query)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         return $this->db->where($query)->select($select)->get($tableName); 
    }
    
    //Get Data Select Where Order Join
    function getDataSelectWhereOrderJoin($tableName,$select,$query,$order,$join)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         $this->db->where($query)->select($select);
         if($join!='' AND count($join)==1){
                $this->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        if($order!=''){ $this->db->order_by(key($order), $order[key($order)]); }
         return $this->db->get($tableName); 
    }

    //Get Data Select Where Order Join Limit
    function getDataSelectWhereOrderJoinLimit($tableName,$select,$query,$order,$join,$limit)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         $this->db->where($query)->select($select);
         if($join!='' AND count($join)==1){
                $this->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
         return $this->db->order_by(key($order), $order[key($order)])->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where OR Where Order Join Limit
    function getDataSelectWhereOrWhereJoinLimit($tableName,$select,$where,$orWhere,$join,$limit)
    {
         $this->db->protect_identifiers($tableName, TRUE);
         $this->db->select($select)->where($where)->or_where($orWhere);
         if($join!='' AND count($join)==1){
                $this->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
         return $this->db->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where Order
    function getDataSelectWhereOrder($tableName,$select,$query,$order)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select)->where($query);
        return $this->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }

    //Get Data Select Where Group
    function getDataSelectWhereGroup($tableName,$select,$query,$group)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->select($select)->where($query);
        return $this->db->group_by($group)->get($tableName); 
    }
    
    //Get Data Where Order
    function getDataWhereOrder($tableName,$where,$order)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->where($where)->order_by(key($order), $order[key($order)])->get($tableName); 
    }
    
    //Get Data Order
    function getDataOrder($tableName,$order)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        return $this->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }

    //Get Data Order Join
    function getDataOrderJoin($tableName,$where,$order,$join)
    {
        $this->db->protect_identifiers($tableName, TRUE);
        $this->db->where($where);
        if($join!='' AND count($join)==1){
                $this->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $this->db->join($key, $value); 
            }
        }
        return $this->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }

    function getUnitKerja()
    {
        return $this->db->where('induk_unit', NULL)->order_by('nama_unit','Asc')->get('tbl_unit_kerja');
    }

    function getNamaUnitKerja($id)
    {
        return $this->db->select('nama_unit')->where('id_unit', $id)->get('tbl_unit_kerja')->row('nama_unit');
    }

    function getNamaIndukUnitKerja($idbidang)
    {
        return $this->db->select('nama_unit')->where('id_unit', $idbidang)->get('tbl_unit_kerja')->row('nama_unit');
    }
    
    function getSubUnitKerja($idbidang)
    {
        return $this->db->where('induk_unit', $idbidang)->order_by('nama_unit','Asc')->get('tbl_unit_kerja');
    }

    function delUnitKerja($id,$type)
    {
    	if($type=='2'){
    		$this->db->where('id_unit', $id)->delete('tbl_unit_kerja');
    		return $this->db->where('induk_unit', $id)->delete('tbl_unit_kerja');
    	}else{
    		return $this->db->where('id_unit', $id)->delete('tbl_unit_kerja');
    	}  
    }
    
        


}
?>