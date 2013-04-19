{* $Header$ *}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=mul class="dropdown-menu sub-menu"tisites">{tr}Multisites{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.MULTISITES_PKG_URL}admin/edit_sites.php">{tr}Edit Sites{/tr}</a></li>
</ul>

