<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Table;
use App\Category;
use App\Menu;
use App\Sales;
use App\SalesDetail;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    //display the cashier page
    public function index() {
        $categories = Category::all();
        return view('cashier.index') -> with('categories', $categories);
    }

    public function getTables() {
        $tables = Table::all();
        $html = '';
        foreach($tables as $table) {
            $html .= '<div class="col-md-2 mb-4">';
            $html .= '
                <button class="btn btn-primary btn-table" data-id="'.$table -> id.'" data-tablename="'.$table -> tableName.'">
                <img class="img-fluid" src="'.url('/images/table.png').'"/>
                <br>';
        
            if ($table -> status == "available") {
                $html .= '<span class="badge badge-success">'.$table -> tableName.'</span>'; 
            }
            else {
                $html .= '<span class="badge badge-danger">'.$table -> tableName.'</span>';
            }        
                
            $html .= '</button>';
            $html .= '</div>';
        }

        return $html;
    }

    public function getMenuByCategory($category_id) {
        $menus = Menu::where('category_id', $category_id) -> get();
        $html = '';
        foreach($menus as $menu) {
            $html .= '
            <div class="col-md-3 text-center">
                <a class="btn btn-outline-secondary btn-menu" data-id="'.$menu -> id.'">
                    <img class="img-fluid" src="'.url('/menu_images/'.$menu -> menuImage).'">
                    <br>
                    '.$menu -> menuName.'
                    <br>
                    $ '.number_format($menu -> price).'
                </a>
            </div>
            '; 
        }

        return $html;
    }

    // public function orderFood(Request $request) {
    //     return $request -> menu_id;
    // }

    public function orderFood(Request $request) {
        //return $request -> menu_id;

        $menu = Menu::find($request -> menu_id);
        $table_id = $request -> table_id;
        $table_name = $request -> table_name;

        $sale = Sales::where('table_id', $table_id) -> where('sales_status', 'unpaid') -> first();
        
        //create new sales record if there are none
        if(!$sale) {
            $user = Auth::user();
            $sale = new Sales();

            $sale -> table_id = $table_id;
            $sale -> table_name = $table_name;
            $sale -> user_id = $user -> id;
            $sale -> user_name = $user -> name;
            $sale -> save();
            $sales_id = $sale -> id;

            //update table status
            $table = Table::find($table_id);
            $table -> status = "unavailable";
            $table -> save();
        }
        else {
            $sales_id = $sale -> id;
        }

        //add details to sales_detail table
        $saleDetail = new SalesDetail();
        $saleDetail -> sales_id = $sales_id;
        $saleDetail -> menu_id = $menu -> id;
        $saleDetail -> menu_name = $menu -> menuName;
        $saleDetail -> menu_price = $menu -> price;
        $saleDetail -> quantity = $request -> quantity;
        $saleDetail -> save();

        //update total price in sales table
        $sale -> total_price = $sale -> total_price + ($request -> quantity * $menu -> price);
        $sale -> save();

        $html = $this -> getSalesDetails($sales_id);
        return $html;
    }

    public function getSalesDetailsByTable($table_id) {
        $sale = Sales::where('table_id', $table_id) -> where('sales_status', 'unpaid') -> first();
        $html = '';

        if($sale) {
            $sales_id = $sale -> id;
            $html .= $this -> getSalesDetails($sales_id);
        }
        else {
            $html .= "Sales Detail Unavailable";
        }

        return $html;
    }

    private function getSalesDetails($sales_id) {
        //list all sales details
        $html = '<p>Sales ID: '.$sales_id.'</p>';
        $saleDetails = SalesDetail::where('sales_id', $sales_id) -> get();
        $html .= '<div class="table-responsive-md" style="overflow-y:scroll; height: 400px; border: 1px solid #343A40">
        <table class="table table-stripped table-dark">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Menu</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>';
            
            $showPaymentButton = true;
        
            foreach($saleDetails as $saleDetail) {

                $decreaseButton = '<button data-id="'.$saleDetail -> id.'" 
                class="btn btn-danger btn-sm btn-decrease-quantity" disabled>-</button>';

                if($saleDetail -> quantity > 1) {
                    $decreaseButton = '<button data-id="'.$saleDetail -> id.'" 
                    class="btn btn-danger btn-sm btn-decrease-quantity">-</button>';
                }

                $html .= '
                <tr>
                    <td>'.$saleDetail -> menu_id.'</td>
                    <td>'.$saleDetail -> menu_name.'</td>
                    <td> '.$decreaseButton.' '.$saleDetail -> quantity.' 
                    <button data-id="'.$saleDetail -> id.'" class="btn btn-primary btn-sm btn-increase-quantity">+</button>
                    </td>
                    <td>'.$saleDetail -> menu_price.'</td>
                    <td>'.($saleDetail -> menu_price * $saleDetail -> quantity).'</td>';
                
                    if($saleDetail -> status == "unconfirmed") {
                        $showPaymentButton = false;
                        $html .= '<td><a data-id="'.$saleDetail -> id.'" class="btn btn-danger btn-delete-salesdetail">
                        <i class="fas fa-trash-alt"></i></a></td>';   
                    }
                    else {
                        $html .= '<td><i class="fas fa-check-circle"></i></td>';
                    }

                //$html .= '<td>'.$saleDetail -> status.'</td>';
                $html .= '</tr>';
            }
        $html .= '</tbody>
        </table>
        </div>';

        $sale = Sales::find($sales_id);
        $html .= '<hr>';
        $html .= '<h3>Total Amount: $'.number_format($sale -> total_price).'</h3>';
        
        if($showPaymentButton) {
            $html .= '<button data-id="'.$sales_id.'" data-totalAmount="'.$sale -> total_price.'" class="btn btn-success btn-block btn-payment" 
            data-toggle="modal" data-target="#exampleModal">Payment</button>';
        } else {
            $html .= '<button data-id="'.$sales_id.'" class="btn btn-warning btn-block btn-confirm-order">Confirm Order</button>';
        }

        

        return $html;
    }  

    public function confirmOrderStatus(Request $request) {
        $sales_id = $request -> sales_id;
        $salesDetails = SalesDetail::where('sales_id', $sales_id) -> update(['status' => 'confirmed']);

        $html = $this -> getSalesDetails($sales_id);

        return $html;
    }

    public function increaseQuantity(Request $request) {
        $salesDetail_id  = $request -> salesDetail_id;
        $salesDetail = SalesDetail::where('id', $salesDetail_id) -> first();
        $salesDetail -> quantity = $salesDetail -> quantity + 1;
        $salesDetail -> save();

        $sale = Sales::where('id', $salesDetail -> sales_id) -> first(); 
        $sale -> total_price = $sale -> total_price + $salesDetail -> menu_price;
        $sale -> save();

        $html = $this -> getSalesDetails($salesDetail -> sales_id);

        return $html;
    }

    public function decreaseQuantity(Request $request) {
        $salesDetail_id  = $request -> salesDetail_id;
        $salesDetail = SalesDetail::where('id', $salesDetail_id) -> first();
        $salesDetail -> quantity = $salesDetail -> quantity - 1;
        $salesDetail -> save();

        $sale = Sales::where('id', $salesDetail -> sales_id) -> first(); 
        $sale -> total_price = $sale -> total_price - $salesDetail -> menu_price;
        $sale -> save();

        $html = $this -> getSalesDetails($salesDetail -> sales_id);

        return $html;
    }

    
    public function deleteSalesDetail(Request $request) {
        $salesDetail_id = $request -> salesDetail_id;
        
        $salesDetail = SalesDetail::find($salesDetail_id);
        $sales_id = $salesDetail -> sales_id;
        $menu_price = ($salesDetail -> menu_price * $salesDetail -> quantity);
        $salesDetail -> delete();

        //update total price
        $sale = Sales::find($sales_id);
        $sale -> total_price = $sale -> total_price - $menu_price;
        $sale -> save();


        //check if sale detail match the sales id
        $salesDetail = SalesDetail::where('sales_id', $sales_id) -> first();
        if ($salesDetail) {
            $html = $this -> getSalesDetails($sales_id);
        }
        else {
            $html = "Sales Detail Unavailable";
        }

        return $html;
    }

    public function savePayment(Request $request) {
        $sales_id = $request -> saleId;
        $receivedAmount = $request -> receivedAmount;
        $paymentType = $request -> paymentType;

        $sale = Sales::find($sales_id);
        $sale -> total_received = $receivedAmount;
        $sale -> change = $receivedAmount - $sale -> total_price;
        $sale -> payment_type = $paymentType;
        $sale -> sales_status = "paid";
        $sale -> save();
        
        $table = Table::find($sale -> table_id);
        $table -> status = "available";
        $table -> save();

        return "/cashier/showReceipt/".$sales_id;
    }

    public function showReceipt($sales_id) {
        $sale = Sales::find($sales_id);
        $salesDetails = SalesDetail::where('sales_id', $sales_id) -> get();
        return view('cashier.showReceipt') -> with('sale', $sale) -> with('salesDetails', $salesDetails);
    }
}
