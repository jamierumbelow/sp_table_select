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

Matrix Support
-------------

SP Table Select fully supports the excellent [Pixel and Tonic Matrix](http://pixelandtonic.com/matrix/) add-on. Just add a SP Table Select field to your Matrix and you're good to go!

License
-------

SP Table Select is licensed under the MIT license:

```
Copyright (c) 2013 Jamie Rumbelow, Adrienne Leigh

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
