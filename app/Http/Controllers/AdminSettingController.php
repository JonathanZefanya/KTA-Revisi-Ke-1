<?php
namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Setting; use App\Models\PaymentRate; use App\Models\RenewalPaymentRate; use App\Models\BankAccount; use Illuminate\Support\Facades\Storage; 
class AdminSettingController extends Controller {
    public function index(){
        $site_name = Setting::getValue('site_name');
        $signature_path = Setting::getValue('signature_path');
    $rates = PaymentRate::orderBy('jenis')->orderBy('kualifikasi')->get();
    $renewalRates = RenewalPaymentRate::orderBy('jenis')->orderBy('kualifikasi')->get();
        $defaultJenis=['BUJKN','BUJKA','BUJKPMA'];
        // Updated kualifikasi list as requested
        $defaultKual=[
            'Kecil / Spesialis 1',
            'Menengah / Spesialis 2',
            'Besar BUJKN / Spesialis 2',
            'Besar PMA / Spesialis 2',
            'BUJKA',
        ];
        // ensure grid matrix presence
        $matrix=[]; foreach($defaultJenis as $j){ foreach($defaultKual as $k){ $matrix["$j|$k"]= $rates->firstWhere('jenis',$j)?->firstWhere('kualifikasi',$k) ?? null; }}
    $bankAccounts = BankAccount::orderBy('sort')->orderBy('bank_name')->get();
    $settings = Setting::query()->pluck('value','key');
    return view('admin.settings.index', compact('site_name','signature_path','rates','renewalRates','defaultJenis','defaultKual','bankAccounts','settings'));
    }
    public function updateSite(Request $r){ $data=$r->validate([
        'site_name'=>['required','string','max:100'],
        'site_logo'=>['nullable','image','mimes:png,jpg,jpeg','max:2048'], // 2MB logo
        'mail_host'=>['nullable','string','max:120'],
        'mail_port'=>['nullable','integer'],
        'mail_username'=>['nullable','string','max:120'],
        'mail_password'=>['nullable','string','max:120'],
        'mail_encryption'=>['nullable','in:tls,ssl,starttls'],
        'mail_from_address'=>['nullable','email','max:150'],
        'mail_from_name'=>['nullable','string','max:150'],
    ]);
        Setting::setValue('site_name',$data['site_name']);
        foreach(['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'] as $k){ if(isset($data[$k])) Setting::setValue($k, (string) $data[$k]); }
        if($r->hasFile('site_logo')){ $path=$r->file('site_logo')->store('uploads/site','public'); Setting::setValue('site_logo_path',$path); }
        return back()->with('success','Pengaturan situs diperbarui'); }
    public function storeSignature(Request $r){ $data=$r->validate(['signature'=>['required','string']]); $raw=$data['signature']; if(str_starts_with($raw,'data:image/png;base64,')){ $raw=substr($raw,22);} $bin=base64_decode($raw); $path='uploads/signatures/'.date('Ymd_His').'.png'; Storage::disk('public')->put($path,$bin); Setting::setValue('signature_path',$path); return back()->with('success','Tanda tangan disimpan'); }
    protected function parseMoney(?string $v): float { if($v===null||$v==='') return 0; $v=str_replace(['.',' ',','],['','',''],$v); return (float)$v; }
    public function saveRates(Request $r){ $amounts=$r->input('amount',[]); foreach($amounts as $row){ $j=$row['jenis']??null; $k=$row['kualifikasi']??null; $val=$row['amount']??null; if($j && $k){ PaymentRate::upsertRate($j,$k, $this->parseMoney($val)); }} return back()->with('success','Tarif registrasi disimpan'); }
    public function saveRenewalRates(Request $r){ $amounts=$r->input('renewal_amount',[]); foreach($amounts as $row){ $j=$row['jenis']??null; $k=$row['kualifikasi']??null; $val=$row['amount']??null; if($j && $k){ RenewalPaymentRate::upsertRate($j,$k, $this->parseMoney($val)); }} return back()->with('success','Tarif perpanjangan disimpan'); }
    public function storeBank(Request $r){ $data=$r->validate(['bank_name'=>['required','string','max:80'],'account_number'=>['required','string','max:40'],'account_name'=>['required','string','max:120'],'sort'=>['nullable','integer','min:0']]); BankAccount::updateOrCreate(['bank_name'=>$data['bank_name'],'account_number'=>$data['account_number']], ['account_name'=>$data['account_name'],'sort'=>$data['sort']??0,'is_active'=>true]); return back()->with('success','Rekening disimpan'); }
    public function deleteBank(BankAccount $bank){ $bank->delete(); return back()->with('success','Rekening dihapus'); }
    
    public function uploadKtaTemplate(Request $r)
    {
        $data = $r->validate([
            'kta_template' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'], // 5MB
        ]);
        
        if ($r->hasFile('kta_template')) {
            $path = $r->file('kta_template')->store('uploads/kta-templates', 'public');
            Setting::setValue('kta_template_path', $path);
        }
        
        return back()->with('success', 'Template KTA berhasil diupload');
    }
    
    public function saveKtaLayout(Request $r)
    {
        $data = $r->validate([
            'layout_config' => ['required', 'string'],
        ]);
        
        // Validate JSON
        $config = json_decode($data['layout_config'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }
        
        Setting::setValue('kta_layout_config', $data['layout_config']);
        
        return response()->json(['success' => true, 'message' => 'Konfigurasi layout KTA disimpan']);
    }
}
