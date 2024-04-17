<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// $app_name = $batchDetails->borrowerName;
// $date1 = $batchDetails->loanAmount;
// $date2 = $batchDetails->loanDate;
// 6 of Total: 190 - 1047088
$found = "";
$date1 = "12/01/2023";
$date2 = "20/01/2023";

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
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Email'))
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

	sleep(5);
	
	$docs = ["MORTGAGE","CANCELLATION - SINGLE","CASH SALE/DEED","AMENDMENT","ASSIGNMENT","DONATION","CORRECTION","CONTRACT","QUIT CLAIM","LEASE","TRANSFER","AGREEMENT","SALE & ASSUMPTION","LIS PENDENS","EXCHANGE","SHERIFF SALE","AFFIDAVIT OF IDENTITY","CANCELLATION - MULTI"];
	sleep(5);
	$total = $cnt = 0;
	foreach($driver->findElement(WebDriverBy::xpath('//*[@data-ref="innerCt"]'))->findElements(WebDriverBy::xpath('//*[@data-ref="boxLabelEl"]')) as $label)
	{		
		$checkBoxLabel = trim(explode("(",$label->getText())[0]);
		
		if(in_array($checkBoxLabel,$docs) == true)
		{
			$label->click();
			if (!file_exists($path.'/'.$checkBoxLabel))
			{
				$checkBoxLabel = str_replace("/", "_", $checkBoxLabel);
				mkdir($path.'/'.$checkBoxLabel);
			}
				
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
        
		// $instNo Array will have all the unique instrument Ids
		// $instNo = ["1066821", "1066828", "1066839", "1066844", "1066853", "1066811", "1066820", "1066824", "1066846", "1066852", "1066814", "1066837", "1066806", "1066807", "1066858", "1066804", "1066808", "1066854", "1066879", "1066912", "1066915", "1066930", "1066869", "1066881", "1066889", "1066910", "1066913", "1066923", "1066920", "1066883", "1066894", "1066874", "1066908", "1066914", "1066924", "1066937", "1066860", "1066897", "1066898", "1066900", "1066911", "1066916", "1066925", "1066947", "1066963", "1066967", "1066968", "1066970", "1066988", "1066992", "1066998", "1067006", "1067007", "1066941", "1066942", "1066945", "1066951", "1066955", "1066956", "1066957", "1066960", "1066995", "1067010", "1066946", "1066952", "1066958", "1066971", "1066973", "1066975", "1066991", "1066997", "1067003", "1067008", "1067009", "1066938", "1066949", "1066962", "1067013", "1066948", "1066965", "1066977", "1066990", "1066993", "1067015", "1067020", "1067027", "1067029", "1067032", "1067043", "1067051", "1067064", "1067071", "1067077", "1067081", "1067085", "1067026", "1067030", "1067035", "1067056", "1067059", "1067073", "1067076", "1067088", "1067091", "1067021", "1067022", "1067024", "1067031", "1067037", "1067040", "1067041", "1067042", "1067058", "1067061", "1067086", "1067019", "1067025", "1067033", "1067034", "1067039", "1067068", "1067069", "1067078", "1067079", "1067089", "1067016", "1067017", "1067018", "1067023", "1067028", "1067036", "1067038", "1067044", "1067050", "1067055", "1067060", "1067107", "1067121", "1067105", "1067126", "1067127", "1067131", "1067140", "1067142", "1067143", "1067144", "1067112", "1067132", "1067135", "1067141", "1067117", "1067120", "1067129", "1067145", "1067109", "1067114", "1067119", "1067149", "1067155", "1067181", "1067185", "1067189", "1067169", "1067170", "1067171", "1067172", "1067186", "1067161", "1067162", "1067167", "1067176", "1067190", "1067193", "1067199", "1067195", "1067197", "1067200", "1067202", "1067203", "1067153", "1067159", "1067168", "1067175", "1067184", "1067191", "1067194", "1067198", "1067206", "1067147", "1067151", "1067177", "1067196"];
		$instNo = ["1066838"];
		echo count($instNo)."\n";
		
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
					$doc_type = str_replace("/", "_", $doc_type);
					$driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$k.']/tbody/tr/td[4]'))->click();
					$t += 1;
					
					$driver->switchTo()->window($driver->getWindowHandles()[2+$t]);
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
					$driver->switchTo()->window($driver->getWindowHandles()[2+$t+1]);
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
						
					$mpdf->Output($path."\\$doc_type\\$doc_type"."_"."$doc_id.pdf",'F');
					
					unset($mpdf);
					gc_collect_cycles();
					foreach ($fa as $fileName)
					unlink($fileName);
					$converterPath = "E:\\AVRAutomation\\TiiffConv\\PDFConversion\\ImageMagick-7.0.10-Q16-HDRI\\";
					$outputFile = $path."\\$doc_type\\$doc_type"."_"."$doc_id.pdf";
					// $tiffs = $path."\\*.tiff";
					// exec("\"".$converterPath."convert\" \"$tiffs\" \"".$outputFile."\"");
					
					$pdfConPath = "E:\\AVRAutomation\\TiiffConv\\PDFConversion\\";
					
					exec($pdfConPath."PDFConversion.exe \"".$outputFile."\"");
					// foreach(glob($path.'/*.tiff') as $file)
						// unlink($file);
					unlink($outputFile);
					$driver->close();
					$driver->switchTo()->window($driver->getWindowHandles()[2+$t]);
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