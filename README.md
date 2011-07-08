SP Table Select
===============

SP Table Select from [Sparkplugs](http://getsparkplugs.com) allows you to setup a dropdown that pulls its values and labels from arbitrary columns in an arbitrary database table. Installation is just like normal, drag and drop this folder into `system/expressionengine/third_party` and install the fieldtype from the CP's **Add-Ons -> Fieldtypes** page.

Usage
-----

When you're adding a custom field, set the fieldtype to **SP Table Select**, give it a name and label, and scroll down to the fieldtype settings. Pick a table from the dropdown list and the value and label dropdowns will be automatically updated to reflect the new table. Pick a value column and a label column and create the field!

If you'd rather not use the dropdowns, or have a more complex query to make, you can use a custom SQL query by entering a query into the query box. Use an SQL **SELECT AS** clause to select your desired value as `sp_table_value` and your desired label as `sp_table_label`. Here's a quick example that pulls the ID and title from a WordPress database:

	SELECT id AS sp_table_value, post_title AS sp_table_label FROM wp_posts ORDER BY post_date DESC

Now, when you view the field on the publish page you'll see the dropdown populated from the datasource. It's that easy!

Template Tags
-------------

The `{custom_field}` template tag returns the label, and the `{custom_field:value}` returns the value.

More
----

This is a pretty simple addon and it's still very early days, so please report any bugs you find and apologies for the crap documentation. The code is well commented so feel free to dig through and take a peek. This addon is [Copyright (c)2011 Jamie Rumbelow at Sparkplugs](http://getsparkplugs.com).