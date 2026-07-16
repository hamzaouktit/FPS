<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use App\Models\Etablissement;
use App\Services\AdministrationEtablissementImportService;
use App\Jobs\ProcessAdministrationEtablissementImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportExcelFileJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_stores_file_and_dispatches_job_correctly()
    {
        Storage::fake('local');
        Queue::fake();
        Excel::fake(); // Fake excel pour éviter l'import réel
        
        $etablissement = Etablissement::factory()->create(['code_efp' => 'TEST01']);
        $user = User::factory()->create();
        $user->etablissement()->associate($etablissement);
        $user->save();
        $this->actingAs($user);

        // 1. Simuler l'upload d'un fichier Excel
        $file = UploadedFile::fake()->create('test_import.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $service = new AdministrationEtablissementImportService();
        $result = $service->import($file, $etablissement);

        $this->assertTrue($result);

        // Vérifier que le fichier est stocké sur le disque local
        $files = Storage::disk('local')->files('imports');
        $this->assertCount(1, $files);
        $relativePath = $files[0];
        
        $this->assertTrue(Storage::disk('local')->exists($relativePath));

        // 2. Vérifier que le Job a bien reçu le bon chemin
        Queue::assertPushed(ProcessAdministrationEtablissementImport::class, function ($job) use ($relativePath) {
            // Empêche de transmettre un objet UploadedFile! (Vérifie la sérialisation en chaîne)
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('relativePath');
            $property->setAccessible(true);
            $pathInJob = $property->getValue($job);

            return $pathInJob === $relativePath;
        });

        // 3. Simuler l'exécution du Job (Succès)
        $job = new ProcessAdministrationEtablissementImport($relativePath, 'TEST01', $user->id);
        $job->handle();

        // 5. Vérifier que le fichier est supprimé après succès
        $this->assertFalse(Storage::disk('local')->exists($relativePath));
    }

    public function test_job_fails_gracefully_and_keeps_file()
    {
        Storage::fake('local');
        Excel::fake();
        
        // Simuler un échec via mock
        Excel::shouldReceive('import')->andThrow(new \Exception('Erreur import simulée'));
        
        $relativePath = 'imports/test_failure.xlsx';
        Storage::disk('local')->put($relativePath, 'fake content');
        
        $this->assertTrue(Storage::disk('local')->exists($relativePath));

        $job = new ProcessAdministrationEtablissementImport($relativePath, 'TEST01', 1);

        try {
            $job->handle();
            $this->fail('Le job aurait dû lancer une exception');
        } catch (\Exception $e) {
            $this->assertEquals('Erreur import simulée', $e->getMessage());
        }

        // 6. Vérifier que le fichier n'a PAS été supprimé lors de l'échec
        $this->assertTrue(Storage::disk('local')->exists($relativePath));
    }
}
