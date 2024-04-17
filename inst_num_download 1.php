<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// $app_name = $batchDetails->borrowerName;
// $date1 = $batchDetails->loanAmount;
// $date2 = $batchDetails->loanDate;
// 6 of Total: 190 - 1047088
$found = "";
$date1 = "02/16/2024";
$date2 = "02/16/2024";

$email = "sunil2023may@gmail.com";
$password = "May@2023$";
$t = 0; $k = 0;

try {
	
	// echo $app_name." : ".$batchDetails->county."\n";

	$url = "https://eclerksla.com/";
	$driver->get($url);
		
	$driver->wait()->until(
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('Email'))
	);
	$driver->findElement(WebDriverBy::id('Email'))->sendKeys($email);
    $driver->findElement(WebDriverBy::id('Password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::xpath('//*[@id="account_header"]/div[3]/button'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('Input_ParishSearchId'))
	);
	
	$driver->findElement(WebDriverBy::xpath('//*[@id="Input_ParishSearchId"]/option[4]'))->click();
	
	$driver->findElement(WebDriverBy::id('parish-search-step1'))->click();
	sleep(1);
	$driver->wait()->until(
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//*[@id="parish-search-decision"]/div[3]/div[3]/div[2]/button'))
	);
    $driver->findElement(WebDriverBy::xpath('//*[@id="parish-search-decision"]/div[3]/div[3]/div[2]/button'))->click();
	
    $driver->switchTo()->window($driver->getWindowHandles()[1]);
	$driver->wait()->until(
    WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('button-1048-btnInnerEl'))
    );
	$driver->findElement(WebDriverBy::id('button-1048-btnInnerEl'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('paramStartDate'))
	);
	
	$docs = ["MORTGAGE","CANCELLATION - SINGLE","CASH SALE/DEED","ASSIGNMENT","DONATION","CORRECTION","QUIT CLAIM","LEASE","TRANSFER","AGREEMENT","SALE & ASSUMPTION","LIS PENDENS","EXCHANGE","SHERIFF SALE","AFFIDAVIT OF IDENTITY","CANCELLATION - MULTI"];
	
	$total = $cnt = 0;
	
    // $instNo Array will have all the unique instrument Ids
    $instNo = ["1086498", "1086501", "1086508", "1086517", "1086518", "1086522", "1086525", "1086531", "1086532", "1086534", "1086535", "1086536", "1086507", "1086511", "1086519", "1086489", "1086491", "1086497", "1086499", "1086505", "1086509", "1086521", "1086526", "1086529", "1086488", "1086513", "1086524", "1086490", "1086495", "1086506", "1086512", "1086514", "1086516", "1086527"];
    echo count($instNo)."\n";
    
    $cnt = 1;
    $size = count($instNo);
    foreach($instNo as $a)		
    {
        echo $cnt." of Total: $size - ".$a."\n";
        $cnt++;
        $driver->wait()->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('paramStartDate'))
        );
        $driver->findElement(WebDriverBy::id('paramStartDate'))->sendKeys($date1);
        $driver->findElement(WebDriverBy::id('paramEndDate'))->sendKeys($date2);
        $driver->findElement(WebDriverBy::id('paramInstrument'))->clear();
        $driver->findElement(WebDriverBy::id('paramInstrument'))->sendKeys($a);
        sleep(1);
        $driver->findElement(WebDriverBy::id('submitButton'))->click();
        // Here we can download the documents by searching with instrument number
        sleep(7);
        $cnnt = count($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")));
        for($k=1; $k<=$cnnt; $k++)
        {
            $doc_type = trim($driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$k.']/tbody/tr/td[4]'))->getText()); 
            $doc_type = trim(explode("(",$doc_type)[0]);
            echo $doc_type."\n";
            if(in_array($doc_type,$docs) == true)
            {
                $doc_type = str_replace("/", "_", $doc_type);
                $driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$k.']/tbody/tr/td[4]'))->click();
                $driver->switchTo()->window($driver->getWindowHandles()[2]);
                $driver->wait()->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::className("party-names"))
                );
                sleep(2);
                $st = $driver->getPageSource();
                $doc_id = trim(explode("</h3>", explode("Details for", $st)[1])[0]);
                echo $doc_id." from web and searched $a \n";
                sleep(5);
                // $pa = $driver->findElement(WebDriverBy::xpath("/html/body/div[4]/main/div/div/div/div[1]/div/div[2]/a"))->getText();
                $read = trim(explode("Page", explode("View Image (", $st)[1])[0]);
                $driver->findElement(WebDriverBy::partialLinkText("View Image"))->click();
                
                // $driver->findElement(WebDriverBy::xpath("/html/body/div[3]/main/div/div/div/div[1]/div/div[2]/a"))->click();
                $driver->switchTo()->window($driver->getWindowHandles()[3]);
                $driver->wait()->until(
                WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::className(".imageContainer.lds-dual-ring"))
                );
                $fa = array();
                for($j=1; $j <= $read; $j++){
                    echo "  Downloading page $j of total pages $read \n";
                    $driver->wait()->until(
                    WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::id("loadingModal"))
                    );
                    sleep(2);
                    if($j > 1)
                    {
                        $driver->findElement(WebDriverBy::className("pagination"))->findElements(WebDriverBy::tagName("li"))[3]->findElement(WebDriverBy::tagName("a"))->click();
                        sleep(2);
                    }
                    sleep(2);
                    $page = $driver->getPageSource();
                    $curr_url = $driver->getCurrentUrl();
                    $img_id = str_replace("&amp;", "&", trim(explode('"', explode('data-src="', explode("<img", explode('id="imgViewer"', $page)[1])[1])[1])[0]));
                    $img_url = "https://eclerksla.com/Subscriptions/ViewerV2/$img_id";
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
                    $img = $doc_type."_".$j;
                    sleep(2);
                    $result->rename($img);
                    array_push($fa, $path."\\".$img.".pdf");
                    sleep(1);
                    $driver->get($curr_url);
                    sleep(2);
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
						
                $mpdf->Output($path."\\$doc_type"."_"."$doc_id.pdf",'F');
                
                unset($mpdf);
                gc_collect_cycles();
                foreach ($fa as $fileName)
                unlink($fileName);
                $converterPath = "D:\\AVRAutomation\\TiiffConv\\PDFConversion\\ImageMagick-7.0.10-Q16-HDRI\\";
                $outputFile = $path."\\$doc_type"."_"."$doc_id.pdf";
                // $tiffs = $path."\\*.tiff";
                // exec("\"".$converterPath."convert\" \"$tiffs\" \"".$outputFile."\"");
                
                $pdfConPath = "D:\\AVRAutomation\\TiiffConv\\PDFConversion\\";
					
                exec($pdfConPath."PDFConversion.exe \"".$outputFile."\"");
                // foreach(glob($path.'/*.tiff') as $file)
                    // unlink($file);
                unlink($outputFile);
                $driver->close();
                $driver->switchTo()->window($driver->getWindowHandles()[2]);
                $driver->close();
                $driver->switchTo()->window($driver->getWindowHandles()[1]);
                break;
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