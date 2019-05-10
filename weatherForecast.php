<?php

    require "vendor/autoload.php";
    use PHPHtmlParser\Dom;
    include("day.php");
    libxml_use_internal_errors(true);

    $url = "https://www.idokep.hu/idojaras/P%C3%A9cs";
    $html = getHTML($url);
    $days = getData($html);
    calculate($days);

    function getHTML($url)
    {
        $dom = new Dom;
        $dom->loadFromUrl($url);
        $html = $dom->outerHtml;
        return $html;
    }
    
    function getData($html)
    {
        $numOfDays = 7;
        $classnameMax = "max-homerseklet-default max";
        $classnameMin = "min-homerseklet-default max";
        $classDatum = "datum";
        $classNap = "nap";

        $domdocument = new DOMDocument();
        $domdocument->loadHTML($html);
        $a = new DOMXPath($domdocument);
        $minTemps = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classnameMin ')]");
        $maxTemps = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classnameMax ')]");
        $dates = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classDatum ')]");
        $dayNames = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classNap ')]");
        

        for ($i = 0; $i < $numOfDays; $i++) {
            $resultDate[] = $dates->item($i)->firstChild->nodeValue;
            $resultName[] = $dayNames->item($i)->firstChild->nodeValue;
            $resultMin[] = $minTemps->item($i)->firstChild->nodeValue;
            $resultMax[] = $maxTemps->item($i)->firstChild->nodeValue;
        }

        for ($i = 0; $i < $numOfDays; $i++)
        {
            $days[] = new day($resultDate[$i], $resultName[$i], $resultMin[$i], $resultMax[$i]);
        }

        echo '
        ╦ ╦┌─┐┌─┐┌┬┐┬ ┬┌─┐┬─┐  ╔═╗┌─┐┬─┐┌─┐┌─┐┌─┐┌─┐┌┬┐     
        ║║║├┤ ├─┤ │ ├─┤├┤ ├┬┘  ╠╣ │ │├┬┘├┤ │  ├─┤└─┐ │ 
        ╚╩╝└─┘┴ ┴ ┴ ┴ ┴└─┘┴└─  ╚  └─┘┴└─└─┘└─┘┴ ┴└─┘ ┴      
         ___  __       
        | _ \/_/ __ ___
        |  _/ -_) _(_-<
        |_| \___\__/__/
                           
        ';
        printf("\n");
        $mask = "\t| %4.10s | %4.10s | %10.10s | %10.10s | %5.10s |\n";
        printf($mask, 'Date', 'Day', 'Min. temp.', 'Max. temp.', 'Range');
        printf("\t=================================================\n");
        for ($i = 0; $i < count($days); $i++)
        {
            printf($mask, $days[$i]->date, $days[$i]->dayName, $days[$i]->maxTemp, $days[$i]->minTemp, $days[$i]->tempDifference());
            printf("\t-------------------------------------------------\n");
        }

        return $days;

    }

    function calculate($days)
    {
        for ($i = 0; $i < count($days); $i++)
        {
            $ranges[] = intval($days[$i]->tempDifference());
        }

        $minRange = min($ranges);
        $maxRange = max($ranges);
        $median = median($ranges);
        $mean = mean($ranges);
        $deviation = deviation($ranges, false);
       
        printf("\tMinimum range: " . $minRange . "\n");
        printf("\tMaximum range: " . $maxRange . "\n");
        printf("\tMedian: " . $median . "\n");
        printf("\tMean: " . $mean . "\n");  
        printf("\tDeviation: " . $deviation . "\n");  
    }

    function median($arr)
    {
        $count = count($arr); 
        $middleval = floor(($count-1)/2); 
        if($count % 2) { 
            $median = $arr[$middleval];
        } else {
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }

    function mean($arr)
    {
        $count = count($arr); 
        $total = 0;
        foreach ($arr as $value) {
            $total = $total + $value;
        }
        $mean = ($total/$count); 
        return $mean;
    }

    function deviation($a, $sample = false)
    {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }
    libxml_clear_errors();
    exit();

?>