<main>

<h1>Neuen Kurzlink anlegen</h1>

<p>Ein Kurzlink darf jeweils immer nur einmal vorhanden sein. Bei HTTPs-Zielseiten in der ZielURL bitte https:// voranstellen.</p>

<form class="form-edit-link" action="" method="post">

	<fieldset class="form-2-cols">
	<label>Kurzurl:
		<input autofocus tabindex="1" type="text" required placeholder="z.B. /foodies" name="shorturl" value="">
	</label>

	<label>Kategorie:
		<select tabindex="3" name="category">
			<?php foreach (CATEGORIES as $category): ?>
				<option value="<?=$category?>"><?=$category?></option>
			<?php endforeach; ?>
		</select>
	</label>

	<label>UTM-Automatik:<br/>
		<input tabindex="4" type="checkbox" name="utm" value="1"> UTM Parameter an Link hängen
	</label>
	</fieldset>

	<fieldset>
	<label>ZielURL:
		<input tabindex="2" type="text" required placeholder="z.B. https://www.lr-online.de/ratgeber/foodies" name="url" value="">
	</label>
	</fieldset>

	<button tabindex="5" type="submit">Kurzlink speichern</button> <a href="<?=$referer?>" class="button light">abbrechen und zurück</a>

</form>

</main>
