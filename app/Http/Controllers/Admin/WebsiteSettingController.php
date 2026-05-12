<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingRequest;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    public function edit(): View
    {
        $settings = WebsiteSetting::query()->firstOrFail();

        return view('admin.website-settings.edit', compact('settings'));
    }

    public function update(UpdateWebsiteSettingRequest $request): RedirectResponse
    {
        $settings = WebsiteSetting::query()->firstOrFail();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $this->replaceLogo($settings, $request->file('logo'));
        }

        unset($data['logo']);
        $settings->fill($data);
        $settings->save();

        return redirect()
            ->route('admin.website-settings.edit')
            ->with('status', 'Website settings saved.');
    }

    protected function replaceLogo(WebsiteSetting $settings, UploadedFile $file): void
    {
        $dir = public_path('uploads/site');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $this->deletePublicLogoIfOwned($settings->logo_path);

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
        $basename = 'logo_'.now()->format('YmdHis').'_'.substr(sha1((string) microtime(true)), 0, 8).'.'.$ext;
        $file->move($dir, $basename);

        $settings->logo_path = url('uploads/site/'.$basename);
    }

    /**
     * Remove previous file from /public/uploads/site when it was stored as a local app URL.
     */
    protected function deletePublicLogoIfOwned(?string $storedPath): void
    {
        $storedPath = trim((string) $storedPath);
        if ($storedPath === '') {
            return;
        }

        $path = parse_url($storedPath, PHP_URL_PATH);
        if (is_string($path) && str_starts_with($path, '/uploads/site/')) {
            $full = public_path(ltrim($path, '/'));
            if (File::isFile($full)) {
                File::delete($full);
            }

            return;
        }

        if (str_starts_with($storedPath, 'uploads/site/')) {
            $full = public_path($storedPath);
            if (File::isFile($full)) {
                File::delete($full);
            }
        }
    }
}
