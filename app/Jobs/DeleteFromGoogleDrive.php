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

class DeleteFromGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageId;
    protected $imageLink;

    public function __construct($imageId, $imageLink)
    {
        $this->imageId = $imageId;
        $this->imageLink = $imageLink;
    }

    public function handle()
    {
        // Xóa hình ảnh từ Google Drive
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        $service = new Google_Service_Drive($client);
        
        $DRIVE_CONFIG_URL = 'https://docs.google.com/uc?id=';
        
        $imageId = substr($this->imageLink, strlen($DRIVE_CONFIG_URL));
        $service->files->delete($imageId);

        // Xóa hình ảnh từ cơ sở dữ liệu
        $image = ProductImage::where('image', $this->imageLink)->first();
        if ($image) {
            $image->delete();
        }
    }   
}
