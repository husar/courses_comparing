<?php
	class Course
	{
		public function readXML(){
            
            $context = stream_context_create(array('ssl'=>array(
                'verify_peer' => false, 
                "verify_peer_name"=>false
                )));

            libxml_set_streams_context($context);

            
            $xml=simplexml_load_file("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml") or die("Error: Cannot create object");
            
            if($xml){
                return $xml;
            }
            
            return NULL;
        }
        
        public function getActualCourseFromXML($readed_xml){
            
            $xml=$readed_xml;
            
            if($xml){
                
                $actual_currency    = array();
                $actual_rate        = array();

                foreach($xml->Cube->Cube->Cube as $course){
                    array_push($actual_currency,$course['currency']);
                    array_push($actual_rate,floatval($course['rate']));
                }
                
                $currency_with_rate = array_combine($actual_currency,$actual_rate);
                
                return $currency_with_rate;
            }
            
            return NULL;
            
        }
        
        public function getActualDateFromXML($readed_xml){
            
            $xml=$readed_xml;
            
            if($xml){
                
                return $xml->Cube->Cube['time'];
                
            }
            
            return NULL;
            
        }
        
        private function getAXConnection(){
            
            $serverName = "axdbp"; 
            $connectionInfo = array( "Database"=>"MKEM_AX2012R3_PROD", "UID"=>"production1", "PWD"=>"production1", "CharacterSet"  => 'UTF-8');
            $conn = sqlsrv_connect( $serverName, $connectionInfo);
            
            if($conn){
                
                return $conn;
                
            }
            
            return NULL;
        }
        
        public function getAXCurrencyAndCourse(){
            
            $conn = $this->getAXConnection();
            $query_tab_1="SELECT epair.TOCURRENCYCODE AS currency, (erate.EXCHANGERATE/100) AS course FROM EXCHANGERATE as erate INNER JOIN EXCHANGERATECURRENCYPAIR as epair ON (erate.EXCHANGERATECURRENCYPAIR = epair.RECID) WHERE erate.VALIDFROM=CAST(GETDATE() as DATE)";
            $apply_tab_1=sqlsrv_query($conn,$query_tab_1);
            $ax_currency=array();
            $ax_course=array();
            
            while($result_tab_1=sqlsrv_fetch_array($apply_tab_1)){
                array_push($ax_currency,$result_tab_1['currency']);
                array_push($ax_course,$result_tab_1['course']);
            }
            $recid_with_currency=array_combine($ax_currency,$ax_course);
            
            return $recid_with_currency;
            
        }
        
        public function getAXCourse($AXCurrencyAndCourse,$findingCurrencyKey){
            return array_key_exists($findingCurrencyKey,$AXCurrencyAndCourse)?doubleval($AXCurrencyAndCourse[$findingCurrencyKey]):"-";
        }
        
        public function getCoursStatus($xmlCourse, $AXCourse){
            if($AXCourse=="-"){
                return '<td style="color: white; background-color:red">Kurz nenájdený v AX</td>';
            }
            else if($xmlCourse==$AXCourse){
                return '<td style="color: white; background-color:green">V poriadku</td>';
            }
            return '<td style="color: white; background-color:orange">Kurzy sa nezhodujú</td>';
        }
        
	}
?>
 