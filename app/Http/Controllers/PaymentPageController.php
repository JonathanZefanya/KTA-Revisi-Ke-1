<?php
namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Invoice;
class PaymentPageController extends Controller { public function index(Request $r){ $user=$r->user(); $invoices=Invoice::where('user_id',$user->id)->latest()->get(); $selected=null; if($id=$r->query('invoice')){ $selected=$invoices->firstWhere('id',(int)$id); } return view('pembayaran', compact('invoices','selected','user')); }}
