<main>

<h1>Kurzlink bearbeiten (ID: <?=$redirect['id']?>)</h1>

<div class="edit-layout">


	<form class="form-edit-link" action="" method="post">

		<fieldset class="form-2-cols">

		<label>Kurzurl:
			<input type="text" required name="shorturl" placeholder="z.B. /foodies" value="<?=$redirect['shorturl']?>">
		</label>

		<label>ZielURL:
			<input type="text" required name="url" placeholder="z.B. https://www.lr-online.de/ratgeber/foodies" value="<?=$redirect['url']?>">
		</label>
		</fieldset>

		<button type="submit">Änderung speichern</button> <a href="/cms" class="button light">abbrechen und zurück</a>

	</form>



	<div class="display-hits">
	<span class="display-hits-text">Hits:</span><?=$redirect['hits']?><img class="display-hits-img" src="/styles/img/fire.png">
	</div>


</div>



<figure style="margin-top:1em">
<?=$chart?>
</figure>


<details class="fleft">
<summary>Klick Details</summary>

<?=dump($redirect['events'])?>

</details>


<div class="edit-info-box">
<div class="edit-info-box-content">Erstellt: <?=formatDate($redirect['created'],'d.m.Y')?> (<?=substr($redirect['created_by_name'] ?? '...',0,3)?>)</div>
<?php if (!empty($redirect['edited'])): ?>
 <div class="edit-info-box-content">Editiert: <?=formatDate($redirect['edited'],'d.m.Y')?> (<?=substr($redirect['edited_by_name'] ?? '...',0,3)?>)</div>
<?php endif; ?>
</div>

</main>