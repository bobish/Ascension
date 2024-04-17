<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// $app_name = $batchDetails->borrowerName;
// $date1 = $batchDetails->loanAmount;
// $date2 = $batchDetails->loanDate;

$found = "";
$date1 = "06/15/2022";
$date2 = "06/15/2022";

$email = "sunil.kumar@stellaripl.com";
$password = "MyNewPassword!082321";
$t = 0; $k = 0;

try {
	
	// echo $app_name." : ".$batchDetails->county."\n";

	$url = "https://eclerksla.com/";
	$driver->get($url);
		
	// $driver->wait()->until(
    // WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath('//*[@id="staticBackdrop"]/div/div/div[1]/button'))
    // );
    // $driver->findElement(WebDriverBy::xpath('//*[@id="staticBackdrop"]/div/div/div[1]/button'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('Email'))
	);
	$driver->findElement(WebDriverBy::id('Email'))->sendKeys($email);
    $driver->findElement(WebDriverBy::id('Password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::xpath('//*[@id="account"]/div[3]/button'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Input_ParishSearchId'))
	);
	
	$driver->findElement(WebDriverBy::xpath('//*[@id="Input_ParishSearchId"]/option[4]'))->click();
	$driver->findElement(WebDriverBy::id('parish-search-step1'))->click();
	sleep(1);
    $driver->findElement(WebDriverBy::xpath('//*[@id="parish-search-decision"]/div[3]/div[3]/div[2]/button'))->click();
    $driver->switchTo()->window($driver->getWindowHandles()[1]);
	$driver->wait()->until(
    WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('button-1048-btnInnerEl'))
    );
	$driver->findElement(WebDriverBy::id('button-1048-btnInnerEl'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('paramStartDate'))
	);
	$driver->findElement(WebDriverBy::id('paramStartDate'))->sendKeys($date1);
	$driver->findElement(WebDriverBy::id('paramEndDate'))->sendKeys($date2);
	sleep(1);
	$driver->findElement(WebDriverBy::id('submitButton'))->click();
	// sleep(2);
	// $driver->findElement(WebDriverBy::id('panel-1035-innerCt'))->click();

	sleep(5);
	
	$docs = ["MORTGAGE","CANCELLATION - SINGLE","CASH SALE","AMENDMENT","ASSIGNMENT","DONATION","CORRECTION","CONTRACT","QUIT CLAIM","LEASE","TRANSFER","AGREEMENT","SALE & ASSUMPTION","LIS PENDENS","EXCHANGE","SHERIFF SALE","AFFIDAVIT OF IDENTITY","CANCELLATION - MULTI"];
  //*[@id="checkbox-1158"]
  
	$total = $cnt = 0;
	foreach($driver->findElement(WebDriverBy::xpath('//*[@data-ref="innerCt"]'))->findElements(WebDriverBy::xpath('//*[@data-ref="boxLabelEl"]')) as $label)
	{		
		$checkBoxLabel = trim(explode("(",$label->getText())[0]);
		
		if(in_array($checkBoxLabel,$docs) == true)
		{
			$label->click();
			if (!file_exists($path.'/'.$checkBoxLabel))
				mkdir($path.'/'.$checkBoxLabel);
			
			preg_match("/\(\d.*\)/", $label->getText(), $nums);
			preg_match("/\d{1,9}/", $nums[0], $num);
			$total = $total+$num[0];
		}	
		usleep(25000);
	}
	// echo $total."\n";
	
	 $driver->findElement(WebDriverBy::id('button-1040-btnInnerEl'))->click();
	 sleep(5);
	if(count($driver->findElements(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table'))) == 0)
	{
		$tax= "No Results returned for this search";
		$result->appraisalError = $tax;
	}
	else
	{	
        sleep(2);
        $driver->wait()->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gridview-1012'))
        );
       
        $driver->findElement(WebDriverBy::id('gridview-1012'))->click();
        $driver->switchTo()->window($driver->getWindowHandles()[2]);
		$driver->close();
        $driver->switchTo()->window($driver->getWindowHandles()[1]);
        
		$instNo = array();
		
		for($i=1; $i <= 60; $i++)
		{
			echo "Page down action $i \n";
			foreach($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")) as $tab)
			{
                $ins = $tab->findElement(WebDriverBy::xpath('tbody/tr/td[1]/div'))->getText();
                
				if(in_array($ins, $instNo) == false)
					array_push($instNo, $ins);
			}
			if(count($instNo) < $total-1)
			{
				$driver->getKeyboard()->pressKey(WebDriverKeys::PAGE_DOWN);
				usleep(250000);
			}
			else
			{
				break;
			}
		}
		
		// $instNo Array will have all the unique instrument Ids
		// $instNo = ["1034894","1034945","1034914","1034915","1034919","1034926","1034929","1034930","1034931","1034932","1034946","1034805","1034816","1034822","1034832","1034842","1034862","1034864","1034877","1034878","1034879","1034881","1034884","1034891","1034893","1034897","1034901","1034904","1034907","1034911","1034913","1034921","1034935","1034937","1034949"];
        // echo count($instNo)."\n";
        // for($i =0; $i < count($instNo); $i++){
        //     echo $instNo[$i];
        // }
		$cnt = 1;
		$size = count($instNo);
		foreach($instNo as $a)		
		{
			echo $cnt." of Total: $size - ".$a."\n";
            $cnt++;
            $url2 = "https://eclerksla.com/Subscriptions/Search/ascension";
            $driver->get($url);
			$driver->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Input_ParishSearchId'))
			);
			
			$driver->findElement(WebDriverBy::xpath('//*[@id="Input_ParishSearchId"]/option[4]'))->click();
			sleep(1);
			$driver->findElement(WebDriverBy::id('parish-search-step1'))->click();
			sleep(1);
			$driver->findElement(WebDriverBy::xpath('//*[@id="parish-search-decision"]/div[3]/div[3]/div[2]/button'))->click();
			sleep(1);
			$driver->switchTo()->window($driver->getWindowHandles()[2]);
			$driver->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('paramStartDate'))
			);
			$driver->findElement(WebDriverBy::id('paramStartDate'))->sendKeys($date1);
			$driver->findElement(WebDriverBy::id('paramEndDate'))->sendKeys($date2);
			$driver->findElement(WebDriverBy::id('paramInstrument'))->clear();
			$driver->findElement(WebDriverBy::id('paramInstrument'))->sendKeys($a);
            sleep(1);
            $driver->findElement(WebDriverBy::id('submitButton'))->click();
            // Here we can download the documents by searching with instrument number
			sleep(5);
			$cnnt = count($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")));
			for($k=1; $k<=$cnnt; $k++)
			{
				$doc_type = trim($driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$k.']/tbody/tr/td[4]'))->getText()); 
				$doc_type = trim(explode("(",$doc_type)[0]);
				echo $doc_type."\n";
				if(in_array($doc_type,$docs) == true)
				{
					$driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$k.']/tbody/tr/td[4]'))->click();
					$t += 1;
					
					$driver->switchTo()->window($driver->getWindowHandles()[2+$t]);
					sleep(2);
					$st = $driver->getPageSource();
					$doc_id = trim(explode("</h3>", explode("Details for", $st)[1])[0]);
					echo $doc_id." from web and searched $a \n";
					// /html/body/div[3]/main/div/div/div/div[1]/div/div[2]/a
					$driver->findElement(WebDriverBy::partialLinkText("View Image"))->click();
					// $driver->findElement(WebDriverBy::xpath("/html/body/div[4]/main/div/div/div/div[1]/div/div[2]/a"))->click();
					$driver->wait()->until(
					WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::className(".imageContainer.lds-dual-ring"))
					);
					sleep(2);
					$read = count($driver->findElements(WebDriverBy::xpath("//*[@id='thumbScroller']/div")));
					$page = $driver->getPageSource();
					
					$fa = array();
					for($j=1; $j <= $read; $j++){
						echo "  Downloading page $j of total pages $read \n";
						$doc = trim(explode('/', explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$j])[1])[0]);
						$img_id = trim(explode('"', explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$j])[1])[0]);
						$i_id = trim(explode('"', explode("image/", explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$j])[1])[1])[0]);
						$img_url = "https://eclerksla.com/Subscriptions/FileViewer/ascension/$img_id";
						$driver->get($img_url); 
						$driver->executeScript("var css = '@page { size: a4 portrait;}',
						head = document.head || document.getElementsByTagName('head')[0],
						style = document.createElement('style');
						style.type = 'text/css';
						style.media = 'print';
					
						if (style.styleSheet){
						style.styleSheet.cssText = css;
						} else {
						style.appendChild(document.createTextNode(css));
						}
					
						head.appendChild(style);window.print();");          
						$img = $doc_type."_".$i_id;
						sleep(2);
						$result->rename($img);
						array_push($fa, $path."\\".$img.".pdf");
					}
					
					// Merge the PDFs
			
					$fileNumber = 1;
					$filesTotal = sizeof($fa);
					
					$mpdf = new \Mpdf\Mpdf();
					
					foreach ($fa as $fileName) {
						if (file_exists($fileName)) {
							$pagesInFile = $mpdf->SetSourceFile($fileName);
					
							for ($k = 1; $k <= $pagesInFile; $k++) {
								$tplId = $mpdf->ImportPage($k);
								$mpdf->UseTemplate($tplId);
								if (($fileNumber < $filesTotal) || ($k != $pagesInFile)) {
									$mpdf->WriteHTML('<pagebreak />');
								}
							}
							
						}
						
						$fileNumber++;
					}
						
					$mpdf->Output($path."\\$doc_type\\$doc_type"."_"."$doc_id.pdf",'F');
					
					unset($mpdf);
					gc_collect_cycles();
					foreach ($fa as $fileName)
					unlink($fileName);
					$converterPath = "C:\\wamp64\\www\\AVRAutomation\\scripts\\ecclix\\PDFConversion1\\ImageMagick-7.0.10-Q16-HDRI\\";
					$outputFile = $path."\\$doc_type\\$doc_type"."_"."$doc_id.pdf";
					// $tiffs = $path."\\*.tiff";
					// exec("\"".$converterPath."convert\" \"$tiffs\" \"".$outputFile."\"");
					
					$pdfConPath = "C:\\wamp64\\www\\AVRAutomation\\scripts\\ecclix\\PDFConversion1\\";
					
					exec($pdfConPath."PDFConversion.exe \"".$outputFile."\"");
					// foreach(glob($path.'/*.tiff') as $file)
						// unlink($file);
					unlink($outputFile);
					$driver->close();
					$driver->switchTo()->window($driver->getWindowHandles()[0]);
					break;
				}
			}
                  			
		}
		
    }
	
	$driver->quit();	
}

catch (Exception $e)
{
	// Log error
	error_log($e->getMessage()." ".$e->getTraceAsString());
	
	//WriteResult($parcelData,"","","","",$e->getMessage());
	$result->otherError = $e->getMessage();
	
	if ($driver != null)
		$driver->quit();
}
?>