<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Concerns\ValidatesLocation;
use App\Models\Post;
use App\Services\LocationDataService;
use App\Services\MediaUploadService;
use App\Services\StoryGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ProfilePageController extends Controller
{
    use ValidatesLocation;

    public function __construct(
        private LocationDataService $locations,
        private MediaUploadService $mediaUpload,
        private StoryGroupService $storyGroups,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $posts = Post::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->get();

        $ownStoryGroup = $this->storyGroups->loadOwnStoryGroup($user);

        return view('web.profile', compact('user', 'posts', 'ownStoryGroup'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'phone' => 'sometimes|string|max:20',
        ]);

        if ($request->hasAny(['country', 'city', 'district'])) {
            $validated = array_merge($validated, $this->validateLocationInput($request, $this->locations, false));
        }

        $request->user()->update($validated);

        return back()->with('success', 'Profil güncellendi.');
    }

    public function uploadPhoto(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $user = $request->user();

        try {
            $this->mediaUpload->deleteByUrl($user->profile_photo_url);
            $url = $this->mediaUpload->uploadProfilePhoto($request->file('photo'));
            $user->update(['profile_photo_url' => $url]);
        } catch (RuntimeException) {
            $message = 'Profil fotoğrafı yüklenemedi. Lütfen tekrar deneyin.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }

            return back()->withErrors(['photo' => $message]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['profile_photo_url' => $url],
            ]);
        }

        return back()->with('success', 'Profil fotoğrafı güncellendi.');
    }
}
