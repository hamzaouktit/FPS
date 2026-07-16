<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AdministrationEtablissementImport;
use Illuminate\Support\Str;

class ProcessAdministrationEtablissementImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 minutes max
    public $tries = 1;

    protected $relativePath;
    protected $codeEfp;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $relativePath, $codeEfp, $userId)
    {
        $this->relativePath = $relativePath;
        $this->codeEfp = $codeEfp;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Job import: Démarrage pour EFP {$this->codeEfp} par User ID {$this->userId}", [
            'relative_path' => $this->relativePath
        ]);
        
        $startTime = microtime(true);
        $importId = Str::uuid()->toString();
        
        // 1. Vérifier si le fichier existe toujours sur le disque local
        if (! Storage::disk('local')->exists($this->relativePath)) {
            throw new \RuntimeException(
                "Fichier d'import introuvable : {$this->relativePath}"
            );
        }

        // 2. Déduire le vrai chemin absolu par rapport à config/filesystems.php ('local' root)
        $absolutePath = Storage::disk('local')->path($this->relativePath);

        try {
            // Lancer l'importation par chunk
            Excel::import(new AdministrationEtablissementImport($this->codeEfp, $importId), $absolutePath);
            
            $duration = round(microtime(true) - $startTime, 2);
            
            Log::info("Job import terminé avec succès", [
                'import_id' => $importId,
                'code_efp' => $this->codeEfp,
                'duree_secondes' => $duration
            ]);

            // Nettoyage du fichier SEULEMENT si succès
            Storage::disk('local')->delete($this->relativePath);

        } catch (\Throwable $e) {
            Log::error('Import Excel failed', [
                'relative_path' => $this->relativePath,
                'absolute_path' => $absolutePath,
                'exists' => Storage::disk('local')->exists($this->relativePath),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('Import job permanently failed', [
            'relative_path' => $this->relativePath,
            'message' => $exception->getMessage(),
        ]);
    }
}
