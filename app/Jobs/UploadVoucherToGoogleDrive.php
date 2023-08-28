<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Google_Client;
use Google\Service\Drive as Google_Service_Drive;

class UploadVoucherToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $voucher;
    protected $base64Image;

    public function __construct($voucher, $base64Image)
    {
        $this->voucher = $voucher;
        $this->base64Image = $base64Image;
    }

    public function handle()
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->base64Image));

        // Initialize Google Client
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        $service = new Google_Service_Drive($client);

        // Create metadata for the file
        $fileMetadata = new Google_Service_Drive\DriveFile([
            'name' => uniqid() . '.jpg', // Modify the naming convention as needed
            'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
        ]);

        // Upload the file to Google Drive
        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $imageData,
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        $DRIVE_CONFIG_URL = 'https://docs.google.com/uc?id=';
        $imageLink = $DRIVE_CONFIG_URL . $uploadedFile->id;

        $this->voucher->image = $imageLink;
        $this->voucher->save();
    }
}
