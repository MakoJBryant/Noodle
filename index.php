<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Noodle</title>
    <!-- -->
    <meta charset="UTF-8">
    <meta name="description" content="Search the web for sites and images.">
    <meta name="keywords" content="Search Engine,Noodle,HTML,CSS,PHP,MySQL,XAMPP,JavaScript">
    <meta name="author" content="Mako J Bryant">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>

    <div class="wrapper indexPage">

        <div class="mainSection">
        
            <div class="logoContainer">
                <img src="assets/images/noodleLogo.png" title="The #1 Search Engine" alt="Site Logo">
            </div>

            <div class="searchContainer">     

                <form action="search.php" method="GET">  

                    <input class="searchBox" type="text" name="term">
                    <input class="searchButton" type="submit" value="Search">


                </form>

            </div>

        </div>

    </div>

</body>
</html>