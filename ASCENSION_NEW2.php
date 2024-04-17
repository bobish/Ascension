<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$day = $batchDetails->borrowerName;
$date1 = $batchDetails->loanAmount;
$date2 = $batchDetails->loanDate;

$found = "";
$date1 = "12/01/2021";
$date2 = "12/08/2021";

$email = "nick.fonseca@stellaripl.com";
$password = "MyNewPassword!082321";
$t = 0; $k = 0;

try {
	
	// echo $app_name." : ".$batchDetails->county."\n";
	// require_once("down.rb");
	
	exec("D:\\PHPScraping\\AVRAutomation\\scripts\\LA\\down.rb");
	
	$url = "https://eclerksla.com/";
	$driver->get($url);
	sleep(5);	
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
    WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('button-1056-btnInnerEl'))
    );
	// echo "Her\n";
	// echo "Her\n";
	// echo count($driver->findElements(WebDriverBy::id('button-1056-btnInnerEl')));
    $driver->findElement(WebDriverBy::id('button-1056-btnInnerEl'))->click();
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('datefield-1028-inputEl'))
	);
	$driver->findElement(WebDriverBy::id('datefield-1028-inputEl'))->sendKeys($date1);
	$driver->findElement(WebDriverBy::id('datefield-1029-inputEl'))->sendKeys($date2);
	sleep(1);
	$driver->findElement(WebDriverBy::id('button-1037'))->click();
	sleep(2);
	$driver->findElement(WebDriverBy::id('panel-1035-innerCt'))->click();

	sleep(1);
	
	$docs = ["MORTGAGE","CANCELLATION - SINGLE","CASH SALE","AMENDMENT","ASSIGNMENT","DONATION","CORRECTION","CONTRACT","QUIT CLAIM","LEASE","TRANSFER","AGREEMENT","SALE & ASSUMPTION","LIS PENDENS","EXCHANGE","SHERIFF SALE","AFFIDAVIT OF IDENTITY","CANCELLATION - MULTI"];
  
	$total = $cnt = 0;
		
	 foreach($driver->findElement(WebDriverBy::xpath('//*[@aria-label="Document Type field set"]'))->findElements(WebDriverBy::xpath('//*[@data-ref="boxLabelEl"]')) as $label)
	 {
			
			
			$checkBoxLabel = trim(explode("(",$label->getText())[0]);
			
			if(in_array($checkBoxLabel,$docs) == true)
			{
				$label->click();
				// echo $label->getText()."\n";
				// echo trim(explode(")",explode("(",$label->getText())[1])[0])."\n";
				if (!file_exists($path.'/'.$checkBoxLabel))
					mkdir($path.'/'.$checkBoxLabel);
				
				preg_match("/\(\d.*\)/", $label->getText(), $nums);
				preg_match("/\d{1,9}/", $nums[0], $num);
				$total = $total+$num[0];
			}
				
			usleep(25000);
	}
	echo $total."\n";
	
	 $driver->findElement(WebDriverBy::id('button-1037'))->click();
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
        $driver->switchTo()->window($driver->getWindowHandles()[1]);
        $driver->switchTo()->window($driver->getWindowHandles()[0]);
        
		$instNo = array();
		$datep = str_replace("/","-",$date1);
		$fp = fopen($path."/Instrument-$day@$datep.txt", 'w');		
		for($i=1; $i <= 75; $i++)
		{
			echo "i is $i\n";
			foreach($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")) as $tab)
			{
				$ins = $tab->findElement(WebDriverBy::xpath('tbody/tr/td[1]/div'))->getText();
				if(in_array($ins, $instNo) == false)
				{
					array_push($instNo, $ins);
					fwrite($fp, $ins."\n");
				}
					
			}
			if(count($instNo) < $total-1)
			{
				$driver->getKeyboard()->pressKey(WebDriverKeys::PAGE_DOWN);
				usleep(25000);
			}
			else
			{
				
				break;
				
			}
		}
		fclose($fp); 
		
		rename($path."/Instrument-$day@$datep.txt", $path."/Instrument-$day@$datep.txt");


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