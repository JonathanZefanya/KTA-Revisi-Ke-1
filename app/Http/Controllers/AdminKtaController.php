<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminKtaController extends Controller
{
    public function index(Request $r)
    {
        $query = User::query()->whereNotNull('membership_card_number');
        if($search = $r->get('q')){
            $query->where(function($q) use ($search){
                $q->where('name','like','%'.$search.'%')
                  ->orWhere('email','like','%'.$search.'%')
                  ->orWhere('membership_card_number','like','%'.$search.'%');
            });
        }
        // Eager load companies; avoid ambiguous column by qualifying id (belongsToMany pivot)
        $users = $query->with(['companies' => function($q){
            $q->select('companies.id','name');
        }])->orderBy('name')->paginate(25)->withQueryString();
        return view('admin.kta.index', compact('users'));
    }

    public function show(User $user, Request $r)
    {
        if(!$user->membership_card_number){
            return back()->with('error','User belum memiliki KTA.');
        }
        $company = $user->companies()->first();
        $logo = \App\Models\Setting::getValue('site_logo_path');
        $signature = \App\Models\Setting::getValue('signature_path');
        $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
        $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
        $qrPngBase64 = base64_encode($qrPngData);
        // Always use full mode for admin preview (light mode with complete info)
        return view('admin.kta.show',[ 'user'=>$user,'company'=>$company,'logo'=>$logo,'signature'=>$signature,'qrSvg'=>$qrSvg,'qrPng'=>$qrPngBase64,'validationUrl'=>$validationUrl ]);
    }

    public function pdf(User $user, Request $r)
    {
        if(!$user->membership_card_number){
            return back()->with('error','User belum memiliki KTA.');
        }
        $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
        $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
        $qrPngBase64 = base64_encode($qrPngData);
        $logo = \App\Models\Setting::getValue('site_logo_path');
        $signature = \App\Models\Setting::getValue('signature_path');
        $full = $r->boolean('full');
        $pdf = Pdf::loadView('kta.pdf',[ 'user'=>$user,'qrSvg'=>$qrSvg,'qrPng'=>$qrPngBase64,'validationUrl'=>$validationUrl,'logo'=>$logo,'signature'=>$signature,'full'=>$full ])->setPaper('a4','landscape');
        $safeNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        return $pdf->download('KTA-'.$safeNumber.($full?'-full':'-plain').'.pdf');
    }
}
