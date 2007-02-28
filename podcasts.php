<?
require_once("scripts/resources_mgr.php");
require_once("scripts/filter_core.php");

$filter = new Filter();
$filter->type = 'podcast';
$filter->sortby = array('date');
$resources = $Resources->get_resources($filter);

// TODO Currently assumes that podcasts are in mp3 format. Need to support other types?
foreach($resources as $podcast) {
  $mp3_link = null;
  foreach ($podcast->links as $link) {
    if ($link->type == 'mp3') {
       $mp3_link = $link;
       break;
    }
  }
  
  if (!$mp3_link) continue;
  
  $date = date("D, j M Y  12:00:00 \G\M\T", $mp3_link->get_date());
  
  $authors = ''; $separator = '';
  foreach ($mp3_link->authors as $author) {
    $authors .= $separator . $author->name;
    $separator = ', ';
  }
  
  $tags = ''; $separator = '';
  foreach ($podcast->categories as $category) {
    $tags .= $separator . $category->title;
    $separator = ', ';
  }
?><item>
	<title><?=$podcast->title?></title>
	<itunes:author><?= $authors ?></itunes:author>
	<itunes:summary><?= $podcast->description ?></itunes:summary>
	<enclosure url="<?= $mp3_link->path?>" type="audio/mpeg" />
	<guid><?= htmlentities($mp3_link->path)?></guid>
	<pubDate><?= $date ?></pubDate>
	<itunes:keywords><?= $tags ?></itunes:keywords>
</item>

<?
}
?>
