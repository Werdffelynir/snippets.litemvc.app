<form class="editSnippet" action="<?=URL.'/edit/exampleEdit'?>" method="post">
    <input type="submit" value="Сохраниеть" style="display: block" />

    <textarea spellcheck="false" name="text" ><?php echo htmlspecialchars($data['text']); ?></textarea>
    <input type="text" hidden="hidden" name="id" value="<?=$data['id']?>" />
    <input type="text" hidden="hidden" name="type" value="<?=$data['type']?>" />
</form>

