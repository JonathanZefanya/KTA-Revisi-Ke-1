<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateKtaPdf extends Command
{
    protected $signature = 'kta:pdf {user_id?} {--full} {--output= : Output path (defaults storage/app/KTA-<number>.pdf)}';
    protected $description = 'Generate a KTA membership card PDF for a user (plain default, --full for panel layout)';

    public function handle(): int
    {
        $userId = $this->argument('user_id');
        if(!$userId){
            $user = User::whereNotNull('membership_card_number')->orderByDesc('updated_at')->first();
            if(!$user){
                $this->error('No user with a membership card found.');
                return 1;
            }
            $this->warn('No user_id supplied. Using user '.$user->id.' ('.$user->name.')');
        } else {
            $user = User::find($userId);
            if(!$user){
                $this->error('User not found');
                return 1;
            }
        }
        if(!$user->hasActiveMembershipCard()){
            $this->error('User does not have an active membership card.');
            return 1;
        }
        $logo = \App\Models\Setting::getValue('site_logo_path');
        $signature = \App\Models\Setting::getValue('signature_path');
        $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
        $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
        $qrPngBase64 = base64_encode($qrPngData);
        $full = (bool)$this->option('full');

        $pdf = Pdf::loadView('kta.pdf',[ 'user'=>$user,'qrSvg'=>$qrSvg,'qrPng'=>$qrPngBase64,'validationUrl'=>$validationUrl,'logo'=>$logo,'signature'=>$signature,'full'=>$full ])->setPaper('a4','landscape');
        $safeNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $outPath = $this->option('output') ?: storage_path('app/KTA-'.$safeNumber.($full?'-full':'-plain').'.pdf');
        file_put_contents($outPath, $pdf->output());
        $this->info('Generated: '.$outPath);
        // quick page count heuristic (count /Type /Page occurrences)
        $pageCount = substr_count($pdf->output(), '/Type /Page');
        $this->line('Detected pages: '.$pageCount);
        return 0;
    }
}
