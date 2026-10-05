<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DatabaseBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {

        return Redirect()->back();

        // Artisan::call('database:backup');
        // Artisan::call('storage:link');



        $files = scandir(public_path('storage/backup'));

        $filePath = public_path('storage/backup/');

        return view('database-backup.database-backup', compact('files', 'filePath'));
    }

    public function download($file)
    {
        $download_file = public_path('storage/backup/'.$file);

        if(! empty($file)) {
            // Check file is exists on given path.
            if(file_exists($download_file)) {
                header('Content-Disposition: attachment; filename='.$file);

                readfile($download_file);

                exit;
            } else {
                echo 'File does not exists on given path';
            }
        }
    }

    public function manualBackup()
    {
        Artisan::call('database:backup');

        return back();
    }

    // $path = public_path('delete');

        // if ($handle = opendir($path)) {
        //     while (false !== ($file = readdir($handle))) {
        //         // dd($file);
        //         if ((time() - filectime($path.'/'.$file)) > 86400) {  // 86400 = 60*60*24 = 1 day

        //             dd(time() - filectime($path.'/'.$file));
        //             // if (strripos($file, '.sql') !== false) {
        //             //     unlink($path.'/'.$file);
        //             // }
        //         }
        //     }
        // }

    public function delete($file): RedirectResponse
    {
        $unlink = public_path('storage/backup/'.$file);

        unlink($unlink);

        return back();
    }
}
