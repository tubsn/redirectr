<main>

<h1>Kurzlink bearbeiten (ID: <?=$redirect['id']?>)</h1>

<div class="edit-layout">

	<form class="form-edit-link" action="" method="post">

		<fieldset class="form-2-cols">

		<label>KurzURL:
			<input tabindex="1" type="text" required name="shorturl" placeholder="z.B. /foodies" value="<?=$redirect['shorturl']?>">
		</label>

		<label>Kategorie:
			<select tabindex="3" name="category">
				<?php if ($redirect['category']): ?>
				<option selected value="<?=$redirect['category']?>"><?=$redirect['category']?></option>
				<?php endif ?>
				<?php foreach (CATEGORIES as $category): ?>
					<?php if ($redirect['category'] == $category) {continue;} ?>
					<option value="<?=$category?>"><?=$category?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>UTM-Automatik:<br/>
			<?php if ($redirect['utm']): ?>
			<input tabindex="4" type="checkbox" name="utm" checked value="1">UTM Parameter an Link hängen
			<?php else: ?>
			<input tabindex="4" type="checkbox" name="utm" value="1">UTM Parameter an Link hängen
			<?php endif; ?>
		</label>

		</fieldset>

		<fieldset>
		<label>ZielURL:
			<input tabindex="2" type="text" required name="url" placeholder="z.B. https://www.lr-online.de/ratgeber/foodies" value="<?=$redirect['url']?>">
		</label>

		</fieldset>


		<button tabindex="5" type="submit">Änderung speichern</button> <a href="<?=$referer?>" class="button light">abbrechen und zurück</a>

	</form>

	<figure title="Klicken um QR-Code als SVG-Datei zu speichern" style="text-center; max-width:200px; justify-self: center; cursor:pointer;">
	
	<?=$qrcode?>

	<script>
	let btn = document.querySelector('.qrcode');
	let svg = document.querySelector('.qrcode');

	let triggerDownload = (imgURI, fileName) => {
	let a = document.createElement('a');
		a.setAttribute('download', '<?=$redirect['shorturl']?>-qrcode.svg');
		a.setAttribute('href', imgURI);
		a.setAttribute('target', '_blank');
		a.click();
	}

	let save = () => {
		let data = (new XMLSerializer()).serializeToString(svg);
		let svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
		let url = URL.createObjectURL(svgBlob);
		triggerDownload(url);;
	}

	btn.addEventListener('click', save);

	</script>

	</figure>


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
