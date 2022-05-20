<?php

namespace RichanFongdasen\Glide;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use RichanFongdasen\Glide\Facade\Glide;

class GlideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Glide::validateRequest($this);
    }

    /**
     * Get the real storage asset path of the request.
     *
     * @return string
     */
    public function getAssetPath(): string
    {
        $prefix = Str::finish(Str::start(config('glide.asset_url_prefix'), '/'), '/');

        return Str::of($this->getPathInfo())->ltrim($prefix);
    }

    /**
     * Get the validated glide parameters from the request.
     *
     * @param array|null $default
     * @return array
     */
    public function getGlideParams(array $default = null): array
    {
        $params = $this->validated();
        unset($params['s']);

        if ($default === null) {
            $default = ['fm' => config('glide.default_image_format')];
        }

        return array_merge($default, $params);
    }

    /**
     * Get the Glide signature validation parameters.
     *
     * @return array
     */
    public function getSignatureValidationParams(): array
    {
        return array_merge([
            'hostname' => $this->getHttpHost(),
        ], $this->all());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'bri' => 'nullable|integer|min:-100|max:100',
            'con' => 'nullable|integer|min:-100|max:100',
            'dpr' => 'nullable|integer|min:1|max:8',
            'filt' => 'nullable|in:greyscale,sepia',
            'fit' => 'nullable|in:contain,max,fill,fill-max,stretch,crop,crop-top-left,crop-top,crop-top-right,crop-left,crop-center,crop-right,crop-bottom-left,crop-bottom,crop-bottom-right',
            'flip' => 'nullable|in:v,h,both',
            'fm' => 'nullable|in:jpg,png,gif,webp,avif',
            'gam' => 'nullable|numeric|min:0.1|max:9.99',
            'h' => 'nullable|integer|min:50|max:2000',
            'or' => 'nullable|in:auto,0,90,180,270',
            'q' => 'nullable|integer|min:0|max:100',
            'sharp' => 'nullable|integer|min:0|max:100',
            'w' => 'nullable|integer|min:50|max:2000',
        ];
    }
}