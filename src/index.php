<?php
require_once("programmeFinder.php");

/**
 * Search page for programmeFinder. Execute the search and present the results 
 * @author María-Eugenia Sierra
 *
 */
 $service = new ProgrammeFinderService();
 $count = 0;
 $search = $_GET["search"];
 $page = $_GET["page"];
 if (!isset($page)) {
 	$page = 1;
 }
 $more = false;
 if (isset($search)) {
 	$results = $service->searchBrand($search, $page, $more);
 	$showResults = true;
 } else {
 	$showResults = false;
 	$search = "Search";
 }
 $fragment = $_GET["fragment"];
 if (!isset($fragment)) {
 	$fragment = false;
 }
 ?>
<?php if (!$fragment): ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Programme Finder Results</title>
		<link type="text/css" href="css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
		<link type="text/css" href="css/style.css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
		
		<script type="text/javascript">
			$(function(){
				var page = <?php echo $page; ?>;
				$("input[name='search']").change(function() {
					if ($(this).val() != "") {
						$("input[name='searchBtn']").button("enable");		
					} else {
						$("input[name='searchBtn']").button("disable");
					}
				});
				$("input[name='searchBtn']").button({
            		icons: {
                		primary: "ui-icon-search"
            		}
        		});
        		$("input[name='moreBtn']").button().click(function() {
        			$(this).val("Loading...");
        			$("#loader").load("index.php?search=" + $("input[name='currentSearch']").val() +
        			                  "&page=" + (page + 1) + "&fragment=true",
        			                  function() {
        			                  	$("#results").append($("#loader #results").html());
        			                  	$("#results").accordion("destroy");
        			                  	$("#results").accordion();
        			                  	$("input[name='moreBtn']").val("Show more");
        			                  	page = page + 1;
        			                  });
        		});
        		
				$("#results").accordion();
			});
			
		</script>
	</head>
	<body>
	<header>
		<h1>Programme Finder</h1>
	</header>
	<section id="searchData">
	<form action="index.php">
	<input class="inputText" name="search" value="<?php echo $search?>" />&nbsp;<input type="submit" name="searchBtn" value="Search" />
	<input type="hidden" name="currentSearch" value="<?php echo $search;?>" disabled="disabled"/>	
	</form>	
	</section>
<?php endif; ?>	
	<?php if ($showResults): ?>
		<section id="results">
		<?php if (count($results) > 0):?>
			<?php foreach ($results as $brand):?>
		    	<h3><a href="#"><?php echo $brand->getName();?></a></h3>
		    	<article id="brand_<?php echo $brand->getId();?>">
			    		<ol>
			    		<?php foreach ($brand->getProgrammes() as $programme):?>
			    			<li><?php echo $programme->getName();?> [<?php echo $programme->getDuration(true);?>]</li>
			    		<?php endforeach ?>
			    		</ol>
		    	</article>
		    <?php endforeach; ?>	
		<?php else: ?>
			No results found.
		<?php endif;?>
		</section>
	<?php endif; ?>
<?php if (!$fragment): ?>
	<?php if ($more): ?>
		<input type="button" name="moreBtn" value="Show More" />
		<div id="loader" style="display:none"></div>
	<?php endif; ?>
	<footer>Programme Finder by María-Eugenia Sierra (<a href="mailto:mariaeugenia.sierra@gmail.com">mariaeugenia.sierra@gmail.com)</a></footer>	
	</body>
</html>
<?php endif;?>  