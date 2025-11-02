<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Storage\StorageServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MediaUploadController extends Controller
{
    public function __construct(
        private readonly StorageServiceInterface $storageService
    ) {
        //
    }

    #[OA\Post(
        path: '/api/v1/media',
        summary: 'Upload a file',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(property: 'file', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        tags: ['Media'],
        responses: [
            new OA\Response(response: 200, description: 'File uploaded successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,mov,avi,wmv,mp3,wav,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:102400', // max 100MB
        ]);

        $path = $this->storageService->upload($request->file('file'), 'media');
        $url = $this->storageService->getUrl($path);

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
