<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,gif,svg,pdf|max:2048',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        $upload = Upload::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'File uploaded successfully', 'file' => $upload]);
    }

    public function listFiles()
    {
        $files = Upload::where('user_id', auth()->id())->get();
        return response()->json($files);
    }

    public function deleteFile($id)
    {
        $file = Upload::findOrFail($id);

        if ($file->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
