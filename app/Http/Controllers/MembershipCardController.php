<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use App\Models\User; use Illuminate\Support\Facades\Crypt; use Illuminate\Support\Facades\URL; use SimpleSoftwareIO\QrCode\Facades\QrCode; use Barryvdh\DomPDF\Facade\Pdf;

class MembershipCardController extends Controller
{
    public function show(Request $r)
    {
        $user=$r->user();
        if(!$user->hasActiveMembershipCard()) return redirect()->route('kta')->with('error','Kartu belum tersedia.');
    $logo = \App\Models\Setting::getValue('site_logo_path');
    $signature = \App\Models\Setting::getValue('signature_path');
    return view('kta.card',compact('user','logo','signature'));
    }

    public function pdf(Request $r)
    {
        $user=$r->user();
        if(!$user->hasActiveMembershipCard()) return back()->with('error','Kartu belum tersedia.');
    // Public validation page URL (number sanitized by replacing slashes)
    $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
    $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
    // SVG sometimes not rendered reliably in DomPDF; generate PNG (base64) for embedding
    $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
    $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
    $qrPngBase64 = base64_encode($qrPngData);
    $logo = \App\Models\Setting::getValue('site_logo_path');
    $signature = \App\Models\Setting::getValue('signature_path');
    $full = $r->boolean('full'); // full=1 => gunakan layout berpanel; default plain
    $pdf = Pdf::loadView('kta.pdf',[ 'user'=>$user, 'qrSvg'=>$qrSvg, 'qrPng'=>$qrPngBase64, 'validationUrl'=>$validationUrl, 'logo'=>$logo, 'signature'=>$signature, 'full'=>$full ])->setPaper('a4','landscape');
    // sanitize filename (membership number contains slashes)
    $safeNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
    return $pdf->download('KTA-'.$safeNumber.'.pdf');
    }

    public function validateCard(Request $r)
    {
        $hash = $r->get('hash');
        $user = User::whereNotNull('membership_card_number')->get()->first(function($u) use ($hash){return sha1($u->membership_card_number.$u->id)===$hash;});
        if(!$user) abort(404);
        return response()->json([
            'valid'=> $user->hasActiveMembershipCard(),
            'member'=>[
                'name'=>$user->name,
                'membership_card_number'=>$user->membership_card_number,
                'issued_at'=>$user->membership_card_issued_at?->toDateString(),
                'expires_at'=>$user->membership_card_expires_at?->toDateString(),
            ]
        ]);
    }

    public function publicPage(Request $r, User $user, string $number)
    {
        // Reverse: original number has slashes, stored membership number uses '/'
        $sanitized = str_replace(['/', '\\'], '-', $user->membership_card_number ?? '');
        if(!$user->membership_card_number || $sanitized !== $number){
            abort(404);
        }
        $company = $user->companies()->first();
        $isValid = $user->hasActiveMembershipCard();
        return view('kta.public', [
            'user'=>$user,
            'company'=>$company,
            'isValid'=>$isValid,
        ]);
    }

}
