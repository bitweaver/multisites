{* $Header$ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />
	{jstabs}
		{jstab title="System Wide Settings"}
			{formfeedback warning='These settings affect all sites.'}
			{foreach from=$multisitesSettings key=feature item=output}
				<div class="control-group">
					{formlabel label=$output.label for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
						{formhelp note=$output.note page=$output.page}
					{/forminput}
				</div>
			{/foreach}
			<div class="control-group">
				{formlabel label="Number of Members" for="member_number"}
				{forminput}
					{html_options name="multisites_limit_member_number" options=$memberLimit values=$memberLimit selected=$gBitSystem->getConfig('multisites_limit_member_number') id=member_number}
					{formhelp note="Here you can specify what number of sites are displayed at the bottom of a page."}
				{/forminput}
			</div>
		{/jstab}
	{/jstabs}

	<div class="control-group submit">
		<input type="submit" class="btn" name="store_preferences" value="{tr}Save Settings{/tr}" />
	</div>
{/form}
{/strip}
