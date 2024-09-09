<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validateWithBag('post', [
            'files' => 'required|array',
            'files.*' => 'file|mimes:doc,pdf,docx,zip,jpeg,jpg,png|max:2048',
        ]);
        
        $files = $request->file('files');

        $uploaded_files = [];

        foreach ($files as $file) {
            $path = $file->store();

            $new_file = File::create([
                'id' => Str::random(10),
                'name' => $file->getClientOriginalName(),
                'url' => Storage::url($path)
            ]);
            $uploaded_files[] = $new_file;
        }

        return response(array_map(function ($file) {
            return [
                'success' => true,
                'message' => 'Success',
                'name' => $file->name,
                'url' => $file->url,
                'file_id' => $file->id
            ];
        }, $uploaded_files));
        // File::create()
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }
}
