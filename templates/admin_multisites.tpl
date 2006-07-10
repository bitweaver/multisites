{* $Header: /cvsroot/bitweaver/_bit_multisites/templates/admin_multisites.tpl,v 1.10 2006/07/10 00:32:52 nickpalmer Exp $ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />
	{jstabs}
		{jstab title="System Wide Settings"}
			{formfeedback warning='Settings in this tab effect all sites.'}
			{foreach from=$multisitesSettings key=feature item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}
			<div class="row">
				{formlabel label="Number of Members" for="member_number"}
				{forminput}
					{html_options name="multisites_limit_member_number" options=$memberLimit values=$memberLimit selected=$gBitSystem->getConfig('multisites_limit_member_number') id=member_number}
					{formhelp note="Here you can specify what number of sites are displayed at the bottom of a page."}
				{/forminput}
			</div>
		{/jstab}
	{/jstabs}

	<div class="row submit">
		<input type="submit" name="store_preferences" value="{tr}Save Settings{/tr}" />
	</div>
{/form}
{/strip}
