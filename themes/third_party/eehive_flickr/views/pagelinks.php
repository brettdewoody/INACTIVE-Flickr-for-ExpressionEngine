<?php
							
	// Initialize page links var
	$pageLinks = '';
							
	// Create first 2 page links if needed
	if ($totalPages > 7 && $page > $totalPages/2) {
		$pageLinks = '<a href="' . $flickrURL . '&p=1">1</a><a href="' . $flickrURL . '&p=2">2</a><span class="break">&nbsp;&nbsp;...&nbsp;&nbsp;</span>';
	}
							
	// Start and End Pages
	$pageStart = $page > 3  ?  $page-3 : 1;
	$pageEnd = $page < ($totalPages-3)  ?  $page+3 : $totalPages;
			
	// Create page links
	for ($i = $pageStart ; $i <= $pageEnd; $i++) {
		if ($i == $page) {
			$pageLinks .= '<span class="current">' . $page . '</span>';
		} else {
			$pageLinks .= '<a href="' . $flickrURL . '&p=' . $i . '">' . $i . '</a>';
		}
	}
							
	// Create last two page links if needed
	if ($totalPages > 7 && $page <= $totalPages/2) {
		$pageLinks .= '<span class="break">&nbsp;&nbsp;...&nbsp;&nbsp;</span><a href="' . $flickrURL . '&p=' . ($totalPages-1) . '">' . ($totalPages-1) . '</a><a href="' . $flickrURL . '&p=' . $totalPages . '">' . $totalPages . '</a>';
	}
?>
<div id="flickrFooter">
	<div id="flickrFooterNext"><?php if($page < $totalPages) { ?><a href="<?=$flickrURL . '&p=' . ($page+1)?>" target="_self">next &#8594;</a><?php } else {?><span>next &#8594;</span><?php } ?></div>
    <div id="flickrFooterPrev"><?php if($page > 1) { ?><a href="<?=$flickrURL . '&p=' . ($page-1)?>" target="_self">&#8592; prev</a><?php } else { ?><span>&#8592; prev</span><?php } ?></div>
    <div id="flickrFooterPages"><?=$pageLinks?><br/><span class="items">(<?=number_format($totalItems)?> items)</span></div>
</div>