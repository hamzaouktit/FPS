<?php

namespace App\Services;

use App\Jobs\ProcessAdministrationEtablissementImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdministrationEtablissementImportService
{
    /**
     * Stoque le fichier et lance le Job d'importation
     */
    public function import(UploadedFile $file, $etablissement)
    {
        // 1. Stocker le fichier avec le disque Laravel 'local'
        $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
        $relativePath = $file->storeAs('imports', $filename, 'local');

        // Vérification de sécurité avant d'envoyer au Job
        if (!Storage::disk('local')->exists($relativePath)) {
            throw new \RuntimeException(
                "Le fichier n'a pas été enregistré : {$relativePath}"
            );
        }
        
        \Illuminate\Support\Facades\Log::info('Import file stored', [
            'relative_path' => $relativePath,
            'absolute_path' => Storage::disk('local')->path($relativePath),
            'exists' => Storage::disk('local')->exists($relativePath),
        ]);

        // 2. Lancer le Job d'importation avec le chemin relatif
        ProcessAdministrationEtablissementImport::dispatch(
            $relativePath,
            $etablissement->code_efp,
            Auth::id()
        );

        return true;
    }
}
