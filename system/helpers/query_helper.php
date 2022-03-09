<?php

class Query 
{    
    //Migration===
    public static function createDatabase($dbName)
    {   
        $CI = getInstance();
        $CI->load->dbforge();        
        return $CI->dbforge->create_database($dbName,TRUE);
    }

    /*
    ALL PRIVILEGES : The user account has full access to the database
    INSERT : The user can insert rows into tables
    DELETE : The user can remove rows from tables
    CREATE : The user can create entirely new tables and databases
    DROP : The user can drop (remove) entire tables and databases
    SELECT : The user gets access to the select command, to read the information in the databases
    UPDATE : The user can update table rows
    GRANT OPTION : The user can modify other user account privileges
    */

    public static function createUserDb($dbName,$userName,$Privilages)
    {
        $CI = getInstance();
        $createUser =  $CI->db->query("CREATE USER '$userName'@'localhost' IDENTIFIED BY 'password'");
        $CI->db->query("GRANT $Privilages ON $dbName.* TO '$userName'@'localhost'");
        return  $CI->db->query("FLUSH PRIVILEGES");
    }
    
    public static function grantUserDbPrivilages($dbName,$userName,$Privilages)
    {
        $CI = getInstance();
        $CI->db->query("GRANT $Privilages ON $dbName.* TO '$userName'@'localhost'");
        return  $CI->db->query("FLUSH PRIVILEGES");
    }

    public static function revokeUserDbPrivilages($dbName,$userName,$Privilages)
    {
        $CI = getInstance();
        $CI->db->query("REVOKE $Privilages ON $dbName.* FROM '$userName'@'localhost'");
        return  $CI->db->query("FLUSH PRIVILEGES");
    }  

    public static function dropUserDb($userName)
    {
        $CI = getInstance();
        return  $CI->db->query("DROP USER '$userName'@'localhost'");
    }

    


    public static function dropDatabase($dbName)
    {   
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->dbforge->drop_database($dbName);
    }

    public static function backupDatabase($pathBackUp,$fileName)
    {        
        $CI = getInstance();
        $namaFile = $fileName.'-'.time();
        $prefs = array(
            'tables'        => array(), 
            'ignore'        => array(), 
            'format'        => 'zip',  
            'filename'      => $namaFile.'.sql', 
            'add_drop'      => TRUE, 
            'add_insert'    => TRUE, 
            'newline'       => "\n"
        );
        $CI->load->dbutil();
        $backup = $CI->dbutil->backup($prefs);
        $CI->load->helper('file');
        write_file($pathBackUp.'/'.$namaFile.'.zip', $backup);
    }

    public static function optimizeDatabase()
    {
        $CI = getInstance();
        $CI->load->dbutil();
        return $CI->dbutil->optimize_database();
    }

    public static function exportTableToCsv($query)
    {        
        $CI = getInstance();
        $CI->load->dbutil();
        $data = $CI->db->query($query);
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = '"';
        return $CI->dbutil->csv_from_result($data, $delimiter, $newline, $enclosure);
    }

    public static function optimizeTable($tableName)
    {
        $CI = getInstance();
        $CI->load->dbutil();
        return $CI->dbutil->optimize_table($tableName);
    }

    public static function createTable($tableName,$arrayField,$keyId)
    {   
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->dbforge->add_field($arrayField)->add_key($keyId, true)->create_table($tableName,TRUE);
    }

    public static function addSingleIndexTable($tableName,$indexName,$ColumnName)
    {
        $CI = getInstance();
        $CI->load->dbforge();
        $sql = "CREATE INDEX $indexName ON $tableName($ColumnName)";
        return $CI->db->query($sql);
    }

    public static function addMultipleIndexTable($tableName,$multipleColumnName)
    {
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->db->query("ALTER TABLE $tableName ADD INDEX($multipleColumnName)");
    }

    public static function addColumn($tableName,$Column,$After)
    {        
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->dbforge->add_column($tableName, $Column,$After);
    }

    public static function renameTable($oldTable,$newTable)
    {   
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->dbforge->rename_table($oldTable,$newTable);
    }

