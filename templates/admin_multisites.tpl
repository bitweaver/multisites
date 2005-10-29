{* $Header: /cvsroot/bitweaver/_bit_multisites/templates/admin_multisites.tpl,v 1.1.1.1.2.3 2005/10/29 11:45:03 squareing Exp $ *}
{strip}
{form}
	{jstabs}
		{jstab title="Multi-Homing Server"}
			{legend legend="Multi-Homing Server"}
				<input type="hidden" name="page" value="{$page}" />
				<input type="hidden" name="multisite_id" value="{$editSite.multisite_id}" />

				{formfeedback warning='When you change the values of these settings in the admin area of specified domains, the preferences will only apply to those domains.'}
				{formfeedback success=$successMsg error=$errorMsg warning=$warningMsg}

				<div class="row">
					{formlabel label="Server Name" for="server_name"}
					{forminput}
						<input type="text" id="server_name" name="server_name" size="50" value="{$editSite.server_name}" />
						{formhelp note="Enter the server name you wish to use for multi-homing here. An example would be <strong>www.bitweaver.org</strong>."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for="description"}
					{forminput}
						<textarea cols="50" rows="3" name="description" id="description">{$editSite.description}</textarea>
						{formhelp note="Enter a brief description what this server name is intended for. The description is for your own reference."}
					{/forminput}
				</div>
			{/legend}
		{/jstab}

		{jstab title="Server Settings"}
			{legend legend="Server Settings"}
				<div class="row">
					{formlabel label="Site Title" for="site_title"}
					{forminput}
						<input type="text" id="site_title" name="server_prefs[siteTitle]" size="50" value="{$editSite.prefs.siteTitle|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Slogan" for="site_slogan"}
					{forminput}
						<input type="text" id="site_slogan" name="server_prefs[site_slogan]" size="50" value="{$editSite.prefs.site_slogan|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Description" for="site_description"}
					{forminput}
						<input size="50" type="text" name="server_prefs[site_description]" id="site_description" maxlength="180" value="{$editSite.prefs.site_description|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Keywords" for="site_keywords"}
					{forminput}
						<textarea cols="50" rows="5" name="server_prefs[site_keywords]" id="site_keywords">{$editSite.prefs.site_keywords|escape}</textarea>
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Home page" for="bitIndex"}
					{forminput}
						<select name="server_prefs[bitIndex]" id="bitIndex">
							<option value=""></option>
							<option value="my_page"{if $editSite.prefs.bitIndex eq 'my_page'} selected="selected"{/if}>{tr}My Page{/tr}</option>
							<option value="user_home"{if $editSite.prefs.bitIndex eq 'user_home'} selected="selected"{/if}>{tr}User's homepage{/tr}</option>
							<option value="group_home"{if $editSite.prefs.bitIndex eq 'group_home'} selected="selected"{/if}>{tr}Group home{/tr}</option>
							<option value="custom_home"{if $editSite.prefs.bitIndex eq $editSite.prefs.urlIndex and $editSite.prefs.bitIndex} selected="selected"{/if}>{tr}Custom home{/tr}</option>
							{foreach key=name item=package from=$gBitSystem->mPackages }
								{if $package.homeable && $package.installed}
									<option {if $editSite.prefs.bitIndex eq $package.url|cat:"index.php"}selected="selected"{/if} value="{$package.url|cat:"index.php"}">{$package.name|capitalize}</option>
								{/if}
							{/foreach}
						</select>
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="URI for custom home" for="urlIndex"}
					{forminput}
						<input type="text" id="urlIndex" name="server_prefs[urlIndex]" value="{$editSite.prefs.urlIndex|escape}" size="50" />
					{/forminput}
				</div>
			{/legend}
		{/jstab}

		{jstab title="Look and Feel"}
			{legend legend="Look and Feel"}
				<div class="row">
					{formlabel label="Theme" for="style"}
					{forminput}
						{html_options name="server_prefs[style]" id="style" output=$styles values=$styles selected=$editSite.prefs.style}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Language" for="bitlanguage"}
					{forminput}
						<select name="server_prefs[bitlanguage]" id="bitlanguage">
							<option value=""></option>
							{foreach from=$languages key=langCode item=lang}
								<option value="{$langCode}" {if $editSite.prefs.bitlanguage eq $langCode}selected="selected"{/if}>{$lang.full_name|escape}</option>
							{/foreach}
						</select>
					{/forminput}
				</div>

				{foreach from=$layoutSettings key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
						{/forminput}
					</div>
				{/foreach}
			{/legend}
		{/jstab}
	{/jstabs}

	<div class="row submit">
		<input type="submit" name="store_server" value="{tr}Save Settings{/tr}" />
		{if $editSite.multisite_id}
			<br />{smartlink ititle="Add New Site" page=multisites}
		{/if}
	</div>

	<div class="row">
		{formhelp note='Any settings that are not set here, will default back to the setting found in the <em>normal</em> administration panel.'}
	</div>
{/form}

<table class="data">
	<caption>{tr}Saved Servers{/tr}</caption>
	<tr>
		<th style="width:18%;">{tr}Server Name{/tr}</th>
		<th style="width:30%;">{tr}Description{/tr}</th>
		<th style="width:50%;">{tr}Settings{/tr}</th>
		<th style="width:2%;">{tr}Actions{/tr}</th>
	</tr>

	{foreach from=`$listMultisites` item=site}
		<tr class="{cycle values='odd,even'}">
			<td>{$site.server_name}</td>
			<td>{$site.description}</td>
			<td>
				{foreach from=`$site.prefs` key=pref item=value}
					{if $value}
						<strong>{$pref}</strong>: {$value}<br />
					{/if}
				{/foreach}
			</td>
			<td class="actionicon">
				{smartlink ititle="edit" ibiticon="liberty/edit" action="edit" ms_id=`$site.multisite_id` page=$page}
				{smartlink ititle="remove" ibiticon="liberty/delete" action="delete" ms_id=`$site.multisite_id` page=$page}
			</td>
		</tr>
	{foreachelse}
		<tr class="norecords">
			<td colspan="3">{tr}No Records Found{/tr}</td>
		</tr>
	{/foreach}
</table>
{/strip}
