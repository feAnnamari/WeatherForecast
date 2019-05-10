<?php
    class day { 
        public $date; 
        public $dayName; 
        public $maxTemp;
        public $minTemp;
        
        function __construct($date, $dayName, $minTemp, $maxTemp) {		
            $this->date = $date;	
            $this->dayName = $dayName;
            $this->maxTemp = $maxTemp;
            $this->minTemp = $minTemp;
		}

        function tempDifference() { 
            $max = $this->maxTemp;
            $min = $this->minTemp;
            $res = intval($max)-intval($min);
            return $res;
        } 
    } 
?>