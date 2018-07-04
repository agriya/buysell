<h2 class="title-one">Etsy items</h2>

<p>If you have a shop in Etsy and would like to sell those items in {{Config::get('generalConfig.site_name')}} too, it's easy and quick to bring them all to your {{Config::get('generalConfig.site_name')}} shop.</p>
<p>Here's the CSV Upload Guide that tells you how to import when you are trying to add items from your Etsy shop. We have termed the product import type as <strong>Etsy</strong>.</p>
        
<ul class="list-unstyled well">
	<li><i class="fa fa-chevron-right"></i><span>As the first step, export your items to a csv file from your shop settings in Etsy.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Save the exported csv file to your local disk. Now you are ready to import your items into your {{Config::get('generalConfig.site_name')}} shop.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Go to <strong>Account Menu</strong> &gt; <strong>CSV Importer</strong>. The CSV Upload and the Uploaded Files list appears.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Select the Import type as <strong>Etsy</strong>.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Click <strong>Browse</strong> and select the etsy product csv file to upload.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Click <strong>Submit</strong>. A background process takes place to import the items. When all the items are imported, status shows <strong>Completed</strong>. Preview the csv file and ensure that all the product datas are correct and not missing or a duplicate data.</span></li>
</ul>
        
<p>All the items you have imported will be in <strong>Draft</strong> status. You won't be able to sell the items immediately after importing because it requires editing like shipping information. Edit the product information of those items you desire in order to publish and sell them. On successful activation, the items will be listed as <strong>Active </strong>in your <strong>My Products</strong>.</p>
        
<h2 class="title-one margin-top-20">Non- Etsy (General) items in detail</h2>

<p>The CSV Importer tool helps you to import your shop and shop products from sites like Etsy, Artfire and mass upload them into your shop at {{Config::get('generalConfig.site_name')}}.</p>
<p>Here's the CSV Upload Guide that tells you how to prepare a csv file and import when you are trying to add products from websites other than Etsy like Artfire. We have termed the non-etsy products import type as <strong>General</strong>.</p>
        
<ul class="list-unstyled well">
	<li><i class="fa fa-chevron-right"></i><span>From the <strong>Account Menu</strong> , select <strong>CSV Importer</strong>. We have provided a sample file for your reference. You can download and save it to your local disk. You can use an excel worksheet or Edit Plus to add and manage your product data.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Add the respective values for the attributes.</span></li>
</ul>
        
<h3 class="title-one">Tip to add values:</h3>
		
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Field name</th>
			<th>Value</th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<td>
				<p>Is Downloadable</p>
				<p>(If the product you are selling is in a downloadable format)</p>
			</td>
			<td><p>1</p></td>
		</tr>
		<tr>
			<td>
				<p>Is Downloadable</p>
				<p>(If the product you are selling is a physical good)</p>
			</td>
			<td><p>0</p></td>
		</tr>
		<tr>
			<td>
				<p>IMAGE_ATTACHED</p>
				<p>(If the images are not hosted and you are providing all the images in a zip file)</p>
				<p>Specify the image file name with extension (jpg) for THUMB_IMAGE, DEFAULT_IMAGE and PREVIEW_IMAGE</p>
			</td>
			<td><p>1</p></td>
		</tr>
		<tr>
			<td>
				<p>IMAGE_ATTACHED</p>
				<p>(If not providing all the images in a zip file)</p>
			</td>
			<td><p>0</p></td>
		</tr>
		<tr>
			<td><p>SHIPPING_TEMPLATE</p></td>
			<td><p>If your product requires a shipping template, enter the valid shipping template name created in your {{Config::get('generalConfig.site_name')}} shop.</p></td>
		</tr>
		<tr>
			<td><p>Demo URL</p></td>
			<td><p>This is optional</p></td>
		</tr>
	</tbody>
</table>
       
<p class="well margin-top-20"><i class="fa fa-chevron-right"></i><span>Get the category id from 
<a target="_blank" href="{{ URL::action('App\Plugins\Importer\Controllers\ImporterController@getCategoryListing') }}" title="{{ Lang::get('importer::importer.get_category_id_from') }}">
Category Listing </a></span></p>

<h3 class="title-one">Tip: You can make note of the category ids in a text file for your quick reference.</h3>

<p>If you are using Edit Plus or any text editor, you must include each new product on a new line. The attributes that you define must be enclosed in double quotation, separated with commas and must be in the same order as the first row. These formats apply only to product data from an non-etsy shop.</p>
<p>If you are editing in Excel, each product will be on a new line. The attributes that you define will fall into the column of the attribute that is listed in the first row.</p>

<ul class="list-unstyled well">
	<li><i class="fa fa-chevron-right"></i><span>Save the text editor or the excel file as a csv file.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>View the csv file and ensure your datas are correctly added.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Now you are ready to import your items into your {{Config::get('generalConfig.site_name')}} shop.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Go to <strong>Account Menu</strong> &gt; <strong>CSV Importer</strong>. The CSV Upload and the Uploaded Files list appears.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Select the Import type as <strong>General</strong>.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Click <strong>Browse</strong> and select the csv file to upload.</span></li>
	<li><i class="fa fa-chevron-right"></i><span>Click <strong>Submit</strong>. A background process takes place to import the items. When all the items are imported, status shows <strong>Completed</strong>. Preview the csv file and ensure that all the product datas are correct and not missing or a duplicate data. If you find any of these issues, go back to the csv file and make the required corrections and re-upload the file.</span></li>
</ul>

<p>All the items you have imported will be in <strong>Draft</strong> status. You won't be able to sell the items immediately after importing because it requires editing. Edit the product information of those items you desire to sell and publish them. On successful activation, the items will be listed as <strong>Active </strong>in your <strong>My Products</strong>.</p>