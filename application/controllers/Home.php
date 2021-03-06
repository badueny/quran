<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    function __construct(){
        parent:: __construct();        
        date_default_timezone_set("Asia/Jakarta"); 
        //$this->output->enable_profiler(TRUE);
    }
	
    public function index()
	{		
        $this->load->view('index');
	}  

    function getListSurat()
    {
        $json = file_get_contents("json/list-surat.json");
        $data = json_decode($json, TRUE);
        $xs = json_encode($data['data']);
        $max = count($data['data']);
        $save=[];
       foreach($data['data'] as $row){
            $x['noSurat'] = $row['number'];
            $x['urutanTurun'] = $row['sequence'];
            $x['jumlahAyat'] = $row['numberOfVerses'];
            $x['namaSingkatArab'] = $row['name']['short'];
            $x['namaPanjangArab'] = $row['name']['long'];
            $x['namaIdn'] = $row['name']['transliteration']['en'];
            $x['namaEng'] = $row['name']['transliteration']['id'];
            $x['terjemahIdn'] = $row['name']['translation']['id'];
            $x['terjemahEng'] = $row['name']['translation']['en'];
            $x['jenisSuratArab'] = $row['revelation']['arab'];
            $x['jenisSuratIdn'] = $row['revelation']['id'];
            $x['jenisSuratEng'] = $row['revelation']['en'];
            $x['tafsirIdn'] = $row['tafsir']['id'];
            $save[] = $x;
        }

        $sim = Query::insertBatchData('surat',$save);

        echo json_encode($sim);
    } 

    function getApiQuran(){
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.quran.sutanlab.id/surah/12',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    function fileGetContent()
    {
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ); 
        //JSON_UNESCAPED_UNICODE decode Arabic
        //JSON_PRETTY_PRINT Prety View
        //JSON_UNESCAPED_SLASHES slah url dari \/ ke /
        $resFile = 0;
        for($i=1;$i<=114;$i++){ 
            $data = file_get_contents('https://api.quran.sutanlab.id/surah/'.$i,false, stream_context_create($arrContextOptions));
            $result = json_decode($data, TRUE);
            $data = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            @file_put_contents('json/surah/'.$i.'.json', $data);
            $resFile++;
        }
        echo $resFile;
    }

    /*
    //GrabImage
    //https://quran.kemenag.go.id/page/598
    function getImage()
    {
            //https://quran.kemenag.go.id/cmsq/source/page/598.jpg
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ); 
            for($i=1;$i<=604;$i++){ 
                $data = file_get_contents('https://quran.kemenag.go.id/cmsq/source/page/'.$i.'.jpg',false, stream_context_create($arrContextOptions));
                @file_put_contents('json/lembar/'.$i.'.jpg', $data);
            }
    }

    
    //GrabAudio
    //https://cdn.alquran.cloud/media/audio/ayah/ar.alafasy/6236
    function getAudio()
    {
            
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ); 
            for($i=5001;$i<=6236;$i++){ 
                $data = file_get_contents('https://cdn.alquran.cloud/media/audio/ayah/ar.alafasy/'.$i,false, stream_context_create($arrContextOptions));
                @file_put_contents('json/audio/'.$i.'.mp3', $data);
            }
    }

    //Link Share
    $url = base_url();
    $namaApp = strtolower('Quran');
    $noSurah = '1';
    $noAyat = '1';
    $arab = '';
    $tafsir = '';
    http://www.facebook.com/sharer.php?u='.$url.'surah/'.$noSurah.'/'.$noAyat;
    https://twitter.com/share?url='.$url.'surah/'.$noSurah.'/'.$noAyat.'&text='.$namaApp.'Q.S '.$noSurah.':'.$noAyat.'&hashtags='.strtolower(str_replace(" ","",$namaApp));
    https://api.whatsapp.com/send?text='.$namaApp.': Q.S '.$noSurah.':'.$noAyat.' '.$arab.' -'.$tafsir.' '.$url.'surah/'.$noSurah.'/'.$noAyat;
     */
    

    function getAyatSurat()
    {
        $json = file_get_contents("json/surah/1.json");
        $data = json_decode($json, TRUE);
        $xs = json_encode($data['data']);
        echo json_encode($xs);
    } 

    function createFileJson(){
        
        for($i=1;$i<=114;$i++){
            fopen('json/surat/'.$i.'.json', "w");
        }
    }

    function renameFile()
    {
        for($i=8;$i<=30;$i++){
            rename('json/json'.$i.'.json','json/juz'.$i.'.json');
        }
    }
    
    
    
    
	
}
