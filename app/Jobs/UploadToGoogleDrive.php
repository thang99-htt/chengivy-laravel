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
use App\Models\ProductImage;

class UploadToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $productCat;
    protected $productBra;
    protected $colorId;
    protected $base64Image;

    public function __construct($productId, $productCat, $productBra, $colorId, $base64Image)
    {
        $this->productId = $productId;
        $this->productCat = $productCat;
        $this->productBra = $productBra;
        $this->colorId = $colorId;
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
            'name' => $this->productId . $this->productBra . $this->productCat . uniqid() . '.jpg', // Modify the naming convention as needed
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

        $productImage = new ProductImage();
        $productImage->product_id = $this->productId;
        $productImage->color_id = $this->colorId;
        $productImage->image = $imageLink;
        $productImage->save();
    }
}
