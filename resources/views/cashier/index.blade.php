@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row" id="table-detail"></div>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <button class="btn btn-primary btn-block" id="btn-show-table">View All Tables</button>
            <div id="selected-table"></div>
            <div id="order-detail"></div>
        </div>
        <div class="col-md-7">
            <nav>
                <div class="nav nav-tabs" id="nav-tabs" role="tablist">
                    @foreach($categories as $category)
                        <a class="nav-item nav-link" data-id="{{$category -> id}}" data-toggle="tab">
                            {{$category -> categoryName}}
                        </a>
                    @endforeach
                </div>
            </nav>
            <div id="list-menu" class="row mt-2"></div>

        </div>
    </div>
</div>

  
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <h3 class="totalAmount"></h3>
        <h3 class="changeAmount"></h3>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">$</span>
            </div>
            <input type="number" id="received-amount" class="form-control">
        </div>
        <div class="form-group">
            <label for="payment">Payment Type</label>
            <select class="form-control" id="payment-type">
                <option value="cash">Cash</option>
                <option value="card">Credit/Debit Card</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-save-payment" disabled>Pay</button>
    </div>
    </div>
</div>
</div>



<script>
$(document).ready(function() {
    //hide table-detail
    $('#table-detail').hide();
    
    //show all table
    $("#btn-show-table").click(function() {
        if ($('#table-detail').is(":hidden")) {
            $.get("/cashier/getTable", function(data) {
                $("#table-detail").html(data);
                $("#table-detail").slideDown('fast');
                $("#btn-show-table").html('Hide Tables').removeClass('btn-primary').addClass('btn-danger');
            })
        }
        else {
            $("#table-detail").slideUp('fast');
            $("#btn-show-table").html('View All Tables').removeClass('btn-danger').addClass('btn-primary');
        }
        
    });

    //load menu by category
    $(".nav-link").click(function(){
        $.get("/cashier/getMenuByCategory/"+$(this).data("id"), function(data){
            $("#list-menu").hide();
            $("#list-menu").html(data);
            $("#list-menu").fadeIn('fast');
        });
    });

    var selected_table = "";
    var selected_table_name = "";
    var sales_id = "";

    //show table data
    $("#table-detail").on("click", ".btn-table", function(){
        selected_table = $(this).data("id");
        selected_table_name = $(this).data("tablename");

        $("#selected-table").html('<br><h3>Table: '+ selected_table_name +'</h3><hr>');
        $.get("/cashier/getSalesDetailsByTable/" + selected_table, function(data) {
            $("#order-detail").html(data);
        });

    });

    $("#list-menu").on("click", ".btn-menu", function() {
        if (selected_table == "") {
            alert("No table selected");
        }
        else {
            var menu_id = $(this).data("id");
            $.ajax({
                type: "POST",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "menu_id": menu_id,
                    "table_id": selected_table,
                    "table_name": selected_table_name,
                    "quantity": 1
                },
                url: "/cashier/orderFood",
                success: function(data) {
                    $("#order-detail").html(data);
                }
            });

        }
    });

    $("#order-detail").on('click', ".btn-confirm-order", function() {
        var salesId = $(this).data("id");

        $.ajax({
           type: "POST",
           data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "sales_id": salesId
           },
           url: "/cashier/confirmOrderStatus",
           success: function(data) {
                $("#order-detail").html(data);
           } 
        });
    });

    //delete sales detail
    $("#order-detail").on('click', ".btn-delete-salesdetail", function() {
        var salesDetailID = $(this).data("id");
        $.ajax({
           type: "POST",
           data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "salesDetail_id": salesDetailID
           },
           url: "/cashier/deleteSalesDetail",
           success: function(data) {
                $("#order-detail").html(data);
           }
        });
    });

    //increase quantity
    $("#order-detail").on('click', ".btn-increase-quantity", function() {
        var salesDetailID = $(this).data("id");
        $.ajax({
           type: "POST",
           data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "salesDetail_id": salesDetailID
           },
           url: "/cashier/increaseQuantity",
           success: function(data) {
                $("#order-detail").html(data);
           }
        });
    });

    //decrease quantity
    $("#order-detail").on('click', ".btn-decrease-quantity", function() {
        var salesDetailID = $(this).data("id");
        $.ajax({
           type: "POST",
           data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "salesDetail_id": salesDetailID
           },
           url: "/cashier/decreaseQuantity",
           success: function(data) {
                $("#order-detail").html(data);
           }
        });
    });


    //when payment button is clicked
    $("#order-detail").on('click', ".btn-payment", function() {
        var totalAmount = $(this).attr('data-totalAmount');

        $(".totalAmount").html("Total Amount: $" + totalAmount);
        $("#received-amount").val('');
        $(".changeAmount").html('');

        sales_id = $(this).data('id');
    });

    //calculate change
    $("#received-amount").keyup(function() {
        var totalAmount = $(".btn-payment").attr('data-totalAmount');
        var receivedAmount = $(this).val();
        var changeAmount = receivedAmount - totalAmount;

        $(".changeAmount").html("Total Change: $" + changeAmount);

        //check if received amount is sufficient
        if (changeAmount >= 0) {
            $(".btn-save-payment").prop('disabled', false);
        }
        else {
            $(".btn-save-payment").prop('disabled', true);
        }
    });

    //save payment
    $(".btn-save-payment").click(function() {
        var receivedAmount = $("#received-amount").val();
        var paymentType = $("#payment-type").val();
        var saleId = sales_id;

        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleId": saleId,
                "receivedAmount": receivedAmount,
                "paymentType": paymentType
            },
            url: "/cashier/savePayment",
            success: function(data) {
                window.location.href = data;
            }
        });
    });
    
}); 

</script>

@endsection
