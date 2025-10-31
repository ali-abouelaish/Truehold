<?php

namespace App\Http\Controllers;

use App\Models\ApProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ApPropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = ApProperty::orderByDesc('created_at')->paginate(20);
        if (auth()->check()) {
            $user = auth()->user();
            $user->ap_properties_last_seen_at = now();
            $user->save();
        }
        return view('admin.ap-properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ap-properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'availability' => 'nullable|date',
            'property_name' => 'required|string|max:255',
            'pcm' => 'nullable|integer|min:0',
            'postcode' => 'nullable|string|max:20',
            'area' => 'nullable|string|max:255',
            'n_rooms' => 'nullable|integer|min:0',
            'n_bathrooms' => 'nullable|integer|min:0',
            'status' => 'required|string|in:booked,available_on_date,renewal,empty_available_now',
            'type' => 'required|string|in:full_flat,house_share',
            'is_room' => 'sometimes|boolean',
            'room_label' => 'nullable|string|max:100|required_if:type,house_share|required_if:is_room,1',
            'couples_allowed' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:20480',
        ]);

        $data = $validated;
        $data['is_room'] = $request->boolean('is_room');
        $data['couples_allowed'] = $request->boolean('couples_allowed');
        $data['images_url'] = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $data['images_url'][] = $this->storeUploadedImage($image);
                }
            }
        }

        $property = ApProperty::create($data);

        return redirect()
            ->route('admin.ap-properties.edit', $property)
            ->with('success', 'AP Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApProperty $ap_property)
    {
        return view('admin.ap-properties.show', ['property' => $ap_property]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApProperty $ap_property)
    {
        return view('admin.ap-properties.edit', ['property' => $ap_property]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApProperty $ap_property)
    {
        $validated = $request->validate([
            'availability' => 'nullable|date',
            'property_name' => 'required|string|max:255',
            'pcm' => 'nullable|integer|min:0',
            'postcode' => 'nullable|string|max:20',
            'area' => 'nullable|string|max:255',
            'n_rooms' => 'nullable|integer|min:0',
            'n_bathrooms' => 'nullable|integer|min:0',
            'status' => 'required|string|in:booked,available_on_date,renewal,empty_available_now',
            'type' => 'required|string|in:full_flat,house_share',
            'is_room' => 'sometimes|boolean',
            'room_label' => 'nullable|string|max:100|required_if:type,house_share|required_if:is_room,1',
            'couples_allowed' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:20480',
        ]);

        $data = $validated;
        $data['is_room'] = $request->boolean('is_room');
        $data['couples_allowed'] = $request->boolean('couples_allowed');

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $paths[] = $this->storeUploadedImage($image);
                }
            }
            $data['images_url'] = $paths;
        } else {
            $data['images_url'] = $ap_property->images_url ?? [];
        }

        $ap_property->update($data);

        return redirect()
            ->route('admin.ap-properties.show', $ap_property)
            ->with('success', 'AP Property updated successfully.');
    }

    /**
     * Store an uploaded image. If the file is HEIC/HEIF and conversion is available,
     * convert to JPEG for broad browser compatibility.
     */
    private function storeUploadedImage($uploadedFile): string
    {
        try {
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $mime = $uploadedFile->getMimeType();

            if (in_array($extension, ['heic', 'heif']) || in_array($mime, ['image/heic', 'image/heif'])) {
                // Convert HEIC/HEIF to WEBP using Intervention Image (requires driver support)
                $image = Image::make($uploadedFile->getRealPath());
                $encoded = $image->encode('webp', 88);
                $filename = 'ap-properties/' . uniqid('ap_', true) . '.webp';
                Storage::disk('public')->put($filename, (string) $encoded);
                return $filename;
            }

            // Non-HEIC: store as-is
            return $uploadedFile->store('ap-properties', 'public');
        } catch (\Throwable $e) {
            // On failure, fall back to storing original
            return $uploadedFile->store('ap-properties', 'public');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApProperty $ap_property)
    {
        $ap_property->delete();

        return redirect()
            ->route('admin.ap-properties.index')
            ->with('success', 'AP Property deleted successfully.');
    }

    /**
     * Delete a single image by index from the property's images_url array.
     */
    public function destroyImage(ApProperty $ap_property, int $index)
    {
        $images = $ap_property->images_url ?? [];
        if (!is_array($images) || !array_key_exists($index, $images)) {
            return back()->with('error', 'Image not found.');
        }

        $path = $images[$index];

        try {
            if ($path && !preg_match('/^https?:/i', $path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            // ignore storage errors, proceed to remove from array
        }

        // Remove and reindex
        array_splice($images, $index, 1);
        $ap_property->images_url = $images;
        $ap_property->save();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image deleted.');
    }
}


