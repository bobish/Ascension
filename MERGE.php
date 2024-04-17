<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
require_once('vendor/autoload.php');

$driver->quit();
//Format Name


$folderPath = "D:\\PHPScraping\\Automation\\2021-08-26\\AVR\\LA_ASCENSION\\BARBARA JOHNSON\\";

$fa = [$folderPath."24955921.pdf",$folderPath."24955922.pdf",$folderPath."24955923.pdf",$folderPath."24955924.pdf"];

$fileNumber = 1;

$filesTotal = sizeof($fa);

	// $mpdf = new \Mpdf\Mpdf(['setAutoTopMargin' => 'stretch',
    // 'autoMarginPadding' => 1, 'format' => 'A5', 'margin_left' => 12, 'margin_right' => 12, 'margin-top' => 5]);
	
	$mpdf = new \Mpdf\Mpdf();
	
	foreach ($fa as $fileName) {
		if (file_exists($fileName)) {
			$pagesInFile = $mpdf->SetSourceFile($fileName);
	
			for ($i = 1; $i <= $pagesInFile; $i++) {
				$tplId = $mpdf->ImportPage($i);
				$mpdf->UseTemplate($tplId);
				if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {
					$mpdf->WriteHTML('<pagebreak />');
				}
			}
		}
		$fileNumber++;
	}
		
	$mpdf->Output($folderPath."Mortgage.pdf",'F');


?>