    public static function dropTable($tableName)
    {   
        $CI = getInstance();
        $CI->load->dbforge();
        return $CI->dbforge->drop_table($tableName,TRUE);
    }
    //==========

    
    //QuerySql
    public static function querySql($query)
    {   
        $CI = getInstance();
        return $CI->db->query($query);
    }
    //======

    //Datatable
    private function searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {   $CI = getInstance();
        $CI->db->from($tableName);
        $i = 0;
        foreach ($searchPola as $item) 
        {   $CI = getInstance();
            if(cleanInputPost('search')['value'])
            {   $CI = getInstance();
                if($i===0) 
                {   $CI = getInstance();
                    $CI->db->group_start(); 
                    $CI->db->like($item, cleanInputPost('search')['value']);
                }
                else
                {   $CI = getInstance();
                    $CI->db->or_like($item, cleanInputPost('search')['value']);
                }
                if(count($searchPola) - 1 == $i) 
                    $CI->db->group_end(); 
            }
            $i++;
        }
        
        if(isset($_POST['order'])) 
        {   $CI = getInstance();
            $CI->db->order_by($columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($orderTable))
        {   $CI = getInstance();
            $order = $orderTable;
            $CI->db->order_by(key($order), $order[key($order)]);
        }
        $customFilter!='' ? $CI->db->where($customFilter) : '';
    }    
    
    public static function getTableData($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->generalModel->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if(cleanInputPost('length') != -1)
        $CI->db->limit(cleanInputPost('length'), cleanInputPost('start'));
        $query = $CI->db->get();
        return $query;
    }
    
    public static function countFilterTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        $query = $CI->db->get();
        return $query->num_rows();
    }
 
