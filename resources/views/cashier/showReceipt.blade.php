<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel Store - Receipt - Sales ID: {{$sale -> id}}</title>

    <link type="text/css" rel="stylesheet" href="{{asset('/css/receipt.css')}}" media="all">
    <link type="text/css" rel="stylesheet" href="{{asset('/css/no-print.css')}}" media="print">
</head>
<body>
    <div id="wrapper">
        <div id="receipt-header">
            <h3 id="restaurant-name">Pixel Store</h3>
            <p>Address: asdasdwawaasdwa</p>
            <p>Tel: 123121312312</p>
            <p>Reference No: <strong>{{$sale -> id}}</strong></p>
        </div>
        
        <div id="receipt-body">
            <table class="tb-sale-detail">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($salesDetails as $salesDetail)
                    <tr>
                        <td width="30">{{$salesDetail -> menu_id}}</td>
                        <td width="180">{{$salesDetail -> menu_name}}</td>
                        <td width="50">{{$salesDetail -> quantity}}</td>
                        <td width="55">{{$salesDetail -> menu_price}}</td>
                        <td width="65">{{$salesDetail -> menu_price * $salesDetail -> quantity}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="tb-sale-total">
                <tbody>
                    <tr>
                        <td>Total Quantity</td>
                        <td>{{$salesDetails -> count()}}</td>

                        <td>Total Price</td>
                        <td>{{number_format($sale -> total_price, 2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Payment Type</td>
                        <td colspan="2">{{$sale -> payment_type}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Amount Paid</td>
                        <td colspan="2">{{number_format($sale -> total_received, 2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Change</td>
                        <td colspan="2">{{number_format($sale -> change, 2)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="receipt-footer">
            <p>Thank You!</p>
        </div>
        
        <div id="button">
            <a href="/cashier">
                <button class="btn btn-back">
                    Back
                </button>
            </a>

            <button class="btn btn-print" type="button" onclick="window.print(); return false;">
                Print
            </button>
        </div>
    </div>
    
</body>
</html>