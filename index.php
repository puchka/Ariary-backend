<?php
header('Content-Type: application/json');
$yql_base_url = "http://query.yahooapis.com/v1/public/yql";
$yql_query_currencies = "select * from html where url='http://www.banque-centrale.mg' and xpath='//td[contains(@class,\"lienGauche\")]/table/tr[1]/td[1]/table/tr[1]/td[1]/table/tr[*]/td/p'";
$yql_query_date = 'select * from html where url="http://www.banque-centrale.mg" and xpath="//td[contains(@class,\'lienGauche\')]/table/tr[1]/td[1]/table/tr[1]/td[1]/strong[text()]"';
$yql_query_url_currencies = $yql_base_url . "?q=" . urlencode($yql_query_currencies);
$yql_query_url_date = $yql_base_url . "?q=" . urlencode($yql_query_date);
$yql_query_url_currencies .= "&format=json";
$yql_query_url_date .= "&format=json";
$session_currencies = curl_init($yql_query_url_currencies);
curl_setopt($session_currencies, CURLOPT_RETURNTRANSFER, true);
$json_currencies = curl_exec($session_currencies);
$session_date = curl_init($yql_query_url_date);
curl_setopt($session_date, CURLOPT_RETURNTRANSFER, true);
$json_date = curl_exec($session_date);
$phpObj_currencies =  json_decode($json_currencies);
$phpObj_date =  json_decode($json_date);
$currenciesArray = $phpObj_currencies->{'query'}->{'results'}->{'p'};
$currenciesNames = array('Euro Member Countries', 'United States Dollar', 'United Kingdom Pound', 'Switzerland Franc',
						 'Japan Yen', 'Canada Dollar', 'Denmark Krone', 'Norway Krone', 'Sweden Krona', 'Djibouti Franc',
						 'International Monetary Fund (IMF) Special Drawing Rights', 'Mauritius Rupee', 'South Africa Rand',
						 'Saudi Arabia Riyal', 'Hong Kong Dollar', 'Singapore Dollar', 'New Zealand Dollar', 'India Rupee');
$currencies = array();
for ($i=0;$i<count($currenciesArray);$i+=2)
{
	$currency = array(
						"Name" => $currenciesNames[$i/2],
						"Symbole" => $currenciesArray[$i],
						"Value" => $currenciesArray[$i+1]
					 );
	$currencies[] = $currency;
}
$data = array();
$data['currencies'] = $currencies;
$dateArray = split('/', $phpObj_date->{'query'}->{'results'}->{'strong'}->{'content'});
$day = split(' ', $dateArray[0])[1];
$month = $dateArray[1];
$year = substr($dateArray[2], 0, 2);
$data['date'] = array(
						"day" => $day,
						"month" => $month,
						"year" => $year
					 );
echo json_encode($data);
?>
