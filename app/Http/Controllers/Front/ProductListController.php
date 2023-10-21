<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderProduct;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;



class ProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('quantity', '>', '1')->paginate(8);
        return view('front.home', compact('products'));
    }
    public function search(Request $request)
    {

        $search = $request->query->get('query');
        $products = Product::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate(8);

        return view('front.home', compact('products'));
    }



    // to show all orders and order details
    public function orderList(Request $request)
    {
        $user= $request->user() ;
            $order = Order::where('user_id',$user->id)->first();
            
            $order_items = OrderProduct::where('order_id', $order->id)->get();
         
            $totalPrice = $order_items->sum(function ($item) {
                return $item->price * $item->quantity;
            });
     
            $orderDetails = OrderProduct::all();
            return view('front.track', compact('order', 'orderDetails','totalPrice'));
    }




    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            "order_status" => "cancelled"
        ]);
        Alert::error('Cancelled', 'your order has been cancelled');

        return redirect()->route('orderList');
    }
    //make filter using Date
    public function filter(Request $request)
    {
        $orderDetails = OrderProduct::all();
        $start_date = $request->start_date;
        $end_date = $request->end_date;



        if ($start_date && $end_date) {
            $user_orders = Order::whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)))
                ->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)))
                ->get();
        } else {
            $user_orders = [];
        }
    
        return view('front.track', compact('orders','orderDetails'));
    }

    public function pdf($id)
    {
        $order =Order::findOrFail($id);
        $pdf = Pdf::loadView('front.pdf.invoice',compact('order'));
        return $pdf->download('front.invoice.pdf');
    }


    /**
     * Display the specified resource.
     */
}
