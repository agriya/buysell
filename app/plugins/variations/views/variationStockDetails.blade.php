@extends('popup')
@section('content')
    <h1>{{ Lang::get('variations::variations.product_variation_stock_head') }}</h1>
    <div class="pop-content">
        <div class="table-responsive">
        @if(count($matrix_data) > 0)
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>	
                        <td>{{ Lang::get('variations::variations.product_variation_slno') }}</td>
                        <td>{{ Lang::get('variations::variations.product_variation_attribute') }}</td>
                        <td>{{ Lang::get('variations::variations.product_variation_stock') }}</td>
                        <td>{{ Lang::get('variations::variations.product_variation_sales') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @if($count = 0)@endif
                    @foreach($matrix_data as $kay => $row)
                    <tr>	
                        <?php
                            $sales_count = new VariationsService;
                            $variation_sales_count = $sales_count->salesVariationCount($row->matrix_id);
                        ?>
                        <td>{{ $count+= 1 }}</td>
                        <td>{{ $row->label }}</td>
                        <td>{{ $row->stock }}</td>
                        <td>{{ $variation_sales_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
         @else
            {{ Lang::get('variations::variations.no_variation_available') }}
         @endif
        </div>
    </div>
@stop