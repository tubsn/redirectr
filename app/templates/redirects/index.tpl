<main>


<div style="float:right; position:relative; top:36px;">
<a class="button" href="/redirect/create">Neuen Eintrag anlegen</a>
</div>


<h1>
Kurzlink Management System<img src="/styles/img/fire.png" style="margin-left:.2em; width:80px; position:relative; top:18px;"></h1>

<table class="fancy wide js-sortable">
	<thead>
		<tr>
			<th>KurzURL</th>
			<th>ZielURL</th>
			<th>Erstellt</th>
			<th class="text-right">Hits</th>
			<th class="text-center" colspan="2">Optionen</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($redirects as $id => $redirect): ?>
	<tr>
		<td><a href="/redirect/<?=$id?>">/<?=$redirect['shorturl']?></a></td>
		<td><a href="/redirect/<?=$id?>"><?=$redirect['url']?></a> <small><a class="noline" target="_blank" href="<?=$redirect['url']?>">
&nbsp;&#128194;</a></small></td>
		<td><?=formatDate($redirect['created'], 'Y-m-d')?></td>
		<td class="text-right"><?=$redirect['hits']?></td>
		<td class="text-right"><a href="/redirect/<?=$id?>">Edit</a></td>
		<td class="text-right">
			<a id="del-redirect<?=$id?>" class="noline pointer"><img class="icon-delete" src="/styles/flundr/img/icon-delete-black.svg"></a>
			<fl-dialog selector="#del-redirect<?=$id?>" href="/redirect/<?=$id?>/remove">
				<h1>/<?=$redirect['shorturl']?> - löschen?</h1>
				<p>Möchten Sie den Kurzlink wirklich löschen?</p>
			</fl-dialog>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php if (isset($stats)): ?>
	<div class="text-center">
		Einträge: <b><?=$stats['redirects']?></b> &ensp; | &ensp; Linkaufrufe: <b><?=$stats['hits']?></b>
	</div>
<?php endif; ?>

<footer class="page-footer text-center">
	<p>redirectR. running on <a class="noline" target="_blank" href="https://github.com/tubsn/flundr"><b>flundr</b></a></p>
</footer>

</main>
