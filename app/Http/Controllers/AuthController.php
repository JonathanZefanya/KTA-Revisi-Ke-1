<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            // Log activity
            try {
                LoginActivity::create([
                    'user_id' => Auth::id(),
                    'email' => $credentials['email'],
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string)$request->userAgent(),0,255),
                    'logged_in_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // swallow logging error
            }
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            // Tab 1 fields
            'bu_name' => ['required','string','max:255'],
            'bentuk' => ['required','in:PT,CV,Koperasi'],
            'jenis' => ['required','in:BUJKN,BUJKA,BUJKPMA'],
            'kualifikasi' => ['required','in:Kecil / Spesialis 1,Menengah / Spesialis 2,Besar BUJKN / Spesialis 2,Besar PMA / Spesialis 2,BUJKA'],
            'penanggung_jawab' => ['required','string','max:255'],
            'npwp' => ['required','string','max:32'],
            'bu_email' => ['required','email','max:255','unique:users,email'],
            'bu_phone' => ['required','string','max:30'],
            'postal_code' => ['nullable','string','max:10'],
            'address' => ['required','string','max:500'],
            'asphalt_mixing_plant_address' => ['nullable','string','max:500'],
            'concrete_batching_plant_address' => ['nullable','string','max:500'],
            'province_code' => ['required','string','max:10'],
            'province_name' => ['required','string','max:100'],
            'city_code' => ['required','string','max:10'],
            'city_name' => ['required','string','max:100'],
            'password' => ['required','confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],

            // Tab 2 files
            'photo_pjbu' => ['required','image','mimes:png,jpg,jpeg','max:3072'], // 3MB
            'npwp_bu_file' => ['required','mimetypes:application/pdf','max:10240'],
            'akte_bu_file' => ['required','mimetypes:application/pdf','max:10240'],
            'nib_file' => ['required','mimetypes:application/pdf','max:10240'],
            'ktp_pjbu_file' => ['required','mimetypes:application/pdf','max:10240'],
            'npwp_pjbu_file' => ['required','mimetypes:application/pdf','max:10240'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'photo_pjbu.image' => 'Foto PJBU harus berupa gambar.',
        ]);

        // Create user (login uses BU email)
        $user = User::create([
            'name' => $data['penanggung_jawab'],
            'email' => $data['bu_email'],
            'password' => Hash::make($data['password']),
        ]);

        // Validate image ratio ~3:4 server-side & create thumbnail using GD (no external package)
        $photoFile = $request->file('photo_pjbu');
        $photoContents = @file_get_contents($photoFile->getRealPath());
        if($photoContents === false){
            return back()->withErrors(['photo_pjbu' => 'Tidak dapat membaca file foto.'])->withInput();
        }
        $img = @imagecreatefromstring($photoContents);
        if(!$img){
            return back()->withErrors(['photo_pjbu' => 'Format gambar tidak valid.'])->withInput();
        }
        $w = imagesx($img); $h = imagesy($img);
        if($h === 0){ imagedestroy($img); return back()->withErrors(['photo_pjbu'=>'Gambar rusak.'])->withInput(); }
        $ratio = $w / $h; // expect ~0.75
        if(abs($ratio - 0.75) > 0.03){
            imagedestroy($img);
            return back()->withErrors(['photo_pjbu' => 'Rasio foto harus 3:4 (portrait).'])->withInput();
        }
        // Store original
        $storageDir = 'uploads/company';
        $photoPath = $photoFile->store($storageDir, 'public');
        // Create 300x400 thumbnail (center crop fit)
        $thumbW = 300; $thumbH = 400; $thumbImg = imagecreatetruecolor($thumbW,$thumbH);
        // Fill white
        $white = imagecolorallocate($thumbImg,255,255,255); imagefill($thumbImg,0,0,$white);
        // Calculate crop to maintain 3:4 while covering
        $targetRatio = 0.75; // w/h
        $srcRatio = $w / $h;
        if($srcRatio > $targetRatio){
            // source too wide -> fit height
            $scaledH = $thumbH; $scale = $scaledH / $h; $scaledW = (int)round($w * $scale); $sx = (int)max(0, ($scaledW - $thumbW)/2 * (1/$scale)); $sy = 0; $cropW = (int)round($thumbW / $scale); $cropH = $h;
        } else {
            // source too tall -> fit width
            $scaledW = $thumbW; $scale = $scaledW / $w; $scaledH = (int)round($h * $scale); $sx = 0; $sy = (int)max(0, ($scaledH - $thumbH)/2 * (1/$scale)); $cropW = $w; $cropH = (int)round($thumbH / $scale);
        }
        imagecopyresampled($thumbImg,$img,0,0,$sx,$sy,$thumbW,$thumbH,$cropW,$cropH);
        ob_start(); imagejpeg($thumbImg,null,85); $thumbData = ob_get_clean();
        imagedestroy($img); imagedestroy($thumbImg);
        $thumbName = pathinfo($photoPath, PATHINFO_FILENAME).'_thumb.jpg';
        $thumbPath = $storageDir.'/'.$thumbName;
        Storage::disk('public')->put($thumbPath,$thumbData);
    $npwpBuPath = $request->file('npwp_bu_file')->store($storageDir, 'public');
    $akteBuPath = $request->file('akte_bu_file')->store($storageDir, 'public');
        $nibPath = $request->file('nib_file')->store($storageDir, 'public');
        $ktpPath = $request->file('ktp_pjbu_file')->store($storageDir, 'public');
        $npwpPjbuPath = $request->file('npwp_pjbu_file')->store($storageDir, 'public');

        $company = Company::create([
            'name' => $data['bu_name'],
            'bentuk' => $data['bentuk'],
            'jenis' => $data['jenis'],
            'kualifikasi' => $data['kualifikasi'],
            'membership_type' => 'AB', // Default Anggota Biasa saat register
            'penanggung_jawab' => $data['penanggung_jawab'],
            'npwp' => $data['npwp'],
            'email' => $data['bu_email'],
            'phone' => $data['bu_phone'],
            'address' => $data['address'],
            'asphalt_mixing_plant_address' => $data['asphalt_mixing_plant_address'] ?? null,
            'concrete_batching_plant_address' => $data['concrete_batching_plant_address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'province_code' => $data['province_code'],
            'province_name' => $data['province_name'],
            'city_code' => $data['city_code'],
            'city_name' => $data['city_name'],
            'photo_pjbu_path' => $photoPath,
            'photo_pjbu_thumb_path' => $thumbPath,
            'npwp_bu_path' => $npwpBuPath,
            'akte_bu_path' => $akteBuPath,
            'nib_file_path' => $nibPath,
            'ktp_pjbu_path' => $ktpPath,
            'npwp_pjbu_path' => $npwpPjbuPath,
        ]);

        $company->users()->syncWithoutDetaching($user->id);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil, selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout.');
    }

}
