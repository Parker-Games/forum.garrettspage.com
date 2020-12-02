{extends "layout_edit.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=encryption action=enter_key keyId=$encryption_key.keyId}">
		<div class="form-group row mx-0">
			<label for="shared_key" class="col-form-label">{tr _0=$encryption_key.name}Enter shared secret for key "%0"{/tr}</label>
			<input type="text" name="shared_key" value="{$shared_key|escape}" class="form-control">
			<div class="form-text">
				{tr}If you have a shared secret key not saved into your account, you can paste it here to encrypt or decrypt data with it.{/tr}
			</div>
		</div>
		<div class="submit">
			<input type="hidden" name="keyId" value="{$encryption_key.keyId|escape}">
			<input type="submit" class="btn btn-primary" value="Submit" onclick="needToConfirm=false;">
		</div>
	</form>
{/block}