    public static function countAllTableData($tableName,$customFilter)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $customFilter!='' ? $CI->db->where($customFilter) : '';
        $CI->db->from($tableName);
        return $CI->db->count_all_results();
    }
    
    //Datatable Select
    public static function getTableDataSelect($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $CI->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if(cleanInputPost('length') != -1)
        $CI->db->limit(cleanInputPost('length'), cleanInputPost('start'));
        $query = $CI->db->get();
        return $query;
    }
    
    public static function countFilterTableSelect($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $CI->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        $query = $CI->db->get();
        return $query->num_rows();
    }
 
    public static function countAllTableDataSelect($tableName,$customFilter,$select)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $customFilter!='' ? $CI->db->where($customFilter) : '';
        $CI->db->from($tableName);
        return $CI->db->count_all_results();
    }
    //=============
           
    //Datatable Join
    public static function getTableDataSelectJoin($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select,$joinTabel)
    {   $CI = getInstance();
        $jumJoint = count($joinTabel);
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $CI->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($joinTabel!='' AND $jumJoint==1){
                $CI->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
            foreach ($joinTabel as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        $maxLimit= (cleanInputPost('length')>100 OR cleanInputPost('length')=='')  ? 100 : cleanInputPost('length');
        if(cleanInputPost('length') != -1)
        $CI->db->limit($maxLimit, cleanInputPost('start'));
        $query = $CI->db->get();
        return $query;
    }
    
    public static function countFilterTableSelectJoin($tableName,$orderTable,$columnOrder,$searchPola,$customFilter,$select,$joinTabel)
    {   $CI = getInstance();
        $jumJoint = count($joinTabel);
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $CI->searchDataTable($tableName,$orderTable,$columnOrder,$searchPola,$customFilter);
        if($joinTabel!='' AND $jumJoint==1){
                $CI->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
             foreach ($joinTabel as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        $query = $CI->db->get();
        return $query->num_rows();
    }
 
    public static function countAllTableDataSelectJoin($tableName,$customFilter,$select,$joinTabel)
    {   $CI = getInstance();
        $jumJoint = count($joinTabel);
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select);
        $customFilter!='' ? $CI->db->where($customFilter) : '';
        if($joinTabel!='' AND $jumJoint==1){
                $CI->db->join(key($joinTabel), $joinTabel[key($joinTabel)]);

        }elseif($jumJoint>1){
             foreach ($joinTabel as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        $CI->db->from($tableName);
        return $CI->db->count_all_results();
    }
    //============

      
        
    //CountData
    public static function countAllData($tableName,$select)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->select($select)->group_by($select)->get($tableName)->num_rows();
    }

    public static function countWhereData($tableName,$query,$select)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->select($select)->where($query)->get($tableName)->num_rows();
    }
    //=============

    //CRUD
    public static function saveData($tableName,$data)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->insert($tableName, $data);
    }
    
    public static function saveDataDelete($tableName,$query,$data)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->where($query)->delete($tableName);
        return $CI->db->insert($tableName, $data);
    }

    public static function updateDataById($tableName,$where,$update)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->where($where)->update($tableName, $update);
    }

    public static function updateBatch($tableName,$data,$id)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->update_batch($tableName, $data, $id);
    }
    
    public static function delDataById($tableName,$where)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->where($where)->delete($tableName);
    }

    public static function insertBatchData($tableName,$data)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);       
        return $CI->db->insert_batch($tableName,$data); 
    }  
    
           
    public static function insertBatchDeleteData($tableName,$query,$data)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $cek = $CI->db->where($query)->get($tableName)->num_rows(); 
        if($cek>0){
            return false;
        }else{
           return $CI->db->insert_batch($tableName,$data); 
        }
        
    }  

    public static function truncateTable($tableName)
    {   $CI = getInstance();
        return $CI->db->truncate($tableName);
    }
    //======

    //Get Data Select
    public static function getDataSelect($tableName,$select)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         return $CI->db->select($select)->get($tableName); 
    }
    
    //Get Data Join Limit
    public static function getDataWhereSelectJoin($tableName,$select,$where,$join)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         $CI->db->select($select)->where($where);
         if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }       
        return $CI->db->get($tableName); 
    }

    public static function getLastDataWhereSelectJoin($tableName,$select,$where,$order,$join,$limit)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         $CI->db->select($select)->where($where);
         if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        if($order!=''){ $CI->db->order_by(key($order), $order[key($order)]); }
         return $CI->db->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where
    public static function getDataSelectWhere($tableName,$select,$query)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         return $CI->db->where($query)->select($select)->get($tableName); 
    }
    
    //Get Data Select Where Order Join
    public static function getDataSelectWhereOrderJoin($tableName,$select,$query,$order,$join)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         $CI->db->where($query)->select($select);
         if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        if($order!=''){ $CI->db->order_by(key($order), $order[key($order)]); }
         return $CI->db->get($tableName); 
    }

    //Get Data Select Where Order Join Limit
    public static function getDataSelectWhereOrderJoinLimit($tableName,$select,$query,$order,$join,$limit)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         $CI->db->where($query)->select($select);
         if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
         return $CI->db->order_by(key($order), $order[key($order)])->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where OR Where Order Join Limit
    public static function getDataSelectWhereOrWhereJoinLimit($tableName,$select,$where,$orWhere,$join,$limit)
    {   $CI = getInstance();
         $CI->db->protect_identifiers($tableName, TRUE);
         $CI->db->select($select)->where($where)->or_where($orWhere);
         if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
         return $CI->db->limit($limit)->get($tableName); 
    }
    
    //Get Data Select Where Order
    public static function getDataSelectWhereOrder($tableName,$select,$query,$order)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select)->where($query);
        return $CI->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }

    //Get Data Select Where Group
    public static function getDataSelectWhereGroup($tableName,$select,$query,$group)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->select($select)->where($query);
        return $CI->db->group_by($group)->get($tableName); 
    }
    
    //Get Data Where Order
    public static function getDataWhereOrder($tableName,$where,$order)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->where($where)->order_by(key($order), $order[key($order)])->get($tableName); 
    }
    
    //Get Data Order
    public static function getDataOrder($tableName,$order)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        return $CI->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }

    //Get Data Order Join
    public static function getDataOrderJoin($tableName,$where,$order,$join)
    {   $CI = getInstance();
        $CI->db->protect_identifiers($tableName, TRUE);
        $CI->db->where($where);
        if($join!='' AND count($join)==1){
                $CI->db->join(key($join), $join[key($join)]);

        }elseif($join!='' AND count($join)>1){
             foreach ($join as $key => $value) {
                $CI->db->join($key, $value); 
            }
        }
        return $CI->db->order_by(key($order), $order[key($order)])->get($tableName); 
    }
}
?>