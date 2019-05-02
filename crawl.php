<?php
include("config.php");
include("classes/DomDocumentParser.php");

$crawling = array();
$alreadyCrawled = array();
$alreadyFoundImages = array();

function linkExists($url) {
    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keyword) {
    global $con;

    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
                            VALUES(:url, :title, :description, :keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

function insertImage($url, $src, $alt, $title) {
    global $con;

    $query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
                            VALUES(:siteUrl, :imageUrl, :alt, :title)");

    $query->bindParam(":siteUrl", $url);
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    $query->execute();
}

function createLink($src, $url) {

    // $scheme:  http https
    // $host:    www.reecekenney.com
    // $src:    /about.php

    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    //  "//www.reecekenney.com"
    if(substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    }
    //  "/about/aboutUs.php"
    else if(substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    /*  "./about/aboutUs.php"
	else if(substr($src, 0, 2) == "./") {
		$src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }*/
    //  "../about/aboutUs.php"
    else if(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    //  "about/aboutUs.php"
    else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    else {
        $src = $scheme . "://www." . $host . "/" . $src;
    }

    return $src;
}

function getDetails($url) {

    global $alreadyFoundImages;

    $parser = new DomDocumentParser($url);

    $titleArray = $parser->getTitleTags();

    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);

    if($title == "") {
        return;
    }

    $description = "";
    $keywords = "";

    $metasArray = $parser->getMetaTags();

    foreach($metasArray as $meta) {

        if($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }

        if($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    if(linkExists($url)) {
        echo "$url already exists<br>";
    }
    else if(insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    }
    else {
        echo "ERROR: Failed to insert $url<br>";
    }

    $imageArray = $parser->getImages();
    foreach($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {
            continue;
        }

        $src = createLink($src, $url);

        if(!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;
            insertImage($url, $src, $alt, $title);
            /*
            if(linkExists($src)) {
                echo "$src already exists<br>";
            }
            else if(insertImage($url, $src, $alt, $title)) {
                echo "SUCCESS: $src<br>";
            }
            else {
                echo "ERROR: Failed to insert $src<br>";
            }
            */
        }
    }
}

function followLinks($url) {

    global $crawling;
    global $alreadyCrawled;

    $parser = new DomDocumentParser($url);

    $linkList = $parser->getLinks();

    foreach($linkList as $link) {
        $href = $link->getAttribute("href");

        if(strpos($href, "#") !== false) {
            continue;
        }
        else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        $href = createLink($href, $url);

        // If not in alreadyCrawled, add to alreadyCrawled and to crawling.
        if(!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            getDetails($href);
        }
    }

    // Take next item off of array.
    array_shift($crawling);

	foreach($crawling as $site) {
		followLinks($site);
	}}

$startUrl = "https://www.soundcloud.com/";
followLinks($startUrl);

?>