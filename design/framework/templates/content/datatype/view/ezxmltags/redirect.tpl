{set-block scope=global variable=cache_ttl}0{/set-block}
{def	$main_node = $#node
	$template_vars = template_vars()
}
{if is_set($template_vars.child)}
	{def $child_id  = $template_vars.child.node_id}
{else}
	{def $child_id  = false()}
{/if}
{if or(eq($child_id, false()), eq($child_id, $main_node))}
	{concat("Location: ",$redirect_url)|header}
{/if}
