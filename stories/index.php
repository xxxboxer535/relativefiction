<?php 

include "../header.php"; 

?>

  <?php

 $id = explode("/", $_SERVER['REQUEST_URI'])[2];
      
   if(!isset($id) || empty($id)){

          $title = "Local Stories - Stories";
     
     include "../stories.php";
     
     return false;
     
   }

print '<link rel="stylesheet" href="story.css" />';
print '<script src="loader.js" /></script>';

include "../secret.php";

 try {

    // variable contains the connection string
    $connection_url = $mongo;

    // create the mongo connection object
    $m = new Mongo($connection_url);

    // use the database we connected to
    $db = $m->selectDB("localstories");

      $collection = $db->selectCollection("stories");
   
   $cursor = $collection->findOne(
        array(
            '_id' => new MongoId($id)
        )
    );
   
   function getDateTimeFromMongoId(MongoId $mongoId)
{
    $dateTime = new DateTime('@'.$mongoId->getTimestamp());
    $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    return $dateTime;
}
   
   $date = getDateTimeFromMongoId($cursor['_id']);
   
   $title = "Local Stories -" . $cursor['title'] . " by " . $cursor['author'];
   
   print "<div id='heading'>";
   print "<h1>".$cursor['title']."</h1>";
   print "<h2>by ".$cursor['author']."</h2>";
   print "<h3>" . "Published on the " . $date->format('mS \o\f F Y') . "</h3>";
   print "</div>";
   
   print "<article>";
   print $cursor['text'];
   print "</article>";
   
    // disconnect from server
    $m->close();
  } catch ( MongoConnectionException $e ) {
    die('Error connecting to MongoDB server');
  } catch ( MongoException $e ) {
    die('Mongo Error: ' . $e->getMessage());
  } catch ( Exception $e ) {
    die('Error: ' . $e->getMessage());
  }

?>

    <?php include "../footer.php"; ?>
