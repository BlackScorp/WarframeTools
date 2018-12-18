<?php
declare(strict_types=1);
error_reporting(-1);
ini_set('display_errors', '1');
set_time_limit(0);
$sourceUrl = "https://warframe.fandom.com/wiki/Mods";
$context = stream_context_set_default([
    'ssl' => [
        'verify_peer' => false
    ]
]);


$domDocument = new DOMDocument();
@$domDocument->loadHTMLFile($sourceUrl);
$xpath = new DOMXPath($domDocument);

$tabs = $xpath->query('//div[@class="tabbertab"]');

$modList = [];
$index = 0;
$languages = [
    'en' => 'English'
];
foreach ($tabs as $categoryIndex => $tab) {
    $category = $tab->getAttribute('title').'-'.$categoryIndex;
    $modList[$category] = [];
    $modData = $xpath->query('./table/tr/td[1]/span/a', $tab);

    echo sprintf("Collecting data for '%s'<br/>",$category);
    foreach ($modData as $modDatum) {

        $link = 'https://warframe.fandom.com'.$modDatum->getAttribute('href');

        $title = $modDatum->getAttribute('title');
        echo sprintf("Fetching language strings for Mod : '%s' from '%s'<br/>",$title,urldecode($link));
        $modDomDocument = new DOMDocument();
        @$modDomDocument->loadHTMLFile($link);
        $modXpath = new DOMXPath($modDomDocument);
        $modList[$category][$index]['en']=$title;

        $languagesElements = $modXpath->query('//nav[@class="WikiaArticleInterlang"]/ul/li/a');
        foreach($languagesElements as $languageElement){
            $languageModUrl = $languageElement->getAttribute('href');
            $languageModDocument = new DOMDocument();
            @$languageModDocument->loadHTMLFile($languageModUrl);
            $languageModXpath = new DOMXPath($languageModDocument);
            $titleElement = $languageModXpath->query('//h1');
            $language = $languageElement->getAttribute('data-tracking');
            $languages[$language] = $languageElement->nodeValue;

            if($titleElement->length === 0){
                $languageTitle = str_replace('_',' ',basename(urldecode($languageModUrl)));
                $modList[$category][$index][$language]=$languageTitle;
                echo sprintf("Failed to find title on '%s'<br/>",urldecode($languageModUrl));
                continue;
            }
            $languageTitle = $titleElement->item(0)->nodeValue;


            $modList[$category][$index][$language]=$languageTitle;
            echo sprintf("Found title '%s' for language '%s' on URL '%s'<br/>",$languageTitle,$language,urldecode($languageModUrl));
        }
        $index++;

       // echo 'en: <a href="' . $link . '" target="_blank">' . $title . '</a></br>';
    }

    echo "<hr/>";
}

file_put_contents('mod_list.json',json_encode($modList));
file_put_contents('languages.json',json_encode($languages));
