<main>

<div style="float:right; position:relative; top:36px;">
<a class="button" href="/redirect/create">Neuen Eintrag anlegen</a>
</div>


<h1>
<a class="plain" href="/cms">Kurzlink Statistiken<img src="/styles/img/fire.png" style="margin-left:.2em; width:80px; position:relative; top:18px;"></a>
</h1>

<figure style="margin-top:1em">
	<?=$chart?>
</figure>


<h2>zuletzt aufgerufene Links:</h2>

<table class="fancy wide">
	<thead>
		<tr>
			<th>KurzURL</th>
			<th>ZielURL</th>
			<th>UTM</th>
			<th>Kategorie</th>
			<!--<th>Erstellt</th>-->
			<th class="text-right">Datum</th>
			<th class="text-right">Zeitpunkt</th>
		</tr>
	</thead>
	<tbody>	
<?php foreach ($latest as $entry): ?>
<tr>
	<td><a href="/redirect/<?=$entry['id']?>">/<?=$entry['shorturl']?></a></td>
	<td>
		<div class="long-link">
			<div><a class="plain" href="/redirect/<?=$entry['id']?>"><?=$entry['rawurl'] ?? $entry['url']?></a></div>
			<div class="external-icon"><a class="plain" target="_blank" href="<?=$entry['url']?>">&#128194;</a></div>
		</div>
	</td>

		<td><?=$redirect['utm'] ? '&#10003;' : '-' ?></td>
		<td><a class="plain" href="/cms/category/<?=$entry['category']?>"><?=$entry['category'] ?? '-'?></a></td>
		<td class="text-right"><?=formatDate($entry['date'], 'd.m.Y')?></td>
		<td class="text-right"><?=formatDate($entry['date'], 'H:i:s')?></td>
</tr>
<?php endforeach ?>
</table>

<hr>

<h2>Gesamtstatistik auf Lebenszeit:</h2>

<table class="fancy wide js-sortable">
	<thead>
		<tr>
			<th>KurzURL</th>
			<th>ZielURL</th>
			<th>UTM</th>
			<th>Kategorie</th>
			<!--<th>Erstellt</th>-->
			<th class="text-right">Aufrufe</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($redirects as $id => $redirect): ?>
	<tr>
		<td><a href="/redirect/<?=$id?>">/<?=$redirect['shorturl']?></a></td>
		<td>
			<div class="long-link">
				<div><a class="plain" href="/redirect/<?=$id?>"><?=$redirect['rawurl'] ?? $redirect['url']?></a></div>
				<div class="external-icon"><a class="plain" target="_blank" href="<?=$redirect['url']?>">&#128194;</a></div>
			</div>
		</td>

		<td><?=$redirect['utm'] ? '&#10003;' : '-' ?></td>
		<td><a class="plain" href="/cms/category/<?=$redirect['category']?>"><?=$redirect['category'] ?? '-'?></a></td>
		<!--<td><?=formatDate($redirect['created'], 'Y-m-d')?></td>-->
		<td class="text-right"><?=$redirect['hits']?></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php if (empty($redirects)): ?>
	<p>Keine Einträge vorhanden</p>
<?php endif ?>


<?php if (isset($pager)): ?>
	<?=$pager?>
<?php endif; ?>

<?php if (isset($stats)): ?>
	<div class="text-center">
		Einträge: <b><?=$stats['redirects']?></b> &ensp; | &ensp; Linkaufrufe: <b><?=$stats['hits']?></b>
	</div>
<?php endif; ?>

<!--
<footer class="page-footer text-center">
	<p>redirectR. running on <a class="noline" target="_blank" href="https://github.com/tubsn/flundr"><b>flundr</b></a></p>
</footer>
-->

</main>
