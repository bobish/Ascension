<?php

use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// $app_name = $batchDetails->borrowerName;
// $date1 = $batchDetails->loanAmount;
// $date2 = $batchDetails->loanDate;

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
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Email'))
	);
	$driver->findElement(WebDriverBy::id('Email'))->sendKeys($email);
    $driver->findElement(WebDriverBy::id('Password'))->sendKeys($password);
    $driver->findElement(WebDriverBy::xpath('//*[@id="account_header"]/div[3]/button'))->click();
	
	$driver->wait()->until(
	WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Input_ParishSearchId'))
	);
	
	$driver->findElement(WebDriverBy::xpath('//*[@id="Input_ParishSearchId"]/option[4]'))->click();
	$driver->findElement(WebDriverBy::id('parish-search-step1'))->click();
	sleep(5);
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
	
	$docs = ["MORTGAGE","CANCELLATION - SINGLE","CASH SALE/DEED","ASSIGNMENT","DONATION","CORRECTION","QUIT CLAIM","LEASE","TRANSFER","AGREEMENT","SALE & ASSUMPTION","LIS PENDENS","EXCHANGE","SHERIFF SALE","AFFIDAVIT OF IDENTITY","CANCELLATION - MULTI"];
  
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
        
        $instNo = array();
        echo "Inst num starts \n";
        $allins = "";
        
		for($i=1; $i <= 60; $i++)
		{
			//echo "Page down action $i \n";
			foreach($driver->findElements(WebDriverBy::xpath("//*[@id='gridview-1012']/div[3]/table")) as $tab)
			{
                $ins = $tab->findElement(WebDriverBy::xpath('tbody/tr/td[1]/div'))->getText();
				if(in_array($ins, $instNo) == false){
					$allins.= "\"".$ins."\"".", ";
					array_push($instNo, $ins);
				}	
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
		
    }
    $allins = rtrim($allins,", ");
    echo $allins."\n\n";

    // Write to the Array splitting script	
	
	echo "Array Size = ".sizeof($instNo)."\n";
	
	$get_data = file_get_contents("D:\\AVRAutomation\\scripts\\LA\\inst_num_download.php");
	
	$ins_old_Data = explode(";", explode("\$instNo = ",$get_data)[1])[0];
	$ins_new_Data = "[".$allins."]";
	$get_data = str_replace($ins_old_Data, $ins_new_Data,$get_data);
	
	file_put_contents("D:\\AVRAutomation\\scripts\\LA\\inst_num_download.php", $get_data);
	
    
    
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