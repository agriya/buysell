@extends('base')
@section('content')
<div class="row">
	<div class="span9">
		<h1>Product Name</h1>
	</div>
</div>

<hr>
<?php $product_id = Str::random(8); ?>
<div class="row">
	<div class="span6">
		<div class="span6">
			<address>
				<strong>Product Code:</strong> <span>{{ $product_id }}</span><br />
				<strong>Availability:</strong> <span>In Stock</span><br />
			</address>
		</div>

		<div class="span6">
			<h2>
				<strong>Price: ${{ 100 }}</strong><br /><br />
			</h2>
		</div>

		<div class="span8">
			<form class="form-horizontal" method="post" action="">
				<input type="hidden" name="item_id" id="item_id" value="{{ $product_id }}" />

				<div class="control-group">
					<label class="control-label text-align-left" for="qty">Quantity:</label>
				    <div class="controls">
						<input type="text" class="span1" name="qty" id="qty" value="1">
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" name="action" value="add_to_cart" class="btn btn-primary">Add to Cart</button>
				</div>
			</form>
		</div>
	</div>
</div>
@stop
