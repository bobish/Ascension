<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$app_name = $batchDetails->borrowerName;
$loan_amt = $batchDetails->loanAmount;
$loan_dt = $batchDetails->loanDate;


$loan_dt = str_replace("-","/",$loan_dt);
$loan_dt = new DateTime($loan_dt);
$found = "";

$email = "nick.fonseca@stellaripl.com";
$password = "MyNewPassword!082321";


function format_amt($amt) 
{
	$amt = trim($amt);
	$amt = str_replace(' ', '', $amt); 
	$amt = str_replace(',', '', $amt);
	$amt = str_replace('$', '', $amt);
	
	return $amt;
}

$arr = explode(' ', $app_name);
$num = count($arr);
if ($num == 3) 
{
    $applicant_name = explode(" ",$app_name, 3)[2];
    $applicant_name1 = explode(" ",$app_name,3)[0]." ".explode(" ",$app_name,3)[1];
} 
else 
{   
    $applicant_name = $name = explode(" ",$app_name, 2)[1];
    $applicant_name1 = explode(" ",$app_name,2)[0];    }


try {
	
	echo $app_name." : ".$batchDetails->county."\n";

	$url = "https://eclerksla.com/";
	$driver->get($url);
		
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Input_Email'))
	);

	$driver->findElement(WebDriverBy::id('Input_Email'))->sendKeys($email);
    $driver->findElement(WebDriverBy::id('Input_Password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::xpath('//*[@id="account"]/div[3]/button'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('My Parish Searches'))
	);
	
    $driver->findElement(WebDriverBy::linkText('My Parish Searches'))->click();
    $driver->findElement(WebDriverBy::xpath('/html/body/div/div/main/div/div/div/div[2]/div/div/table/tbody/tr[2]/td/a/span'))->click();
    
    $driver->wait()->until(
    WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('button-1053-btnInnerEl'))
    );
    $driver->findElement(WebDriverBy::id('button-1053-btnInnerEl'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('textfield-1024-inputEl'))
	);
	$driver->findElement(WebDriverBy::id('textfield-1024-inputEl'))->sendKeys($applicant_name);
    $driver->findElement(WebDriverBy::id('textfield-1025-inputEl'))->sendKeys($applicant_name1);
    sleep(1);
    $driver->findElement(WebDriverBy::id('button-1036'))->click();
	sleep(5);
   
	
	if(count($driver->findElements(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table'))) == 0)
	{
		$tax= "No Results returned for this search";
		$result->appraisalError = $tax;
	}
	else
	{	
        sleep(5);
		$driver->wait()->until(
		WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gridview-1012'))
		);
		$count = count($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")));
		for($i=1; $i <= $count; $i++){
			$doc_type = trim($driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$i.']/tbody/tr/td[4]'))->getText()); 
            $temp_record_dt = $driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$i.']/tbody/tr/td[2]'))->getText();
		    $temp_record_dt = new DateTime($temp_record_dt); 
            $temp_loan_amt = trim($driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$i.']/tbody/tr/td[7]'))->getText()); 
            $consideration = format_amt(explode(" ", $temp_loan_amt)[0]);
			if($temp_record_dt >= $loan_dt && $consideration == $loan_amt && $doc_type == "MTG")
			{
				
				$driver->findElement(WebDriverBy::xpath('//*[@id="gridview-1012"]/div[3]/table['.$i.']/tbody/tr/td[4]'))->click();
                $found = true;
                break;
			}else{
                $found = false;
            }			
        }		
    }		
    if($found == true)
    {	
        sleep(10);
		$driver->switchTo()->window($driver->getWindowHandles()[1]);
		$st = $driver->getPageSource();
		$doc_id = trim(explode("</h3>", explode("Details for", $st)[1])[0]);
        $driver->findElement(WebDriverBy::xpath("/html/body/div/div/main/div/div/div[1]/div/div[2]/a"))->click();
        $driver->wait()->until(
        WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::className(".imageContainer.lds-dual-ring"))
        );
        sleep(10);
        $read = count($driver->findElements(WebDriverBy::xpath("//*[@id='thumbScroller']/div")));
        $page = $driver->getPageSource();
		
		$fa = array();
        for($i=1; $i <= $read; $i++){
			$doc = trim(explode('/', explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$i])[1])[0]);
            $img_id = trim(explode('"', explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$i])[1])[0]);
            $i_id = trim(explode('"', explode("image/", explode('data-src="', explode("<img", explode('id="thumbScroller"', $page)[1])[$i])[1])[1])[0]);
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
            sleep(3);
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
			
		$mpdf->Output($path."\\$doc_type"."_"."$doc_id.pdf",'F');
		
		unset($mpdf);
		gc_collect_cycles();
		foreach ($fa as $fileName)
		unlink($fileName);
		
		$img_url1 = "https://eclerksla.com/Subscriptions/detail/ascension/$doc";
		$driver->get($img_url1); 
		$rel_count = count($driver->findElements(WebDriverBy::xpath("/html/body/div/div/main/div/div/div[2]/table/tbody/tr")));
		if($rel_count >= 1){
			for($i=1; $i <= $count; $i++){
				$rel_doc = trim($driver->findElement(WebDriverBy::xpath('/html/body/div/div/main/div/div/div[2]/table/tbody/tr['.$i.']/td[1]'))->getText());
				$rel_id = trim($driver->findElement(WebDriverBy::xpath('/html/body/div/div/main/div/div/div[2]/table/tbody/tr['.$i.']/th/a'))->getText());
				$driver->findElement(WebDriverBy::xpath('/html/body/div/div/main/div/div/div[2]/table/tbody/tr['.$i.']/th/a'))->click();
				$driver->findElement(WebDriverBy::xpath("/html/body/div/div/main/div/div/div[1]/div/div[2]/a"))->click();
				$driver->wait()->until(
				WebDriverExpectedCondition::invisibilityOfElementLocated(WebDriverBy::className(".imageContainer.lds-dual-ring"))
				);
				$doc_read = count($driver->findElements(WebDriverBy::xpath("//*[@id='thumbScroller']/div")));
				$page1 = $driver->getPageSource();

				$fb = array();
				for($i=1; $i <= $doc_read; $i++){
					$doc_img_id = trim(explode('"', explode('data-src="', explode("<img", explode('id="thumbScroller"', $page1)[1])[$i])[1])[0]);
					$doc_i_id = trim(explode('"', explode("image/", explode('data-src="', explode("<img", explode('id="thumbScroller"', $page1)[1])[$i])[1])[1])[0]);
					$doc_img_url = "https://eclerksla.com/Subscriptions/FileViewer/ascension/$doc_img_id";
					$driver->get($doc_img_url); 
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
					$doc_img = $rel_doc."_".$doc_i_id;
					sleep(3);
					$result->rename($doc_img);
					array_push($fb, $path."\\".$doc_img.".pdf");
				}
				$doc_fileNumber = 1;
				$doc_filesTotal = sizeof($fa);
				
				$mpdf = new \Mpdf\Mpdf();
				
				foreach ($fb as $doc_fileName) {
					if (file_exists($doc_fileName)) {
						$doc_pagesInFile = $mpdf->SetSourceFile($doc_fileName);
				
						for ($i = 1; $i <= $doc_pagesInFile; $i++) {
							$doc_tplId = $mpdf->ImportPage($i);
							$mpdf->UseTemplate($doc_tplId);
							if (($doc_fileNumber < $doc_filesTotal) || ($i != $doc_pagesInFile)) {
								$mpdf->WriteHTML('<pagebreak />');
							}
						}
						
					}
					
					$doc_fileNumber++;
				}
					
				$mpdf->Output($path."\\$rel_doc"."_"."$rel_id.pdf",'F');
				
				unset($mpdf);
				gc_collect_cycles();
				foreach ($fb as $doc_fileName)
				unlink($doc_fileName);
		
			}
		}
		

	}else if($found == false){
        $tax= "No Results returned for this search";
        $result->appraisalError = $tax;
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