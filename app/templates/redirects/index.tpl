
<h1><img src="/styles/flundr/img/flundr-logo.svg" style="width:80px; position:relative; top:22px;">
...flip flap flundr</h1>



<table class="fancy">

<?php foreach ($redirects as $id => $redirect): ?>
	<tr>
		<td><a href="/redirect/<?=$id?>">/<?=$redirect['shorturl']?></a></td>
		<td><a target="_blank" href="<?=$redirect['url']?>"><?=$redirect['url']?></a></td>
		<td><?=formatDate($redirect['created'], 'Y-m-d')?></td>
		<td><?=$redirect['hits']?></td>
	</tr>
<?php endforeach; ?>
</table>
