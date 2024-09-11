<?php

namespace App\Http\Controllers;

use App\Exceptions\Forbidden;
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
        //
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
                $file_id = Str::random(10);
                while (File::where('id', $file_id)->exists()) {
                    $file_id = Str::random(10);
                }

                $file->storeAs(null, $file_id);

                $file_original_name = mb_strstr($file->getClientOriginalName(), '.', true);
                $file_extension = $file->extension();
                $file_full_name = $file_original_name . '.' . $file_extension;
                $file_number = 1;

                while (File::where('author_id', Auth::user()->id)->where('name', $file_full_name)->exists()) {
                    $file_full_name = "$file_original_name ($file_number).$file_extension";
                    $file_number += 1;
                }

                $new_file = File::create([
                    'id' => $file_id,
                    'name' => $file_full_name,
                    'url' => url('files/' . $file_id),
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
        // dump($file->users);
        if ($file->users->contains($new_user)) {
            throw new Forbidden('Этот пользователь уже добавлен');
        }

        $file->users()->attach($new_user);

        // return $file->users;

        // $file->fresh('users');
        $file->load('users');
        return FileAccessesResource::collection($file->users);
    }
}
