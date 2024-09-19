<?php

namespace App\Http\Controllers;

use App\Exceptions\Forbidden;
use App\Exceptions\NotFound;
use App\Http\Requests\AccessFileRequest;
use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\FileAccessesResource;
use App\Http\Resources\FileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $files = $user->files->filter(fn ($file) => $file->author_id == $user->id);

        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'file|mimes:doc,pdf,docx,zip,jpeg,jpg,png|max:2048',
            ]);
        } catch (ValidationException $e) {
            
        }

        $response = [];
        $files = $request->file('files');
        
        foreach ($files as $file) {
            if ($file->isValid()) {
                $file_original_name = mb_strstr($file->getClientOriginalName(), '.', true);
                $file_extension = $file->extension();
                $file_full_name = $file_original_name . '.' . $file_extension;
                $file_number = 1;

                while (File::where('author_id', Auth::user()->id)->where('name', $file_full_name)->exists()) {
                    $file_full_name = "$file_original_name ($file_number).$file_extension";
                    $file_number += 1;
                }

                $new_file_id = File::genId();
                $file->storeAs(null, $new_file_id);
                $new_file = File::create([
                    'id' => $new_file_id,
                    'name' => $file_full_name,
                    'url' => url('files/' . $new_file_id),
                    'author_id' => Auth::user()->id
                ]);
                $new_file->users()->attach(Auth::user());

                $response[] = [
                    'success' => true,
                    'message' => 'Success',
                    'name' => $new_file->name,
                    'url' => $new_file->url,
                    'file_id' => $new_file->id
                ];
            } else {
                $response[] = [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }

        return response($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        return Storage::download($file->id, $file->name);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        $data = $request->validated();

        $file->name = $data['name'];
        $file->save();

        return response([
            'success' => true,
            'message' => 'Renamed'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        Storage::delete($file->id);
        $file->users()->detach();
        $file->delete();

        return response([
            'success' => true,
            'message' => 'File already deleted'
        ]);
    }

    function addAccess(Request $request, File $file) {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $new_user = User::where('email', $data['email'])->first();
        if ($file->author_id == $new_user->id) {
            throw new Forbidden('Вы и так владелец файла');
        }
        if ($file->users->contains($new_user)) {
            throw new Forbidden('Этот пользователь уже добавлен');
        }

        $file->users()->attach($new_user);
        $file->load('users');

        return FileAccessesResource::collection($file->users);
    }
    function deleteAccess(Request $request, File $file) {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $deleted_user = User::where('email', $data['email'])->first();
        if ($file->author_id == $deleted_user->id) {
            throw new Forbidden();
        }
        if (!$file->users->contains($deleted_user)) {
            throw new NotFound();
        }

        $file->users()->detach($deleted_user);
        $file->load('users');

        return FileAccessesResource::collection($file->users);
    }

    function accesses() {
        $user = Auth::user();

        return $user->files->filter(fn ($file) => $file->pivot->user_id != $file->author_id)->map(fn ($file) => [
            'file_id' => $file->id,
            'name' => $file->name,
            'url' => $file->url
        ]);
    }
}
