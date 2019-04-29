<?php
include("config.php");
include("classes/SiteResultsProvider.php");

    // Get term if it has been set.
    if(isset($_GET["term"])) {
        $term = $_GET["term"];
    }
    else {
        exit("You must enter a search term.");
    }

    // Get type if it has been set.
    $type = isset($_GET["type"]) ? $_GET["type"] : "sites";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Noodle</title>
    <meta charset="UTF-8">
    <meta name="description" content="Search the web for sites and images.">
    <meta name="keywords" content="Search Engine,Noodle,HTML,CSS,PHP,MySQL,XAMPP,JavaScript">
    <meta name="author" content="Mako J Bryant">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"></head>
<body>

    <div class="wrapper">

        <div class="header">

            <div class="headerContent">

                <div class="logoContainer">
                    <a href="index.php">
                        <img src="assets/images/noodleLogo.png">
                    </a>
                </div>

                <div class="searchContainer">

                    <form action="search.php" method="GET">
                        
                        <div class="searchBarContainer">
                            
                            <input class="searchBox" type="text" name="term" value="<?php echo $term ?>">
                            <button class="searchButton">
                                <img src="assets/images/icons/search.png">
                            </button>

                        </div>

                    </form>

                </div>

            </div>

            <div class="tabsContainer">
                
                <ul class="tabList">

                    <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>
                            Sites
                        </a>
                    </li>

                    <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=images"; ?>'>
                            Images
                        </a>
                    </li>

                </ul>
            </div>
        </div>



        <div class="mainResultsSection">

            <?php
                $resultsProvider = new SiteResultsProvider($con);

                $numResults = $resultsProvider->getNumResults($term);

                echo "<p class='resultsCount'>$numResults results found</p>";

                echo $resultsProvider->getResultsHtml(1, 20, $term);
            ?>

        </div>


    </div>

</body>
</html>

