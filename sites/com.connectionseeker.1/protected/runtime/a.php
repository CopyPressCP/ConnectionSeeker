<?php
$url = "https://ajax.googleapis.com/ajax/services/search/web?v=1.0&callback=CBK.1001&q=seo&rsz=8&start=0";

echo $ser = file_get_contents($url);

?>
